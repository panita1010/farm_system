<?php
session_start();
$open_connect = 1;
require('../connect.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../loginform.php");
    exit();
}

/* =======================
   ดึงข้อมูลรอบการปลูก
======================= */
$sql = "
SELECT c.*, 
       f.farm_name,
       p.plant_name,
       c.variety
FROM cycles c
LEFT JOIN farms f ON c.farm_id = f.farm_id
LEFT JOIN plants p ON c.plant_id = p.plant_id
ORDER BY c.start_date DESC
";

$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll();
$count = count($result);

/* =======================
   สรุปสถานะรอบการปลูก
======================= */
$sql_summary = "
SELECT 
    COUNT(*) FILTER (WHERE status = 'กำลังปลูก') AS growing,
    COUNT(*) FILTER (WHERE status = 'เจริญเติบโต') AS developing,
    COUNT(*) FILTER (WHERE status = 'พร้อมเก็บเกี่ยว') AS ready,
    COUNT(*) FILTER (WHERE status = 'สิ้นสุดรอบ') AS completed
FROM cycles
";

$stmt_summary = $conn->prepare($sql_summary);
$stmt_summary->execute();
$summary = $stmt_summary->fetch();
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>รอบการปลูก</title>
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../mystyle.css">
</head>

<body>

<?php include("layout.php"); ?>

<div class="content">

    <div class="row mb-4">

        <div class="col-md-3">
            <div class="card summary-card card-growing text-center p-3">
                <h6>กำลังปลูก</h6>
                <h3><?= $summary['growing'] ?? 0 ?></h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card summary-card card-develop text-center p-3">
                <h6>เจริญเติบโต</h6>
                <h3><?= $summary['developing'] ?? 0 ?></h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card summary-card card-ready text-center p-3">
                <h6>พร้อมเก็บเกี่ยว</h6>
                <h3><?= $summary['ready'] ?? 0 ?></h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card summary-card card-done text-center p-3">
                <h6>สิ้นสุดรอบ</h6>
                <h3><?= $summary['completed'] ?? 0 ?></h3>
            </div>
        </div>

    </div>

    <div class="card-box">

        <div class="d-flex justify-content-end mt-3">
            <a href="add_cycle.php" class="btn btn-primary">+ เพิ่มรอบการปลูก</a>
        </div>

        <?php if ($count > 0) { ?>
            <div class="container mt-3">
                <div class="row g-3">

                    <?php foreach ($result as $row) {
                        $status_class = 'status-growing';

                        if ($row['status'] == 'กำลังปลูก') $status_class = 'status-growing';
                        else if ($row['status'] == 'เจริญเติบโต') $status_class = 'status-develop';
                        else if ($row['status'] == 'พร้อมเก็บเกี่ยว') $status_class = 'status-ready';
                        else $status_class = 'status-done';
                    ?>

                        <div class="col-md-12">
                            <div class="card">

                                <div class="header d-flex justify-content-end">
                                    <div class="status-badge <?= $status_class ?>">
                                        <?= $row['status'] ?>
                                    </div>
                                </div>

                                <div>
                                    <div class="title"><?= $row["plant_name"] ?></div>
                                    <div class="sub">
                                        พันธุ์: <?= $row["variety"] ?><br>
                                        กำหนดการฉีดยาบำรุง: <?= date('d/m/Y', strtotime($row['spray_sch'])) ?><br>
                                        กำหนดการใส่ปุ๋ย: <?= date('d/m/Y', strtotime($row['fert_sch'])) ?><br>
                                        กำหนดการรดน้ำ: <?= date('d/m/Y', strtotime($row['water_sch'])) ?>
                                    </div>

                                    <div class="row mt-3">
                                        <div class="col-md-4">
                                            <div class="sub-box">
                                                <small class="sub-box-title">แปลงปลูก</small><br>
                                                <span class="sub-box-p"><?= $row['farm_name'] ?></span>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="sub-box">
                                                <small class="sub-box-title">วันที่ปลูก</small><br>
                                                <span class="sub-box-p"><?= date('d/m/Y', strtotime($row['start_date'])) ?></span>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="sub-box">
                                                <small class="sub-box-title">วันที่เก็บเกี่ยว</small><br>
                                                <span class="sub-box-p"><?= date('d/m/Y', strtotime($row['harvest_date'])) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex gap-2 justify-content-end">
                                    <a href="edit_cycle.php?cycle_id=<?= $row['cycle_id'] ?>"
                                       class="btn btn-outline-primary btn-sm">แก้ไข</a>

                                    <a href="delete_cycle.php?cycle_id=<?= $row['cycle_id'] ?>"
                                       class="btn btn-outline-danger btn-sm"
                                       onclick="return confirm('ยืนยันการลบรอบนี้?')">ลบ</a>
                                </div>

                            </div>
                        </div>

                    <?php } ?>

                </div>
            </div>

        <?php } else { ?>
            <p class="text-center mt-4">ยังไม่มีข้อมูลรอบการปลูก</p>
        <?php } ?>

    </div>
</div>

</body>
</html>
