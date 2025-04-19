# 🌱 Web Gizi Balita
## 🚀 Installation Guide
## 1. 🧑‍💻 Clone the Repository
```bash
git clone https://github.com/Jaa0/Web-Gizi-Balita.git
cd web-gizi-balita
```
## 2. 🌐 Backend Setup (CodeIgniter4 + PHP) & (Python)
**A. Install PHP (for CodeIgniter 4)**
Make sure you have PHP (v7.4 or above) installed
```bash
php -v
```
If PHP isn't installed, download it from the [official website](https://www.php.net/).

**B. Install Composer**

Download the Composer installer from [Composer](https://getcomposer.org/Composer-Setup.exe)

**C. Install CodeIgniter Dependencies**
```bash
composer install
```
**D. Install Python (if you installed it already, skip this)**
1. Download Python
- Go to the official [Python website](https://www.python.org/downloads/)
- Download the version of Python that is suitable for your operating system (Windows, macOS, Linux):

  - Windows: Download the installer for Windows.

  - macOS: Python comes pre-installed, but you can download a newer version if necessary.

  - Linux: Python is often pre-installed on Linux, but you can install it via the terminal if needed.

2. Install Python
- Windows:
  - Run the downloaded installer.

  - Ensure that you check the box labeled Add Python to **PATH** during installation.

  - Click Install Now.

3. Check on terminal if Python has Successfully Downloaded
```bash
python --version
```

**E. Create and activate a virtual environment (Optional)**
```bash
# On Windows
python -m venv venv
venv\Scripts\activate

# On macOS/Linux
python3 -m venv venv
source venv/bin/activate
```
**F. Install Python dependencies:**
```bash
pip install -r requirements.txt
```
## 3. 🎨 Frontend Setup (Tailwind CSS)
```bash
# Install Node.js dependencies
npm install
```
## 4. 🛠️ Build Tailwind CSS
While developing, you can run Tailwind in watch mode:
```bash
npm run build
```
This will watch for CSS changes and output the compiled file to ./public/css/main.css.
## 5. 🏃 Run the App
because this app use CodeIgniter4 and Python, you need to run 2 command on different terminal:
```bash
php spark serve
```
```bash
python app/Python/app.py
```
