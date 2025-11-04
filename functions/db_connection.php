<?php
$config = include __DIR__ . '/config.php';

function getDbConnection() {
    global $config;
    $conn = mysqli_connect(
        $config['servername'],
        $config['username'],
        $config['password'],
        $config['dbname'],
        $config['port']
    );

    if (!$conn) {
        die("Kết nối database thất bại: " . mysqli_connect_error());
    }
    mysqli_set_charset($conn, "utf8");
    return $conn;
}
?>
