<?php
session_start();
$open_connect = 1;
require('../connect.php'); 

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../loginform.php");
    exit();
}

if (!isset($_GET['user_id'])) {
    header("Location: index_user.php");
    exit();
}

$user_id = $_GET['user_id'];

$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = :user_id");
$stmt->execute(['user_id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "<script>alert('ไม่พบข้อมูลผู้ใช้'); window.location='index_user.php';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    $tel = $_POST['tel'];
    $email = $_POST['email'];
    $status_user = $_POST['status_user'];

    $check = $conn->prepare("
        SELECT 1 FROM users 
        WHERE username = :username AND user_id != :user_id
    ");
    $check->execute([
        'username' => $username,
        'user_id' => $user_id
    ]);

    if ($check->rowCount() > 0) {
        echo "<script>alert('ชื่อผู้ใช้นี้มีอยู่แล้ว กรุณาเลือกชื่ออื่น');</script>";
    } else {

        if (!empty($password)) {
            $sql = "
                UPDATE users SET
                    fullname = :fullname,
                    username = :username,
                    password = :password,
                    role = :role,
                    tel = :tel,
                    email = :email,
                    status_user = :status_user
                WHERE user_id = :user_id
            ";

            $params = [
                'fullname' => $fullname,
                'username' => $username,
                'password' => $password,
                'role' => $role,
                'tel' => $tel,
                'email' => $email,
                'status_user' => $status_user,
                'user_id' => $user_id
            ];
        } else {
            $sql = "
                UPDATE users SET
                    fullname = :fullname,
                    username = :username,
                    role = :role,
                    tel = :tel,
                    email = :email,
                    status_user = :status_user
                WHERE user_id = :user_id
            ";

            $params = [
                'fullname' => $fullname,
                'username' => $username,
                'role' => $role,
                'tel' => $tel,
                'email' => $email,
                'status_user' => $status_user,
                'user_id' => $user_id
            ];
        }

        $stmt = $conn->prepare($sql);
        if ($stmt->execute($params)) {
            echo "<script>alert('อัพเดทข้อมูลสำเร็จ'); window.location='index_user.php';</script>";
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
    <title>แก้ไขผู้ใช้งาน</title>
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../mystyle.css">
</head>

<body>

<?php include("layout.php"); ?>

<div class="content">
    <div class="card-box">
        <div class="container mt-3">

            <h2 class="text-center">แก้ไขข้อมูลผู้ใช้งาน</h2>
            <hr>

            <form method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) . '?user_id=' . $user_id; ?>">

                <div class="mb-3">
                    <label>ชื่อ-สกุล</label>
                    <input type="text" name="fullname" class="form-control"
                        value="<?= htmlspecialchars($user['fullname']); ?>" required>
                </div>

                <div class="mb-3">
                    <label>ชื่อผู้ใช้งาน</label>
                    <input type="text" name="username" class="form-control"
                        value="<?= htmlspecialchars($user['username']); ?>" required>
                </div>

                <div class="mb-3">
                    <label>รหัสผ่านใหม่ (เว้นว่างถ้าไม่เปลี่ยน)</label>
                    <input type="password" name="password" class="form-control">
                </div>

                <div class="mb-3">
                    <label>บทบาท</label>
                    <select name="role" class="form-control">
                        <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                        <option value="employee" <?= $user['role'] === 'employee' ? 'selected' : ''; ?>>Employee</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label>เบอร์โทรศัพท์</label>
                    <input type="text" name="tel" class="form-control"
                        value="<?= htmlspecialchars($user['tel']); ?>" required>
                </div>

                <div class="mb-3">
                    <label>อีเมล</label>
                    <input type="email" name="email" class="form-control"
                        value="<?= htmlspecialchars($user['email']); ?>" required>
                </div>

                <div class="mb-3">
                    <label>สถานะการใช้งาน</label>
                    <select name="status_user" class="form-control">
                        <option value="active" <?= $user['status_user'] === 'active' ? 'selected' : ''; ?>>เปิดใช้งาน</option>
                        <option value="inactive" <?= $user['status_user'] === 'inactive' ? 'selected' : ''; ?>>ปิดใช้งาน</option>
                    </select>
                </div>

                <div class="my-3">
                    <button type="submit" class="btn btn-success">บันทึกการแก้ไข</button>
                    <a href="index_user.php" class="btn btn-secondary">ยกเลิก</a>
                </div>

            </form>
        </div>
    </div>
</div>

</body>
</html>
