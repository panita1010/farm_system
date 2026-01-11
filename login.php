<?php
session_start();
$open_connect = 1;
require 'connect.php';

$username = $_POST['username'];
$password = $_POST['password'];

$sql = "SELECT * FROM users WHERE username = :username AND password = :password";
$stmt = $conn->prepare($sql);
$stmt->execute([
    ':username' => $username,
    ':password' => $password
]);

$user = $stmt->fetch(); // ดึงแถวเดียว

if ($user) {

    if (isset($user['status_user']) && $user['status_user'] === 'inactive') {
        header("Location: loginform.php?inactive=1");
        exit();
    }

    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['fullname'] = $user['fullname'];
    $_SESSION['role'] = $user['role'];

    if ($user['role'] === 'admin') {
        header("Location: admin/index_admin.php");
    } else {
        header("Location: emp_act/index_emp.php");
    }

} else {
    echo "<script>
        alert('ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง');
        window.location='loginform.php';
    </script>";
}
?>
