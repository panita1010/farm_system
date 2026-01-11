<?php
session_start();
$open_connect = 1;
require('../connect.php'); // $conn = PDO

// ตรวจสอบสิทธิ์
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../loginform.php");
    exit();
}

// ดึงข้อมูลแปลง
$stmt_farm = $conn->query("SELECT farm_id, farm_name FROM farms ORDER BY farm_name ASC");
$farms = $stmt_farm->fetchAll(PDO::FETCH_ASSOC);

// 🔽 ดึงข้อมูลพืช (ไม่ซ้ำตามชื่อพืช)
$stmt_plant = $conn->query("
    SELECT MIN(plant_id) AS plant_id, plant_name
    FROM plants
    GROUP BY plant_name
    ORDER BY plant_name ASC
");
$plants = $stmt_plant->fetchAll(PDO::FETCH_ASSOC);

// เมื่อกดปุ่มบันทึก
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $sql_insert = "
        INSERT INTO cycles (
            farm_id,
            plant_id,
            variety,
            start_date,
            harvest_date,
            spray_sch,
            fert_sch,
            water_sch,
            status
        ) VALUES (
            :farm_id,
            :plant_id,
            :variety,
            :start_date,
            :harvest_date,
            :spray_sch,
            :fert_sch,
            :water_sch,
            :status
        )
    ";

    $stmt = $conn->prepare($sql_insert);
    $stmt->execute([
        'farm_id'      => $_POST['farm_id'],
        'plant_id'     => $_POST['plant_id'],
        'variety'      => $_POST['variety'],
        'start_date'   => $_POST['start_date'],
        'harvest_date' => $_POST['harvest_date'] ?: null,
        'spray_sch'    => $_POST['spray_sch'] ?: null,
        'fert_sch'     => $_POST['fert_sch'] ?: null,
        'water_sch'    => $_POST['water_sch'] ?: null,
        'status'       => $_POST['status'],
    ]);

    header("Location: index_admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>เพิ่มรอบการปลูก</title>
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../mystyle.css">
</head>

<body>

<?php include("layout.php"); ?>

<div class="content">
    <div class="card-box">

        <h3 class="text-center">เพิ่มรอบการปลูก</h3>

        <form method="POST">

            <div class="mb-3">
                <label>แปลงปลูก</label>
                <select name="farm_id" class="form-control" required>
                    <option value="">-- เลือกแปลง --</option>
                    <?php foreach ($farms as $f) { ?>
                        <option value="<?= $f['farm_id'] ?>">
                            <?= htmlspecialchars($f['farm_name']) ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="mb-3">
                <label>ชนิดพืช</label>
                <select name="plant_id" class="form-control" required>
                    <option value="">-- เลือกพืช --</option>
                    <?php foreach ($plants as $p) { ?>
                        <option value="<?= $p['plant_id'] ?>">
                            <?= htmlspecialchars($p['plant_name']) ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="mb-3">
                <label>พันธุ์พืช</label>
                <input type="text" name="variety" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>วันที่เริ่มปลูก</label>
                <input type="date" name="start_date" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>วันที่เก็บเกี่ยว</label>
                <input type="date" name="harvest_date" class="form-control">
            </div>

            <div class="mb-3">
                <label>กำหนดการฉีดยาบำรุง</label>
                <input type="date" name="spray_sch" class="form-control">
            </div>

            <div class="mb-3">
                <label>กำหนดการใส่ปุ๋ย</label>
                <input type="date" name="fert_sch" class="form-control">
            </div>

            <div class="mb-3">
                <label>กำหนดการรดน้ำ</label>
                <input type="date" name="water_sch" class="form-control">
            </div>

            <div class="mb-3">
                <label>สถานะ</label>
                <select name="status" class="form-control" required>
                    <option value="">-- เลือกสถานะ --</option>
                    <option value="กำลังปลูก">กำลังปลูก</option>
                    <option value="เจริญเติบโต">เจริญเติบโต</option>
                    <option value="พร้อมเก็บเกี่ยว">พร้อมเก็บเกี่ยว</option>
                    <option value="สิ้นสุดรอบ">สิ้นสุดรอบ</option>
                </select>
            </div>

            <button type="submit" class="btn btn-success">บันทึก</button>
            <a href="index_admin.php" class="btn btn-secondary">ยกเลิก</a>

        </form>

    </div>
</div>

</body>
</html>
