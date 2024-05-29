<?php
header("Content-Type: application/json");

$host = 'localhost';
$db = 'hr';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

$pdo = new PDO($dsn, $user, $pass, $options);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $usersStmt = $pdo->query("SELECT userid, username, email FROM accounts");
    $users = $usersStmt->fetchAll();

    $departmentsStmt = $pdo->query("SELECT dnumber, dname FROM department");
    $departments = $departmentsStmt->fetchAll();

    echo json_encode(['users' => $users, 'departments' => $departments]);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (isset($input['username'], $input['pass'], $input['email'])) {
        $sql = "INSERT INTO accounts (username, pass, email) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$input['username'], $input['pass'], $input['email']]);
        echo json_encode(['message' => 'User added successfully']);
    } elseif (isset($input['dname'])) {
        $sql = "INSERT INTO department (dname) VALUES (?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$input['dname']]);
        echo json_encode(['message' => 'Department added successfully']);
    } else {
        echo json_encode(['error' => 'Invalid input']);
    }
}
?>