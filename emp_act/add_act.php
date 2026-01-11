<?php
session_start();
$open_connect = 1;
require('../connect.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../loginform.php");
    exit();
}

/* ================== ดึงข้อมูลพืช ================== */
$stmt_plants = $conn->prepare(
    "SELECT plant_id, plant_name FROM plants ORDER BY plant_name ASC"
);
$stmt_plants->execute();
$plants = $stmt_plants->fetchAll(PDO::FETCH_ASSOC);

/* ================== ดึงข้อมูลแปลง ================== */
$stmt_farms = $conn->prepare(
    "SELECT farm_id, farm_name FROM farms ORDER BY farm_name ASC"
);
$stmt_farms->execute();
$farms = $stmt_farms->fetchAll(PDO::FETCH_ASSOC);

/* ================== บันทึกข้อมูล ================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $plant_id = $_POST['plant_id'] ?? null;
    $farm_id  = $_POST['farm_id'] ?? null;
    $act_type = $_POST['act_type'] ?? null;
    $variety  = $_POST['variety'] ?? null;
    $des_act  = $_POST['des_act'] ?? null;
    $act_date = $_POST['act_date'] ?? null;

    $user_id = $_SESSION['user_id'];   // FK
    $act_by  = $user_id;               // NOT NULL

    /* ---------- อัปโหลดรูปไป Supabase ---------- */
    $act_img = null;

    if (!empty($_FILES['act_img']['name'])) {

        if ($_FILES['act_img']['error'] !== 0) {
            die("<script>alert('อัปโหลดรูปไม่สำเร็จ'); history.back();</script>");
        }

        $ext = strtolower(pathinfo($_FILES['act_img']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif'];

        if (!in_array($ext, $allowed)) {
            die("<script>alert('ไฟล์รูปไม่ถูกต้อง'); history.back();</script>");
        }

        $filename = uniqid("act_") . "." . $ext;
        $act_img = uploadToSupabase($_FILES['act_img'], $filename);

        if (!$act_img) {
            die("<script>alert('อัปโหลดรูปไป Supabase ไม่สำเร็จ'); history.back();</script>");
        }
    }

    /* ================== INSERT ================== */
    $sql = "
        INSERT INTO activities
        (plant_id, farm_id, act_type, variety, des_act, act_date, user_id, act_by, act_img)
        VALUES
        (:plant_id, :farm_id, :act_type, :variety, :des_act, :act_date, :user_id, :act_by, :act_img)
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':plant_id' => $plant_id,
        ':farm_id'  => $farm_id,
        ':act_type' => $act_type,
        ':variety'  => $variety,
        ':des_act'  => $des_act,
        ':act_date' => $act_date,
        ':user_id'  => $user_id,
        ':act_by'   => $act_by,
        ':act_img'  => $act_img
    ]);

    echo "<script>alert('บันทึกข้อมูลสำเร็จ'); window.location='index_act.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>เพิ่มกิจกรรม</title>
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../mystyle.css">
</head>

<body>

    <?php include("layout.php"); ?>

    <div class="content">
        <div class="card-box">

            <h3 class="text-center mb-3">เพิ่มกิจกรรม</h3>

            <form method="POST" enctype="multipart/form-data">

                <div class="mb-3">
                    <label>เลือกแปลง</label>
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
                    <label>เลือกพืช</label>
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
                    <label>ประเภทกิจกรรม</label>
                    <select name="act_type" class="form-control" required>
                        <option value="">-- กรุณาเลือก --</option>
                        <option value="รดน้ำ">รดน้ำ</option>
                        <option value="ใส่ปุ๋ย">ใส่ปุ๋ย</option>
                        <option value="ฉีดยาบำรุงพืช">ฉีดยาบำรุงพืช</option>
                        <option value="กำจัดศัตรูพืช">กำจัดศัตรูพืช</option>
                        <option value="เก็บเกี่ยว">เก็บเกี่ยว</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label>รายละเอียดกิจกรรม</label>
                    <textarea name="des_act" class="form-control" rows="3"></textarea>
                </div>

                <div class="mb-3">
                    <label>วันที่ทำกิจกรรม</label>
                    <input type="date" name="act_date" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>รูปกิจกรรม</label>
                    <input type="file" name="act_img" class="form-control" accept="image/*">
                </div>

                <button type="submit" class="btn btn-success">บันทึก</button>
                <a href="index_act.php" class="btn btn-secondary">ยกเลิก</a>

            </form>

        </div>
    </div>

</body>

</html>