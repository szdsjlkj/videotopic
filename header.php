<?php
// 启动会话
session_start();
?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>选题管理</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #121212;
            color: #e0e0e0;
        }

        header {
            background-color: #1e1e1e;
            color: #00bfff;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 8px rgba(0, 191, 255, 0.2);
            border-bottom: 2px solid #00bfff;
        }

        header h1 {
            margin: 0;
            font-size: 28px;
            text-shadow: 0 0 5px #00bfff;
        }

        header nav {
            display: flex;
            gap: 20px;
        }

        header nav a {
            color: #00bfff;
            text-decoration: none;
            font-size: 18px;
            transition: all 0.3s ease;
            position: relative;
        }

        header nav a::after {
            content: '';
            position: absolute;
            width: 100%;
            transform: scaleX(0);
            height: 2px;
            bottom: -3px;
            left: 0;
            background-color: #00bfff;
            transform-origin: bottom right;
            transition: transform 0.3s ease-out;
        }

        header nav a:hover {
            color: #00ffff;
        }

        header nav a:hover::after {
            transform: scaleX(1);
            transform-origin: bottom left;
        }

        header .actions {
            display: flex;
            gap: 15px;
        }

        header .actions button,
        header .actions input[type="text"],
        header .actions button[type="submit"] {
            padding: 10px 15px;
            border-radius: 6px;
            border: none;
            font-size: 16px;
            background-color: #2a2a2a;
            color: #00bfff;
            transition: all 0.3s ease;
        }

        header .actions button {
            cursor: pointer;
        }

        header .actions button:hover,
        header .actions button[type="submit"]:hover {
            background-color: #333;
            color: #00ffff;
            box-shadow: 0 0 10px rgba(0, 191, 255, 0.5);
        }

        header .actions input[type="text"] {
            border: 1px solid #00bfff;
        }

        header .actions input[type="text"]:focus {
            outline: none;
            box-shadow: 0 0 10px rgba(0, 191, 255, 0.5);
        }

        #form-container {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 2;
            background-color: #1e1e1e;
            box-shadow: 0 0 20px rgba(0, 191, 255, 0.5);
            border: 2px solid #00bfff;
            border-radius: 10px;
            padding: 20px;
        }

        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            z-index: 1;
        }

        #form-container form label {
            display: block;
            margin-bottom: 5px;
            color: #00bfff;
        }

        #form-container form input[type="text"],
        #form-container form textarea,
        #form-container form input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #00bfff;
            border-radius: 6px;
            background-color: #2a2a2a;
            color: #e0e0e0;
        }

        #form-container form input[type="text"]:focus,
        #form-container form textarea:focus {
            outline: none;
            box-shadow: 0 0 10px rgba(0, 191, 255, 0.5);
        }

        #form-container form input[type="submit"] {
            padding: 10px 15px;
            border-radius: 6px;
            border: none;
            font-size: 16px;
            background-color: #00bfff;
            color: #1e1e1e;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        #form-container form input[type="submit"]:hover {
            background-color: #00ffff;
            box-shadow: 0 0 10px rgba(0, 191, 255, 0.5);
        }

        .close {
            position: absolute;
            top: 10px;
            right: 10px;
            color: #00bfff;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .close:hover {
            color: #00ffff;
        }
    </style>
    <script>
        function toggleForm() {
            var formContainer = document.getElementById('form-container');
            var overlay = document.getElementById('overlay');
            if (formContainer.style.display === 'none' || formContainer.style.display === '') {
                formContainer.style.display = 'block';
                overlay.style.display = 'block';
            } else {
                formContainer.style.display = 'none';
                overlay.style.display = 'none';
            }
        }

        function closeForm() {
            var formContainer = document.getElementById('form-container');
            var overlay = document.getElementById('overlay');
            formContainer.style.display = 'none';
            overlay.style.display = 'none';
        }
    </script>
</head>

<body>
    <!-- 导航菜单 -->
    <header>
        <h1>选题管理</h1>
        <nav>
            <a href="topics.php">选题管理</a>
            <a href="daily_management.php">日程安排</a>
            <a href="logout.php">退出登录</a>
        </nav>
        <div class="actions">
            <button onclick="toggleForm()">新增选题</button>
            <form method="get" action="topics.php" style="display: flex; gap: 10px;">
                <input type="text" name="search" placeholder="搜索选题">
                <button type="submit">搜索</button>
            </form>
        </div>
    </header>
    <div id="overlay" class="overlay"></div>
    <div id="form-container" class="form-container">
        <span class="close" onclick="closeForm()">&times;</span>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
            <label for="topic_name">选题名称:</label>
            <input type="text" id="topic_name" name="topic_name" required>
            <label for="description">描述:</label>
            <textarea id="description" name="description" required></textarea>
            <label for="videos">视频:</label>
            <input type="file" id="videos" name="videos[]" accept="video/*" multiple>
            <label for="images">图片:</label>
            <input type="file" id="images" name="images[]" accept="image/*" multiple>
            <input type="submit" name="add_topic" value="添加选题">
        </form>
    </div>
