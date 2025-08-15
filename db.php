<?php
// Update your credentials here
$host = '127.0.0.1';
$user = 'root';
$pass = '1234'; 
$db   = 'hospitals';

$mysqli = new mysqli($host, $user, $pass, $db);
if ($mysqli->connect_errno) {
    die('Failed to connect to MySQL: ' . $mysqli->connect_error);
}

$mysqli->set_charset('utf8mb4');

function run_stmt($mysqli, $sql, $types = '', $params = []) {
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) die('Prepare failed: ' . $mysqli->error);
    if ($types && $params) {
        $stmt->bind_param($types, ...$params);
    }
    if (!$stmt->execute()) die('Execute failed: ' . $stmt->error);
    return $stmt;
}

session_start();
?>
