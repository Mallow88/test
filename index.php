<?php
session_start();
require_once 'config/database.php';

// ตรวจสอบการล็อกอิน
checkLogin();

// ดึงข้อมูลสำหรับแดชบอร์ด
$stats = getDashboardStats($pdo);
$staff_tasks = getTasksByStaff($pdo);
$active_tasks = getActiveTasks($pdo);
$it_staff = getITStaff($pdo);
$recent_reports = getDailyReports($pdo, 5);
$pending_requests = getServiceRequests($pdo, 'รอการอนุมัติ');
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

    <!-- Main Content -->
    <div class="container-fluid main-content">
        <div class="tab-content">
            <!-- Dashboard Tab -->
            <div class="tab-pane active" id="dashboard">
                <div class="page-header">
                    <h1><i class="fa fa-dashboard"></i> แดชบอร์ดแผนกไอที <small>ภาพรวมการทำงานและสถานะงาน</small></h1>
                </div>
                
                <!-- Stats Cards -->
                <div class="row">
                    <div class="col-lg-3 col-md-6">
                        <div class="panel panel-primary stats-card">
                            <div class="panel-heading">
                                <div class="row">
                                    <div class="col-xs-3">
                                        <i class="fa fa-tasks fa-5x"></i>
                                    </div>
                                    <div class="col-xs-9 text-right">
                                        <div class="huge"><?php echo $stats['total_tasks']; ?></div>
                                        <div>งานทั้งหมด</div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer">
                                <span class="pull-left">ดูรายละเอียด</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="panel panel-green stats-card">
                            <div class="panel-heading">
                                <div class="row">
                                    <div class="col-xs-3">
                                        <i class="fa fa-check fa-5x"></i>
                                    </div>
                                    <div class="col-xs-9 text-right">
                                        <div class="huge"><?php echo $stats['completed_tasks']; ?></div>
                                        <div>เสร็จสิ้น</div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer">
                                <span class="pull-left">ดูรายละเอียด</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="panel panel-yellow stats-card">
                            <div class="panel-heading">
                                <div class="row">
                                    <div class="col-xs-3">
                                        <i class="fa fa-clock-o fa-5x"></i>
                                    </div>
                                    <div class="col-xs-9 text-right">
                                        <div class="huge"><?php echo $stats['in_progress_tasks']; ?></div>
                                        <div>กำลังดำเนินการ</div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer">
                                <span class="pull-left">ดูรายละเอียด</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="panel panel-red stats-card">
                            <div class="panel-heading">
                                <div class="row">
                                    <div class="col-xs-3">
                                        <i class="fa fa-exclamation-triangle fa-5x"></i>
                                    </div>
                                    <div class="col-xs-9 text-right">
                                        <div class="huge"><?php echo $stats['problem_tasks']; ?></div>
                                        <div>มีปัญหา</div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer">
                                <span class="pull-left">ดูรายละเอียด</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>
                </div>

                  <!-- Staff Workload -->
                <div class="row">
                 <?php include 'Work_status.php'; ?>
                </div>

                    <div class="col-lg-4">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <i class="fa fa-bell"></i> การแจ้งเตือนด่วน
                            </div>
                            <div class="panel-body">
                                <div class="list-group">
                                    <a href="#" class="list-group-item">
                                        <i class="fa fa-exclamation-triangle fa-fw text-danger"></i> คำขอรอการอนุมัติ: <?php echo count($pending_requests); ?> รายการ
                                        <span class="pull-right text-muted small"><em>ล่าสุด</em></span>
                                    </a>
                                    <a href="#" class="list-group-item">
                                        <i class="fa fa-clock-o fa-fw text-warning"></i> ระบบสินค้าคลังใกล้ครบกำหนด
                                        <span class="pull-right text-muted small"><em>5 ชั่วโมงที่แล้ว</em></span>
                                    </a>
                                    <a href="#" class="list-group-item">
                                        <i class="fa fa-info-circle fa-fw text-info"></i> มีการอัพเดทระบบรักษาความปลอดภัย
                                        <span class="pull-right text-muted small"><em>1 วันที่แล้ว</em></span>
                                    </a>
                                </div>
                                <div class="text-right">
                                    <a href="requests/index.php">ดูคำขอทั้งหมด <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Reports Tab -->
            <div class="tab-pane" id="reports">
                <div class="page-header">
                    <h1><i class="fa fa-file-text"></i> รายงานรายวัน <small>รายงานการทำงานของเจ้าหน้าที่</small></h1>
                </div>
                
                <!-- Search and Filter -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <form class="form-inline">
                                    <div class="form-group">
                                        <label for="search">ค้นหา:</label>
                                        <input type="text" class="form-control" id="search" placeholder="ค้นหารายงาน...">
                                    </div>
                                    <div class="form-group">
                                        <label for="staff-filter">เจ้าหน้าที่:</label>
                                        <select class="form-control" id="staff-filter">
                                            <option value="<?php echo $_SESSION['staff_id']; ?>" selected><?php echo htmlspecialchars($_SESSION['full_name']); ?></option>
                                            <?php foreach ($it_staff as $staff): ?>
                                            <?php if ($staff['staff_id'] != $_SESSION['staff_id']): ?>
                                            <option value="<?php echo $staff['staff_id']; ?>"><?php echo htmlspecialchars($staff['staff_name']); ?></option>
                                            <?php endif; ?>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="date-filter">วันที่:</label>
                                        <input type="date" class="form-control" id="date-filter">
                                    </div>
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> ค้นหา</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reports Table -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <i class="fa fa-table"></i> รายการรายงานรายวัน
                            </div>
                            <div class="panel-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>วันที่</th>
                                                <th>เจ้าหน้าที่</th>
                                                <th>สรุปงาน</th>
                                                <th>ชั่วโมงทำงาน</th>
                                                <th>การดำเนินการ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recent_reports as $report): ?>
                                            <tr>
                                                <td><?php echo date('d/m/Y', strtotime($report['report_date'])); ?></td>
                                                <td><?php echo htmlspecialchars($report['staff_name']); ?></td>
                                                <td><?php echo htmlspecialchars(substr($report['work_summary'], 0, 50)) . '...'; ?></td>
                                                <td><?php echo $report['working_hours']; ?> ชั่วโมง</td>
                                                <td>
                                                    <a href="reports/view_report.php?id=<?php echo $report['report_id']; ?>" class="btn btn-xs btn-info" target="_blank">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <button class="btn btn-xs btn-warning"><i class="fa fa-edit"></i></button>
                                                    <button class="btn btn-xs btn-success" onclick="window.open('reports/view_report.php?id=<?php echo $report['report_id']; ?>', '_blank')">
                                                        <i class="fa fa-print"></i>
                                                    </button>
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

           
        </div>
    </div>
    <!-- Scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="script.js"></script>
</body>
</html>