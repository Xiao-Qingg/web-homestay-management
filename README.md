# 🏡 Hệ thống Quản lý Homestay

Dự án **Web Quản lý Homestay** được xây dựng bằng **PHP (MySQLi)** nhằm hỗ trợ người dùng đặt phòng, quản lý thông tin homestay, và theo dõi booking dễ dàng.

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

```
Bước 1: Tải Xampp & MySQL
```
https://www.apachefriends.org/download.html
https://dev.mysql.com/downloads/workbench/


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
