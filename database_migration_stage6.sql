USE travel_tourism;

-- Run once on an existing Stage 5 database. This keeps all users and bookings.
ALTER TABLE users
    ADD COLUMN role ENUM('user', 'admin') NOT NULL DEFAULT 'user' AFTER password_hash;

ALTER TABLE bookings
    ADD COLUMN unit_price DECIMAL(10, 2) NULL AFTER travelers,
    ADD COLUMN total_amount DECIMAL(12, 2) NULL AFTER unit_price;

-- Existing requests use the package's current per-person price as their snapshot.
UPDATE bookings b
JOIN packages p ON p.id = b.package_id
SET b.unit_price = p.price,
    b.total_amount = ROUND(p.price * b.travelers, 2)
WHERE b.unit_price IS NULL OR b.total_amount IS NULL;

ALTER TABLE bookings
    MODIFY COLUMN unit_price DECIMAL(10, 2) NOT NULL,
    MODIFY COLUMN total_amount DECIMAL(12, 2) NOT NULL;

-- To grant admin access, first register your own account, then run this manually
-- with that exact email address. No default admin account or password is created.
-- UPDATE users SET role = 'admin' WHERE email = 'your-account@example.com';
