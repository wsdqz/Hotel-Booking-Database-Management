SELECT setval(pg_get_serial_sequence('clients', 'id'), MAX(id)) FROM clients;
SELECT setval(pg_get_serial_sequence('employees', 'id'), MAX(id)) FROM employees;
SELECT setval(pg_get_serial_sequence('rooms', 'id'), MAX(id)) FROM rooms;
SELECT setval(pg_get_serial_sequence('bookings', 'id'), MAX(id)) FROM bookings;
SELECT setval(pg_get_serial_sequence('payments', 'id'), MAX(id)) FROM payments;
SELECT setval(pg_get_serial_sequence('employees_activities', 'id'), MAX(id)) FROM employees_activities;
