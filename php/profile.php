<?php
header("Content-Type: application/json");

try {
    require_once "db.php";
    $db = getDB();
    $pdo = $db['pdo'];
    $redis = $db['redis'];
    $profiles = $db['mongo']->profiles;

    $headers = getallheaders();
    $token = $headers["Authorization"] ?? "";

    $userId = $redis->get($token);
    if (!$userId) {
      http_response_code(401);
      echo json_encode(["error" => "Unauthorized"]);
      exit;
    }

    if ($_SERVER["REQUEST_METHOD"] === "GET") {
      $profile = $profiles->findOne(["user_id" => (int)$userId]);
      if ($profile) {
        echo json_encode([
          "name" => $profile["name"] ?? "",
          "age" => $profile["age"] ?? "",
          "dob" => $profile["dob"] ?? "",
          "contact" => $profile["contact"] ?? ""
        ]);
      } else {
        echo json_encode([
          "name" => "",
          "age" => "",
          "dob" => "",
          "contact" => ""
        ]);
      }
      exit;
    } elseif ($_SERVER["REQUEST_METHOD"] === "POST") {
        $profiles->updateOne(
          ["user_id" => (int)$userId],
          ['$set' => [
            "name" => $_POST["name"],
            "age" => $_POST["age"],
            "dob" => $_POST["dob"],
            "contact" => $_POST["contact"]
          ]]
        );
        echo json_encode(["message" => "Updated"]);
    } elseif ($_SERVER["REQUEST_METHOD"] === "DELETE") {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$userId]);

        $profiles->deleteOne(["user_id" => (int)$userId]);
        $redis->del($token);

        echo json_encode(["message" => "Account deleted"]);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
