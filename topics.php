<?php
// 引入配置文件
include 'config.php';
// 引入用户认证文件
include 'auth.php';
// 启动会话
session_start();

// 检查用户是否已登录
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// 处理添加选题请求
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_topic'])) {
    $topicName = $_POST['topic_name'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $userId = $_SESSION['user_id'];

    // 确保素材文件夹存在
    $targetDir = __DIR__ . '/sucai/'; // 使用绝对路径
    if (!is_dir($targetDir)) {
        if (!mkdir($targetDir, 0777, true) && !is_dir($targetDir)) {
            error_log("无法创建素材文件夹: " . $targetDir);
            die("无法创建素材文件夹，请检查目录权限。当前路径：" . $targetDir);
        }
    }

    // 插入选题信息
    $stmt = $conn->prepare("INSERT INTO topics (user_id, topic_name, description, category, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("isss", $userId, $topicName, $description, $category);
    if ($stmt->execute()) {
        $topicId = $stmt->insert_id;
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


        // 添加重定向
        header("Location: topics.php");
        exit;
    } else {
        echo "<p style='color: red;'>Error: " . $stmt->error . "</p>";
    }
}

// 获取当前分类
$category = isset($_GET['category']) ? $_GET['category'] : '';

// 获取当前用户的所有选题
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sql = "SELECT * FROM topics WHERE user_id = {$_SESSION['user_id']}";
if (!empty($search)) {
    $sql .= " AND (topic_name LIKE '%$search%' OR description LIKE '%$search%')";
}
if ($category) {
    $sql .= " AND category = '$category'";
}
$sql .= " ORDER BY created_at DESC";
$result = $conn->query($sql);
$topics = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $topics[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>选题库</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        header {
            background-color: #3467f9;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header h1 {
            margin: 0;
            font-size: 24px;
        }

        header nav {
            display: flex;
            gap: 15px;
        }

        header nav a {
            color: white;
            text-decoration: none;
            font-size: 16px;
        }

        header .actions {
            display: flex;
            gap: 10px;
        }

        header .actions button,
        header .actions input[type="text"],
        header .actions button[type="submit"] {
            padding: 8px 12px;
            border-radius: 100px;
            border: none;
            font-size: 14px;
        }

        header .actions button {
            background-color: #555;
            color: white;
            cursor: pointer;
        }

        header .actions button:hover {
            background-color: #777;
        }

        header .actions input[type="text"] {
            border: 1px solid #ccc;
        }

        header .actions button[type="submit"] {
            background-color: #fff;
            color: #3467f9;
            cursor: pointer;
        }

        header .actions button[type="submit"]:hover {
            background-color: #777;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
            // 修改为单栏布局
            display: block; 
        }


        /*
        .timeline {
            position: relative;
        }

        .timeline::before {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            left: 10px;
            width: 2px;
            background-color: #ccc;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        .timeline-item::after {
            content: '';
            position: absolute;
            left: 4px;
            width: 12px;
            height: 12px;
            background-color: #333;
            border-radius: 50%;
        }

        .timeline-item p {
            margin: 0;
            font-size: 14px;
            color: #666;
            padding-left: 30px;
        }
        */

        .content {
            flex: 1;
        }

        .form-container {
            display: none;
            max-width: 600px;
            margin: 20px auto;
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
        form input[type="file"] {
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

        .topics-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .topic-item {
            background-color: white;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: flex; /* 修改为 flex 布局 */
            flex-direction: column; /* 垂直排列子元素 */
            gap: 10px;
        }

        .topic-item h2 {
            margin-top: 0;
            font-size: 18px;
            display: flex;
            align-items: center;
        }

        .topic-item h2 span {
            font-size: 14px;
            color: #666;
            margin-left: 10px;
        }

        .topic-item p {
            margin: 0;
        }

        .media-preview-container {
            display: flex;
            gap: 20px;
        }

        .media-preview {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            margin-top: 5px;
        }

        .media-preview .media-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100px;
            height: 100px;
            margin: 5px;
            position: relative;
            overflow: hidden;
        }

        .media-preview .media-item:hover::after {
            content: attr(data-filename);
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: rgba(0, 0, 0, 0.7);
            color: white;
            font-size: 12px;
            padding: 2px 5px;
            text-align: center;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .media-preview video {
            width: 100%;
            height: 100%;
            object-fit: cover;  // 预览时裁剪为方形
            border-radius: 4px;
            cursor: pointer;
            transition: transform 0.3s ease;
            background-color: black;
        }

        // 全屏样式保持不变
        video:-webkit-full-screen {
            object-fit: contain;  // 全屏时显示完整内容
            background-color: black;
        }

        video:-moz-full-screen {
            object-fit: contain;
            background-color: black;
        }

        video:-ms-fullscreen {
            object-fit: contain;
            background-color: black;
        }

        video:fullscreen {
            object-fit: contain;
            background-color: black;
        }
        .media-preview video:hover {
            transform: scale(1.05);
        }

        .media-preview span {
            font-size: 12px;
            color: #333;
            margin-top: 5px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
            text-align: center;
        }

        .media-preview form {
            margin-top: 3px;
        }

        .media-preview form input[type="submit"] {
            background-color: red;
            color: white;
            padding: 3px 5px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 10px;
        }

        .media-preview form input[type="submit"]:hover {
            background-color: darkred;
        }

        .media-preview img:hover {
            cursor: pointer;
        }

        .actions {
            align-self: flex-end;
            display: flex;
            gap: 5px;
            margin-top: 5px;
        }

        .actions a {
            color: white;
            background-color: #333;
            padding: 3px 5px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 12px;
        }

        .actions a:hover {
            background-color: #555;
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
        function toggleForm() {
            var formContainer = document.getElementById('form-container');
            if (formContainer.style.display === 'none' || formContainer.style.display === '') {
                formContainer.style.display = 'block';
            } else {
                formContainer.style.display = 'none';
            }
        }

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
    <!-- 导航菜单 -->
    <header>
        <h1>选题库</h1>
        <nav>
            <a href="topics.php">选题管理</a>
            <a href="daily_management.php">拍摄日历</a>
            <a href="logout.php">退出登录</a>
        </nav>

    </header>

 
    <nav class="category-nav">
        <div class="category-buttons">
            <?php 
            $categories = ['分类一', '分类二', '分类三', '其他'];
            $currentCategory = isset($_GET['category']) ? $_GET['category'] : '';
            foreach ($categories as $cat): ?>
                <a href="?search=<?= urlencode($search) ?>&category=<?= $cat === '全部' ? '' : urlencode($cat) ?>"
                   class="category-button <?= ($currentCategory === $cat) ? 'active' : '' ?>">
                    <?= $cat ?>
                </a>
            <?php endforeach; ?>
            
            <!-- 添加搜索和新增按钮 -->
            <div class="actions">
            <script>
                // 添加搜索功能
                function handleSearch(event) {
                    event.preventDefault(); // 阻止表单默认提交行为
                    const searchValue = document.querySelector('input[name="search"]').value;
                    const category = new URLSearchParams(window.location.search).get('category') || '';
                    window.location.href = `?search=${encodeURIComponent(searchValue)}&category=${encodeURIComponent(category)}`;
                }

                // 绑定搜索事件
                document.querySelector('form[method="get"]').addEventListener('submit', handleSearch);

            </script>
            <div class="actions">
                <form method="get" action="topics.php" style="display: flex; gap: 10px;">
                    <input type="text" name="search" placeholder="搜索选题" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    <button type="submit" class="icon-button"><i class="fas fa-search"></i></button>
                </form>
                <button onclick="toggleForm()" class="icon-button"><i class="fas fa-plus"></i></button>
            </div>

        </div>
    </nav>
    

    <style>
        .category-nav {
            background: #f8f9fa;
            padding: 15px 20px;
            margin-bottom: 20px;
            border-bottom: 1px solid #e9ecef;
            max-width: 1400px;  /* 添加最大宽度 */
            margin: 0 auto;    /* 添加居中 */
            padding: 20px;     /* 与容器一致 */
            display: flex;  /* 添加flex布局 */
            align-items: center;  /* 垂直居中 */
            justify-content: space-between;  /* 水平对齐 */
        }
    
        .category-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;  /* 添加垂直居中 */
        }
    
        .category-button {
            padding: 4px 12px;  /* 调整padding */
            border-radius: 20px;
            background-color: #e9ecef;
            color: #495057;
            text-decoration: none;
            font-size: 12px;  /* 减小字体大小 */
            transition: all 0.3s ease;
            border: 1px solid transparent;
            min-width: 80px;  /* 设置最小宽度 */
            text-align: center;  /* 文字居中 */
            line-height: 1.2;  /* 固定行高 */
            height: 24px;  /* 固定高度 */
            display: inline-flex;  /* 使用flex布局 */
            align-items: center;  /* 内容垂直居中 */
            justify-content: center;  /* 内容水平居中 */
            white-space: nowrap;  /* 防止文字换行 */
        }
    
        .category-button:hover {
            background-color: #dee2e6;
            transform: translateY(-1px);
        }
    
        .category-button.active {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }
    
        .category-button.active:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
    </style>

    <div class="container">

        <!-- 
        <div class="timeline">
            <?php foreach ($topics as $topic) : ?>
                <div class="timeline-item">
                    <p><?php echo $topic['created_at']; ?></p>
                </div>
            <?php endforeach; ?>
        </div>
        -->
        <div class="content">
            <div id="form-container" class="form-container">
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
                    <label for="topic_name">选题名称:</label>
                    <input type="text" id="topic_name" name="topic_name" required>
                    <label for="description">文案:</label>
                    <textarea id="description" name="description" required></textarea>
                    <label for="category">分类:</label>
                    <select id="category" name="category" required>
                        <option value="分类一">分类一</option>
                        <option value="分类二">分类二</option>
                        <option value="分类三">分类三</option>
                        <option value="其他">其他</option>
                    </select>
                    <label for="videos">视频:</label>
                    <input type="file" id="videos" name="videos[]" accept="video/*" multiple>
                    <label for="images">图片:</label>
                    <input type="file" id="images" name="images[]" accept="image/*" multiple>
                    <input type="submit" name="add_topic" value="添加选题">
                </form>
            </div>
            <div class="topics-list">
                <?php foreach ($topics as $topic) : ?>
                    <div class="topic-item">
                        <h2>
                            <?php echo $topic['topic_name']; ?>
                            <span><?php echo $topic['created_at']; ?></span>
                        </h2>
                        <p><?php echo $topic['description']; ?></p>
                        <div class="media-preview-container">
                            <div class="media-preview">

                                <?php
                                // 先查询并显示视频
                                $sql = "SELECT * FROM topic_files WHERE topic_id = {$topic['id']} AND file_type = 'video'";
                                $result = $conn->query($sql);
                                while ($file = $result->fetch_assoc()) {
                                    echo "<div class='media-item' data-filename='".basename($file['file_path'])."'>";
                                    echo "<video controls ondblclick=\"this.requestFullscreen()\">";
                                    echo "<source src='{$file['file_path']}' type='video/mp4'>";
                                    echo "您的浏览器不支持视频标签。";
                                    echo "</video>";
                                    echo "<span>" . basename($file['file_path']) . "</span>";
                                    echo "</div>";
                                }
                                
                                // 再查询并显示图片
                                $sql = "SELECT * FROM topic_files WHERE topic_id = {$topic['id']} AND file_type = 'image'";
                                $result = $conn->query($sql);
                                while ($file = $result->fetch_assoc()) {
                                    echo "<div class='media-item' data-filename='".basename($file['file_path'])."'>";
                                    echo "<img src='{$file['file_path']}' alt='Image' ondblclick='showModal(\"{$file['file_path']}\")'>";
                                    echo "<span>" . basename($file['file_path']) . "</span>";
                                    echo "</div>";
                                }
                                ?>
                            </div>
                        </div>
                        <div class="actions">
                            <a href="edit_topic.php?id=<?php echo $topic['id']; ?>">编辑</a>
                            <a href="delete_topic.php?id=<?php echo $topic['id']; ?>" onclick="return confirm('确定要删除吗？')">删除</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- The Modal -->
    <div id="myModal" class="modal">
        <span class="close" onclick="closeModal()">&times;</span>
        <div class="modal-content">
            <img id="img01">
        </div>
    </div>
    <script>
        // 添加搜索框点击事件
        document.querySelector('.search-container').addEventListener('click', function() {
            this.classList.add('active');
            this.querySelector('.search-input').focus();
        });

        // 点击其他地方收起搜索框
        document.addEventListener('click', function(e) {
            const searchContainer = document.querySelector('.search-container');
            if (!searchContainer.contains(e.target)) {
                searchContainer.classList.remove('active');
            }
        });

    </script>
</body>

</html>

    <style>
        .icon-button {
            width: 30px;  /* 减小宽度 */
            height: 30px;  /* 减小高度 */
            padding: 5px;  /* 减小内边距 */
            border-radius: 50%;
            background-color: #e9ecef;
            color: #495057;
            border: 1px solid transparent;
            cursor: pointer;
            font-size: 12px;  /* 减小字体大小 */
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
        }

        .search-form input[type="text"] {
            padding: 5px 10px;  /* 减小内边距 */
            border: 1px solid #ccc;
            border-radius: 20px;
            font-size: 12px;  /* 减小字体大小 */
            line-height: 1;
            height: 30px;  /* 设置固定高度 */
        }

        .icon-button:hover {
            background-color: #dee2e6;
            transform: translateY(-1px);
        }

        .icon-button.active {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }

        .icon-button.active:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
    </style>