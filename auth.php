<?php
// api/auth.php - simple authentication endpoint
header('Content-Type: application/json');
session_start();

// connect to database (same as items.php)
$mysqli = new mysqli('localhost', 'root', '', 'sari_sari_db');
if ($mysqli->connect_errno) {
    http_response_code(500);
    echo json_encode(['error' => 'db_connection_failed']);
    exit;
}
$mysqli->set_charset('utf8mb4');

$method = $_SERVER['REQUEST_METHOD'];
switch ($method) {
    case 'POST': // login
        $data = json_decode(file_get_contents('php://input'), true);
        $username = $data['username'] ?? '';
        $password = $data['password'] ?? '';
        if (!$username || !$password) {
            http_response_code(400);
            echo json_encode(['error' => 'username_password_required']);
            exit;
        }
        $stmt = $mysqli->prepare('SELECT id, username, password, role FROM users WHERE username = ?');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        if ($user && $password === $user['password']) {
            // success
            $_SESSION['user'] = ['id' => $user['id'], 'username' => $user['username'], 'role' => $user['role']];
            echo json_encode(['success' => true, 'user' => $_SESSION['user']]);
        } else {
            http_response_code(401);
            echo json_encode(['error' => 'invalid_credentials']);
        }
        break;
    case 'GET': // check session
        if (isset($_SESSION['user'])) {
            echo json_encode(['logged_in' => true, 'user' => $_SESSION['user']]);
        } else {
            echo json_encode(['logged_in' => false]);
        }
        break;
    case 'DELETE': // logout
        session_unset();
        session_destroy();
        echo json_encode(['success' => true]);
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'method_not_allowed']);
}
$mysqli->close();
?>