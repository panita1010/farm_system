<?php
session_start();
$open_connect = 1;
require('../connect.php'); 

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../loginform.php");
    exit();
}

if (!isset($_GET['cycle_id'])) {
    header("Location: index_admin.php");
    exit();
}

$cycle_id = $_GET['cycle_id'];

try {
    $stmt = $conn->prepare("DELETE FROM cycles WHERE cycle_id = :cycle_id");
    $stmt->execute([
        'cycle_id' => $cycle_id
    ]);

    echo "<script>
        alert('ลบข้อมูลสำเร็จ');
        window.location='index_admin.php';
    </script>";
} catch (PDOException $e) {
    echo "<script>
        alert('เกิดข้อผิดพลาดในการลบข้อมูล');
        window.location='index_admin.php';
    </script>";
}
?>
