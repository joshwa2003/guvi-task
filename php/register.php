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
    // Check for "Base table or view not found" (1146 / 42S02)
    if ($e->getCode() == '42S02') {
        try {
            // Create the table automatically
            $pdo->exec("CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                email VARCHAR(255) UNIQUE,
                password VARCHAR(255)
            )");
            
            // Retry the insertion
            $stmt = $pdo->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
            $stmt->execute([$email, $hashedPassword]);
            $userId = $pdo->lastInsertId();

            // Continue with MongoDB insertion
            $profiles->insertOne([
              "user_id" => (int)$userId,
              "name" => "",
              "age" => "",
              "dob" => "",
              "contact" => ""
            ]);

            echo json_encode(["message" => "Registration successful (Table created)"]);
            exit;

        } catch (Exception $ex) {
             http_response_code(500);
             echo json_encode(["message" => "Failed to create table: " . $ex->getMessage()]);
             exit;
        }
    }

    http_response_code(500);
    // Determine if it's a duplicate entry (error 1062 / 23000)
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
