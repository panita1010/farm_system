<?php
session_start();
$open_connect = 1;
require('../connect.php'); // $conn = PDO

// ตรวจสิทธิ์
if (!isset($_SESSION['role'])) {
    header("Location: ../loginform.php");
    exit();
}

$start_date = $end_date = "";
$activities = [];

if (isset($_GET['start_date'], $_GET['end_date'])) {

    $start_date = $_GET['start_date'];
    $end_date   = $_GET['end_date'];

    $sql = "
        SELECT a.*, 
               f.farm_name,
               p.plant_name,
               p.variety,
               u.fullname
        FROM activities a
        LEFT JOIN farms f ON a.farm_id = f.farm_id
        LEFT JOIN plants p ON a.plant_id = p.plant_id
        LEFT JOIN users u ON a.user_id = u.user_id
        WHERE a.act_date BETWEEN :start_date AND :end_date
        ORDER BY a.act_date DESC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        'start_date' => $start_date,
        'end_date'   => $end_date
    ]);

    $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>รายงานกิจกรรม</title>
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../mystyle.css">
</head>

<body>

<?php include("layout.php"); ?>

<div class="content">
    <div class="card-box">

        <!-- ฟอร์มค้นหา -->
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-4">
                <label>วันที่เริ่มต้น</label>
                <input type="date" name="start_date" class="form-control"
                       required value="<?= htmlspecialchars($start_date) ?>">
            </div>

            <div class="col-md-4">
                <label>วันที่สิ้นสุด</label>
                <input type="date" name="end_date" class="form-control"
                       required value="<?= htmlspecialchars($end_date) ?>">
            </div>

            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">ค้นหา</button>
            </div>
        </form>

        <?php if (!empty($activities)) { ?>

        <div class="table-responsive">
            <table class="table table-bordered table-striped align-top">
                <thead class="table-success">
                    <tr>
                        <th width="12%">วันที่</th>
                        <th width="11%">กิจกรรม</th>
                        <th width="11%">พืช</th>
                        <th width="11%">แปลง</th>
                        <th width="40%">รายละเอียด</th>
                        <th width="15%">ผู้บันทึก</th>
                    </tr>
                </thead>
                <tbody>

                <?php foreach ($activities as $row) { ?>
                    <tr>
                        <td><?= htmlspecialchars($row['act_date']) ?></td>
                        <td><?= htmlspecialchars($row['act_type']) ?></td>
                        <td>
                            <?= htmlspecialchars($row['plant_name']) ?><br>
                            (<?= htmlspecialchars($row['variety']) ?>)
                        </td>
                        <td><?= htmlspecialchars($row['farm_name']) ?></td>
                        <td><?= nl2br(htmlspecialchars($row['des_act'])) ?></td>
                        <td><?= htmlspecialchars($row['fullname']) ?></td>
                    </tr>
                <?php } ?>

                </tbody>
            </table>
        </div>

        <?php } elseif (isset($_GET['start_date'])) { ?>

            <div class="text-center text-muted">
                ไม่พบข้อมูลกิจกรรม
            </div>

        <?php } ?>

    </div>
</div>

</body>
</html>
