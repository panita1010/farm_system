<?php
session_start();
$open_connect = 1;
require('../connect.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../loginform.php");
    exit();
}

/* ================= ตรวจสอบ activity_id ================= */
if (!isset($_GET['activity_id'])) {
    header("Location: index_act.php");
    exit();
}

$activity_id = $_GET['activity_id'];

/* ================= ดึงข้อมูลกิจกรรม ================= */
$sql = "
    SELECT a.*,
           p.plant_name,
           f.farm_name
    FROM activities a
    LEFT JOIN plants p ON a.plant_id = p.plant_id
    LEFT JOIN farms f  ON a.farm_id  = f.farm_id
    WHERE a.activity_id = :id
";
$stmt = $conn->prepare($sql);
$stmt->execute([':id' => $activity_id]);
$act = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$act) {
    header("Location: index_act.php");
    exit();
}

/* ================= ดึงพืช ================= */
$stmt_plants = $conn->prepare(
    "SELECT plant_id, plant_name FROM plants ORDER BY plant_name ASC"
);
$stmt_plants->execute();
$plants = $stmt_plants->fetchAll(PDO::FETCH_ASSOC);

/* ================= ดึงแปลง ================= */
$stmt_farms = $conn->prepare(
    "SELECT farm_id, farm_name FROM farms ORDER BY farm_name ASC"
);
$stmt_farms->execute();
$farms = $stmt_farms->fetchAll(PDO::FETCH_ASSOC);

/* ================= UPDATE ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $plant_id = $_POST['plant_id'];
    $farm_id  = $_POST['farm_id'];
    $act_type = $_POST['act_type'];
    $variety  = $_POST['variety'];
    $des_act  = $_POST['des_act'];
    $act_date = $_POST['act_date'];

    /* ---------- รูปเดิม ---------- */
    $act_img = $act['act_img'];

    /* ---------- ถ้ามีการอัปโหลดรูปใหม่ ---------- */
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
        $new_img = uploadToSupabase($_FILES['act_img'], $filename);

        if (!$new_img) {
            die("<script>alert('อัปโหลดรูปไป Supabase ไม่สำเร็จ'); history.back();</script>");
        }

        $act_img = $new_img;
    }

    $sql_update = "
        UPDATE activities SET
            plant_id = :plant_id,
            farm_id  = :farm_id,
            act_type = :act_type,
            variety  = :variety,
            des_act  = :des_act,
            act_date = :act_date,
            act_img  = :act_img
        WHERE activity_id = :id
    ";

    $stmt = $conn->prepare($sql_update);
    $stmt->execute([
        ':plant_id' => $plant_id,
        ':farm_id'  => $farm_id,
        ':act_type' => $act_type,
        ':variety'  => $variety,
        ':des_act'  => $des_act,
        ':act_date' => $act_date,
        ':act_img'  => $act_img,
        ':id'       => $activity_id
    ]);

    echo "<script>alert('อัปเดตข้อมูลสำเร็จ'); window.location='index_act.php';</script>";
    exit();
}
?>


<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>แก้ไขกิจกรรม</title>
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../mystyle.css">
</head>

<body>

    <?php include("layout.php") ?>

    <div class="content">
        <div class="card-box">

            <h3 class="mb-3">แก้ไขกิจกรรม</h3>

            <form method="POST" enctype="multipart/form-data">

                <div class="mb-3">
                    <label>ประเภทกิจกรรม</label>
                    <select name="act_type" class="form-control" required>
                        <option value="">-- กรุณาเลือก --</option>
                        <?php
                        $types = ["รดน้ำ", "ใส่ปุ๋ย", "ฉีดยาบำรุงพืช", "กำจัดศัตรูพืช", "เก็บเกี่ยว"];
                        foreach ($types as $t) {
                            $sel = ($act['act_type'] == $t) ? "selected" : "";
                            echo "<option value=\"$t\" $sel>$t</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label>เลือกพืช</label>
                    <select name="plant_id" class="form-control" required>
                        <?php foreach ($plants as $p) { ?>
                            <option value="<?= $p['plant_id'] ?>"
                                <?= $p['plant_id'] == $act['plant_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($p['plant_name']) ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label>พันธุ์พืช</label>
                    <input type="text" class="form-control" name="variety"
                        value="<?= htmlspecialchars($act['variety']) ?>">
                </div>

                <div class="mb-3">
                    <label>เลือกแปลง</label>
                    <select name="farm_id" class="form-control" required>
                        <?php foreach ($farms as $f) { ?>
                            <option value="<?= $f['farm_id'] ?>"
                                <?= $f['farm_id'] == $act['farm_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($f['farm_name']) ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label>รายละเอียดกิจกรรม</label>
                    <textarea name="des_act" class="form-control" rows="3"><?= htmlspecialchars($act['des_act']) ?></textarea>
                </div>

                <div class="mb-3">
                    <label>วันที่ทำกิจกรรม</label>
                    <input type="date" name="act_date" class="form-control"
                        value="<?= $act['act_date'] ?>" required>
                </div>

                <div class="mb-3">
                    <label>รูปกิจกรรม</label><br>
                    <?php if (!empty($act['act_img'])) { ?>
                        <img src="<?= htmlspecialchars($act['act_img']) ?>"
                            width="120" class="mb-2"><br>
                    <?php } ?>

                    <input type="file" name="act_img" class="form-control">
                </div>

                <button type="submit" class="btn btn-success">บันทึก</button>
                <a href="index_act.php" class="btn btn-secondary">ยกเลิก</a>

            </form>

        </div>
    </div>

</body>

</html>