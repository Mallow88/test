<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

// ดึงข้อมูลคำขอทั้งหมด
$stmt = $pdo->query("
    SELECT r.*, d.department_name, s.staff_name as approver_name
    FROM service_requests r 
    LEFT JOIN departments d ON r.department_id = d.department_id
    LEFT JOIN it_staff s ON r.approved_by = s.staff_id
    ORDER BY r.created_at DESC
");
$requests = $stmt->fetchAll();

// ดึงข้อมูลแผนก
$departments = $pdo->query("SELECT * FROM departments ORDER BY department_name")->fetchAll();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>คำขอบริการจากแผนกอื่น</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <nav class="navbar navbar-inverse navbar-fixed-top custom-navbar">
        <div class="container-fluid">
            <div class="navbar-header">
                <a class="navbar-brand" href="../index.php">
                    <i class="fa fa-desktop"></i> ระบบรายงานการทำงานแผนกไอที
                </a>
            </div>
            <div class="navbar-right" style="margin: 15px;">
                <a href="../logout.php" class="btn btn-danger btn-sm">
                    <i class="fa fa-sign-out"></i> ออกจากระบบ
                </a>
            </div>
        </div>
    </nav>

    <div class="container-fluid main-content">
        <div class="page-header">
            <h1><i class="fa fa-inbox"></i> คำขอบริการจากแผนกอื่น <small>จัดการคำขอและการอนุมัติ</small></h1>
        </div>

        <!-- สถิติคำขอ -->
        <div class="row">
            <div class="col-lg-3 col-md-6">
                <div class="panel panel-primary stats-card">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-xs-3">
                                <i class="fa fa-inbox fa-5x"></i>
                            </div>
                            <div class="col-xs-9 text-right">
                                <div class="huge"><?php echo count($requests); ?></div>
                                <div>คำขอทั้งหมด</div>
                            </div>
                        </div>
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
                                <div class="huge"><?php echo count(array_filter($requests, function($r) { return $r['status'] == 'รอการอนุมัติ'; })); ?></div>
                                <div>รอการอนุมัติ</div>
                            </div>
                        </div>
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
                                <div class="huge"><?php echo count(array_filter($requests, function($r) { return $r['status'] == 'อนุมัติแล้ว'; })); ?></div>
                                <div>อนุมัติแล้ว</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="panel panel-red stats-card">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-xs-3">
                                <i class="fa fa-times fa-5x"></i>
                            </div>
                            <div class="col-xs-9 text-right">
                                <div class="huge"><?php echo count(array_filter($requests, function($r) { return $r['status'] == 'ไม่อนุมัติ'; })); ?></div>
                                <div>ไม่อนุมัติ</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ฟอร์มสร้างคำขอใหม่ -->
        <div class="row">
            <div class="col-md-4">
                <div class="panel panel-success">
                    <div class="panel-heading">
                        <i class="fa fa-plus"></i> สร้างคำขอใหม่
                    </div>
                    <div class="panel-body">
                        <form id="newRequestForm">
                            <div class="form-group">
                                <label>แผนกที่ขอ</label>
                                <select class="form-control" name="department_id" required>
                                    <option value="">เลือกแผนก</option>
                                    <?php foreach ($departments as $dept): ?>
                                    <option value="<?php echo $dept['department_id']; ?>"><?php echo htmlspecialchars($dept['department_name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>ชื่อผู้ขอ</label>
                                <input type="text" class="form-control" name="requester_name" required>
                            </div>
                            <div class="form-group">
                                <label>ประเภทงาน</label>
                                <select class="form-control" name="request_type" required>
                                    <option value="">เลือกประเภท</option>
                                    <option value="พัฒนาระบบ">พัฒนาระบบ</option>
                                    <option value="แก้ไขระบบ">แก้ไขระบบ</option>
                                    <option value="ติดตั้งอุปกรณ์">ติดตั้งอุปกรณ์</option>
                                    <option value="ซ่อมแซม">ซ่อมแซม</option>
                                    <option value="ฝึกอบรม">ฝึกอบรม</option>
                                    <option value="อื่นๆ">อื่นๆ</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>หัวข้อคำขอ</label>
                                <input type="text" class="form-control" name="title" required>
                            </div>
                            <div class="form-group">
                                <label>รายละเอียด</label>
                                <textarea class="form-control" name="description" rows="4" required></textarea>
                            </div>
                            <div class="form-group">
                                <label>ความเร่งด่วน</label>
                                <select class="form-control" name="priority" required>
                                    <option value="ต่ำ">ต่ำ</option>
                                    <option value="ปานกลาง" selected>ปานกลาง</option>
                                    <option value="สูง">สูง</option>
                                    <option value="สูงมาก">สูงมาก</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success btn-block">
                                <i class="fa fa-paper-plane"></i> ส่งคำขอ
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- รายการคำขอ -->
            <div class="col-md-8">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <i class="fa fa-list"></i> รายการคำขอทั้งหมด
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>วันที่</th>
                                        <th>แผนก</th>
                                        <th>ผู้ขอ</th>
                                        <th>หัวข้อ</th>
                                        <th>ประเภท</th>
                                        <th>ความเร่งด่วน</th>
                                        <th>สถานะ</th>
                                        <th>การดำเนินการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($requests as $request): ?>
                                    <tr>
                                        <td><?php echo date('d/m/Y', strtotime($request['created_at'])); ?></td>
                                        <td><?php echo htmlspecialchars($request['department_name']); ?></td>
                                        <td><?php echo htmlspecialchars($request['requester_name']); ?></td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($request['title']); ?></strong>
                                            <br><small class="text-muted"><?php echo htmlspecialchars(substr($request['description'], 0, 50)) . '...'; ?></small>
                                        </td>
                                        <td><span class="label label-info"><?php echo $request['request_type']; ?></span></td>
                                        <td>
                                            <?php
                                            $priority_class = '';
                                            switch($request['priority']) {
                                                case 'สูงมาก': $priority_class = 'label-danger'; break;
                                                case 'สูง': $priority_class = 'label-warning'; break;
                                                case 'ปานกลาง': $priority_class = 'label-info'; break;
                                                case 'ต่ำ': $priority_class = 'label-default'; break;
                                            }
                                            ?>
                                            <span class="label <?php echo $priority_class; ?>"><?php echo $request['priority']; ?></span>
                                        </td>
                                        <td>
                                            <?php
                                            $status_class = '';
                                            switch($request['status']) {
                                                case 'รอการอนุมัติ': $status_class = 'label-warning'; break;
                                                case 'อนุมัติแล้ว': $status_class = 'label-success'; break;
                                                case 'ไม่อนุมัติ': $status_class = 'label-danger'; break;
                                                case 'เสร็จสิ้น': $status_class = 'label-primary'; break;
                                            }
                                            ?>
                                            <span class="label <?php echo $status_class; ?>"><?php echo $request['status']; ?></span>
                                        </td>
                                        <td>
                                            <button class="btn btn-xs btn-info" onclick="viewRequest(<?php echo $request['request_id']; ?>)">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                            <?php if ($request['status'] == 'รอการอนุมัติ'): ?>
                                            <button class="btn btn-xs btn-success" onclick="approveRequest(<?php echo $request['request_id']; ?>)">
                                                <i class="fa fa-check"></i>
                                            </button>
                                            <button class="btn btn-xs btn-danger" onclick="rejectRequest(<?php echo $request['request_id']; ?>)">
                                                <i class="fa fa-times"></i>
                                            </button>
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
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script>
        $('#newRequestForm').on('submit', function(e) {
            e.preventDefault();
            
            $.ajax({
                url: 'create_request.php',
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {
                        alert('สร้างคำขอเรียบร้อยแล้ว');
                        location.reload();
                    } else {
                        alert('เกิดข้อผิดพลาด: ' + response.message);
                    }
                },
                error: function() {
                    alert('เกิดข้อผิดพลาดในการส่งข้อมูล');
                }
            });
        });

        function viewRequest(id) {
            window.open('view_request.php?id=' + id, '_blank');
        }

        function approveRequest(id) {
            if (confirm('คุณต้องการอนุมัติคำขอนี้หรือไม่?')) {
                $.post('update_request.php', {
                    request_id: id,
                    action: 'approve'
                }, function(response) {
                    if (response.success) {
                        alert('อนุมัติคำขอเรียบร้อยแล้ว');
                        location.reload();
                    }
                });
            }
        }

        function rejectRequest(id) {
            var reason = prompt('กรุณาระบุเหตุผลที่ไม่อนุมัติ:');
            if (reason) {
                $.post('update_request.php', {
                    request_id: id,
                    action: 'reject',
                    reason: reason
                }, function(response) {
                    if (response.success) {
                        alert('ไม่อนุมัติคำขอเรียบร้อยแล้ว');
                        location.reload();
                    }
                });
            }
        }
    </script>
</body>
</html>