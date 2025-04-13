# Web-Gizi-Balita

Installation Guide
1. 🧑‍💻 Clone the Repository
bash
Copy
Edit
git clone https://github.com/your-username/webgizibalita.git
cd webgizibalita
2. 🌐 Backend Setup (Python + Flask)
a. Create and activate a virtual environment:
bash
Copy
Edit
# On Windows
python -m venv venv
venv\Scripts\activate

# On macOS/Linux
python3 -m venv venv
source venv/bin/activate
b. Install Python dependencies:
bash
Copy
Edit
pip install -r requirements.txt
3. 🎨 Frontend Setup (Tailwind CSS)
bash
Copy
Edit
# Install Node.js dependencies
npm install
4. 🛠️ Build Tailwind CSS
While developing, you can run Tailwind in watch mode:

bash
Copy
Edit
npm run build
This will watch for CSS changes and output the compiled file to ./public/css/main.css.

5. 🏃 Run the App
Depending on your app’s structure (e.g., app.py or main.py), you can start the backend server like this:

bash
Copy
Edit
flask run
Or, if you're using Gunicorn in production:

bash
Copy
Edit
gunicorn app:app
