<?php
require_once __DIR__ . "/../vendor/autoload.php";

function getDB() {
    // MySQL Connection
    // MySQL Connection
    $mysql_url = getenv("MYSQL_URL") ?: getenv("JAWSDB_URL"); // MYSQL_URL for Render
    if ($mysql_url) {
        // Remote Database (Render/Heroku)
        $dbparts = parse_url($mysql_url);
        $hostname = $dbparts['host'];
        $username = $dbparts['user'];
        $password = $dbparts['pass'];
        $database = ltrim($dbparts['path'], '/');
        $port = isset($dbparts['port']) ? $dbparts['port'] : 3306;

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ];

        // Aiven/External SSL Support
        if (file_exists(__DIR__ . '/../aiven_ca.pem')) {
            $options[PDO::MYSQL_ATTR_SSL_CA] = realpath(__DIR__ . '/../aiven_ca.pem');
        }

        $pdo = new PDO("mysql:host=$hostname;port=$port;dbname=$database", $username, $password, $options);
    } else {
        // Localhost
        $pdo = new PDO("mysql:host=localhost;dbname=auth_db", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    // MongoDB Connection
    $mongo_uri = getenv("MONGODB_URI") ?: "mongodb://localhost:27017";
    $mongo_client = new MongoDB\Client($mongo_uri);
    // On Heroku/Atlas, the DB name is usually in the URI or standard, 
    // but for simplicity we use 'auth_db' or parse it if needed.
    // For Atlas, simple 'auth_db' works if the user has permissions.
    $mongo_db = $mongo_client->selectDatabase("auth_db"); 

    // Redis Connection
    $redis = new Redis();
    $redis_url = getenv("REDIS_URL");
    if ($redis_url) {
        // Heroku Redis
        $parts = parse_url($redis_url);
        $redis->connect($parts['host'], $parts['port']);
        if (!empty($parts['pass'])) {
            $redis->auth($parts['pass']);
        }
    } else {
        // Localhost
        $redis->connect("127.0.0.1", 6379);
    }

    return [
        'pdo' => $pdo,
        'mongo' => $mongo_db,
        'redis' => $redis
    ];
}
