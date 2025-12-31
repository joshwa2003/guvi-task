<?php
header("Content-Type: application/json");

require_once "db.php";
$db = getDB();
$redis = $db['redis'];
$profiles = $db['mongo']->profiles;

$headers = getallheaders();
$token = $headers["Authorization"] ?? "";

$userId = $redis->get($token);
if (!$userId) {
  http_response_code(401);
  exit;
}

if ($_SERVER["REQUEST_METHOD"] === "GET") {
  try {
     $profile = $profiles->findOne(["user_id" => (int)$userId]);
     if (!$profile) {
         // Self-healing: Create missing profile
         $profile = [
             "user_id" => (int)$userId,
             "name" => "",
             "age" => "",
             "dob" => "",
             "contact" => ""
         ];
         $profiles->insertOne($profile);
     }
     echo json_encode($profile);
  } catch (Exception $e) {
      http_response_code(500);
      echo json_encode(["error" => "Database error"]);
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
