<?php
session_start();
$open_connect = 1;
require('../connect.php'); // $conn = PDO

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../loginform.php");
    exit();
}

if (!isset($_GET['farm_id'])) {
    header("Location: index_plot.php");
    exit();
}

$farm_id = $_GET['farm_id'];

/* ===== ดึงข้อมูลแปลง ===== */
$stmt = $conn->prepare("SELECT * FROM farms WHERE farm_id = :farm_id");
$stmt->execute(['farm_id' => $farm_id]);
$farm = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$farm) {
    echo "<script>alert('ไม่พบข้อมูลแปลงพืช'); window.location='index_plot.php';</script>";
    exit();
}

/* ===== เมื่อกดบันทึก ===== */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $farm_name     = $_POST['farm_name'];
    $area          = $_POST['area'];
    $soil_type     = $_POST['soil_type'];
    $water_system  = $_POST['water_system'];
    $loca          = $_POST['loca'];
    $des_farm      = $_POST['des_farm'];

    /* ตรวจชื่อซ้ำ */
    $check = $conn->prepare("
        SELECT COUNT(*) 
        FROM farms 
        WHERE farm_name = :farm_name 
          AND farm_id != :farm_id
    ");
    $check->execute([
        'farm_name' => $farm_name,
        'farm_id'   => $farm_id
    ]);

    if ($check->fetchColumn() > 0) {
        echo "<script>alert('ชื่อแปลงพืชนี้มีอยู่แล้ว กรุณาใช้ชื่ออื่น');</script>";
    } else {

        $update = $conn->prepare("
            UPDATE farms SET
                farm_name    = :farm_name,
                area         = :area,
                soil_type    = :soil_type,
                water_system = :water_system,
                loca         = :loca,
                des_farm     = :des_farm
            WHERE farm_id = :farm_id
        ");

        $success = $update->execute([
            'farm_name'    => $farm_name,
            'area'         => $area,
            'soil_type'    => $soil_type,
            'water_system' => $water_system,
            'loca'         => $loca,
            'des_farm'     => $des_farm,
            'farm_id'      => $farm_id
        ]);

        if ($success) {
            echo "<script>alert('อัพเดทข้อมูลสำเร็จ'); window.location='index_plot.php';</script>";
            exit();
        } else {
            echo "<script>alert('เกิดข้อผิดพลาด');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>แก้ไขแปลงพืช</title>
<link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="../mystyle.css">
</head>

<body>
<?php include("layout.php") ?>

<div class="content">
    <div class="card-box">
        <div class="container mt-3">

        <h2 class="text-center">แก้ไขแปลงพืช</h2>
        <hr>

        <form method="POST">

            <div class="mb-3">
                <label>ชื่อแปลงพืช</label>
                <input type="text" name="farm_name" class="form-control"
                       value="<?= htmlspecialchars($farm['farm_name']) ?>" required>
            </div>

            <div class="mb-3">
                <label>พื้นที่</label>
                <input type="text" name="area" class="form-control"
                       value="<?= htmlspecialchars($farm['area']) ?>" required>
            </div>

            <div class="mb-3">
                <label>ลักษณะดิน</label>
                <input type="text" name="soil_type" class="form-control"
                       value="<?= htmlspecialchars($farm['soil_type']) ?>" required>
            </div>

            <div class="mb-3">
                <label>ระบบน้ำที่ใช้</label>
                <select name="water_system" class="form-control" required>
                    <option value="สปริงเกอร์" <?= $farm['water_system']=='สปริงเกอร์'?'selected':'' ?>>สปริงเกอร์</option>
                    <option value="ระบบน้ำหยด" <?= $farm['water_system']=='ระบบน้ำหยด'?'selected':'' ?>>ระบบน้ำหยด</option>
                    <option value="ยังไม่ได้วางระบบน้ำ" <?= $farm['water_system']=='ยังไม่ได้วางระบบน้ำ'?'selected':'' ?>>ยังไม่ได้วางระบบน้ำ</option>
                </select>
            </div>

            <div class="mb-3">
                <label>ที่ตั้งของแปลง</label>
                <input type="text" name="loca" class="form-control"
                       value="<?= htmlspecialchars($farm['loca']) ?>" required>
            </div>

            <div class="mb-3">
                <label>หมายเหตุ</label>
                <input type="text" name="des_farm" class="form-control"
                       value="<?= htmlspecialchars($farm['des_farm']) ?>" required>
            </div>

            <div class="my-3">
                <button type="submit" class="btn btn-success">บันทึกการแก้ไข</button>
                <a href="index_plot.php" class="btn btn-secondary">ยกเลิก</a>
            </div>

        </form>

        </div>
    </div>
</div>

</body>
</html>
