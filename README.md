# 🏡 Hệ thống Quản lý Homestay

Dự án **Web Quản lý Homestay** được xây dựng bằng **PHP (MySQLi)** nhằm hỗ trợ người dùng đặt phòng, quản lý thông tin homestay, và theo dõi booking dễ dàng.


<p align="center">
  <img src="https://github.com/tyanzuq2811/BTL_Quan_ly_doan_vien/raw/main/docs/logo/aiotlab_logo.png" alt="AIoT Lab" width="250">
  <img src="./images/fit-logo.png" alt="Faculty of Information Technology" width="250">
  <img src="./images/dainam-logo.png" alt="Dai Nam University" width="250">
</p>

<p align="center">
  <strong>Faculty of Information Technology (DaiNam University)</strong>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.0-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white" alt="Bootstrap">
  <img src="https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black" alt="JavaScript">
</p>
---

## 📋 Tính năng chính

- 👤 **Đăng ký / Đăng nhập / Phân quyền người dùng**
- 🏠 **Xem danh sách homestay**
- 📅 **Đặt phòng / Hủy phòng**
- 💾 **Quản lý thông tin booking của người dùng**
- 🔒 **Bảo mật thông tin kết nối Database bằng `.env` hoặc `config_local.php`**

---

## 🧰 Công nghệ sử dụng

| Thành phần | Mô tả |
|-------------|--------|
| **Ngôn ngữ** | PHP 8.x |
| **Database** | MySQL |
| **Web Server** | Apache (XAMPP) |
| **Frontend** | HTML5, CSS3, Bootstrap |
| **Backend** | PHP thuần |

---

## ⚙️ Cài đặt và chạy dự án

Bước 1: Tải Xampp & MySQL
```
https://www.apachefriends.org/download.html 

https://dev.mysql.com/downloads/workbench/
```

Bước 2: Clone repository trong thư mục htdocs của xampp
```bash
git clone https://github.com/Xiao-Qingg/Web-Homestay-Management.git
```
Bước 3: Tạo file config.php trong folder functions
```bash
<?php
return [
    'servername' => 'your_server',
    'username' => 'your_username',
    'password' => 'your_password',
    'dbname' => 'homestay',
    'port' => your_port
];
```
