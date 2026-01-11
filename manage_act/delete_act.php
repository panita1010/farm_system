<?php
session_start();
$open_connect = 1;
require('../connect.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../loginform.php");
    exit();
}

if (!isset($_GET['activity_id'])) {
    header("Location: index_act.php");
    exit();
}

$activity_id = $_GET['activity_id'];

try {
    $sql = "DELETE FROM activities WHERE activity_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$activity_id]);

    echo "<script>
            alert('ลบข้อมูลสำเร็จ');
            window.location='index_act.php';
          </script>";
    exit();

} catch (PDOException $e) {
    echo "<script>
            alert('เกิดข้อผิดพลาด');
            window.location='index_act.php';
          </script>";
    exit();
}
?>
