<?php
// การตั้งค่าฐานข้อมูล MySQL สำหรับ XAMPP
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "it_work_system";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// ตรวจสอบการล็อกอิน
function checkLogin() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}


// ฟังก์ชันสำหรับดึงข้อมูลเจ้าหน้าที่ไอที
function getITStaff($pdo) {
    $stmt = $pdo->query("SELECT * FROM it_staff ORDER BY staff_id");
    return $stmt->fetchAll();
}

// ฟังก์ชันสำหรับดึงข้อมูลงานที่กำลังดำเนินการ
function getActiveTasks($pdo) {
    $stmt = $pdo->query("
        SELECT t.*, s.staff_name, s.position 
        FROM tasks t 
        LEFT JOIN it_staff s ON t.assigned_to = s.staff_id 
        WHERE t.status IN ('กำลังดำเนินการ', 'รอดำเนินการ') 
        ORDER BY t.priority DESC, t.created_at DESC
    ");
    return $stmt->fetchAll();
}

// ฟังก์ชันสำหรับดึงสถิติแดชบอร์ด
function getDashboardStats($pdo) {
    $stats = [];
    
    // งานทั้งหมด
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM tasks");
    $stats['total_tasks'] = $stmt->fetch()['total'];
    
    // งานที่เสร็จแล้ว
    $stmt = $pdo->query("SELECT COUNT(*) as completed FROM tasks WHERE status = 'เสร็จสิ้น'");
    $stats['completed_tasks'] = $stmt->fetch()['completed'];
    
    // งานที่กำลังดำเนินการ
    $stmt = $pdo->query("SELECT COUNT(*) as in_progress FROM tasks WHERE status = 'กำลังดำเนินการ'");
    $stats['in_progress_tasks'] = $stmt->fetch()['in_progress'];
    
    // งานที่มีปัญหา
    $stmt = $pdo->query("SELECT COUNT(*) as problem FROM tasks WHERE status = 'มีปัญหา'");
    $stats['problem_tasks'] = $stmt->fetch()['problem'];
    
    return $stats;
}

// ฟังก์ชันสำหรับดึงงานตามเจ้าหน้าที่
function getTasksByStaff($pdo) {
    $stmt = $pdo->query("
        SELECT s.staff_name, s.position, 
               COUNT(t.task_id) as total_tasks,
               SUM(CASE WHEN t.status = 'เสร็จสิ้น' THEN 1 ELSE 0 END) as completed,
               SUM(CASE WHEN t.status = 'กำลังดำเนินการ' THEN 1 ELSE 0 END) as in_progress
        FROM it_staff s 
        LEFT JOIN tasks t ON s.staff_id = t.assigned_to 
        GROUP BY s.staff_id
        ORDER BY s.staff_id
    ");
    return $stmt->fetchAll();
}

// ฟังก์ชันสำหรับดึงรายงานรายวัน
function getDailyReports($pdo, $limit = null) {
    $sql = "
        SELECT dr.*, s.staff_name, s.position 
        FROM daily_reports dr 
        LEFT JOIN it_staff s ON dr.staff_id = s.staff_id 
        ORDER BY dr.report_date DESC, dr.created_at DESC
    ";
    if ($limit) {
        $sql .= " LIMIT " . intval($limit);
    }
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll();
}

// ฟังก์ชันสำหรับดึงคำขอบริการ
function getServiceRequests($pdo, $status = null) {
    $sql = "
        SELECT r.*, d.department_name, s.staff_name as approver_name
        FROM service_requests r 
        LEFT JOIN departments d ON r.department_id = d.department_id
        LEFT JOIN it_staff s ON r.approved_by = s.staff_id
    ";
    if ($status) {
        $sql .= " WHERE r.status = '" . $status . "'";
    }
    $sql .= " ORDER BY r.created_at DESC";
    
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll();
}
?>