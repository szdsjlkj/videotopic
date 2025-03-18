<?php
// 引用公共的 header.php 文件
include 'header.php';
?>

<?php
include 'config.php';
session_start();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <!-- 共用样式 -->
    <style>
        /* 继承原有样式... */
        .chart-container {
            height: 500px;
            padding: 30px;
        }
    </style>
</head>
<body class="dashboard">
    <!-- 导航栏 -->

    // 在<head>的样式中添加以下内容
    <style>
        /* 新增科技感元素 */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                linear-gradient(
                    45deg,
                    transparent 48%,
                    rgba(138,43,226,0.1) 50%,
                    transparent 52%
                ),
                repeating-linear-gradient(
                    -45deg,
                    rgba(0,255,0,0.05) 0 1px,
                    transparent 1px 20px
                );
            z-index: -1;
        }
    
        .cyber-nav {
            border-bottom: 2px solid var(--primary-color);
            position: relative;
        }
    
        .cyber-nav::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 100%;
            height: 2px;
            background: linear-gradient(
                90deg,
                transparent,
                var(--secondary-color),
                transparent
            );
            animation: scanline 3s linear infinite;
        }
    
        .data-card {
            position: relative;
            transition: all 0.3s ease;
            transform-style: preserve-3d;
        }
    
        .data-card:hover {
            transform: perspective(1000px) rotateX(5deg) rotateY(5deg);
            box-shadow: 
                0 0 30px rgba(138,43,226,0.5),
                0 0 60px rgba(0,255,0,0.2);
        }
    
        .chart-container {
            border: 1px solid;
            border-image: linear-gradient(
                45deg,
                var(--primary-color),
                var(--secondary-color)
            ) 1;
            position: relative;
        }
    
        @keyframes scanline {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
    
        /* 增强图表样式 */
        #main-chart {
            background: rgba(0,0,0,0.3);
            border-radius: 10px;
            border: 1px solid rgba(138,43,226,0.5);
            box-shadow: inset 0 0 15px rgba(138,43,226,0.2);
        }
    </style>
    
    <!-- 在<body>开始处添加全息效果 -->
    <div class="hologram-effect">
        <div class="grid-lines"></div>
        <div class="particles"></div>
    </div>

    <style>
        /* 继承index.php的仪表盘样式 */
        body.dashboard {
            background: var(--bg-gradient);
            font-family: 'Roboto', 'Orbitron', sans-serif;
            color: #fff;
        }
    
        .cyber-nav {
            background: rgba(0, 0, 0, 0.8);
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 20px rgba(0, 243, 255, 0.2);
        }
    
        /* 同步数据卡片样式 */
        .data-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--primary-color);
            border-radius: 10px;
            padding: 20px;
            margin: 15px;
            backdrop-filter: blur(5px);
        }
    
        /* 保持原有图表容器样式 */
        .chart-container {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid var(--primary-color);
            border-radius: 15px;
            padding: 20px;
            margin: 20px;
        }
    
        /* 同步网格系统 */
        .grid-system {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            padding: 20px;
        }
    </style>
    
    <!-- 修改导航栏结构保持与index一致 -->
    <nav class="cyber-nav">
        <div class="nav-brand">
            <i class="fas fa-robot"></i>
            智能管理中心
        </div>
        <div class="nav-links">
            <a href="daily_management.php"><i class="fas fa-calendar-alt"></i> 日程管理</a>
            <a href="topics.php"><i class="fas fa-project-diagram"></i> 项目管理</a>
            <a href="reports.php"><i class="fas fa-chart-line"></i> 数据看板</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> 安全退出</a>
        </div>
    </nav>
    
    <!-- 用网格系统包裹内容 -->
    <div class="grid-system">
        <div class="data-card chart-container">
            <!-- 原有图表内容保持不变 -->
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/echarts@5.4.0/dist/echarts.min.js"></script>
    <script>
        // 获取统计数据
        fetch('stats.php')
            .then(response => response.json())
            .then(data => {
                const chart = echarts.init(document.getElementById('main-chart'));
                const option = {
                    tooltip: { trigger: 'item' },
                    series: [{
                        type: 'pie',
                        data: [
                            { value: data.tasks, name: '任务数量' },
                            { value: data.projects, name: '项目数量' }
                        ],
                        itemStyle: {
                            color: function(params) {
                                return params.dataIndex === 0 
                                    ? '#8A2BE2' 
                                    : '#00FF00';
                            }
                        }
                    }]
                };
                chart.setOption(option);
            });
    </script>

    <style>
    /* 新增数据表格样式 */
    .data-table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
    }

    .data-table th, .data-table td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid var(--primary-color);
    }

    .data-table th {
        background: rgba(0, 243, 255, 0.1);
    }
    </style>

    <!-- 在图表容器后添加 -->
    <div class="chart-container">
        <h3><i class="fas fa-table"></i> 详细数据</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>类型</th>
                    <th>今日新增</th>
                    <th>总计</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total_tasks = $conn->query("SELECT COUNT(*) FROM tasks")->fetch_row()[0];
                $total_projects = $conn->query("SELECT COUNT(*) FROM projects")->fetch_row()[0];
                ?>
                <tr>
                    <td>任务</td>
                    <td><?php echo $task_count ?></td>
                    <td><?php echo $total_tasks ?></td>
                </tr>
                <tr>
                    <td>项目</td>
                    <td><?php echo $project_count ?></td>
                    <td><?php echo $total_projects ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>
