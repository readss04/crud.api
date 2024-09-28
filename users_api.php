<?php
header('Content-Type: application/json');
include 'db.php';

// Retrieve request method and ID if available
$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

// Function to handle the response
function response($status, $data = [], $message = '') {
    echo json_encode(['status' => $status, 'data' => $data, 'message' => $message]);
    exit();
}

// Get All Users
if ($method == 'GET' && !$id) {
    try {
        $stmt = $pdo->query('SELECT * FROM Users');
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        response(200, $users);
    } catch (Exception $e) {
        response(500, [], $e->getMessage());
    }
}

// Get Single User by ID
if ($method == 'GET' && $id) {
    try {
        $stmt = $pdo->prepare('SELECT * FROM Users WHERE id = ?');
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            response(200, $user);
        } else {
            response(404, [], 'User not found');
        }
    } catch (Exception $e) {
        response(500, [], $e->getMessage());
    }
}

// Insert Single User
if ($method == 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!isset($input['firstname']) || !isset($input['lastname']) || !isset($input['is_admin'])) {
        response(400, [], 'Invalid input');
    }
    try {
        $stmt = $pdo->prepare('INSERT INTO Users (firstname, lastname, is_admin) VALUES (?, ?, ?)');
        $stmt->execute([$input['firstname'], $input['lastname'], $input['is_admin']]);
        $id = $pdo->lastInsertId();
        response(201, ['id' => $id], 'User created');
    } catch (Exception $e) {
        response(500, [], $e->getMessage());
    }
}

// Update Single User
if ($method == 'PUT' && $id) {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!isset($input['firstname']) || !isset($input['lastname']) || !isset($input['is_admin'])) {
        response(400, [], 'Invalid input');
    }
    try {
        $stmt = $pdo->prepare('UPDATE Users SET firstname = ?, lastname = ?, is_admin = ? WHERE id = ?');
        $stmt->execute([$input['firstname'], $input['lastname'], $input['is_admin'], $id]);
        if ($stmt->rowCount()) {
            response(200, [], 'User updated');
        } else {
            response(404, [], 'User not found');
        }
    } catch (Exception $e) {
        response(500, [], $e->getMessage());
    }
}

// Delete Single User
if ($method == 'DELETE' && $id) {
    try {
        $stmt = $pdo->prepare('DELETE FROM Users WHERE id = ?');
        $stmt->execute([$id]);
        if ($stmt->rowCount()) {
            response(200, [], 'User deleted');
        } else {
            response(404, [], 'User not found');
        }
    } catch (Exception $e) {
        response(500, [], $e->getMessage());
    }
}

// If method not supported
response(405, [], 'Method Not Allowed');

echo "hello";
?>
