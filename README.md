<h2 align="center">
    <a href="https://dainam.edu.vn/vi/khoa-cong-nghe-thong-tin">
    🎓 Faculty of Information Technology (DaiNam University)
    </a>
</h2>
<h2 align="center">
    Tourist Homestay Management
</h2>
<div align="center">
    <p align="center">
        <img src="https://github.com/tyanzuq2811/BTL_Quan_ly_doan_vien/raw/main/docs/logo/aiotlab_logo.png" alt="AIoTLab Logo" width="170"/>
        <img src="https://github.com/tyanzuq2811/BTL_Quan_ly_doan_vien/raw/main/docs/logo/fitdnu_logo.png" alt="AIoTLab Logo" width="180"/>
        <img src="https://github.com/tyanzuq2811/BTL_Quan_ly_doan_vien/raw/main/docs/logo/dnu_logo.png" alt="DaiNam University Logo" width="200"/>
    </p>

[![AIoTLab](https://img.shields.io/badge/AIoTLab-green?style=for-the-badge)](https://www.facebook.com/DNUAIoTLab)
[![Faculty of Information Technology](https://img.shields.io/badge/Faculty%20of%20Information%20Technology-blue?style=for-the-badge)](https://dainam.edu.vn/vi/khoa-cong-nghe-thong-tin)
[![DaiNam University](https://img.shields.io/badge/DaiNam%20University-orange?style=for-the-badge)](https://dainam.edu.vn)

</div>



## 🏡 Hệ thống Quản lý Homestay

- Dự án Web Quản lý Homestay được xây dựng nhằm hỗ trợ chủ homestay và khách hàng trong việc quản lý, đặt phòng và vận hành hệ thống homestay một cách hiệu quả, nhanh chóng và minh bạch.
Hệ thống giúp tự động hóa quy trình quản lý, giảm thiểu sai sót thủ công, và mang lại trải nghiệm thuận tiện cho cả người quản lý lẫn khách thuê.
---
## 🔧 2. Các công nghệ được sử dụng
<div align="center">


### Hệ điều hành
[![Windows](https://img.shields.io/badge/Windows-0078D6?style=for-the-badge&logo=windows&logoColor=white)](https://www.microsoft.com/en-us/windows/)
[![Ubuntu](https://img.shields.io/badge/Ubuntu-E95420?style=for-the-badge&logo=ubuntu&logoColor=white)](https://ubuntu.com/)

### Công nghệ chính
[![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)](#)
[![CSS](https://img.shields.io/badge/CSS-1572B6?style=for-the-badge&logo=css3&logoColor=white)](#)
[![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)](#)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white)](https://getbootstrap.com/)

### Web Server & Database
[![Apache](https://img.shields.io/badge/Apache-D22128?style=for-the-badge&logo=apache&logoColor=white)](https://httpd.apache.org/)
[![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com/) 
[![XAMPP](https://img.shields.io/badge/XAMPP-FB7A24?style=for-the-badge&logo=xampp&logoColor=white)](https://www.apachefriends.org/)

### Database Management Tools
[![MySQL Workbench](https://img.shields.io/badge/MySQL_Workbench-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://dev.mysql.com/downloads/workbench/)
</div>

---

## 📋 Tính năng chính

- 👤 **Đăng ký / Đăng nhập / Phân quyền người dùng**
- 🏠 **Xem danh sách homestay**
- 📅 **Đặt phòng / Hủy phòng**
- 💾 **Quản lý thông tin booking của người dùng**
- 🔒 **Bảo mật thông tin kết nối Database bằng `.env` hoặc `config_local.php`**


## Hình ảnh các chức năng
### Trang đăng nhập
<img width="916" height="422" alt="image" src="https://github.com/user-attachments/assets/e50b8e04-6340-4fb1-8c87-ae3ab62ae9aa" />

### Trang Dashboard admin
<img width="1828" height="844" alt="image" src="https://github.com/user-attachments/assets/0a586cf5-68b9-41de-b145-1b45a6c43012" />

### Trang Quản lý Homestay
<img width="1833" height="849" alt="image" src="https://github.com/user-attachments/assets/21317f30-52fb-463b-b8f7-22acdbb15f5a" />

### Trang Quản lý Booking
<img width="1851" height="844" alt="image" src="https://github.com/user-attachments/assets/5d7d5ee5-c48d-4078-ab37-088a527e468d" />

### Trang Quản lý người dùng
<img width="1852" height="855" alt="image" src="https://github.com/user-attachments/assets/e4f3e7fa-28b6-4869-a363-830285fe1826" />

### Trang Thống kê
<img width="1834" height="846" alt="image" src="https://github.com/user-attachments/assets/d38ab5b7-a178-4649-b2b9-d9602e9bcba3" />

### Trang Cài đặt
<img width="1835" height="851" alt="image" src="https://github.com/user-attachments/assets/895ab1dd-5df9-45a8-bfa1-7195fe540aaa" />


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
