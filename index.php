<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Welcome to Our Farm</title>

  <style>
    body {
      margin: 0;
      font-family: "Prompt", sans-serif;
      background-color: #f5f7f4;
      color: #333;
    }

    /* HERO SECTION */
    .hero {
      position: relative;
      width: 100%;
      height: 55vh;
      background-image: url("./img/A6.jpeg");
      background-size: cover;
      background-position: center;
      display: flex;
      justify-content: center;
      align-items: center;
      text-align: center;
      color: white;
    }

    .hero-overlay {
      position: absolute;
      inset: 0;
      background: rgba(0,0,0,0.45);
    }

    .hero-content {
      position: relative;
      z-index: 2;
      animation: fadeIn 1.2s ease;
      padding: 0 20px;
    }

    .hero h1 {
      font-size: 3rem;
      margin: 0 0 15px;
      font-weight: 600;
      text-shadow: 1px 1px 4px rgba(0,0,0,0.4);
    }

    .hero p {
      margin: 5px 0;
      font-size: 1.1rem;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }

    /* BUTTON */
    .btn-login {
      padding: 12px 30px;
      background: #ffd54f;
      border: none;
      border-radius: 10px;
      font-size: 1.1rem;
      cursor: pointer;
      margin-top: 20px;
      transition: 0.3s;
      box-shadow: 0px 4px 10px rgba(0,0,0,0.25);
    }

    .btn-login:hover {
      background: #ffca28;
      transform: translateY(-2px);
    }

    .contact-container {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      align-items: stretch;
      gap: 20px;
      padding: 50px 20px;
    }

    .contact-box {
      width: 300px;
      background: #ffffff;
      border-radius: 15px;
      padding: 20px;
      box-shadow: 0px 4px 10px rgba(0,0,0,0.15);
      transition: 0.3s;
      border-top: 5px solid #81c784;
    }

    .contact-box:hover {
      transform: translateY(-5px);
      box-shadow: 0px 8px 20px rgba(0,0,0,0.2);
    }

    .contact-box h3 {
      margin: 0 0 10px;
      font-size: 1.2rem;
      color: #2e7d32;
    }

    .contact-box p {
      margin: 0;
      font-size: 0.95rem;
      line-height: 1.5rem;
    }
  </style>
</head>

<body>
  <!-- HERO -->
  <div class="hero">
    <div class="hero-overlay"></div>
    <div class="hero-content">
      <h1>ยินดีต้อนรับ</h1>
      <p>ระบบจัดการข้อมูลและติดตามผลผลิตของสวน</p>
      <p>สำหรับเจ้าของสวนและพนักงาน</p>
      <a href="loginform.php">
        <button class="btn-login"><b>เข้าสู่ระบบ</b></button>
      </a>
    </div>
  </div>

  <!-- FEATURES -->
  <div class="contact-container">

    <div class="contact-box">
      <h3>จัดการสิทธิ์ผู้ใช้</h3>
      <p>แบ่งผู้ใช้เป็น Admin และ User เพื่อควบคุมการเข้าถึงข้อมูลอย่างปลอดภัย</p>
    </div>

    <div class="contact-box">
      <h3>จัดการข้อมูลพืชผล</h3>
      <p>บันทึกข้อมูลพืช ติดตามวันปลูก-เก็บเกี่ยว และจัดการพื้นที่ปลูกอย่างมีประสิทธิภาพ</p>
    </div>

    <div class="contact-box">
      <h3>บันทึกกิจกรรมในสวน</h3>
      <p>บันทึกกิจกรรม รดน้ำ ใส่ปุ๋ย ฉีดยา และเก็บเกี่ยวแบบเป็นระบบ</p>
    </div>

    <div class="contact-box">
      <h3>รายงาน</h3>
      <p>ดูภาพรวม วิเคราะห์ข้อมูลผ่านรายงานที่อ่านง่ายและใช้งานสะดวก</p>
    </div>

  </div>

</body>
</html>
