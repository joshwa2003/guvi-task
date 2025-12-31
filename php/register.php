<?php
header("Content-Type: application/json");

try {
    require_once "db.php";
    $db = getDB();
    $pdo = $db['pdo'];
    $profiles = $db['mongo']->profiles;

    $email = $_POST["email"] ?? '';
    $password = $_POST["password"] ?? '';

    if (empty($email) || empty($password)) {
        throw new Exception("Email and password are required", 400);
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
    $stmt->execute([$email, $hashedPassword]);

    $userId = $pdo->lastInsertId();

    $profiles->insertOne([
      "user_id" => (int)$userId,
      "name" => "",
      "age" => "",
      "dob" => "",
      "contact" => ""
    ]);

    echo json_encode(["message" => "Registration successful"]);

} catch (PDOException $e) {
    http_response_code(500);
    if ($e->getCode() == 23000) {
        http_response_code(409);
        echo json_encode(["message" => "Email already exists"]);
    } else {
        echo json_encode(["message" => "Database error: " . $e->getMessage()]);
    }
} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode(["message" => $e->getMessage()]);
}
