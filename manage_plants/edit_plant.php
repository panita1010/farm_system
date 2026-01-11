<?php
session_start();
$open_connect = 1;

require('../connect.php');
require('../supabase_storage.php'); // ⭐ ใช้ Supabase

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../loginform.php");
    exit();
}

/* ---------- ตรวจ plant_id ---------- */
if (!isset($_GET['plant_id'])) {
    header("Location: index_plant.php");
    exit();
}

$plant_id = $_GET['plant_id'];

/* ---------- ดึงข้อมูลพืช ---------- */
$stmt = $conn->prepare("SELECT * FROM plants WHERE plant_id = ?");
$stmt->execute([$plant_id]);
$plant = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$plant) {
    header("Location: index_plant.php");
    exit();
}

/* ---------- UPDATE ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $plant_name = $_POST["plant_name"];
    $variety    = $_POST["variety"];
    $growtime   = $_POST["growtime"];
    $planting   = $_POST["planting"];
    $care       = $_POST["care"];
    $des_plant  = $_POST["des_plant"];

    $img_plant = $plant['img_plant']; // ค่าเดิม (URL)

    /* ---------- ถ้ามีการอัปโหลดรูปใหม่ ---------- */
    if (!empty($_FILES['img_plant']['name'])) {

        if ($_FILES['img_plant']['error'] !== 0) {
            echo "<script>alert('อัปโหลดรูปไม่สำเร็จ');</script>";
            exit();
        }

        $ext = strtolower(pathinfo($_FILES['img_plant']['name'], PATHINFO_EXTENSION));
        $allow = ['jpg','jpeg','png','gif'];

        if (!in_array($ext, $allow)) {
            echo "<script>alert('กรุณาอัปโหลดไฟล์รูปภาพเท่านั้น');</script>";
            exit();
        }

        $filename = uniqid("plant_") . "." . $ext;
        $upload_url = uploadToSupabase($_FILES['img_plant'], $filename);

        if (!$upload_url) {
            echo "<script>alert('อัปโหลดรูปไป Supabase ไม่สำเร็จ');</script>";
            exit();
        }

        $img_plant = $upload_url; // ⭐ ใช้ URL ใหม่
    }

    /* ---------- UPDATE ---------- */
    $stmt = $conn->prepare("
        UPDATE plants SET
            plant_name = ?,
            variety    = ?,
            growtime   = ?,
            planting   = ?,
            care       = ?,
            des_plant  = ?,
            img_plant  = ?
        WHERE plant_id = ?
    ");

    $stmt->execute([
        $plant_name,
        $variety,
        $growtime,
        $planting,
        $care,
        $des_plant,
        $img_plant,
        $plant_id
    ]);

    echo "<script>alert('แก้ไขข้อมูลสำเร็จ'); window.location='index_plant.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แก้ไขข้อมูลพืช</title>
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../mystyle.css">
</head>

<body>

<?php include("layout.php"); ?>

<div class="content">
    <div class="card-box">

        <h3 class="mb-3">แก้ไขข้อมูลพืช</h3>

        <form method="POST" enctype="multipart/form-data">

            <div class="mb-3">
                <label>ชื่อพืช</label>
                <input type="text" name="plant_name" class="form-control"
                       value="<?= htmlspecialchars($plant['plant_name']) ?>" required>
            </div>

            <div class="mb-3">
                <label>พันธุ์</label>
                <input type="text" name="variety" class="form-control"
                       value="<?= htmlspecialchars($plant['variety']) ?>" required>
            </div>

            <div class="mb-3">
                <label>ระยะเวลาการปลูก</label>
                <input type="text" name="growtime" class="form-control"
                       value="<?= htmlspecialchars($plant['growtime']) ?>">
            </div>

            <div class="mb-3">
                <label>วิธีการปลูก</label>
                <textarea name="planting" class="form-control"><?= htmlspecialchars($plant['planting']) ?></textarea>
            </div>

            <div class="mb-3">
                <label>การดูแล</label>
                <textarea name="care" class="form-control"><?= htmlspecialchars($plant['care']) ?></textarea>
            </div>

            <div class="mb-3">
                <label>รายละเอียด</label>
                <textarea name="des_plant" class="form-control"><?= htmlspecialchars($plant['des_plant']) ?></textarea>
            </div>

            <div class="mb-3">
                <label>รูปพืช</label><br>
                <?php if (!empty($plant['img_plant'])) { ?>
                    <img src="<?= htmlspecialchars($plant['img_plant']) ?>"
                         class="img-fluid mb-2" style="max-width:150px;">
                <?php } ?>
                <input type="file" name="img_plant" class="form-control">
            </div>

            <button type="submit" class="btn btn-success">บันทึก</button>
            <a href="index_plant.php" class="btn btn-secondary">ยกเลิก</a>

        </form>

    </div>
</div>

</body>
</html>
