<?php
session_start();
$open_connect = 1;
require('../connect.php'); 
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../loginform.php");
    exit();
}

$stmt = $conn->query("SELECT * FROM users ORDER BY user_id ASC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

$count = count($users);
$order = 1;
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>จัดการผู้ใช้งาน</title>
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../mystyle.css">
</head>

<body>

<?php include("layout.php"); ?>

<div class="content">
    <div class="card-box">

        <div class="d-flex justify-content-end mt-3">
            <a href="add_user.php" class="btn btn-primary">+ เพิ่มผู้ใช้งาน</a>
        </div>

        <?php if ($count > 0) { ?>
            <div class="container mt-3">
                <table class="table table-bordered">
                    <thead class="table-success">
                        <tr>
                            <th>#</th>
                            <th>ชื่อ-สกุล</th>
                            <th>เบอร์โทรศัพท์</th>
                            <th>ชื่อผู้ใช้งาน</th>
                            <th>อีเมล</th>
                            <th>บทบาท</th>
                            <th>สถานะ</th>
                            <th>แก้ไข</th>
                            <th>ลบ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $row) { ?>
                            <tr>
                                <td><?= $order++; ?></td>
                                <td><?= htmlspecialchars($row['fullname']); ?></td>
                                <td><?= htmlspecialchars($row['tel']); ?></td>
                                <td><?= htmlspecialchars($row['username']); ?></td>
                                <td><?= htmlspecialchars($row['email']); ?></td>
                                <td><?= htmlspecialchars($row['role']); ?></td>
                                <td><?= htmlspecialchars($row['status_user']); ?></td>

                                <td>
                                    <a href="edit_user.php?user_id=<?= $row['user_id']; ?>"
                                       class="btn btn-success btn-sm">
                                        แก้ไข
                                    </a>
                                </td>

                                <td>
                                    <a href="delete_user.php?user_id=<?= $row['user_id']; ?>"
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('ยืนยันการลบข้อมูล')">
                                        ลบ
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } else { ?>
            <p class="text-center mt-3">ยังไม่มีผู้ใช้งาน</p>
        <?php } ?>

    </div>
</div>

</body>
</html>
