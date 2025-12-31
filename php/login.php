<?php
header("Content-Type: application/json");

try {
    require_once "db.php";
    $db = getDB();
    $pdo = $db['pdo'];
    $redis = $db['redis'];

    $email = $_POST["email"] ?? '';
    $password = $_POST["password"] ?? '';

    if (empty($email) || empty($password)) {
        throw new Exception("Email and password are required", 400);
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user["password"])) {
        throw new Exception("Invalid email or password", 401);
    }

    $token = bin2hex(random_bytes(32));
    $redis->setex($token, 3600, $user["id"]);

    echo json_encode(["token" => $token]);

} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode(["message" => $e->getMessage()]);
}
