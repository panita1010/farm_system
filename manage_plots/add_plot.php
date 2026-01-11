<?php
session_start();
$open_connect = 1;
require('../connect.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../loginform.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $farm_name    = $_POST['farm_name'];
    $area         = $_POST['area'];
    $soil_type    = $_POST['soil_type'];
    $water_system = $_POST['water_system'];
    $loca         = $_POST['loca'];
    $des_farm     = $_POST['des_farm'];

    $check_stmt = $conn->prepare(
        "SELECT farm_id FROM farms WHERE farm_name = :farm_name"
    );
    $check_stmt->execute([
        ':farm_name' => $farm_name
    ]);

    if ($check_stmt->rowCount() > 0) {
        echo "<script>alert('ชื่อแปลงพืชนี้มีอยู่แล้ว กรุณาเลือกชื่ออื่น');</script>";
    } else {

        $stmt = $conn->prepare("
            INSERT INTO farms 
            (farm_name, area, water_system, loca, soil_type, des_farm)
            VALUES 
            (:farm_name, :area, :water_system, :loca, :soil_type, :des_farm)
        ");

        $result = $stmt->execute([
            ':farm_name'    => $farm_name,
            ':area'         => $area,
            ':water_system' => $water_system,
            ':loca'         => $loca,
            ':soil_type'    => $soil_type,
            ':des_farm'     => $des_farm
        ]);

        if ($result) {
            echo "<script>alert('บันทึกข้อมูลสำเร็จ'); window.location='index_plot.php';</script>";
            exit();
        } else {
            echo "<script>alert('เกิดข้อผิดพลาด');</script>";
        }
    }
}
?>



<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>หน้าแรก</title>
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../mystyle.css">


</head>

<body>
    <?php include("layout.php") ?>


    <div class="content">
        <div class="card-box">
            <div class="container mt-3">


                <h2 class="text-center">เพิ่มแปลงพืช</h2>
                <hr>

                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" class="form-addplot">
                    <div class="form-group mb-3">
                        <label for="farm_name">ชื่อแปลงพืช</label>
                        <input type="text" name="farm_name" class="form-control" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="area">ขนาดพื้นที่</label>
                        <input type="text" name="area" class="form-control" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="soil_type">ลักษณะดิน</label>
                        <input type="text" name="soil_type" class="form-control" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="water_system">ระบบน้ำที่ใช้</label>
                        <select name="water_system" class="form-control" required>
                            <option value="">-- กรุณาเลือก --</option>
                            <option value="สปริงเกอร์">สปริงเกอร์</option>
                            <option value="ระบบน้ำหยด">ระบบน้ำหยด</option>
                            <option value="ยังไม่ได้วางระบบน้ำ">ยังไม่ได้วางระบบน้ำ</option>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="loca">ที่ตั้งของแปลง</label>
                        <input type="text" name="loca" class="form-control" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="des_farm">รายละเอียด</label>
                        <input type="text" name="des_farm" class="form-control" required>
                    </div>




                    <div class="my-3">
                        <input type="submit" value="บันทึกข้อมูล" class="btn btn-success">
                        <input type="reset" value="ล้างข้อมูล" class="btn btn-danger">
                        <a href="index_plot.php" class="btn btn-primary">กลับ</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>

</html>






