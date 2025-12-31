<?php
header("Content-Type: application/json");

require_once "db.php";
$db = getDB();
$pdo = $db['pdo'];
$profiles = $db['mongo']->profiles;

$email = $_POST["email"];
$password = password_hash($_POST["password"], PASSWORD_DEFAULT);

$stmt = $pdo->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
$stmt->execute([$email, $password]);

$userId = $pdo->lastInsertId();


$profiles->insertOne([
  "user_id" => (int)$userId,
  "name" => "",
  "age" => "",
  "dob" => "",
  "contact" => ""
]);

echo json_encode(["message" => "Registration successful"]);
