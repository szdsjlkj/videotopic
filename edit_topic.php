<?php
include 'config.php';
include 'auth.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $topicId = intval($_GET['id']);
    $sql = "SELECT * FROM topics WHERE id = $topicId AND user_id = {$_SESSION['user_id']}";
    $result = $conn->query($sql);
    if ($result->num_rows == 0) {
        echo "<p style='color: red;'>选题不存在或无权限编辑</p>";
        exit;
    }
    $topic = $result->fetch_assoc();
}

// 处理编辑选题请求
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_topic'])) {
    $topicId = $_POST['topic_id'];
    $topicName = $_POST['topic_name'];
    $description = $_POST['description'];
    $category = $_POST['category'];

    // 更新选题信息
    $stmt = $conn->prepare("UPDATE topics SET topic_name = ?, description = ?, category = ? WHERE id = ?");
    $stmt->bind_param("sssi", $topicName, $description, $category, $topicId);
    if ($stmt->execute()) {
        $stmt->close();

        // 文件上传处理
        $folderName = $topicName; // 直接使用原始项目标题
        $targetDir = "sucai/{$folderName}/"; // 使用项目标题创建子目录
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        // 处理视频上传
        foreach ($_FILES['videos']['name'] as $key => $video) {
            $targetFileVideo = $targetDir . $video; // 直接使用原始文件名
            if (move_uploaded_file($_FILES["videos"]["tmp_name"][$key], $targetFileVideo)) {
                $stmt = $conn->prepare("INSERT INTO topic_files (topic_id, file_path, file_type) VALUES (?, ?, 'video')");
                $stmt->bind_param("is", $topicId, $targetFileVideo);
                $stmt->execute();
                $stmt->close();
            }
        }

        // 处理图片上传
        foreach ($_FILES['images']['name'] as $key => $image) {
            $targetFileImage = $targetDir . $image; // 直接使用原始文件名
            if (move_uploaded_file($_FILES["images"]["tmp_name"][$key], $targetFileImage)) {
                $stmt = $conn->prepare("INSERT INTO topic_files (topic_id, file_path, file_type) VALUES (?, ?, 'image')");
                $stmt->bind_param("is", $topicId, $targetFileImage);
                $stmt->execute();
                $stmt->close();
            }
        }

        header("Location: topics.php");
        exit;
    } else {
        echo "<p style='color: red;'>Error: " . $stmt->error . "</p>";
    }
}

if (isset($_GET['id'])) {
    $topicId = $_GET['id'];
    $sql = "SELECT * FROM topics WHERE id = $topicId";
    $result = $conn->query($sql);
    if (!$result) {
        die("Error: " . $conn->error);
    }
    $topic = $result->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_file'])) {
    $fileId = $_POST['file_id'];
    $filePath = $_POST['file_path'];
    $topicId = $_POST['topic_id'];

    // 删除文件
    if (unlink($filePath)) {
        $stmt = $conn->prepare("DELETE FROM topic_files WHERE id = ?");
        $stmt->bind_param("i", $fileId);
        $stmt->execute();
        $stmt->close();
        echo "<p style='color: green;'>文件删除成功</p>";
    } else {
        echo "<p style='color: red;'>文件删除失败</p>";
    }

    // 重定向回当前页面
    header("Location: edit_topic.php?id=$topicId");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['upload'])) {
    $targetDir = "uploads/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    foreach ($_FILES['videos']['name'] as $key => $video) {
        $targetFile = $targetDir . basename($video);
        if (move_uploaded_file($_FILES["videos"]["tmp_name"][$key], $targetFile)) {
            echo "文件 " . basename($video) . " 上传成功。<br>";
        } else {
            echo "文件 " . basename($video) . " 上传失败。<br>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>编辑选题</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        nav {
            background-color: #3467f9;
            color: white;
            padding: 15px;
            text-align: right;
        }

        nav a {
            color: white;
            text-decoration: none;
            margin: 0 15px;
            font-size: 18px;
        }

        h1 {
            text-align: center;
            color: #333;
            margin-top: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        form label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        form input[type="text"],
        form textarea,
        form input[type="file"],
        form select {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        form input[type="submit"] {
            background-color: #333;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        form input[type="submit"]:hover {
            background-color: #555;
        }

        .media-preview {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 20px;
        }

        .media-preview .media-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100px;
        }

        .media-preview video,
        .media-preview img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 4px;
        }

        .media-preview span {
            font-size: 12px;
            color: #333;
            margin-top: 5px;
        }

        .media-preview form {
            margin-top: 5px;
        }

        .media-preview form input[type="submit"] {
            background-color: red;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }

        .media-preview form input[type="submit"]:hover {
            background-color: darkred;
        }

        .media-preview img:hover {
            cursor: pointer;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0, 0, 0);
            background-color: rgba(0, 0, 0, 0.9);
        }

        .modal-content {
            margin: auto;
            display: block;
            width: 80%;
            max-width: 700px;
        }

        .modal-content img {
            width: 100%;
        }

        .close {
            position: absolute;
            top: 15px;
            right: 35px;
            color: #fff;
            font-size: 40px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: #bbb;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
    <script>
        function showModal(src) {
            var modal = document.getElementById("myModal");
            var modalImg = document.getElementById("img01");
            modal.style.display = "block";
            modalImg.src = src;
        }

        function closeModal() {
            var modal = document.getElementById("myModal");
            modal.style.display = "none";
        }
    </script>
</head>

<body>
    <nav>
        <a href="topics.php">选题管理</a>
        <a href="daily_management.php">拍摄日历</a>
        <a href="logout.php">退出登录</a>
    </nav>
    <div class="container">
        <h1>编辑选题</h1>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?id=$topicId"; ?>" enctype="multipart/form-data">
            <input type="hidden" name="topic_id" value="<?php echo $topic['id']; ?>">
            <label for="topic_name">选题名称:</label>
            <input type="text" id="topic_name" name="topic_name" value="<?php echo htmlspecialchars($topic['topic_name']); ?>" required>
            <label for="description">文案:</label>
            <textarea id="description" name="description" required><?php echo htmlspecialchars($topic['description']); ?></textarea>
            <label for="category">分类:</label>
            <select id="category" name="category" required>
                <option value="分类一" <?php if ($topic['category'] == '分类一') echo 'selected'; ?>>分类一</option>
                <option value="分类二" <?php if ($topic['category'] == '分类二') echo 'selected'; ?>>分类二</option>
                <option value="分类三" <?php if ($topic['category'] == '分类三') echo 'selected'; ?>>分类三</option>
                <option value="其他" <?php if ($topic['category'] == '其他') echo 'selected'; ?>>其他</option>
            </select>
            <label for="videos">视频:</label>
            <input type="file" id="videos" name="videos[]" accept="video/*" multiple>
            <label for="images">图片:</label>
            <input type="file" id="images" name="images[]" accept="image/*" multiple>
            <input type="submit" name="edit_topic" value="更新选题">
        </form>
        <div class="media-preview">
            <h2>当前视频:</h2>
            <?php
            $sql = "SELECT * FROM topic_files WHERE topic_id = $topicId AND file_type = 'video'";
            $result = $conn->query($sql);
            if (!$result) {
                die("Error: " . $conn->error);
            }
            while ($file = $result->fetch_assoc()) {
                echo "<div class='media-item'>";
                echo "<video controls><source src='{$file['file_path']}' type='video/mp4'>您的浏览器不支持视频标签。</video>";
                echo "<span>" . basename($file['file_path']) . "</span>";
                echo "<form method='post' action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "' onsubmit='return confirmDelete(\"{$file['file_path']}\")'>";
                echo "<input type='hidden' name='file_id' value='{$file['id']}'>";
                echo "<input type='hidden' name='file_path' value='{$file['file_path']}'>";
                echo "<input type='hidden' name='topic_id' value='$topicId'>";
                echo "<input type='submit' name='delete_file' value='删除'>";
                echo "</form>";
                echo "</div>";
            }
            ?>
        </div>
        <div class="media-preview">
            <h2>当前图片:</h2>
            <?php
            $sql = "SELECT * FROM topic_files WHERE topic_id = $topicId AND file_type = 'image'";
            $result = $conn->query($sql);
            if (!$result) {
                die("Error: " . $conn->error);
            }
            while ($file = $result->fetch_assoc()) {
                echo "<div class='media-item'>";
                echo "<img src='{$file['file_path']}' alt='Image' ondblclick='showModal(\"{$file['file_path']}\")'>";
                echo "<span>" . basename($file['file_path']) . "</span>";
                echo "<form method='post' action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "' onsubmit='return confirmDelete(\"{$file['file_path']}\")'>";
                echo "<input type='hidden' name='file_id' value='{$file['id']}'>";
                echo "<input type='hidden' name='file_path' value='{$file['file_path']}'>";
                echo "<input type='hidden' name='topic_id' value='$topicId'>";
                echo "<input type='submit' name='delete_file' value='删除'>";
                echo "</form>";
                echo "</div>";
            }
            ?>
        </div>
    </div>

    <!-- The Modal -->
    <div id="myModal" class="modal">
        <span class="close" onclick="closeModal()">&times;</span>
        <div class="modal-content">
            <img id="img01">
        </div>
    </div>
</body>

</html>

<script>
function confirmDelete(filePath) {
    return confirm(`确定要删除文件 "${filePath}" 吗？此操作不可恢复！`);
}
</script>