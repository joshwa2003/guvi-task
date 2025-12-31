<?php
header("Content-Type: application/json");

require_once "db.php";
$db = getDB();
$pdo = $db['pdo'];
$redis = $db['redis'];

$email = $_POST["email"];
$password = $_POST["password"];

$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user["password"])) {
  http_response_code(401);
  exit;
}

$token = bin2hex(random_bytes(32));

$redis->setex($token, 3600, $user["id"]);

echo json_encode(["token" => $token]);
