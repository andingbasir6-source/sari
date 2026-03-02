<?php
// api/items.php - REST API for inventory items
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

session_start();
// require login
if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['error' => 'not_authenticated']);
    exit;
}

// connect to MySQL using mysqli (XAMPP default)
$mysqli = new mysqli('localhost', 'root', '', 'sari_sari_db');
if ($mysqli->connect_errno) {
    http_response_code(500);
    echo json_encode(['error' => 'db_connection_failed', 'details' => $mysqli->connect_error]);
    exit;
}
$mysqli->set_charset('utf8mb4');

// ensure database/table exists (idempotent)
$mysqli->query("CREATE DATABASE IF NOT EXISTS `sari_sari_db`");
$mysqli->query("USE `sari_sari_db`");
$mysqli->query("CREATE TABLE IF NOT EXISTS items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    manufacture VARCHAR(255),
    `type` VARCHAR(255),
    grams INT,
    price DECIMAL(10,2),
    expiration DATE,
    made_date DATE,
    availability INT
)");

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $result = $mysqli->query('SELECT * FROM items ORDER BY id DESC');
        if (!$result) {
            http_response_code(500);
            echo json_encode(['error' => $mysqli->error]);
            break;
        }
        $items = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode($items);
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Validate required fields
        if (empty($data['name'])) {
            http_response_code(400);
            echo json_encode(['error' => 'name_required']);
            break;
        }

        $stmt = $mysqli->prepare(
            'INSERT INTO items (name, manufacture, `type`, grams, price, expiration, made_date, availability) VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );
        
        if (!$stmt) {
            http_response_code(500);
            echo json_encode(['error' => $mysqli->error]);
            break;
        }

        $stmt->bind_param(
            'sssidssi',
            $data['name'],
            $data['manufacture'],
            $data['type'],
            $data['grams'],
            $data['price'],
            $data['expiration'],
            $data['madeDate'],
            $data['availability']
        );
        
        if ($stmt->execute()) {
            echo json_encode(['id' => $stmt->insert_id, 'success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => $stmt->error]);
        }
        $stmt->close();
        break;

    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $_GET['id'] ?? null;
        
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'id_required']);
            break;
        }

        if (empty($data['name'])) {
            http_response_code(400);
            echo json_encode(['error' => 'name_required']);
            break;
        }

        $stmt = $mysqli->prepare(
            'UPDATE items SET name=?, manufacture=?, `type`=?, grams=?, price=?, expiration=?, made_date=?, availability=? WHERE id = ?'
        );
        
        if (!$stmt) {
            http_response_code(500);
            echo json_encode(['error' => $mysqli->error]);
            break;
        }

        $stmt->bind_param(
            'sssidssii',
            $data['name'],
            $data['manufacture'],
            $data['type'],
            $data['grams'],
            $data['price'],
            $data['expiration'],
            $data['madeDate'],
            $data['availability'],
            $id
        );
        
        if ($stmt->execute()) {
            echo json_encode(['affected' => $stmt->affected_rows, 'success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => $stmt->error]);
        }
        $stmt->close();
        break;

    case 'DELETE':
        $id = $_GET['id'] ?? null;
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'id_required']);
            break;
        }

        $stmt = $mysqli->prepare('DELETE FROM items WHERE id = ?');
        if (!$stmt) {
            http_response_code(500);
            echo json_encode(['error' => $mysqli->error]);
            break;
        }

        $stmt->bind_param('i', $id);
        if ($stmt->execute()) {
            echo json_encode(['deleted' => $stmt->affected_rows, 'success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => $stmt->error]);
        }
        $stmt->close();
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'method_not_allowed']);
        break;
}

$mysqli->close();
?>
