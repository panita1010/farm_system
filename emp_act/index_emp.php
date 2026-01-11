<?php
session_start();
$open_connect = 1;
require('../connect.php');

// ตรวจสอบ role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'employee') {
    header("Location: ../loginform.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// ดึงข้อมูลกิจกรรมเฉพาะผู้ใช้นี้
$sql = "
    SELECT a.*, 
           p.plant_name,
           f.farm_name,
           u.fullname
    FROM activities a
    LEFT JOIN plants p ON a.plant_id = p.plant_id
    LEFT JOIN farms f ON a.farm_id = f.farm_id
    LEFT JOIN users u ON a.user_id = u.user_id
    WHERE a.user_id = :user_id
    ORDER BY a.act_date DESC
";

$stmt = $conn->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();

$rows  = $stmt->fetchAll(PDO::FETCH_ASSOC);
$count = count($rows);
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>รายการกิจกรรม</title>
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../mystyle.css">
</head>

<body>

    <?php include("layout.php"); ?>

    <div class="content">
        <div class="card-box">

            <div class="d-flex justify-content-end mt-3">
                <a href="add_act.php" class="btn btn-primary">+ เพิ่มกิจกรรม</a>
            </div>

            <?php if ($count > 0) { ?>
                <div class="container mt-3">
                    <div class="row g-3">

                        <?php foreach ($rows as $row) { ?>
                            <div class="col-md-12">
                                <div class="card">

                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="title"><?= htmlspecialchars($row['act_type']); ?></div>
                                    </div>

                                    <p class="info">
                                        <strong>วันที่:</strong> <?= date('d/m/Y', strtotime($row['act_date'])) ?><br>
                                        <strong>พืช:</strong> <?= htmlspecialchars($row['plant_name']); ?><br>
                                        <strong>พันธุ์:</strong> <?= htmlspecialchars($row['variety']); ?><br>
                                        <strong>แปลง:</strong> <?= htmlspecialchars($row['farm_name']); ?><br>
                                        <strong>ผู้ทำกิจกรรม:</strong>
                                        <?= !empty($row['fullname']) ? htmlspecialchars($row['fullname']) : '-' ?><br>
                                    </p>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <button class="btn btn-outline-primary btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#actModal<?= $row['activity_id'] ?>">
                                            ดูเพิ่มเติม
                                        </button>

                                        <div class="d-flex gap-2">
                                            <a href="edit_act.php?activity_id=<?= $row['activity_id'] ?>"
                                                class="btn btn-success btn-sm">แก้ไข</a>

                                            <a href="delete_act.php?activity_id=<?= $row['activity_id'] ?>"
                                                class="btn btn-danger btn-sm"
                                                onclick="return confirm('ยืนยันการลบกิจกรรมนี้?');">
                                                ลบ
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal -->
                            <div class="modal fade" id="actModal<?= $row['activity_id'] ?>" tabindex="-1">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content">

                                        <div class="modal-header">
                                            <h5 class="modal-title"><?= date('d/m/Y', strtotime($row['act_date'])) ?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            <h5><?= htmlspecialchars($row['act_type']); ?></h5>
                                            <strong>พืช:</strong> <?= htmlspecialchars($row['plant_name']); ?><br>
                                            <strong>พันธุ์:</strong> <?= htmlspecialchars($row['variety']); ?><br>
                                            <strong>แปลง:</strong> <?= htmlspecialchars($row['farm_name']); ?><br>
                                            <strong>รายละเอียด:</strong><br>
                                            <?= nl2br(htmlspecialchars($row['des_act'])); ?><br>
                                            <strong>โดย:</strong>
                                            <?= !empty($row['fullname']) ? htmlspecialchars($row['fullname']) : '-' ?><br>

                                            <?php if (!empty($row['act_img'])) { ?>
                                                <img src="../image_act/<?= htmlspecialchars($row['act_img']); ?>"
                                                    class="img-fluid rounded mb-3 d-block mx-auto">
                                            <?php } ?>
                                        </div>

                                        <div class="modal-footer">
                                            <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                                                ปิด
                                            </button>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        <?php } ?>

                    </div>
                </div>
            <?php } else { ?>
                <p class="text-center mt-4">ยังไม่มีกิจกรรม</p>
            <?php } ?>

        </div>
    </div>

    <script src="../bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>
