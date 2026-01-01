# 🔐 Authentication & Profile Management System

A simple authentication system built as part of an internship task.
The application supports **user registration**, **login**, and **profile management** using secure, modern practices.

---

## 🚀 Live URL

👉 [https://guvi-task-app.onrender.com/](https://guvi-task-app.onrender.com/)

---


## 🔁 Application Flow

```
Register → Login → Profile
```

* Register using email and password
* Login with registered credentials
* Successful login redirects to profile page
* Profile details can be viewed and updated

---

## 🛠 Tech Stack

* **Frontend:** HTML, CSS, Bootstrap, JavaScript, jQuery (AJAX)
* **Backend:** PHP
* **Databases:**

  * MySQL – Authentication data
  * MongoDB – User profile data
* **Session Management:** 
  * Redis – Backend session store
  * LocalStorage – Client-side token
* **Hosting:** 
  * Render
  * Aiven (MySQL)

---

## 📂 Folder Structure

```
project/
├── css/style.css
├── js/register.js
├── js/login.js
├── js/profile.js
├── php/register.php
├── php/login.php
├── php/profile.php
├── index.html
├── register.html
├── login.html
└── profile.html

```

---

## 🔐 Key Rules Followed

* No form submission (AJAX only)
* No PHP Sessions
* Prepared Statements used in MySQL
* Session stored in Redis
* Token stored in browser localStorage
* HTML, CSS, JS, PHP kept in separate files
* Responsive UI using Bootstrap

---

## ⚙️ Requirements

* PHP (8+)
* MySQL
* MongoDB
* Redis
* Composer
* Apache / XAMPP / WAMP

---

## ▶️ How to Run Locally

### 1️⃣ Clone the repository

```bash
git clone https://github.com/joshwa2003/guvi-task.git
```

### 2️⃣ Install PHP dependencies

```bash
composer install
```

### 3️⃣ Start required services

```bash
# MySQL
sudo service mysql start

# MongoDB
sudo service mongod start

# Redis
redis-server
```

### 4️⃣ Run the application

* Place the project in `htdocs`
* Open in browser:

```text
http://localhost/guvi-task
```

---

## 🧪 Usage

1. Register a new user
2. Login using credentials
3. Access profile page
4. Update profile details
5. Logout

---

## 👨‍💻 Author

**Joshwa**
Web Developer | MCA Student

---
