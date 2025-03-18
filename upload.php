<?php
// 开启错误显示
ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['file'])) {
        // 记录上传文件信息
        error_log('Uploading file: ' . print_r($_FILES['file'], true));
        
        // 检查文件上传错误
        if ($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            $errorMsg = '文件上传出错，错误代码：' . $_FILES['file']['error'];
            error_log($errorMsg);
            die($errorMsg);
        }

        $targetDir = "uploads/";
        // 确保上传目录存在
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }
        
        $targetFile = $targetDir . basename($_FILES["file"]["name"]);
        
        // 检查文件大小
        $maxSize = 1024 * 1024 * 1024; // 1GB
        if ($_FILES["file"]["size"] > $maxSize) {
            $errorMsg = "文件太大，最大允许1GB";
            error_log($errorMsg);
            die($errorMsg);
        }

        // 尝试移动上传的文件
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
            echo "文件上传成功";
        } else {
            $errorMsg = "文件移动失败";
            error_log($errorMsg);
            die($errorMsg);
        }
    } else {
        $errorMsg = "未接收到文件";
        error_log($errorMsg);
        die($errorMsg);
    }
}
?>
