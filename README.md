<img width="625" height="289" alt="image" src="https://github.com/user-attachments/assets/f2b04191-8dd1-43c8-a77f-5307a220fa12" />


# 🏨 Hotel Booking Database Management
A web application for managing a hotel booking system database. The project provides a user-friendly interface for performing CRUD operations, searching, sorting, and aggregating data in a PostgreSQL database.

## 🚀 Features

- **Record Management (CRUD):** Add, read, update, and delete data for all tables.
- **Search:** Smart search across all key fields in the selected table.
- **Sorting:** Ability to sort records by ID (ascending and descending).
- **Data Aggregation:** Support for `SUM`, `AVG`, `COUNT`, `MAX`, `MIN` functions for selected columns.
- **Cross Join:** Perform cross joins between two tables.
- **Fix ID:** Synchronize PostgreSQL sequences with the maximum ID values in tables.

## 🛠 Technologies

- **Frontend:** HTML5, Bootstrap 5.3, JavaScript (Vanilla)
- **Backend:** PHP (PDO)
- **Database:** PostgreSQL

## 🗄 Database Structure
![ERD](https://github.com/user-attachments/assets/6997aacf-9c6f-47d7-b6db-b21428b5c0f3)
The project works with the following tables:
1. `clients` — hotel clients (name, surname, phone, email).
2. `rooms` — rooms (type, price, availability).
3. `bookings` — bookings (linking client to room, check-in and check-out dates).
4. `payments` — payments (payment information).
5. `employees` — staff (name, surname, position).
6. `employees_activities` — employee activities (linked to bookings, activity type).

## 📁 Project Structure

- `index.php` — main page with the user interface.
- `api.php` — REST-like API for handling database requests.
- `db.php` — configuration file for PostgreSQL database connection.
- `script.js` — client-side JavaScript logic for API interaction.
- `db/` — directory containing database scripts or dumps.

## ⚙️ Installation and Setup

1. Clone the repository or download the source code.
2. Ensure you have a web server (e.g., Apache/Nginx) and PHP installed.
3. Install and start PostgreSQL.
4. Configure the database connection in `db.php`.
5. Import the database structure (e.g., from the `db/` folder if available).
6. Open the project in your browser (e.g., `http://localhost/hotel_bookings/index.php`).

---
Developed for efficient hotel booking management.
