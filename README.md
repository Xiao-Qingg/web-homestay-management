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

## 📂 Cấu trúc thư mục

WEB-HOMESTAY-MANAGEMENT/
├── css/
│   ├── booking.css
│   ├── dashboard.css
│   ├── homestay_detail.css
│   ├── login.css
│   └── style.css
├── functions/
│   ├── auth_functions.php
│   ├── booking_function.php
│   ├── config.php
│   ├── db_connection.php
│   ├── homestay_functions.php
│   ├── payment_function.php
│   ├── rooms_functions.php
│   └── user_functions.php
├── handlers/
│   ├── booking-process.php
│   ├── homestay-process.php
│   ├── login_process.php
│   ├── logout_process.php
│   ├── profile_process.php
│   └── user_process.php
├── images/
│   ├── sidebar.png
│   ├── sidebar2.png
│   └── sidebar3.png
├── views/
│   └── dashboard/
│   │    ├── homestay/
│   │    │   ├── create_homestay.php
│   │    │   ├── edit_homestay.php
│   │    ├── booking.php
│   │    ├── dashboard.php
│   │    ├── homestay.php
│   │    ├── menu.php
│   │    ├── user.php
│   ├── booking.php
│   ├── homestay_detail.php
│   ├── login.php
│   ├── my_bookings.php
│   ├── profile.php
│   ├── register.php
│   gitignore
│   index.php
└── README.md
