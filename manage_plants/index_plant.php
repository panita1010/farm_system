<?php
session_start();
$open_connect = 1;
require('../connect.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../loginform.php");
    exit();
}

/* ---------- ดึงข้อมูลพืช ---------- */
$stmt = $conn->prepare("SELECT * FROM plants ORDER BY plant_id DESC");
$stmt->execute();
$plants = $stmt->fetchAll(PDO::FETCH_ASSOC);
$count = count($plants);

function shortText($text, $length = 60)
{
    return mb_strimwidth($text, 0, $length, '...');
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แสดงข้อมูลพืช</title>
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../mystyle.css">
</head>

<body>

<?php include("layout.php"); ?>

<div class="content">
    <div class="card-box">

        <div class="d-flex justify-content-end mt-3">
            <a href="add_plant.php" class="btn btn-primary">＋ เพิ่มชนิดพืช</a>
        </div>

        <?php if ($count > 0) { ?>
            <div class="container mt-4">
                <div class="row">

                    <?php foreach ($plants as $row) { ?>

                        <div class="col-md-6 mb-4">
                            <div class="card">

                                <?php if (!empty($row['img_plant'])) { ?>
                                    <img src="<?= htmlspecialchars($row['img_plant']); ?>"
                                         class="img"
                                         alt="plant image">
                                <?php } else { ?>
                                    <div class="text-center p-4 text-muted">
                                        ไม่มีรูป
                                    </div>
                                <?php } ?>

                                <div class="card-body">

                                    <h5 class="title">
                                        <?= htmlspecialchars($row['plant_name']); ?>
                                    </h5>

                                    <p class="info">
                                        <strong>สายพันธุ์:</strong> <?= htmlspecialchars($row['variety']); ?><br>
                                        <strong>ระยะเวลาเจริญเติบโต:</strong> <?= htmlspecialchars($row['growtime']); ?><br>
                                        <strong>การปลูก:</strong> <?= shortText($row['planting']); ?><br>
                                        <strong>การดูแล:</strong> <?= shortText($row['care']); ?>
                                    </p>

                                    <div class="d-flex justify-content-between align-items-center">

                                        <button class="btn btn-outline-primary btn-sm"
                                                data-bs-toggle="modal"
                                                data-bs-target="#plantModal<?= $row['plant_id']; ?>">
                                            ดูเพิ่มเติม
                                        </button>

                                        <div class="d-flex gap-2">
                                            <a href="edit_plant.php?plant_id=<?= $row['plant_id']; ?>"
                                               class="btn btn-success btn-sm">
                                                แก้ไข
                                            </a>

                                            <a href="delete_plant.php?plant_id=<?= $row['plant_id']; ?>"
                                               class="btn btn-danger btn-sm"
                                               onclick="return confirm('ยืนยันการลบข้อมูลพืชนี้?');">
                                                ลบ
                                            </a>
                                        </div>

                                    </div>

                                </div>
                            </div>
                        </div>

                        <!-- Modal -->
                        <div class="modal fade"
                             id="plantModal<?= $row['plant_id']; ?>"
                             tabindex="-1">

                            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                                <div class="modal-content">

                                    <div class="modal-header">
                                        <h5 class="modal-title">
                                            <?= htmlspecialchars($row['plant_name']); ?>
                                        </h5>
                                        <button type="button"
                                                class="btn-close"
                                                data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body">

                                        <?php if (!empty($row['img_plant'])) { ?>
                                            <img src="<?= htmlspecialchars($row['img_plant']); ?>"
                                                 class="img-fluid rounded mb-3 d-block mx-auto">
                                        <?php } ?>

                                        <p>
                                            <strong>สายพันธุ์:</strong> <?= htmlspecialchars($row['variety']); ?><br><br>

                                            <strong>ลักษณะทางพฤกษศาสตร์:</strong><br>
                                            <?= nl2br(htmlspecialchars($row['des_plant'])); ?><br><br>

                                            <strong>ระยะเวลาเจริญเติบโต:</strong>
                                            <?= htmlspecialchars($row['growtime']); ?><br><br>

                                            <strong>การปลูก:</strong><br>
                                            <?= nl2br(htmlspecialchars($row['planting'])); ?><br><br>

                                            <strong>การดูแล:</strong><br>
                                            <?= nl2br(htmlspecialchars($row['care'])); ?>
                                        </p>

                                    </div>

                                    <div class="modal-footer">
                                        <button class="btn btn-secondary"
                                                data-bs-dismiss="modal">
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
            <p class="text-center empty-text mt-4">ยังไม่มีข้อมูลพืช</p>
        <?php } ?>

    </div>
</div>

<script src="../bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
