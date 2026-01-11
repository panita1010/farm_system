<?php
session_start();
$open_connect = 1;
require('../connect.php'); 

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../loginform.php");
    exit();
}

if (!isset($_GET['user_id'])) {
    header("Location: index_user.php");
    exit();
}

$user_id = $_GET['user_id'];

if ($user_id == $_SESSION['user_id']) {
    echo "<script>
        alert('ไม่สามารถลบบัญชีของตัวเองได้');
        window.location='index_user.php';
    </script>";
    exit();
}

$stmt = $conn->prepare("DELETE FROM users WHERE user_id = :user_id");
$result = $stmt->execute([
    'user_id' => $user_id
]);

if ($result) {
    echo "<script>
        alert('ลบข้อมูลสำเร็จ');
        window.location='index_user.php';
    </script>";
} else {
    echo "<script>
        alert('เกิดข้อผิดพลาด');
        window.location='index_user.php';
    </script>";
}
