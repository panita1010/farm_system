<?php
session_start();
$open_connect = 1;
require('../connect.php'); // $conn = PDO

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../loginform.php");
    exit();
}

$stmt = $conn->query("SELECT * FROM farms ORDER BY farm_id DESC");
$farms = $stmt->fetchAll(PDO::FETCH_ASSOC);
$count = count($farms);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>จัดการแปลงพืช</title>
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../mystyle.css">
</head>

<body>

<?php include("layout.php") ?>

<div class="content">
    <div class="card-box">

        <div class="d-flex justify-content-end mt-3">
            <a href="add_plot.php" class="btn btn-primary">＋ เพิ่มแปลงพืช</a>
        </div>

        <?php if ($count > 0) { ?>
            <div class="container mt-4">
                <div class="row g-4">

                    <?php foreach ($farms as $row) { ?>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">

                                    <h5 class="title"><?= htmlspecialchars($row['farm_name']) ?></h5>

                                    <p class="info">
                                        <strong>ขนาดพื้นที่:</strong> <?= htmlspecialchars($row['area']) ?><br>
                                        <strong>ลักษณะดิน:</strong> <?= htmlspecialchars($row['soil_type']) ?><br>
                                        <strong>ระบบน้ำ:</strong> <?= htmlspecialchars($row['water_system']) ?>
                                    </p>

                                    <div class="d-flex justify-content-between">
                                        <button class="btn btn-outline-primary btn-sm"
                                                data-bs-toggle="modal"
                                                data-bs-target="#farmModal<?= $row['farm_id'] ?>">
                                            ดูเพิ่มเติม
                                        </button>

                                        <div class="d-flex gap-2">
                                            <a href="edit_plot.php?farm_id=<?= $row['farm_id'] ?>"
                                               class="btn btn-success btn-sm">แก้ไข</a>

                                            <a href="delete_plot.php?farm_id=<?= $row['farm_id'] ?>"
                                               class="btn btn-danger btn-sm"
                                               onclick="return confirm('ยืนยันการลบข้อมูล?')">
                                                ลบ
                                            </a>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <!-- Modal -->
                        <div class="modal fade" id="farmModal<?= $row['farm_id'] ?>" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">

                                    <div class="modal-header">
                                        <h5 class="modal-title">
                                            <?= htmlspecialchars($row['farm_name']) ?>
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body">
                                        <p><strong>ขนาดพื้นที่:</strong> <?= htmlspecialchars($row['area']) ?></p>
                                        <p><strong>ลักษณะดิน:</strong> <?= htmlspecialchars($row['soil_type']) ?></p>
                                        <p><strong>ระบบน้ำ:</strong> <?= htmlspecialchars($row['water_system']) ?></p>
                                        <p><strong>ที่ตั้ง:</strong> <?= htmlspecialchars($row['loca']) ?></p>
                                        <p><strong>หมายเหตุ:</strong><br>
                                            <?= nl2br(htmlspecialchars($row['des_farm'])) ?>
                                        </p>
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
            <p class="text-center mt-4">ยังไม่มีแปลงพืช</p>
        <?php } ?>

    </div>
</div>

<script src="../bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
