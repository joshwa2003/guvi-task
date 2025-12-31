<?php
require_once __DIR__ . "/../vendor/autoload.php";

function getDB() {

    $mysql_url = getenv("MYSQL_URL") ?: getenv("JAWSDB_URL");
    if ($mysql_url) {
        $dbparts = parse_url($mysql_url);
        $hostname = $dbparts['host'];
        $username = $dbparts['user'];
        $password = $dbparts['pass'];
        $database = ltrim($dbparts['path'], '/');
        $port = isset($dbparts['port']) ? $dbparts['port'] : 3306;

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ];

        if (file_exists(__DIR__ . '/../aiven_ca.pem')) {
            $options[PDO::MYSQL_ATTR_SSL_CA] = realpath(__DIR__ . '/../aiven_ca.pem');
        }

        $pdo = new PDO("mysql:host=$hostname;port=$port;dbname=$database", $username, $password, $options);
    } else {
        $pdo = new PDO("mysql:host=localhost;dbname=auth_db", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    $mongo_uri = getenv("MONGODB_URI") ?: "mongodb://localhost:27017";
    $mongo_client = new MongoDB\Client($mongo_uri);
    $mongo_db = $mongo_client->selectDatabase("auth_db"); 
    $redis = new Redis();
    $redis_url = getenv("REDIS_URL");
    if ($redis_url) {
        $parts = parse_url($redis_url);
        $redis->connect($parts['host'], $parts['port']);
        if (!empty($parts['pass'])) {
            $redis->auth($parts['pass']);
        }
    } else {
        $redis->connect("127.0.0.1", 6379);
    }

    return [
        'pdo' => $pdo,
        'mongo' => $mongo_db,
        'redis' => $redis
    ];
}
