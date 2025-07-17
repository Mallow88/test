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

 <!-- Create Report Tab -->
            <div class="tab-pane" id="create">
                <div class="page-header">
                    <h1><i class="fa fa-plus"></i> สร้างรายงานรายวัน <small>บันทึกการทำงานประจำวัน</small></h1>
                </div>
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <i class="fa fa-edit"></i> ข้อมูลรายงานรายวัน
                            </div>
                            <div class="panel-body">
                                <form id="create-report-form">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="staff-select">เจ้าหน้าที่ <span class="text-danger">*</span></label>
                                                <select class="form-control" id="staff-select" required>
                                                    <option value="">เลือกเจ้าหน้าที่</option>
                                                    <?php foreach ($it_staff as $staff): ?>
                                                    <option value="<?php echo $staff['staff_id']; ?>"><?php echo htmlspecialchars($staff['staff_name']); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="report-date">วันที่รายงาน <span class="text-danger">*</span></label>
                                                <input type="date" class="form-control" id="report-date" required>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="work-summary">สรุปการทำงานวันนี้ <span class="text-danger">*</span></label>
                                        <textarea class="form-control" id="work-summary" rows="4" placeholder="สรุปงานที่ทำในวันนี้โดยย่อ" required></textarea>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="tasks-completed">งานที่เสร็จสิ้น</label>
                                        <textarea class="form-control" id="tasks-completed" rows="4" placeholder="รายละเอียดงานที่ทำเสร็จในวันนี้"></textarea>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="tasks-progress">งานที่กำลังดำเนินการ</label>
                                        <textarea class="form-control" id="tasks-progress" rows="4" placeholder="งานที่ยังทำไม่เสร็จและจะต้องทำต่อ"></textarea>
                                    </div>
                            
                                    <div class="form-group">
                                        <label for="problems">ปัญหาที่พบ</label>
                                        <textarea class="form-control" id="problems" rows="4" placeholder="ปัญหาหรืออุปสรรคที่พบในการทำงาน"></textarea>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="solutions">วิธีการแก้ไข</label>
                                        <textarea class="form-control" id="solutions" rows="4" placeholder="วิธีการแก้ไขปัญหาที่พบ"></textarea>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="next-plan">แผนการทำงานวันถัดไป</label>
                                        <textarea class="form-control" id="next-plan" rows="4" placeholder="แผนการทำงานสำหรับวันถัดไป"></textarea>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="working-hours">ชั่วโมงทำงาน <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control" id="working-hours" step="0.5" min="0" max="24" placeholder="8.0" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="overtime-hours">ชั่วโมงล่วงเวลา</label>
                                                <input type="number" class="form-control" id="overtime-hours" step="0.5" min="0" max="12" placeholder="0.0">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <div class="btn-group" role="group">
                                            <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> บันทึกรายงาน</button>
                                            <button type="button" class="btn btn-info"><i class="fa fa-eye"></i> ดูตัวอย่าง</button>
                                            <button type="button" class="btn btn-warning"><i class="fa fa-floppy-o"></i> บันทึกร่าง</button>
                                            <button type="reset" class="btn btn-default"><i class="fa fa-refresh"></i> ล้างข้อมูล</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="panel panel-info">
                            <div class="panel-heading">
                                <i class="fa fa-info-circle"></i> คำแนะนำการเขียนรายงาน
                            </div>
                            <div class="panel-body">
                                <h5><i class="fa fa-lightbulb-o"></i> เทคนิคการเขียน</h5>
                                <ul class="list-unstyled">
                                    <li><i class="fa fa-check text-success"></i> ระบุงานที่ทำให้ชัดเจน</li>
                                    <li><i class="fa fa-check text-success"></i> บันทึกปัญหาและวิธีแก้ไข</li>
                                    <li><i class="fa fa-check text-success"></i> วางแผนงานวันถัดไป</li>
                                    <li><i class="fa fa-check text-success"></i> ระบุเวลาทำงานที่แท้จริง</li>
                                </ul>
                                
                                <h5><i class="fa fa-clock-o"></i> เวลาทำงาน</h5>
                                <ul class="list-unstyled">
                                    <li><i class="fa fa-info text-info"></i> ปกติ: 8 ชั่วโมง/วัน</li>
                                    <li><i class="fa fa-info text-info"></i> ล่วงเวลา: นับเพิ่มจากเวลาปกติ</li>
                                    <li><i class="fa fa-info text-info"></i> สามารถระบุเป็นทศนิยมได้</li>
                                </ul>
                            </div>
                        </div>
                        <div class="panel panel-success">
                            <div class="panel-heading">
                                <i class="fa fa-tasks"></i> งานที่กำลังทำ
                            </div>
                            <div class="panel-body">
                                <div class="list-group">
                                    <?php foreach (array_slice($active_tasks, 0, 3) as $task): ?>
                                    <a href="#" class="list-group-item">
                                        <h5 class="list-group-item-heading"><?php echo htmlspecialchars($task['task_title']); ?></h5>
                                        <p class="list-group-item-text">
                                            <span class="label label-info"><?php echo $task['priority']; ?></span>
                                            <span class="pull-right"><?php echo $task['progress_percentage']; ?>%</span>
                                        </p>
                                    </a>
                                    <?php endforeach; ?>
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