CREATE DATABASE IF NOT EXISTS ridego_rentals;
USE ridego_rentals;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    driver_license VARCHAR(255) NOT NULL,
    motorcycle_name VARCHAR(100) NOT NULL,
    price_per_day DECIMAL(10,2) NOT NULL,
    pickup_date DATE NOT NULL,
    pickup_time TIME NOT NULL,
    return_date DATE NOT NULL,
    return_time TIME NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    pickup_branch VARCHAR(150) NOT NULL,
    dropoff_branch VARCHAR(150) NOT NULL,
    status VARCHAR(20) DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE motorcycle_inventory (
    id INT AUTO_INCREMENT PRIMARY KEY,
    motorcycle_name VARCHAR(100) NOT NULL UNIQUE,
    price_per_day DECIMAL(10,2) NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    total_units INT NOT NULL DEFAULT 10,
    available_units INT NOT NULL DEFAULT 10,
    is_active TINYINT(1) NOT NULL DEFAULT 1
);

INSERT INTO motorcycle_inventory
(motorcycle_name, price_per_day, image_path, total_units, available_units)
VALUES
('Honda Click 125 cc', 629.00, 'assets/honda-click-125.png', 10, 10),
('Yamaha Fazzio 125cc', 819.00, 'assets/yamaha-fazzio-125.png', 10, 10),
('Yamaha AEROX 155 CC', 799.00, 'assets/yamaha-aerox-155.png', 10, 10),
('Honda Beat 110', 449.00, 'assets/honda-beat-110.png', 10, 10),
('Honda ADV 160', 900.00, 'assets/adv.png', 10, 10),
('Yamaha PG-1', 800.00, 'assets/pg1.png', 10, 10),
('Honda NAVi', 600.00, 'assets/navi.png', 10, 10),
('Yamaha NMAX ABS', 850.00, 'assets/nmax.png', 10, 10),
('Honda XRM 125', 600.00, 'assets/xrm.png', 10, 10),
('Yamaha Vino Classic', 650.00, 'assets/yamaha vino.png', 10, 10),
('Kawasaki Ninja 1000SX', 3500.00, 'assets/kawasaki ninja.png', 10, 10),
('Yamaha Sniper 155', 750.00, 'assets/sniper.png', 10, 10);