USE vuelos;

CREATE TABLE IF NOT EXISTS Users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS Flights (
    flight_id INT AUTO_INCREMENT PRIMARY KEY,
    origin VARCHAR(50) NOT NULL,
    destination VARCHAR(50) NOT NULL,
    departure_date DATE NOT NULL,
    return_date DATE,
    price DECIMAL(10, 2) NOT NULL,
    available_seats INT NOT NULL DEFAULT 20
);

CREATE TABLE IF NOT EXISTS Reservations (
    reservation_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    flight_id INT,
    reservation_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(20) NOT NULL DEFAULT 'CONFIRMED',
    seats INT NOT NULL DEFAULT 1,
    total_price DECIMAL(10, 2) NOT NULL DEFAULT 0,
    payment_method VARCHAR(30) NOT NULL DEFAULT 'Tarjeta',
    payment_status VARCHAR(20) NOT NULL DEFAULT 'PAID',
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (flight_id) REFERENCES Flights(flight_id) ON DELETE CASCADE
);

INSERT IGNORE INTO Users (username, password, email) VALUES
('Bruno', '$2y$10$T3H2ayNlAQ8cJE0KQeJ3eeMH.LuouUi/ojDk7ukitVoGRWUQfijAK', 'bruno@gmail.com'),
('Carlos', '$2y$10$T3H2ayNlAQ8cJE0KQeJ3eeMH.LuouUi/ojDk7ukitVoGRWUQfijAK', 'carlos@gmail.com');

INSERT IGNORE INTO Flights (origin, destination, departure_date, return_date, price) VALUES
('Ciudad Juárez', 'Ciudad de México', '2026-10-15', '2026-10-22', 2400.00),
('Ciudad Juárez', 'Guadalajara', '2026-10-16', '2026-10-23', 1800.00),
('Ciudad de México', 'Cancún', '2026-10-20', '2026-10-27', 3000.00),
('Guadalajara', 'Monterrey', '2026-11-05', '2026-11-12', 1500.00),
('Monterrey', 'Tijuana', '2026-11-08', '2026-11-15', 2100.00),
('Cancún', 'Mérida', '2026-11-12', '2026-11-19', 1500.00),
('Ciudad de México', 'Oaxaca', '2026-11-18', '2026-11-22', 1500.00),
('Tijuana', 'Guadalajara', '2026-11-25', '2026-12-02', 2700.00);

INSERT IGNORE INTO Reservations (user_id, flight_id, seats, total_price, payment_method, payment_status) VALUES (2, 1, 1, 2400.00, 'Tarjeta', 'PAID');