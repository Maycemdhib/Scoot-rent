ScootRent is a full-stack web application for renting scooters online.
It allows users to browse available scooters, make reservations, and manage their bookings.
Admins can manage scooters and reservations through a dedicated dashboard.

🚀 Features
👤 User Features
User registration & login
Multilingual support (FR / EN)
Browse available scooters
Make reservations with date & time
View personal reservations
Session-based authentication
🛠️ Admin Features
Admin dashboard
Manage scooters (CRUD)
Manage reservations
Update reservation status (pending / confirmed / cancelled)
🧰 Tech Stack
Backend: PHP 
Frontend: HTML, CSS, Bootstrap 5
Database: MySQL
Other: Sessions, Cookies, PDO
📁 Project Structure

/controllers
/models
/views
/helpers
/config
/public/css
/public/uploads
/lang

🔐 Authentication
Secure login system using PHP sessions
Passwords should be hashed using password_hash()
Role-based access (user / admin)
🌍 Multilingual Support

The app supports:

🇫🇷 French
🇬🇧 English

Language is stored using:

GET parameters
Sessions
Cookies
📸 Screenshots (optional)

