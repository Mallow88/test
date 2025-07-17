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

 <!-- IT Staff Tab -->
            <div class="tab-pane" id="staff">
                <div class="page-header">
                    <h1><i class="fa fa-users"></i> เจ้าหน้าที่แผนกไอที <small>ข้อมูลและสถานะการทำงาน</small></h1>
                </div>
                
                <div class="row">
                    <?php foreach ($it_staff as $staff): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="panel panel-default staff-card">
                            <div class="panel-body text-center">
                                <div class="staff-avatar">
                                    <i class="fa fa-user-circle fa-5x text-primary"></i>
                                </div>
                                <h4><?php echo htmlspecialchars($staff['staff_name']); ?></h4>
                                <p class="text-muted"><?php echo htmlspecialchars($staff['position']); ?></p>
                                <div class="staff-info">
                                    <p><i class="fa fa-envelope"></i> <?php echo htmlspecialchars($staff['email']); ?></p>
                                    <p><i class="fa fa-phone"></i> <?php echo htmlspecialchars($staff['phone']); ?></p>
                                    <p><i class="fa fa-calendar"></i> เริ่มงาน: <?php echo date('d/m/Y', strtotime($staff['hire_date'])); ?></p>
                                </div>
                                <div class="staff-status">
                                    <?php
                                    $status_class = '';
                                    switch($staff['status']) {
                                        case 'ปฏิบัติงาน': $status_class = 'label-success'; break;
                                        case 'ลาป่วย': $status_class = 'label-danger'; break;
                                        case 'ลาพักร้อน': $status_class = 'label-warning'; break;
                                        case 'ไม่อยู่': $status_class = 'label-default'; break;
                                    }
                                    ?>
                                    <span class="label <?php echo $status_class; ?>"><?php echo $staff['status']; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

    <!-- Scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="script.js"></script>
</body>
</html>