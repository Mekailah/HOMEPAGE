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
    total_units INT NOT NULL DEFAULT 10,
    available_units INT NOT NULL DEFAULT 10
);

INSERT INTO motorcycle_inventory
(motorcycle_name, total_units, available_units)
VALUES
('Honda Click 125 cc', 10, 10),
('Yamaha Fazzio 125cc', 10, 10),
('Yamaha AEROX 155 CC', 10, 10),
('Honda Beat 110', 10, 10),
('Honda ADV 160', 10, 10),
('Yamaha PG-1', 10, 10),
('Honda NAVi', 10, 10),
('Yamaha NMAX ABS', 10, 10),
('Honda XRM 125', 10, 10),
('Yamaha Vino Classic', 10, 10),
('Kawasaki Ninja 1000SX', 10, 10),
('Yamaha Sniper 155', 10, 10);
