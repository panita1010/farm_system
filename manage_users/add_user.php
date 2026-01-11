<?php
session_start();
$open_connect = 1;
require('../connect.php'); 

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../loginform.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $fullname     = $_POST['fullname'];
    $username     = $_POST['username'];
    $password     = $_POST['password']; 
    $role         = $_POST['role'];
    $tel          = $_POST['tel'];
    $email        = $_POST['email'];
    $status_user  = $_POST['status_user'];

    $checkStmt = $conn->prepare(
        "SELECT 1 FROM users WHERE username = :username"
    );
    $checkStmt->execute([
        'username' => $username
    ]);

    if ($checkStmt->fetch()) {
        echo "<script>alert('ชื่อผู้ใช้นี้มีอยู่แล้ว กรุณาเลือกชื่ออื่น');</script>";
    } else {

        $stmt = $conn->prepare("
            INSERT INTO users 
            (fullname, username, password, role, tel, email, status_user)
            VALUES
            (:fullname, :username, :password, :role, :tel, :email, :status_user)
        ");

        $result = $stmt->execute([
            'fullname'    => $fullname,
            'username'    => $username,
            'password'    => $password,
            'role'        => $role,
            'tel'         => $tel,
            'email'       => $email,
            'status_user' => $status_user
        ]);

        if ($result) {
            echo "<script>
                alert('บันทึกข้อมูลสำเร็จ');
                window.location='index_user.php';
            </script>";
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


                <h2 class="text-center">ลงทะเบียนผู้ใช้งาน</h2>
                <hr>

                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" class="form-signin">
                    <div class="form-group mb-3">
                        <label for="fullname">ชื่อ-สกุล</label>
                        <input type="text" name="fullname" class="form-control" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="username">ชื่อผู้ใช้งาน</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="password">รหัสผ่าน</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="role">บทบาท</label>
                        <select name="role" class="form-control" required>
                            <option value="">-- เลือกบทบาท --</option>
                            <option value="admin">Admin</option>
                            <option value="employee">Employee</option>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="tel">เบอร์โทรศัพท์</label>
                        <input type="text" name="tel" class="form-control" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="email">อีเมล</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="status_user">สถานะการใช้งาน</label>
                        <select name="status_user" class="form-control" required>
                            <option value="">-- เลือกสถานะ --</option>
                            <option value="active">เปิดใช้งาน</option>
                            <option value="inactive">ปิดใช้งาน</option>
                        </select>
                    </div>

                    <div class="my-3">
                        <input type="submit" value="บันทึกข้อมูล" class="btn btn-success">
                        <input type="reset" value="ล้างข้อมูล" class="btn btn-danger">
                        <a href="index_user.php" class="btn btn-primary">กลับ</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>

</html>



