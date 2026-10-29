
CREATE DATABASE strathmore_ordering CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE strathmore_ordering;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('student', 'admin', 'kitchen') NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE menu (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    available BOOLEAN DEFAULT 1
);

INSERT INTO menu (name, price, available) VALUES
('House Coffee', 250.00, 1),
('Café Latte', 300.00, 1),
('Café Mocha', 320.00, 1),
('Cappuccino', 300.00, 1),
('Masala Tea', 250.00, 1),
('Iced Coffee', 320.00, 1),
('Fresh Passion Juice', 350.00, 1),
('Java Milkshake', 450.00, 1),
('Smoothie (Mango)', 400.00, 1),
('Bottled Water', 100.00, 1),
('Pilau ya Nyama', 600.00, 1),
('Biryani ya Kuku', 750.00, 1),
('Samaki wa Kupaka', 850.00, 1),
('Ugali na Sukuma Wiki', 300.00, 1),
('Maharagwe ya Nazi', 350.00, 1),
('Chicken Samosa (2pcs)', 300.00, 1),
('Veggie Wrap', 600.00, 1),
('Cheese Toastie', 400.00, 1),
('Chips Masala', 350.00, 1),
('Viazi Karai', 250.00, 1);
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    menu_id INT NOT NULL,
    quantity INT NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'paid') DEFAULT 'pending',
    order_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (menu_id) REFERENCES menu(id)
);
