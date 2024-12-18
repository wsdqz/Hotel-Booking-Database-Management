CREATE TABLE clients (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    surname VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(255) NOT NULL
);

CREATE TABLE employees (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    surname VARCHAR(255) NOT NULL,
    position VARCHAR(255) NOT NULL
);

CREATE TABLE rooms (
    id SERIAL PRIMARY KEY,
    type VARCHAR(255) NOT NULL,
    price INT NOT NULL,
    available BOOLEAN NOT NULL DEFAULT true
);

CREATE TABLE payments (
    id SERIAL PRIMARY KEY,
    client_id INT NOT NULL,
    amount INT NOT NULL,
    date DATE NOT NULL
);

CREATE TABLE bookings (
    id SERIAL PRIMARY KEY,
    client_id INT NOT NULL,
    room_id INT NOT NULL,
    check_in DATE NOT NULL,
    check_out DATE NOT NULL
);

CREATE TABLE employees_activities (
    id SERIAL PRIMARY KEY,
    employee_id INT NOT NULL,
    booking_id INT NOT NULL,
    activity VARCHAR(255) NOT NULL
);
