<?php
// Enable CORS and set response headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: X-Requested-With, Origin, Content-Type");
header("Access-Control-Max-Age: 86400");

// Uncomment for debugging (disable in production)
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// Set timezone and increase script timeout
date_default_timezone_set("Asia/Manila");
set_time_limit(1000);

// Set root path and API path
$rootPath = $_SERVER["DOCUMENT_ROOT"];
$apiPath = $rootPath . "/crud_api";

// Ensure required files exist before including them
if (!file_exists($apiPath . '/config/Connection.php') ||
    !file_exists($apiPath . '/models/try.model.php') ||
    !file_exists($apiPath . '/models/Global.model.php')) {
    error_log('Missing required files', 3, $rootPath . '/php-error.log');
    die(json_encode(['error' => 'Server configuration error']));
}

// Require necessary files
require_once($apiPath . '/config/Connection.php');
require_once($apiPath . '/models/try.model.php');
require_once($apiPath . '/models/Global.model.php');

// Establish database connection
$db = new Connection();
$pdo = $db->connect();

// Instantiate models
$global = new GlobalMethods();
$try = new Example($pdo, $global);

// Parse request and method
$req = isset($_REQUEST['request']) ? explode('/', rtrim($_REQUEST['request'], '/')) : ["errorcatcher"];
$method = $_SERVER['REQUEST_METHOD'];

// Handle request based on method
switch ($method) {
    case 'GET':
        if ($req[0] === 'try') {
            echo json_encode($try->hello());
        } elseif ($req[0] === 'getAllUsers') {
            echo json_encode($try->getAll());
        } elseif ($req[0] === 'getUserById' && isset($req[1])) {
            $userId = $req[1];
            echo json_encode($try->getUserById($userId));
        } else {
            echo json_encode(['error' => 'Invalid GET request']);
        }
        break;

    case 'POST':
        $data_input = json_decode(file_get_contents("php://input"));
        if (empty($data_input)) {
            echo json_encode(['error' => 'No input data provided']);
        } elseif ($req[0] === 'insert') {
            echo json_encode($try->insert($data_input));
        } else {
            echo json_encode(['error' => 'Invalid POST request']);
        }
        break;

    case 'PUT':
        if ($req[0] === 'updateUser' && isset($req[1])) {
            $userId = $req[1];
            $input = json_decode(file_get_contents('php://input'));
            if (empty($input)) {
                echo json_encode(['error' => 'No input data provided']);
            } else {
                echo json_encode($try->updateUser($userId, $input));
            }
        } else {
            echo json_encode(['error' => 'Invalid PUT request']);
        }
        break;

    case 'DELETE':
        if ($req[0] === 'deleteUser' && isset($req[1])) {
            $userId = $req[1];
            echo json_encode($try->deleteUser($userId));
        } else {
            echo json_encode(['error' => 'Invalid DELETE request']);
        }
        break;

    default:
        echo json_encode(['error' => 'Invalid request method']);
        http_response_code(403);
        break;
}
