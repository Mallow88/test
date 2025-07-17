<?php

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

      <!-- Staff Workload -->
                <div class="row">
                    <div class="col-lg-8">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <i class="fa fa-users"></i> สถานะการทำงานของเจ้าหน้าที่ไอที
                            </div>
                            <div class="panel-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>ชื่อ-นามสกุล</th>
                                                <th>ตำแหน่ง</th>
                                                <th>งานทั้งหมด</th>
                                                <th>เสร็จแล้ว</th>
                                                <th>กำลังทำ</th>
                                                <th>สถานะ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($staff_tasks as $staff): ?>
                                            <tr>
                                                <td><strong><?php echo htmlspecialchars($staff['staff_name']); ?></strong></td>
                                                <td><?php echo htmlspecialchars($staff['position']); ?></td>
                                                <td><span class="badge badge-primary"><?php echo $staff['total_tasks']; ?></span></td>
                                                <td><span class="badge badge-success"><?php echo $staff['completed']; ?></span></td>
                                                <td><span class="badge badge-warning"><?php echo $staff['in_progress']; ?></span></td>
                                                <td>
                                                    <?php if ($staff['in_progress'] > 0): ?>
                                                        <span class="label label-warning">มีงานค้าง</span>
                                                    <?php else: ?>
                                                        <span class="label label-success">ว่าง</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
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