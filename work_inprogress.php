<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}
// ดึงข้อมูลสำหรับแดชบอร์ด
$stats = getDashboardStats($pdo);
$staff_tasks = getTasksByStaff($pdo);
$active_tasks = getActiveTasks($pdo);
$it_staff = getITStaff($pdo);
$recent_reports = getDailyReports($pdo, 5);
$pending_requests = getServiceRequests($pdo, 'รอการอนุมัติ');

// ตรวจสอบการล็อกอิน
checkLogin();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบรายงานการทำงานแผนกไอที</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
       <!-- Navigation -->
     <?php include 'Navigation.php'; ?>

  <!-- Tasks Tab -->
            <div class="tab-pane" id="tasks">
                <div class="page-header">
                    <h1><i class="fa fa-tasks"></i> งานที่กำลังดำเนินการ <small>ติดตามความคืบหน้าและปัญหา</small></h1>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <i class="fa fa-list"></i> รายการงานทั้งหมด
                                <div class="pull-right">
                                    <button class="btn btn-success btn-sm"><i class="fa fa-plus"></i> เพิ่มงานใหม่</button>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>หัวข้องาน</th>
                                                <th>ผู้รับผิดชอบ</th>
                                                <th>ความสำคัญ</th>
                                                <th>สถานะ</th>
                                                <th>ความคืบหน้า</th>
                                                <th>กำหนดเสร็จ</th>
                                                <th>การดำเนินการ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($active_tasks as $task): ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($task['task_title']); ?></strong>
                                                    <br><small class="text-muted"><?php echo htmlspecialchars(substr($task['task_description'], 0, 50)) . '...'; ?></small>
                                                </td>
                                                <td><?php echo htmlspecialchars($task['staff_name']); ?></td>
                                                <td>
                                                    <?php
                                                    $priority_class = '';
                                                    switch($task['priority']) {
                                                        case 'สูงมาก': $priority_class = 'label-danger'; break;
                                                        case 'สูง': $priority_class = 'label-warning'; break;
                                                        case 'ปานกลาง': $priority_class = 'label-info'; break;
                                                        case 'ต่ำ': $priority_class = 'label-default'; break;
                                                    }
                                                    ?>
                                                    <span class="label <?php echo $priority_class; ?>"><?php echo $task['priority']; ?></span>
                                                </td>
                                                <td>
                                                    <?php
                                                    $status_class = '';
                                                    switch($task['status']) {
                                                        case 'เสร็จสิ้น': $status_class = 'label-success'; break;
                                                        case 'กำลังดำเนินการ': $status_class = 'label-primary'; break;
                                                        case 'รอดำเนินการ': $status_class = 'label-warning'; break;
                                                        case 'มีปัญหา': $status_class = 'label-danger'; break;
                                                    }
                                                    ?>
                                                    <span class="label <?php echo $status_class; ?>"><?php echo $task['status']; ?></span>
                                                </td>
                                                <td>
                                                    <div class="progress" style="margin-bottom: 0;">
                                                        <div class="progress-bar progress-bar-info" style="width: <?php echo $task['progress_percentage']; ?>%">
                                                            <?php echo $task['progress_percentage']; ?>%
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><?php echo $task['due_date'] ? date('d/m/Y', strtotime($task['due_date'])) : '-'; ?></td>
                                                <td>
                                                    <button class="btn btn-xs btn-info" title="ดู"><i class="fa fa-eye"></i></button>
                                                    <button class="btn btn-xs btn-warning" title="แก้ไข"><i class="fa fa-edit"></i></button>
                                                    <button class="btn btn-xs btn-success" title="อัพเดท"><i class="fa fa-refresh"></i></button>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


    <!-- Scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="script.js"></script>
</body>
</html>