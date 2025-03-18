<!DOCTYPE html>
<html>

<head>
    <title>日历展示</title>
</head>

<body>
    <h1>日历日程</h1>
    <?php
    // 连接数据库
    $servername = "localhost";
    $username = "your_username";
    $password = "your_password";
    $dbname = "your_database";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // 检查连接
    if ($conn->connect_error) {
        die("连接失败: " . $conn->connect_error);
    }

    // 查询数据
    $sql = "SELECT date, content FROM calendar";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // 输出数据
        while ($row = $result->fetch_assoc()) {
            echo "<p>日期: " . $row["date"] . " - 内容: " . $row["content"] . "</p>";
        }
    } else {
        echo "暂无日程安排";
    }

    $conn->close();
    ?>
</body>

</html>
