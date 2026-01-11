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


$stmt = $conn->prepare("SELECT * FROM cycles WHERE cycle_id = :cycle_id");
$stmt->execute(['cycle_id' => $cycle_id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    header("Location: index_admin.php");
    exit();
}


$stmt_farm = $conn->prepare("
    SELECT farm_id, farm_name 
    FROM farms 
    ORDER BY farm_name ASC
");
$stmt_farm->execute();
$farms = $stmt_farm->fetchAll(PDO::FETCH_ASSOC);


$stmt_plant = $conn->prepare("
    SELECT 
        MIN(plant_id) AS plant_id,
        plant_name
    FROM plants
    GROUP BY plant_name
    ORDER BY plant_name ASC
");
$stmt_plant->execute();
$plants = $stmt_plant->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $sql_update = "
        UPDATE cycles SET
            farm_id      = :farm_id,
            plant_id     = :plant_id,
            variety      = :variety,
            start_date   = :start_date,
            harvest_date = :harvest_date,
            spray_sch    = :spray_sch,
            fert_sch     = :fert_sch,
            water_sch    = :water_sch,
            status       = :status
        WHERE cycle_id = :cycle_id
    ";

    $stmt_update = $conn->prepare($sql_update);

    $success = $stmt_update->execute([
        'farm_id'      => $_POST['farm_id'],
        'plant_id'     => $_POST['plant_id'],
        'variety'      => $_POST['variety'],
        'start_date'   => $_POST['start_date'],
        'harvest_date' => $_POST['harvest_date'],
        'spray_sch'    => $_POST['spray_sch'],
        'fert_sch'     => $_POST['fert_sch'],
        'water_sch'    => $_POST['water_sch'],
        'status'       => $_POST['status'],
        'cycle_id'     => $cycle_id
    ]);

    if ($success) {
        echo "<script>
            alert('บันทึกข้อมูลเรียบร้อย');
            window.location='index_admin.php';
        </script>";
        exit();
    } else {
        echo "เกิดข้อผิดพลาดในการบันทึกข้อมูล";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แก้ไขรอบการปลูก</title>
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../mystyle.css">
</head>

<body>

<?php include('layout.php'); ?>

<div class="content">
    <div class="card-box">

        <h3 class="mb-3">แก้ไขรอบการปลูก</h3>

        <form method="POST">

            <div class="mb-3">
                <label>แปลงปลูก</label>
                <select name="farm_id" class="form-control" required>
                    <?php foreach ($farms as $f) { ?>
                        <option value="<?= $f['farm_id'] ?>"
                            <?= $f['farm_id'] == $data['farm_id'] ? 'selected' : '' ?>>
                            <?= $f['farm_name'] ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="mb-3">
                <label>ชนิดพืช</label>
                <select name="plant_id" class="form-control" required>
                    <?php foreach ($plants as $p) { ?>
                        <option value="<?= $p['plant_id'] ?>"
                            <?= $p['plant_id'] == $data['plant_id'] ? 'selected' : '' ?>>
                            <?= $p['plant_name'] ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="mb-3">
                <label>พันธุ์พืช</label>
                <input type="text" name="variety" class="form-control"
                       value="<?= htmlspecialchars($data['variety']) ?>" required>
            </div>

            <div class="mb-3">
                <label>วันที่เริ่มปลูก</label>
                <input type="date" name="start_date" class="form-control"
                       value="<?= $data['start_date'] ?>" required>
            </div>

            <div class="mb-3">
                <label>วันที่เก็บเกี่ยว</label>
                <input type="date" name="harvest_date" class="form-control"
                       value="<?= $data['harvest_date'] ?>">
            </div>

            <div class="mb-3">
                <label>กำหนดการฉีดยาบำรุง</label>
                <input type="date" name="spray_sch" class="form-control"
                       value="<?= $data['spray_sch'] ?>">
            </div>

            <div class="mb-3">
                <label>กำหนดการใส่ปุ๋ย</label>
                <input type="date" name="fert_sch" class="form-control"
                       value="<?= $data['fert_sch'] ?>">
            </div>

            <div class="mb-3">
                <label>กำหนดการรดน้ำ</label>
                <input type="date" name="water_sch" class="form-control"
                       value="<?= $data['water_sch'] ?>">
            </div>

            <div class="mb-3">
                <label>สถานะ</label>
                <select name="status" class="form-control" required>
                    <option value="กำลังปลูก" <?= $data['status']=='กำลังปลูก'?'selected':'' ?>>กำลังปลูก</option>
                    <option value="เจริญเติบโต" <?= $data['status']=='เจริญเติบโต'?'selected':'' ?>>เจริญเติบโต</option>
                    <option value="พร้อมเก็บเกี่ยว" <?= $data['status']=='พร้อมเก็บเกี่ยว'?'selected':'' ?>>พร้อมเก็บเกี่ยว</option>
                    <option value="สิ้นสุดรอบ" <?= $data['status']=='สิ้นสุดรอบ'?'selected':'' ?>>สิ้นสุดรอบ</option>
                </select>
            </div>

            <button type="submit" class="btn btn-success">บันทึก</button>
            <a href="index_admin.php" class="btn btn-secondary">ยกเลิก</a>

        </form>

    </div>
</div>

</body>
</html>
