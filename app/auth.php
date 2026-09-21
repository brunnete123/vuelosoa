<?php
session_start();

$servername = getenv('DB_HOST') ?: "127.0.0.1";
$username = getenv('DB_USER') ?: "root";
$password = getenv('DB_PASSWORD') ?: "";
$dbname = getenv('DB_NAME') ?: "vuelos";
$port = getenv('DB_PORT') ?: 3306;

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

$action = $_POST['action'] ?? '';

header('Content-Type: application/json');

// Registro
if ($action === 'register') {
    $user  = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $pass  = password_hash($_POST['password'] ?? '', PASSWORD_BCRYPT);

    $check = $conn->prepare("SELECT username, email FROM Users WHERE username = ? OR email = ?");
    $check->bind_param("ss", $user, $email);
    $check->execute();
    $existing = $check->get_result()->fetch_assoc();
    $check->close();

    if ($existing) {
        $errors = [];
        if ($existing['username'] === $user) {
            $errors['username'] = 'Ese usuario ya existe.';
        }
        if ($existing['email'] === $email) {
            $errors['email'] = 'Ese correo ya está registrado.';
        }
        echo json_encode(['status' => 'error', 'errors' => $errors]);
        $conn->close();
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO Users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $user, $email, $pass);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Registro exitoso.']);
    } else {
        echo json_encode(['status' => 'error', 'errors' => ['username' => 'No se pudo completar el registro.']]);
    }
    $stmt->close();
}

// Inicio de sesión
if ($action === 'login') {
    $user = $_POST['username'] ?? ($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT user_id, username, password FROM Users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $user, $user);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($pass, $row['password'])) {
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['username'] = $user;
            echo json_encode(['status' => 'success', 'message' => 'Inicio de sesión exitoso.', 'user' => ['id' => $row['user_id'], 'username' => $row['username']]]);
        } else {
            echo json_encode(['status' => 'error', 'errors' => ['password' => 'La contraseña es incorrecta.']]);
        }
    } else {
        echo json_encode(['status' => 'error', 'errors' => ['username' => 'El usuario o correo no existe.']]);
    }
    $stmt->close();
}

if ($action === 'logout') {
    session_unset();
    session_destroy();
    echo json_encode(['status' => 'success']);
}

$conn->close();
?>