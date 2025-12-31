<?php
// php/install.php
require_once "db.php";

echo "<h1>Installing Database...</h1>";

try {
    $db = getDB();
    $pdo = $db['pdo'];

    // Read SQL from file
    $sql = file_get_contents(__DIR__ . '/../assets/setup_mysql.sql');
    
    if (!$sql) {
        throw new Exception("Could not read assets/setup_mysql.sql");
    }

    echo "<p>Running SQL...</p>";
    $pdo->exec($sql);
    
    echo "<h2 style='color:green'>Success! Table 'users' created.</h2>";
    echo "<p>You can now delete this file and <a href='../register.html'>Register</a>.</p>";

} catch (Exception $e) {
    echo "<h2 style='color:red'>Error: " . $e->getMessage() . "</h2>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
