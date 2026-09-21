<?php
header('Content-Type: application/json');

$servername = getenv('DB_HOST') ?: (getenv('MYSQLHOST') ?: "127.0.0.1");
$username   = getenv('DB_USER') ?: (getenv('MYSQLUSER') ?: "root");
$password   = getenv('DB_PASSWORD') ?: (getenv('MYSQLPASSWORD') ?: "");
$dbname     = getenv('DB_NAME') ?: (getenv('MYSQLDATABASE') ?: "vuelos");
$port       = getenv('DB_PORT') ?: (getenv('MYSQLPORT') ?: 3306);

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit();
}

$conn->set_charset("utf8mb4");

$user_id   = $_POST['user_id'] ?? 0;
$flight_id = $_POST['flight_id'] ?? 0;
$seats = filter_var($_POST['seats'] ?? 0, FILTER_VALIDATE_INT);
$payment_method = $_POST['payment_method'] ?? '';

if ($user_id > 0 && $flight_id > 0 && $seats > 0 && in_array($payment_method, ['Tarjeta', 'Transferencia'], true)) {
    $conn->begin_transaction();
    $stmt = $conn->prepare("SELECT price FROM Flights WHERE flight_id = ? AND available_seats >= ? FOR UPDATE");
    $stmt->bind_param("ii", $flight_id, $seats);
    $stmt->execute();
    $flight = $stmt->get_result()->fetch_assoc();

    if ($flight) {
        $stmt->close();
        $stmt = $conn->prepare("UPDATE Flights SET available_seats = available_seats - ? WHERE flight_id = ?");
        $stmt->bind_param("ii", $seats, $flight_id);
        $stmt->execute();
        $total_price = $flight['price'] * $seats;
        $stmt->close();
        $stmt = $conn->prepare("INSERT INTO Reservations (user_id, flight_id, status, seats, total_price, payment_method, payment_status) VALUES (?, ?, 'CONFIRMED', ?, ?, ?, 'PAID')");
        $stmt->bind_param("iiids", $user_id, $flight_id, $seats, $total_price, $payment_method);
        $success = $stmt->execute();
    } else {
        $success = false;
    }

    if ($success) {
        $conn->commit();
        echo json_encode(["status" => "success", "message" => "Reserva realizada con éxito"]);
    } else {
        $conn->rollback();
        echo json_encode(["status" => "error", "message" => "No hay suficientes asientos disponibles"]);
    }
    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Selecciona asientos y un método de pago"]);
}

$conn->close();
?>