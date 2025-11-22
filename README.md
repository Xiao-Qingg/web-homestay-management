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
<img width="1009" height="460" alt="image" src="https://github.com/user-attachments/assets/551b341f-2556-4b8c-b32d-a9571ff813e5" />


### Trang Quản lý Homestay
<img width="1009" height="463" alt="image" src="https://github.com/user-attachments/assets/b6869569-3ca9-416f-aa81-47891eb1b1fe" />

### Trang Quản lý người dùng
<img width="1009" height="463" alt="image" src="https://github.com/user-attachments/assets/d3fadb6c-5783-4f39-9edf-54da349171e4" />

### Trang Quản lý Booking
<img width="1009" height="469" alt="image" src="https://github.com/user-attachments/assets/68aef613-c28f-401f-82fa-c00dfe09313e" />

### Trang Chi tiết Booking
<img width="1009" height="470" alt="image" src="https://github.com/user-attachments/assets/aaf2b691-466d-4da6-991e-5a25bfa83333" />


### Trang Cài đặt
<img width="1009" height="464" alt="image" src="https://github.com/user-attachments/assets/6cc07d31-8fe3-4c34-96e4-ed91317506dc" />



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
