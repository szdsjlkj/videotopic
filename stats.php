<?php
include 'config.php';

// 获取任务数量（假设存在tasks表）
$task_query = "SELECT COUNT(*) as task_count FROM tasks WHERE DATE(created_at) = CURDATE()";
$project_query = "SELECT COUNT(*) as project_count FROM projects WHERE DATE(created_at) = CURDATE()";

// 添加错误处理
if (!$task_result) {
    die("任务查询错误: " . $conn->error);
}
if (!$project_result) {
    die("项目查询错误: " . $conn->error);
}
$task_result = $conn->query($task_query);
$task_count = $task_result->fetch_assoc()['task_count'];

// 获取项目数量（假设存在projects表）
$project_result = $conn->query($project_query);
$project_count = $project_result->fetch_assoc()['project_count'];

// 返回JSON格式数据
header('Content-Type: application/json');
echo json_encode([
    'tasks' => $task_count,
    'projects' => $project_count
]);
