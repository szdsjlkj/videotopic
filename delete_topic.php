<?php
include 'config.php';
include 'auth.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $topicId = $_GET['id'];

    // 删除文件
    $sql = "SELECT file_path FROM topic_files WHERE topic_id = $topicId";
    $result = $conn->query($sql);
    while ($file = $result->fetch_assoc()) {
        unlink($file['file_path']);
    }

    // 删除文件记录
    $stmt = $conn->prepare("DELETE FROM topic_files WHERE topic_id = ?");
    $stmt->bind_param("i", $topicId);
    $stmt->execute();
    $stmt->close();

    // 删除选题记录
    $stmt = $conn->prepare("DELETE FROM topics WHERE id = ?");
    $stmt->bind_param("i", $topicId);
    if ($stmt->execute()) {
        echo "<p style='color: green;'>选题删除成功</p>";
    } else {
        echo "<p style='color: red;'>Error: " . $stmt->error . "</p>";
    }
    $stmt->close();

    header("Location: topics.php");
    exit;
}
?>