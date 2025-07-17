<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'ไม่ได้เข้าสู่ระบบ']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $request_id = $_POST['request_id'];
        $action = $_POST['action'];
        
        if ($action == 'approve') {
            $stmt = $pdo->prepare("
                UPDATE service_requests 
                SET status = 'อนุมัติแล้ว', 
                    approved_by = ?, 
                    approved_at = NOW() 
                WHERE request_id = ?
            ");
            $stmt->execute([$_SESSION['staff_id'], $request_id]);
        } elseif ($action == 'reject') {
            $reason = $_POST['reason'] ?? '';
            $stmt = $pdo->prepare("
                UPDATE service_requests 
                SET status = 'ไม่อนุมัติ', 
                    approved_by = ?, 
                    approved_at = NOW(),
                    rejection_reason = ?
                WHERE request_id = ?
            ");
            $stmt->execute([$_SESSION['staff_id'], $reason, $request_id]);
        }
        
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>