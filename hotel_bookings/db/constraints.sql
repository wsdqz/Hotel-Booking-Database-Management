ALTER TABLE clients ADD CONSTRAINT unique_phone UNIQUE (phone);

ALTER TABLE clients ADD CONSTRAINT unique_email UNIQUE (email);

ALTER TABLE payments ADD CONSTRAINT payments_client_id_fkey 
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE;

ALTER TABLE bookings ADD CONSTRAINT bookings_client_id_fkey 
	FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE;

ALTER TABLE bookings ADD CONSTRAINT bookings_room_id_fkey 
	FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE;

ALTER TABLE employees_activities ADD CONSTRAINT employees_activities_employee_id_fkey 
	FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE;

ALTER TABLE employees_activities ADD CONSTRAINT employees_activities_booking_id_fkey 
	FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE;
	
ALTER TABLE clients ADD CONSTRAINT check_email_format CHECK (email LIKE '%@%');

ALTER TABLE clients ADD CONSTRAINT check_phone_starts CHECK (phone LIKE '+7%');

ALTER TABLE rooms ADD CONSTRAINT check_price_positive CHECK (price > 0);

ALTER TABLE bookings ADD CONSTRAINT check_dates CHECK (check_out > check_in);

ALTER TABLE payments ADD CONSTRAINT check_amount_positive CHECK (amount > 0);

