<?php
// 重定向到topics.php
header("Location: topics.php");
exit;
?>

<?php
include 'config.php';
session_start();
?>  <!-- Add closing PHP tag here -->

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>智能管理中心</title>
    <!-- 共用样式 -->
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Roboto:wght@300;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* 继承登录页的变量 */
        :root {
            --primary-color: #00f3ff;
            --secondary-color: #ff0266;
            --bg-gradient: linear-gradient(45deg, #0a0a2e, #1a1a4a);
        }

        /* 仪表盘专属样式 */
        .dashboard {
            background: var(--bg-gradient);
            min-height: 100vh;
            color: #fff;
        }

        /* 增强版导航栏 */
        .cyber-nav {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--primary-color);
            box-shadow: 0 0 20px rgba(0, 243, 255, 0.2);
            padding: 15px 30px;
        }

        .cyber-nav a {
            color: var(--primary-color);
            padding: 10px 20px;
            border-radius: 5px;
            transition: all 0.3s ease;
            text-decoration: none;
            position: relative;
        }

        .cyber-nav a:hover {
            background: rgba(0, 243, 255, 0.1);
            transform: translateY(-2px);
        }

        .cyber-nav a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 50%;
            width: 0;
            height: 2px;
            background: var(--primary-color);
            transition: all 0.3s ease;
        }

        .cyber-nav a:hover::after {
            width: 100%;
            left: 0;
        }

        /* 数据卡片 */
        .data-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--primary-color);
            border-radius: 10px;
            padding: 20px;
            margin: 15px;
            backdrop-filter: blur(5px);
            transition: transform 0.3s ease;
        }

        .data-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 243, 255, 0.3);
        }

        /* 图表容器 */
        .chart-container {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid var(--primary-color);
            border-radius: 15px;
            padding: 20px;
            margin: 20px;
        }

        /* 任务列表 */
        .task-list {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            padding: 15px;
        }

        .task-item {
            padding: 12px;
            margin: 8px 0;
            background: rgba(0, 243, 255, 0.05);
            border-left: 3px solid var(--primary-color);
            transition: all 0.3s ease;
        }

        .task-item:hover {
            background: rgba(0, 243, 255, 0.1);
            transform: translateX(10px);
        }

        /* 响应式网格布局 */
        .grid-system {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            padding: 20px;
        }
    </style>
</head>
<body class="dashboard">
    <!-- 增强导航栏 -->


    <!-- 主内容区 -->
    <div class="grid-system">
        <!-- 数据卡片示例 -->
        <div class="data-card">
            <h3><i class="fas fa-tasks"></i> 今日任务</h3>
            <div class="task-list">
                <div class="task-item">完成系统界面优化</div>
                <div class="task-item">测试用户权限模块</div>
            </div>
        </div>
    </div>
    <!-- 移除原有图表容器和ECharts引用 -->
</body>
</html>