<?php
header('Content-Type: application/json');

$servername = getenv('DB_HOST') ?: "127.0.0.1";
$username   = getenv('DB_USER') ?: "root";
$password   = getenv('DB_PASSWORD') ?: "";
$dbname     = getenv('DB_NAME') ?: "vuelos";
$port       = getenv('DB_PORT') ?: 3306;

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit();
}

$conn->set_charset("utf8mb4");

$method = $_SERVER['REQUEST_METHOD'];
$action = $_POST['action'] ?? '';
$user_id = $_GET['user_id'] ?? ($_POST['user_id'] ?? 0);

if ($method === 'POST' && $action === 'cancel') {
    $reservation_id = $_POST['reservation_id'] ?? 0;
    $stmt = $conn->prepare("SELECT flight_id, seats FROM Reservations WHERE reservation_id = ? AND user_id = ? AND status = 'CONFIRMED'");
    $stmt->bind_param("ii", $reservation_id, $user_id);
    $stmt->execute();
    $reservation = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($reservation) {
        $conn->begin_transaction();
        $stmt = $conn->prepare("UPDATE Reservations SET status = 'CANCELLED' WHERE reservation_id = ?");
        $stmt->bind_param("i", $reservation_id);
        $stmt->execute();
        $stmt->close();
        $stmt = $conn->prepare("UPDATE Flights SET available_seats = available_seats + ? WHERE flight_id = ?");
        $stmt->bind_param("ii", $reservation['seats'], $reservation['flight_id']);
        $stmt->execute();
        $conn->commit();
        echo json_encode(["status" => "success", "message" => "Reserva cancelada"]);
    } else {
        echo json_encode(["status" => "error", "message" => "La reserva no está disponible para cancelar"]);
    }
    $conn->close();
    exit;
}

$stmt = $conn->prepare("
    SELECT r.reservation_id, r.reservation_date, r.status, r.seats, r.total_price, r.payment_method, r.payment_status, f.origin, f.destination, f.departure_date, f.price 
    FROM Reservations r
    JOIN Flights f ON r.flight_id = f.flight_id
    WHERE r.user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$reservations = [];
while ($row = $result->fetch_assoc()) {
    $reservations[] = $row;
}

echo json_encode(["status" => "success", "reservations" => $reservations]);

$stmt->close();
$conn->close();
?>
