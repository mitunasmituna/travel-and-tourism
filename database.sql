CREATE DATABASE IF NOT EXISTS travel_tourism
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
USE travel_tourism;

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS destinations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    country VARCHAR(120) NOT NULL,
    region VARCHAR(120) DEFAULT NULL,
    description TEXT NOT NULL,
    image_url VARCHAR(500) NOT NULL,
    featured TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_destinations_name_country (name, country),
    INDEX idx_destinations_featured (featured)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS packages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    destination_id INT UNSIGNED NOT NULL,
    name VARCHAR(150) NOT NULL,
    duration_days TINYINT UNSIGNED NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    description TEXT NOT NULL,
    featured TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_packages_destination_name (destination_id, name),
    CONSTRAINT fk_packages_destination FOREIGN KEY (destination_id)
        REFERENCES destinations (id) ON UPDATE CASCADE ON DELETE RESTRICT,
    INDEX idx_packages_featured (featured)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS bookings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED DEFAULT NULL,
    package_id INT UNSIGNED NOT NULL,
    destination_id INT UNSIGNED NOT NULL,
    full_name VARCHAR(120) NOT NULL,
    email VARCHAR(190) NOT NULL,
    travel_date DATE NOT NULL,
    travelers TINYINT UNSIGNED NOT NULL,
    unit_price DECIMAL(10, 2) NOT NULL,
    total_amount DECIMAL(12, 2) NOT NULL,
    status ENUM('pending', 'confirmed', 'cancelled') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_bookings_user FOREIGN KEY (user_id)
        REFERENCES users (id) ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_bookings_package FOREIGN KEY (package_id)
        REFERENCES packages (id) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_bookings_destination FOREIGN KEY (destination_id)
        REFERENCES destinations (id) ON UPDATE CASCADE ON DELETE RESTRICT,
    INDEX idx_bookings_travel_date (travel_date)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS contact_messages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(190) NOT NULL,
    subject VARCHAR(180) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT IGNORE INTO destinations (name, country, region, description, image_url, featured) VALUES
('Ooty', 'India', 'Tamil Nadu', 'Winding tea gardens, cool mountain air and toy-train views make this hill escape a timeless favourite.', 'https://images.unsplash.com/photo-1597074866923-dc0589150358?auto=format&fit=crop&w=1200&q=85', 1),
('Kerala', 'India', 'South India', 'Drift through palm-fringed backwaters, spice-scented hills and a coastline made for slowing down.', 'https://images.unsplash.com/photo-1602216056096-3b40cc0c9944?auto=format&fit=crop&w=1200&q=85', 1),
('Paris', 'France', 'Île-de-France', 'A city of café mornings, art-filled afternoons and golden-hour walks along the Seine.', 'https://images.unsplash.com/photo-1502602898657-3e91760cbb34?auto=format&fit=crop&w=1200&q=85', 1),
('Swiss Alps', 'Switzerland', 'Central Europe', 'Ride alpine railways past emerald valleys, mirror lakes and peaks that reach into the clouds.', 'https://images.unsplash.com/photo-1531366936337-7c912a4589a7?auto=format&fit=crop&w=1200&q=85', 1),
('Bali', 'Indonesia', 'Southeast Asia', 'Find your rhythm among rice terraces, temple courtyards, surf breaks and quiet island sunsets.', 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=1200&q=85', 1),
('Dubai', 'United Arab Emirates', 'Middle East', 'Pair desert horizons and Arabian hospitality with design-forward hotels and city-night energy.', 'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?auto=format&fit=crop&w=1200&q=85', 0),
('Kyoto', 'Japan', 'East Asia', 'A thoughtful journey through lantern-lit lanes, garden sanctuaries and beautifully preserved traditions.', 'https://images.unsplash.com/photo-1493976040374-85c8e12f0c0e?auto=format&fit=crop&w=1200&q=85', 1),
('Maldives', 'Maldives', 'Indian Ocean', 'Overwater mornings, luminous reefs and slow island days in the heart of the Indian Ocean.', 'https://images.unsplash.com/photo-1514282401047-d79a71a590e8?auto=format&fit=crop&w=1200&q=85', 0);

INSERT IGNORE INTO packages (destination_id, name, duration_days, price, description, featured)
SELECT id, 'Tea Trails & Mountain Air', 4, 389.00, 'A restful hill-country escape with a tea estate stay, scenic railway ride and guided garden walk.', 1 FROM destinations WHERE name = 'Ooty' AND country = 'India';
INSERT IGNORE INTO packages (destination_id, name, duration_days, price, description, featured)
SELECT id, 'Kerala, Slowly', 6, 649.00, 'A private backwater cruise, a spice garden visit and two nights among the Western Ghats.', 1 FROM destinations WHERE name = 'Kerala' AND country = 'India';
INSERT IGNORE INTO packages (destination_id, name, duration_days, price, description, featured)
SELECT id, 'Paris in Good Company', 5, 879.00, 'Neighbourhood-led city days, a Seine evening cruise and time to wander at your own pace.', 1 FROM destinations WHERE name = 'Paris' AND country = 'France';
INSERT IGNORE INTO packages (destination_id, name, duration_days, price, description, featured)
SELECT id, 'Alpine Grand Journey', 7, 1499.00, 'Panoramic rail journeys, a lakeside stay and unhurried time in the Swiss mountain villages.', 1 FROM destinations WHERE name = 'Swiss Alps' AND country = 'Switzerland';
INSERT IGNORE INTO packages (destination_id, name, duration_days, price, description, featured)
SELECT id, 'Island Rituals of Bali', 6, 999.00, 'Rice terrace sunrise, a private villa, temple visits and a coast-to-coast island day.', 0 FROM destinations WHERE name = 'Bali' AND country = 'Indonesia';
INSERT IGNORE INTO packages (destination_id, name, duration_days, price, description, featured)
SELECT id, 'The Kyoto Edit', 5, 1129.00, 'A considered introduction to Kyoto with garden visits, a tea experience and boutique stays.', 0 FROM destinations WHERE name = 'Kyoto' AND country = 'Japan';
