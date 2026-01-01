<?php
header("Content-Type: application/json");

try {
    require_once "db.php";
    $db = getDB();
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
    }
    
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
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
