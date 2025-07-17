<?php
session_start();
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    try {
        // ตรวจสอบข้อมูลผู้ใช้
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND password = MD5(?)");
        $stmt->execute([$username, $password]);
        $user = $stmt->fetch();
        
        if ($user) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['staff_id'] = $user['staff_id'];
            
            // อัพเดทเวลาล็อกอินล่าสุด
            $stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE user_id = ?");
            $stmt->execute([$user['user_id']]);
            
            header('Location: index.php');
            exit;
        } else {
            header('Location: login.php?error=1');
            exit;
        }
    } catch (PDOException $e) {
        header('Location: login.php?error=1');
        exit;
    }
} else {
    header('Location: login.php');
    exit;
}
?>