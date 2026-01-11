<?php
session_start();
require('../connect.php'); // ต้องเป็น PDO -> $conn

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../loginform.php");
    exit();
}

if (!isset($_GET['farm_id'])) {
    header("Location: index_plot.php");
    exit();
}

$farm_id = $_GET['farm_id'];

try {
    $sql = "DELETE FROM farms WHERE farm_id = :farm_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':farm_id', $farm_id, PDO::PARAM_INT);
    $stmt->execute();

    echo "<script>
        alert('ลบข้อมูลสำเร็จ');
        window.location='index_plot.php';
    </script>";
} catch (PDOException $e) {
    echo "<script>
        alert('เกิดข้อผิดพลาด');
        window.location='index_plot.php';
    </script>";
}
