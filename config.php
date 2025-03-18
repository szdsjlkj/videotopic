<?php
// 数据库配置
$servername = "localhost";
$username = "yourname";
$password = "yourpassword";
$dbname = "yourdbname";

// 创建连接
$conn = new mysqli($servername, $username, $password, $dbname);

// 检查连接
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 设置字符编码
$conn->set_charset("utf8mb4");
?>
