<?php
// 引入配置文件
include 'config.php';
// 启动会话
session_start();

// 检查用户是否已登录
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// 处理添加任务请求
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_task']) && $_POST['add_task'] === '1') {
    $taskName = $_POST['task_name'];
    $taskDate = $_POST['task_date'];
    $userId = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO daily_tasks (user_id, task_name, task_date, is_completed) VALUES (?, ?, ?, 0)");
    $stmt->bind_param("iss", $userId, $taskName, $taskDate);
    if ($stmt->execute()) {
        // 直接重定向到当前页面
        header("Location: daily_management.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// 处理更新任务请求
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_task']) && $_POST['update_task'] === '1') {
    $taskId = $_POST['task_id'];
    $taskName = $_POST['task_name'];
    $taskDate = $_POST['task_date'];

    // 使用预处理语句更新 daily_tasks 表中的任务
    $stmt = $conn->prepare("UPDATE daily_tasks SET task_name = ?, task_date = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ssii", $taskName, $taskDate, $taskId, $_SESSION['user_id']);
    if ($stmt->execute()) {
        // 任务更新成功，重定向到当前页面
        header("Location: daily_management.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// 处理任务完成状态更新请求
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['complete_task']) && $_POST['complete_task'] === '1') {
    $taskId = $_POST['task_id'];
    $isCompleted = $_POST['is_completed'];

    // 使用预处理语句更新 daily_tasks 表中的任务完成状态
    $stmt = $conn->prepare("UPDATE daily_tasks SET is_completed = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("iii", $isCompleted, $taskId, $_SESSION['user_id']);
    if ($stmt->execute()) {
        // 任务完成状态更新成功，重定向到当前页面
        header("Location: daily_management.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// 处理删除任务请求
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_task']) && $_POST['delete_task'] === '1') {
    $taskId = $_POST['task_id'];

    // 使用预处理语句从 daily_tasks 表中删除任务
    $stmt = $conn->prepare("DELETE FROM daily_tasks WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $taskId, $_SESSION['user_id']);
    if ($stmt->execute()) {
        // 任务删除成功，重定向到当前页面
        header("Location: daily_management.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// 获取当前用户的所有任务
$stmt = $conn->prepare("SELECT * FROM daily_tasks WHERE user_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$tasks = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $tasks[] = [
            'id' => $row['id'],
            'title' => $row['task_name'],
            'start' => $row['task_date'],
            'completed' => $row['is_completed']
        ];
    }
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>拍摄日历</title>
    <!-- 引入 Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <!-- 引入 FullCalendar -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>
    <!-- 在FullCalendar核心库后添加中文语言包 -->
    <!-- 将原来的远程引用 -->
    <!-- <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.15/locales/zh-cn.global.min.js'></script> -->
    
    <!-- 改为本地路径 -->
    <script src='/node_modules/@fullcalendar/core/zh-cn.global.min.js'></script>
    
    <style>
        /* 全局样式 */
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f0f2f5; /* 更改为浅灰色背景 */
            color: #333; /* 主要文字改为深灰色 */
            margin: 0;
            padding: 0;
        }

        /* 导航栏样式 */
        nav {
            background-color: #3467f9; /* 白色背景 */
            color: #fff; /* 文字改为深色 */
            padding: 15px;
            text-align: right;
            justify-content: space-around;
            align-items: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        nav a {
            color: #fff;
            text-decoration: none;
            margin-right: 10px;
            transition: color 0.3s ease;
        }

        nav a:hover {
            color: #fff;
        }

        /* 日历容器样式 */
        #calendar {
            max-width: 1600px;
            margin: 40px auto;
            background-color: #f0f2f5;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* 任务完成样式 */
        .task-completed {
            text-decoration: line-through;
            color: #777;
        }

        /* 任务编辑模态框样式 */
        #task-modal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #27293d;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            z-index: 1000;
            border-radius: 8px;
        }

        #task-modal label {
            display: block;
            color: #fff;
            margin-bottom: 5px;
        }

        #task-modal input {
            width: 100%;
            margin-bottom: 10px;
            padding: 8px;
            border: none;
            border-radius: 4px;
            background-color: #34364d;
            color: #fff;
        }

        #task-modal button {
            margin-right: 10px;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            background-color: #00bfff;
            color: #fff;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        #task-modal button:hover {
            background-color: #009acd;
        }

        /* 去除任务背景 */
        .fc-event {
            background-color: transparent;
            border: none;
            color: inherit;
            white-space: normal;
        }

        /* 复选框样式 */
        .task-checkbox {
            margin-right: 5px;
        }

        /* 新增规则，去除任务标题的下划线 */
        .fc-event .task-completed,
        .fc-event span {
            text-decoration: none;
        }

        /* 添加样式规则，将输入内容文字颜色改为黑色 */
        #task-modal input,
        #task-modal textarea {
            color: #fff;
        }

        /* 新增或修改 .fc-h-event .fc-event-main 的颜色为黑色 */
        .fc-h-event .fc-event-main {
            color: #000000;
        }

        /* 动画效果 */
        .fade-in {
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        /* 删除按钮样式 */
        .delete-button {
            background-color: #3788d8;
            border: none;
            border-radius: 50%;
            color: white;
            cursor: pointer;
            font-size: 12px;
            height: 20px;
            width: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-left: 5px;
        }

        .delete-button:hover {
            background-color: #2a6bbf;
        }
    </style>
</head>

<body>
    <!-- 导航菜单 -->
    <nav>
        <h1 style="float: left; margin: 0; padding: 0; color: #fff; font-size: 24px; line-height: 1;">拍摄日历</h1>
        <a href="topics.php">选题管理</a>
        <a href="daily_management.php">拍摄日历</a>
        <a href="logout.php">退出登录</a>
    </nav>
    <div id='calendar' class="fade-in"></div>

    <!-- 任务编辑模态框 -->
    <div id="task-modal">
        <form id="task-form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <input type="hidden" id="task-id" name="task_id">
            <label for="task-name">任务名称:</label>
            <input type="text" id="task-name" name="task_name" required>
            <label for="task-date">任务日期:</label>
            <input type="date" id="task-date" name="task_date" required>
            <input type="hidden" id="add-task" name="add_task" value="0">
            <input type="hidden" id="update-task" name="update_task" value="0">
            <input type="hidden" id="delete-task" name="delete_task" value="0">
            <button type="submit">保存</button>
            <button type="button" onclick="closeModal()">取消</button>
        </form>
    </div>

    <script>
        // 将 closeModal 函数定义在全局作用域
        function closeModal() {
            document.getElementById('task-modal').style.display = 'none';
        }

        function deleteTask(taskId) {
            if (confirm('确定要删除此任务吗？')) {
                fetch('daily_management.php', {
                    method: 'POST',
                    body: new URLSearchParams({
                        'delete_task': 1,
                        'task_id': taskId
                    })
                })
                .then(response => {
                    if (response.ok) {
                        // 重定向到当前页面
                        window.location.href = 'daily_management.php';
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                locale: 'zh-cn', // 设置中文语言
                initialView: 'multiMonthYear',
                views: {
                    multiMonthYear: {
                        type: 'multiMonth',
                        duration: { months: 12 },  // 显示12个月
                        multiMonthMaxColumns: 1,    // 设置1列显示
                        fixedWeekCount: false,
                        monthMode: true,
                        titleFormat: { year: 'numeric' } // 自动显示中文年份
                    }
                },
                events: <?php echo json_encode($tasks); ?>,
                eventContent: function (info) {
                    var completedClass = info.event.extendedProps.completed ? 'task-completed' : '';
                    var checkbox = '<input type="checkbox" class="task-checkbox" ' + (info.event.extendedProps.completed ? 'checked' : '') + '>';
                    var deleteButton = '<button class="delete-button" onclick="deleteTask(' + info.event.id + ')">-</button>';
                    return {
                        html: checkbox + '<span class="' + completedClass + '">' + info.event.title + '</span>' + deleteButton
                    };
                },
                dateClick: function (info) {
                    // 双击日期添加任务
                    document.getElementById('task-id').value = '';
                    document.getElementById('task-name').value = '';
                    document.getElementById('task-date').value = info.dateStr;
                    document.getElementById('add-task').value = 1;
                    document.getElementById('update-task').value = 0;
                    document.getElementById('task-modal').style.display = 'block';
                },
                eventClick: function (info) {
                    // 点击任务编辑任务
                    document.getElementById('task-id').value = info.event.id;
                    document.getElementById('task-name').value = info.event.title;
                    document.getElementById('task-date').value = info.event.startStr;
                    document.getElementById('add-task').value = 0;
                    document.getElementById('update-task').value = 1;
                    document.getElementById('delete-task').value = 0;
                    document.getElementById('task-modal').style.display = 'block';
                },
                eventDidMount: function (info) {
                    var checkbox = info.el.querySelector('.task-checkbox');
                    checkbox.addEventListener('change', function () {
                        var isCompleted = this.checked ? 1 : 0;
                        if (confirm('确定要更新此任务的完成状态吗？')) {
                            fetch('daily_management.php', {
                                method: 'POST',
                                body: new URLSearchParams({
                                    'complete_task': 1,
                                    'task_id': info.event.id,
                                    'is_completed': isCompleted
                                })
                            })
                            .then(response => {
                                if (response.ok) {
                                    // 重新获取并渲染事件
                                    calendar.refetchEvents();
                                }
                            })
                            .catch(error => console.error('Error:', error));
                        }
                    });
                }
            });
            calendar.render();

            // 处理表单提交事件
            document.getElementById('task-form').addEventListener('submit', function (event) {
                event.preventDefault();
                var formData = new FormData(this);
                fetch('daily_management.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (response.ok) {
                        // 直接重定向到当前页面
                        window.location.href = 'daily_management.php';
                    }
                })
                .catch(error => console.error('Error:', error));
            });
        });
    </script>
</body>

</html>
