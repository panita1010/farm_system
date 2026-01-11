<?php
session_start();
$open_connect = 1;
require('../connect.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../loginform.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $plant_name = $_POST["plant_name"];
    $variety    = $_POST["variety"];
    $growtime   = $_POST["growtime"];
    $planting   = $_POST["planting"];
    $care       = $_POST["care"];
    $des_plant  = $_POST["des_plant"];

    // 🔒 ตรวจไฟล์รูป
    if (!isset($_FILES['img_plant']) || $_FILES['img_plant']['error'] != 0) {
        echo "<script>alert('กรุณาเลือกรูป');</script>";
        exit();
    }

    $ext = strtolower(pathinfo($_FILES['img_plant']['name'], PATHINFO_EXTENSION));
    $allow = ['jpg','jpeg','png','gif'];

    if (!in_array($ext, $allow)) {
        echo "<script>alert('กรุณาอัปโหลดไฟล์รูปภาพเท่านั้น');</script>";
        exit();
    }

    $new_image_name = uniqid("plant_") . "." . $ext;
    $upload_path = "../image_plant/" . $new_image_name;

    try {
        // 🔒 เช็คข้อมูลซ้ำ
        $check = $conn->prepare("
            SELECT 1 FROM plants 
            WHERE plant_name = ? AND variety = ?
        ");
        $check->execute([$plant_name, $variety]);

        if ($check->rowCount() > 0) {
            echo "<script>alert('พืชชนิดนี้มีอยู่แล้ว');</script>";
            exit();
        }

        // 🔒 บันทึกข้อมูล
        $stmt = $conn->prepare("
            INSERT INTO plants
            (plant_name, variety, growtime, planting, care, des_plant, img_plant)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $plant_name,
            $variety,
            $growtime,
            $planting,
            $care,
            $des_plant,
            $new_image_name
        ]);

        move_uploaded_file($_FILES['img_plant']['tmp_name'], $upload_path);

        echo "<script>alert('บันทึกข้อมูลสำเร็จ'); window.location='index_plant.php';</script>";

    } catch (PDOException $e) {
        echo "<script>alert('เกิดข้อผิดพลาด');</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>เพิ่มข้อมูลพืช</title>
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../mystyle.css">
</head>

<body>

    <?php include("layout.php"); ?>

    <div class="content">
        <div class="card-box">
            <div class="container mt-3">
                <h2 class="text-center">เพิ่มข้อมูลพืช</h2>
                <hr>

                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>"
                    method="POST"
                    enctype="multipart/form-data"
                    class="form-addplant">

                    <div class="form-group mb-3">
                        <label>ชื่อพืช</label>
                        <input type="text" name="plant_name" class="form-control" required>
                    </div>

                    <div class="form-group mb-3">
                        <label>สายพันธุ์</label>
                        <input type="text" name="variety" class="form-control" required>
                    </div>

                    <div class="form-group mb-3">
                        <label>ระยะเวลาในการเจริญเติบโต</label>
                        <input type="text" name="growtime" class="form-control" required>
                    </div>

                    <div class="form-group mb-3">
                        <label>ลักษณะทางพฤกษศาสตร์</label>
                        <textarea name="des_plant" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="form-group mb-3">
                        <label>การปลูก</label>
                        <textarea name="planting" class="form-control" rows="3"
                            value="<?php echo htmlspecialchars($plant['planting']); ?>"></textarea>
                    </div>

                    <div class="form-group mb-3">
                        <label>การดูแล</label>
                        <textarea name="care" class="form-control" rows="3"
                            value="<?php echo htmlspecialchars($plant['care']); ?>"></textarea>
                    </div>

                    <div class="form-group mb-3">
                        <label>รูปพืช</label>
                        <input type="file" name="img_plant" class="form-control" accept="image/*" required>
                    </div>

                    <div class="my-3">
                        <input type="submit" value="บันทึกข้อมูล" class="btn btn-success">
                        <input type="reset" value="ล้างข้อมูล" class="btn btn-danger">
                        <a href="index_plant.php" class="btn btn-primary">กลับ</a>
                    </div>

                </form>
            </div>
        </div>
    </div>

</body>

</html>