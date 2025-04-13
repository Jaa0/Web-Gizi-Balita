from flask import Flask, request, jsonify, Response
from collections import OrderedDict
import joblib
import json
import datetime
import pandas as pd
import os

app = Flask(__name__)
app.secret_key = "BAA"

BASE_DIR = os.path.dirname(os.path.abspath(__file__))
# Load models and scalers
try:
    model = joblib.load(os.path.join(BASE_DIR, "Models", "best_svm_model_gizi.joblib"))
    model2 = joblib.load(os.path.join(BASE_DIR, "Models", "best_rf_model_bb.joblib"))
    model3 = joblib.load(os.path.join(BASE_DIR, "Models", "best_xgboost_model_tb.joblib"))
    scaler = joblib.load(os.path.join(BASE_DIR, "Models", "scalergizi.joblib"))
    scaler2 = joblib.load(os.path.join(BASE_DIR, "Models", "scaler.joblib"))
    scaler3 = joblib.load(os.path.join(BASE_DIR, "Models", "scalertb.joblib"))
except FileNotFoundError as e:
    raise RuntimeError(f"Model or scaler file missing: {e}")
except Exception as e:
    raise RuntimeError(f"Error loading model/scaler: {e}")

# Helper function to calculate age
def calculate_age(birthdate):
    today = datetime.date.today()
    delta = today - birthdate
    years = delta.days // 365
    months = (delta.days % 365) // 30
    return years, months

# API endpoint for health check
@app.route("/health", methods=["GET"])
def health_check():
    return jsonify({"status": "API is running"}), 200

# API endpoint for predictions
@app.route("/predict", methods=["POST"])
def predict():
    data = request.json

    # Validate input
    required_fields = ["nik", "birthdate", "gender", "height", "weight"]
    missing_fields = [field for field in required_fields if field not in data]

    if missing_fields:
        return jsonify({"error": f"Missing fields: {', '.join(missing_fields)}"}), 400

    try:
        # Parse and validate inputs
        nik = data["nik"]
        if len(nik) != 16 or not nik.isdigit():
            return jsonify({"error": "NIK must be a 16-digit number."}), 400

        birthdate = datetime.datetime.strptime(data["birthdate"], "%Y-%m-%d").date()
        if birthdate > datetime.date.today():
            return jsonify({"error": "Birthdate cannot be in the future."}), 400

        gender = data["gender"]
        if gender not in ["Pria", "Wanita"]:
            return jsonify({"error": "Gender must be 'Pria' or 'Wanita'."}), 400

        height = float(data["height"])
        weight = float(data["weight"])
        if not (40 <= height <= 150):
            return jsonify({"error": "Height must be between 40 and 150 cm."}), 400
        if not (3 <= weight <= 50):
            return jsonify({"error": "Weight must be between 3 and 50 kg."}), 400

        # Calculate age in months
        years, months = calculate_age(birthdate)
        age_in_months = years * 12 + months

        # Encode gender
        gender_value = 0 if gender == "Pria" else 1

        # Weight Prediction Logic
        input_data_bb = pd.DataFrame([[age_in_months, gender_value, weight]], 
                                    columns=['Age in Month', 'Gender', 'Weight'])
        input_data_scaled_bb = scaler2.transform(input_data_bb[['Age in Month', 'Weight']])
        input_data_scaled_bb = pd.DataFrame(input_data_scaled_bb, columns=['Age in Month', 'Weight'])
        input_data_scaled_bb['Gender'] = input_data_bb['Gender'].values
        input_data_scaled_bb = input_data_scaled_bb[['Age in Month', 'Gender', 'Weight']]
        weight_prediction = model2.predict(input_data_scaled_bb)[0]
        
        # Gizi Prediction Logic
        input_data = pd.DataFrame([[age_in_months, gender_value, height, weight]], 
                        columns=['Age in Month', 'Gender', 'Height', 'Weight'])
        input_data_scaled = scaler.transform(input_data[['Age in Month', 'Height', 'Weight']])
        input_data_scaled = pd.DataFrame(input_data_scaled, columns=['Age in Month', 'Height', 'Weight'])
        input_data_scaled['Gender'] = input_data['Gender'].values
        input_data_scaled = input_data_scaled[['Age in Month', 'Gender', 'Height', 'Weight']]
        nutrition_prediction = model.predict(input_data_scaled)[0]

        # Height Prediction Logic
        input_data_tb = pd.DataFrame([[age_in_months, gender_value, height]], 
                                    columns=['Age in Month', 'Gender', 'Height'])
        input_data_scaled_tb = scaler3.transform(input_data_tb[['Age in Month', 'Height']])
        input_data_scaled_tb = pd.DataFrame(input_data_scaled_tb, columns=['Age in Month', 'Height'])
        input_data_scaled_tb['Gender'] = input_data_tb['Gender'].values
        input_data_scaled_tb = input_data_scaled_tb[['Age in Month', 'Gender', 'Height']]
        height_prediction = model3.predict(input_data_scaled_tb)[0]

        if weight_prediction == 0:  # Berat Badan Sangat Kurang
            if nutrition_prediction in [2, 3, 4, 5]:  # Gizi Baik, Berisiko Gizi Lebih, Gizi Lebih, Obesitas
                nutrition_prediction = 1  # Gizi Kurang

        if weight_prediction == 1:  # Berat Badan Kurang
            if nutrition_prediction in [2, 3, 4]:  # Gizi Baik, Berisiko Gizi Lebih, Gizi Lebih
                nutrition_prediction = 1  # Gizi Kurang

        if weight_prediction == 3:  # Risiko Berat Badan Lebih
            if nutrition_prediction == 0:  # Gizi Buruk
                nutrition_prediction = 1  # Gizi Kurang

        if height_prediction == 3:  # Tinggi
            if nutrition_prediction in [0, 1]:  # Gizi Buruk, Gizi Kurang
                nutrition_prediction = 2  # Gizi Baik
        
        if height_prediction == 2:  # Sangat Pendek
            if weight_prediction == 2:  # Berat Badan Normal
                nutrition_prediction = 1  # Gizi Kurang

        if height_prediction == 1:  # Pendek
            if weight_prediction == 2:  
                nutrition_prediction = 1  # Gizi Kurang
        
        if height_prediction in [1, 2]: 
            if nutrition_prediction in [4, 5]:  
                nutrition_prediction = 2  
        
        if height_prediction == 3: 
            if weight_prediction in [0, 1]: 
                nutrition_prediction = 1 
        
        if weight_prediction==2:
            if height_prediction in [0]:
                nutrition_prediction = 2

        # Map predictions to categories
        nutrition_mapping = {
            0: "Gizi Buruk",
            1: "Gizi Kurang",
            2: "Gizi Baik",
            3: "Berisiko Gizi Lebih",
            4: "Gizi Lebih",
            5: "Obesitas"
        }

        weight_mapping = {
            0: 'Berat Badan Sangat Kurang',
            1: 'Berat Badan Kurang',
            2: 'Berat Badan Normal',
            3: 'Risiko Berat Badan Lebih'
        }

        height_mapping = {
            0: 'Tinggi Normal',
            1: 'Pendek',
            2: 'Sangat Pendek',
            3: 'Tinggi'
        }
        
        response_data = OrderedDict([
            ("nik", nik),
            ("age_in_months", age_in_months),
            ("gender", gender),
            ("height", height),
            ("weight", weight),
            ("nutrition_status", nutrition_mapping.get(nutrition_prediction, "Unknown")),
            ("weight_category", weight_mapping.get(weight_prediction, "Unknown")),
            ("height_category", height_mapping.get(height_prediction, "Unknown"))
        ])
        return Response(
            json.dumps(response_data, ensure_ascii=False), 
            mimetype='application/json', 
            status=200
        )

    except Exception as e:
        return jsonify({"error": f"An error occurred: {str(e)}"}), 500

if __name__ == "__main__":
    app.run(debug=True,port=5000)