<?php
session_start();
$open_connect = 1;
require('../connect.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../loginform.php");
    exit();
}

if (!isset($_GET['plant_id'])) {
    header("Location: index_plant.php");
    exit();
}

$plant_id = $_GET['plant_id'];

try {
    // 🔒 ลบข้อมูลพืช
    $stmt = $conn->prepare("DELETE FROM plants WHERE plant_id = ?");
    $stmt->execute([$plant_id]);

    if ($stmt->rowCount() > 0) {
        echo "<script>alert('ลบข้อมูลสำเร็จ'); window.location='index_plant.php';</script>";
    } else {
        echo "<script>alert('ไม่พบข้อมูลพืช'); window.location='index_plant.php';</script>";
    }

} catch (PDOException $e) {
    echo "<script>alert('เกิดข้อผิดพลาด'); window.location='index_plant.php';</script>";
}
?>
