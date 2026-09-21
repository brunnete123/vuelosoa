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

$origin = $_GET['origin'] ?? '';
$destination = $_GET['destination'] ?? '';

$stmt = $conn->prepare("SELECT flight_id, origin, destination, departure_date, return_date, price, available_seats FROM Flights WHERE origin LIKE ? AND destination LIKE ? AND available_seats > 0 ORDER BY departure_date");
$param_origin = "%$origin%";
$param_dest = "%$destination%";
$stmt->bind_param("ss", $param_origin, $param_dest);

$stmt->execute();
$result = $stmt->get_result();

$flights = [];
while ($row = $result->fetch_assoc()) {
    $flights[] = $row;
}

echo json_encode(["status" => "success", "flights" => $flights]);

$stmt->close();
$conn->close();
?>