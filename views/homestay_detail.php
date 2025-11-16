<?php
require_once '../functions/auth_functions.php';
checkLogin('../views/login.php');

$functions_path = __DIR__ . '/../functions/homestay_functions.php';
if (!file_exists($functions_path)) {
    http_response_code(500);
    echo 'Lỗi: file functions/homestay_functions.php không tìm thấy.';
    exit;
}
require_once $functions_path;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra role admin
if (isset($_SESSION['role_id']) && (int)$_SESSION['role_id'] === 1) {
    session_unset();
    session_destroy();
    header("Location: ./index.php");
    exit();
}

// Lấy ID homestay từ URL
$homestay_id = (int)($_GET['id'] ?? 0);
$homestay = null;
$images = getHomestayImages($homestay_id);
$rooms = getRoomsByHomestayId($homestay_id);
$amenities_list = getAmenitiesByHomestayId($homestay_id);

if ($homestay_id > 0) {
    $homestay = getHomestayById($homestay_id);
}

// Nếu không tìm thấy homestay, redirect về trang chủ
if (!$homestay) {
    header("Location: ../index.php");
    exit();
}

$logged = isset($_SESSION['id']) || isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($homestay['homestay_name']) ?> - Homestay Paradise</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        /* ===== HOMESTAY DETAIL PAGE - COMPLETE STYLES ===== */

        :root {
            --primary-color: #4b78bc;
            --secondary-color: #295370;
            --accent-color: #ff6b6b;
            --text-dark: #2c3e50;
            --text-light: #6c757d;
            --border-color: #e0e0e0;
            --bg-light: #f8f9fa;
            --success-color: #28a745;
            --warning-color: #ffc107;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--bg-light);
            color: var(--text-dark);
            line-height: 1.6;
        }

        /* ===== MAIN CONTAINER ===== */
        .main-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 30px 20px;
        }

        /* ===== BREADCRUMB ===== */
        .breadcrumb-section {
            margin-bottom: 20px;
        }

        .breadcrumb {
            background: transparent;
            padding: 0;
            margin: 0;
            font-size: 14px;
        }

        .breadcrumb-item + .breadcrumb-item::before {
            content: "›";
            color: var(--text-light);
        }

        .breadcrumb-item a {
            color: var(--primary-color);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .breadcrumb-item a:hover {
            color: var(--secondary-color);
        }

        .breadcrumb-item.active {
            color: var(--text-light);
        }

        /* ===== GALLERY SECTION ===== */
        .gallery-section {
            margin-bottom: 40px;
            position: relative;
        }

        /* Base gallery grid */
        .gallery-grid {
            display: grid;
            gap: 12px;
            border-radius: 16px;
            overflow: hidden;
            height: 550px;
            grid-template-columns: 2fr 1fr 1fr;
            grid-template-rows: 1fr 1fr;
        }

        .gallery-item {
            position: relative;
            overflow: hidden;
            cursor: pointer;
            background: #f0f0f0;
        }

        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }

        .gallery-item:hover img {
            transform: scale(1.08);
        }

        /* Main image always spans 2 rows on the left */
        .gallery-grid .gallery-item:first-child {
            grid-column: 1;
            grid-row: 1 / 3;
        }

        /* ===== GALLERY LAYOUTS ===== */

        /* Single image only (no room images) */
        .gallery-grid.layout-1 {
            grid-template-columns: 1fr;
            grid-template-rows: 1fr;
        }

        .gallery-grid.layout-1 .gallery-item:first-child {
            grid-column: 1;
            grid-row: 1;
        }

        /* 1 room image: takes full right side */
        .gallery-grid.layout-2 .gallery-item:nth-child(2) {
            grid-column: 2 / 4;
            grid-row: 1 / 3;
        }

        /* 2 room images: stack vertically on right (R1 top, R2 bottom) */
        .gallery-grid.layout-3 .gallery-item:nth-child(2) {
            grid-column: 2 / 4;
            grid-row: 1;
        }

        .gallery-grid.layout-3 .gallery-item:nth-child(3) {
            grid-column: 2 / 4;
            grid-row: 2;
        }

        /* 3 room images: R1, R2 on top row, R3 spans bottom */
        .gallery-grid.layout-4 .gallery-item:nth-child(2) {
            grid-column: 2;
            grid-row: 1;
        }

        .gallery-grid.layout-4 .gallery-item:nth-child(3) {
            grid-column: 3;
            grid-row: 1;
        }

        .gallery-grid.layout-4 .gallery-item:nth-child(4) {
            grid-column: 2 / 4;
            grid-row: 2;
        }

        /* 4+ room images: R1, R2, R3, R4 in 2x2 grid */
        .gallery-grid.layout-5 .gallery-item:nth-child(2),
        .gallery-grid.layout-6 .gallery-item:nth-child(2) {
            grid-column: 2;
            grid-row: 1;
        }

        .gallery-grid.layout-5 .gallery-item:nth-child(3),
        .gallery-grid.layout-6 .gallery-item:nth-child(3) {
            grid-column: 3;
            grid-row: 1;
        }

        .gallery-grid.layout-5 .gallery-item:nth-child(4),
        .gallery-grid.layout-6 .gallery-item:nth-child(4) {
            grid-column: 2;
            grid-row: 2;
        }

        .gallery-grid.layout-5 .gallery-item:nth-child(5),
        .gallery-grid.layout-6 .gallery-item:nth-child(5) {
            grid-column: 3;
            grid-row: 2;
        }

        /* View all button */
        .view-all-btn {
            position: absolute;
            bottom: 24px;
            right: 24px;
            background: rgba(255, 255, 255, 0.95);
            color: var(--text-dark);
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transition: all 0.3s ease;
            border: 1px solid rgba(0,0,0,0.1);
            z-index: 10;
        }

        .view-all-btn:hover {
            background: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.2);
        }

        /* ===== GALLERY MODAL ===== */
        .gallery-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.96);
            z-index: 9999;
            overflow-y: auto;
        }

        .gallery-modal.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .modal-content-wrapper {
            max-width: 1400px;
            margin: 0 auto;
            padding: 60px 20px;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            color: white;
        }

        .modal-header h3 {
            font-size: 24px;
            font-weight: 600;
        }

        .modal-header p {
            color: rgba(255, 255, 255, 0.7);
            margin: 5px 0 0 0;
            font-size: 14px;
        }

        .modal-close {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.1);
            border: none;
            border-radius: 50%;
            color: white;
            font-size: 24px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .modal-close:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: rotate(90deg);
        }

        .modal-gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 20px;
        }

        .modal-gallery-item {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            aspect-ratio: 4/3;
            background: #1a1a1a;
        }

        .modal-gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .modal-gallery-item:hover img {
            transform: scale(1.05);
        }

        .image-caption {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
            color: white;
            padding: 20px;
            font-size: 14px;
            font-weight: 500;
        }

        /* ===== CONTENT LAYOUT ===== */
        .content-wrapper {
            display: grid;
            grid-template-columns: 1fr 420px;
            gap: 50px;
            align-items: start;
        }

        /* ===== MAIN CONTENT ===== */
        .main-content {
            background: white;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            overflow: hidden;
        }

        .content-section {
            padding: 40px;
            border-bottom: 1px solid var(--border-color);
        }

        .content-section:last-child {
            border-bottom: none;
        }

        /* ===== TITLE SECTION ===== */
        .title-section {
            padding: 40px 40px 30px;
        }

        .title-section h1 {
            font-size: 36px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 16px;
            line-height: 1.2;
        }

        .location-row {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--text-light);
            margin-bottom: 16px;
            font-size: 16px;
        }

        .location-row i {
            color: var(--accent-color);
        }

        .rating-row {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
        }

        .rating-stars {
            display: flex;
            align-items: center;
            gap: 4px;
            color: #ffc107;
            font-size: 16px;
        }

        .rating-text {
            font-weight: 600;
            color: var(--text-dark);
        }

        .review-count {
            color: var(--text-light);
            font-size: 14px;
        }

        /* ===== SECTION TITLES ===== */
        .section-title {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--text-dark);
        }

        .section-title i {
            color: var(--primary-color);
            font-size: 22px;
        }

        /* ===== DESCRIPTION SECTION ===== */
        .description-text {
            color: var(--text-dark);
            line-height: 1.8;
            font-size: 16px;
        }

        .description-text p {
            margin-bottom: 16px;
        }

        .read-more-btn {
            background: none;
            border: none;
            color: var(--primary-color);
            font-weight: 600;
            cursor: pointer;
            padding: 0;
            text-decoration: underline;
            transition: color 0.3s ease;
        }

        .read-more-btn:hover {
            color: var(--secondary-color);
        }

        /* ===== ROOM TABLE ===== */
        .room-table-wrapper {
            overflow-x: auto;
        }

        .room-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .room-table thead {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        }

        .room-table th {
            padding: 16px;
            text-align: left;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: white;
        }

        .room-table td {
            padding: 20px 16px;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-dark);
            vertical-align: top;
            font-size: 15px;
        }

        .room-table tbody tr:hover {
            background: #f8f9ff;
        }

        .room-table tbody tr:last-child td {
            border-bottom: none;
        }

        .room-name {
            font-weight: 600;
            color: var(--text-dark);
            font-size: 16px;
        }

        .room-capacity {
            display: flex;
            align-items: center;
            gap: 6px;
            color: var(--text-light);
        }

        .room-capacity i {
            color: var(--primary-color);
        }

        /* ===== AMENITIES GRID ===== */
        .amenities-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 16px;
        }

        .amenity-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 16px;
            background: var(--bg-light);
            border-radius: 10px;
            transition: all 0.3s ease;
            border: 1px solid transparent;
        }

        .amenity-item:hover {
            background: #e9ecef;
            border-color: var(--primary-color);
            transform: translateY(-2px);
        }

        .amenity-icon {
            font-size: 22px;
            color: var(--primary-color);
            width: 30px;
            text-align: center;
        }

        .amenity-text {
            font-size: 15px;
            color: var(--text-dark);
        }

        /* ===== HOST SECTION ===== */
        .host-card {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 24px;
            background: var(--bg-light);
            border-radius: 12px;
        }

        .host-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 28px;
            font-weight: 700;
            flex-shrink: 0;
        }

        .host-info h3 {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 6px;
            color: var(--text-dark);
        }

        .host-info p {
            color: var(--text-light);
            margin: 0;
            font-size: 14px;
        }

        .host-stats {
            display: flex;
            gap: 20px;
            margin-top: 12px;
        }

        .host-stat {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 14px;
            color: var(--text-light);
        }

        .host-stat i {
            color: var(--primary-color);
        }

        /* ===== REVIEWS SECTION ===== */
        .reviews-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .review-summary {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .review-score {
            font-size: 32px;
            font-weight: 700;
            color: var(--text-dark);
        }

        .review-card {
            padding: 24px;
            background: var(--bg-light);
            border-radius: 12px;
            margin-bottom: 16px;
            border: 1px solid var(--border-color);
        }

        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .reviewer-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .reviewer-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 16px;
        }

        .reviewer-name {
            font-weight: 600;
            color: var(--text-dark);
            font-size: 16px;
        }

        .review-date {
            color: var(--text-light);
            font-size: 13px;
        }

        .review-stars {
            color: #ffc107;
            font-size: 14px;
        }

        .review-text {
            color: var(--text-dark);
            line-height: 1.7;
            margin-top: 12px;
            font-size: 15px;
        }

        /* ===== SIDEBAR - BOOKING CARD ===== */
        .sidebar {
            position: sticky;
            top: 100px;
        }

        .booking-card {
            background: white;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 6px 24px rgba(0,0,0,0.12);
            border: 1px solid var(--border-color);
        }

        .price-section {
            display: flex;
            align-items: baseline;
            gap: 8px;
            margin-bottom: 24px;
            padding-bottom: 24px;
            border-bottom: 1px solid var(--border-color);
        }

        .price {
            font-size: 32px;
            font-weight: 700;
            color: var(--text-dark);
        }

        .price-currency {
            font-size: 20px;
            color: var(--text-light);
        }

        .price-label {
            color: var(--text-light);
            font-size: 16px;
        }

        /* Date Picker */
        .date-picker {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 20px;
        }

        .date-group label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-light);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .date-input {
            width: 100%;
            padding: 12px 14px;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .date-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(75, 120, 188, 0.1);
        }

        /* Guest Selector */
        .guest-selector {
            padding: 16px;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }

        .guest-selector:hover {
            border-color: var(--primary-color);
        }

        .guest-label {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-light);
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .guest-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .guest-text {
            font-weight: 600;
            color: var(--text-dark);
        }

        .guest-controls {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .guest-btn {
            width: 36px;
            height: 36px;
            border: 2px solid var(--primary-color);
            background: white;
            border-radius: 50%;
            cursor: pointer;
            font-size: 18px;
            font-weight: 600;
            color: var(--primary-color);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .guest-btn:hover:not(:disabled) {
            background: var(--primary-color);
            color: white;
            transform: scale(1.1);
        }

        .guest-btn:disabled {
            opacity: 0.3;
            cursor: not-allowed;
            border-color: var(--text-light);
            color: var(--text-light);
        }

        .guest-count {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-dark);
            min-width: 30px;
            text-align: center;
        }

        /* Booking Button */
        .btn-booking {
            width: 100%;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            border: none;
            padding: 16px;
            border-radius: 10px;
            font-size: 17px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 4px 15px rgba(75, 120, 188, 0.3);
        }

        .btn-booking:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(75, 120, 188, 0.4);
        }

        .btn-booking:active {
            transform: translateY(-1px);
        }

        .booking-note {
            text-align: center;
            margin-top: 16px;
            color: var(--text-light);
            font-size: 13px;
        }

        /* Price Breakdown */
        .price-breakdown {
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid var(--border-color);
        }

        .price-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            color: var(--text-dark);
            font-size: 15px;
        }

        .price-row-label {
            color: var(--text-light);
        }

        .price-total {
            display: flex;
            justify-content: space-between;
            font-weight: 700;
            font-size: 18px;
            margin-top: 16px;
            padding-top: 16px;
            border-top: 2px solid var(--border-color);
            color: var(--text-dark);
        }

        /* ===== RESPONSIVE DESIGN ===== */
        @media (max-width: 1200px) {
            .content-wrapper {
                grid-template-columns: 1fr 380px;
                gap: 40px;
            }

            .modal-gallery-grid {
                grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            }
        }

        @media (max-width: 992px) {
            .content-wrapper {
                grid-template-columns: 1fr;
                gap: 30px;
            }

            .sidebar {
                position: relative;
                top: 0;
            }

            .gallery-grid {
                height: 450px;
            }

            .title-section h1 {
                font-size: 28px;
            }

            .modal-gallery-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .main-container {
                padding: 20px 15px;
            }

            .gallery-grid {
                grid-template-columns: 1fr !important;
                grid-template-rows: 300px repeat(auto-fit, 200px) !important;
                height: auto !important;
            }

            /* Reset all grid positions on mobile */
            .gallery-grid .gallery-item {
                grid-column: 1 !important;
                grid-row: auto !important;
            }

            .view-all-btn {
                bottom: 16px;
                right: 16px;
                padding: 10px 18px;
                font-size: 13px;
            }

            .content-section {
                padding: 24px;
            }

            .title-section {
                padding: 24px;
            }

            .title-section h1 {
                font-size: 24px;
            }

            .section-title {
                font-size: 20px;
            }

            .booking-card {
                padding: 24px;
            }

            .price {
                font-size: 28px;
            }

            .date-picker {
                grid-template-columns: 1fr;
            }

            .amenities-grid {
                grid-template-columns: 1fr;
            }

            .room-table {
                font-size: 14px;
            }

            .room-table th,
            .room-table td {
                padding: 12px;
            }

            .modal-content-wrapper {
                padding: 40px 15px;
            }

            .modal-gallery-grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }

            .host-card {
                flex-direction: column;
                text-align: center;
            }

            .host-stats {
                justify-content: center;
            }
        }

        @media (max-width: 480px) {
            .title-section h1 {
                font-size: 20px;
            }

            .rating-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }

            .guest-controls {
                gap: 12px;
            }

            .guest-btn {
                width: 32px;
                height: 32px;
                font-size: 16px;
            }

            .guest-count {
                font-size: 16px;
            }
        }
    </style>
    <link rel="stylesheet" href="/web-homestay-management/assets/css/homestay_detail.css">
</head>
<body>
    <!-- Header -->
    <?php include '../views/header.php'; ?>

    <!-- Main Container -->
    <div class="main-container">
        <!-- Gallery Section -->
        <div class="gallery-section">
            <?php 
            // Only count room images (not including main homestay image)
            $roomImagesCount = count($images);
            $totalImages = $roomImagesCount + 1; // +1 for main image (for modal display)
            
            // Determine layout based on ROOM images count only
            if ($roomImagesCount == 0) {
                $layoutClass = 'layout-1'; // Only main image
                $displayImages = [];
                $showViewAll = false;
            } elseif ($roomImagesCount == 1) {
                $layoutClass = 'layout-2'; // Main + 1 room
                $displayImages = $images;
                $showViewAll = false;
            } elseif ($roomImagesCount == 2) {
                $layoutClass = 'layout-3'; // Main + 2 rooms (stacked vertically)
                $displayImages = $images;
                $showViewAll = false;
            } elseif ($roomImagesCount == 3) {
                $layoutClass = 'layout-4'; // Main + 3 rooms
                $displayImages = $images;
                $showViewAll = false;
            } elseif ($roomImagesCount == 4) {
                $layoutClass = 'layout-5'; // Main + 4 rooms (2x2 grid)
                $displayImages = $images;
                $showViewAll = false;
            } else {
                // 5+ room images: show first 4 room images + view all button
                $layoutClass = 'layout-6';
                $displayImages = array_slice($images, 0, 4);
                $showViewAll = true;
            }
            ?>
            
            <div class="gallery-grid <?= $layoutClass ?>">
                <!-- Main Homestay Image -->
                <div class="gallery-item" onclick="openGalleryModal()">
                    <img src="<?= htmlspecialchars($homestay['image_url']) ?>" 
                         alt="<?= htmlspecialchars($homestay['homestay_name']) ?>">
                </div>
                
                <!-- Room Images -->
                <?php foreach ($displayImages as $index => $img): ?>
                    <div class="gallery-item" onclick="openGalleryModal()">
                        <img src="<?= htmlspecialchars($img['room_image_url']) ?>" 
                             alt="Ảnh phòng <?= $index + 1 ?>">
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- View All Button -->
            <?php if ($showViewAll): ?>
                <div class="view-all-btn" onclick="openGalleryModal()">
                    <i class="fas fa-th"></i>
                    <span>Xem tất cả ảnh</span>
                </div>
            <?php endif; ?>
        </div>

        <!-- Gallery Modal -->
        <div class="gallery-modal" id="galleryModal">
            <div class="modal-content-wrapper">
                <div class="modal-header">
                    <div>
                        <h3><?= htmlspecialchars($homestay['homestay_name']) ?></h3>
                        <p><?= $totalImages ?> hình ảnh</p>
                    </div>
                    <button class="modal-close" onclick="closeGalleryModal()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="modal-gallery-grid">
                    <!-- Main Image -->
                    <div class="modal-gallery-item">
                        <img src="<?= htmlspecialchars($homestay['image_url']) ?>" 
                             alt="<?= htmlspecialchars($homestay['homestay_name']) ?>">
                        <div class="image-caption">Ảnh đại diện homestay</div>
                    </div>
                    
                    <!-- All Room Images -->
                    <?php foreach ($images as $index => $img): ?>
                        <div class="modal-gallery-item">
                            <img src="<?= htmlspecialchars($img['room_image_url']) ?>" 
                                 alt="Ảnh phòng <?= $index + 1 ?>">
                            <div class="image-caption">Ảnh phòng <?= $index + 1 ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Main Content -->
            <div class="main-content">
                <!-- Title Section -->
                <div class="title-section">
                    <h1><?= htmlspecialchars($homestay['homestay_name']) ?></h1>
                    
                    <div class="location-row">
                        <i class="fas fa-map-marker-alt"></i>
                        <span><?= htmlspecialchars($homestay['location']) ?></span>
                    </div>
                    
                    <div class="rating-row">
                        <div class="rating-stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <span class="rating-text">5.0</span>
                        <span class="review-count">(2 đánh giá)</span>
                    </div>
                </div>

                <!-- Description Section -->
                <div class="content-section">
                    <h2 class="section-title">
                        <i class="fas fa-align-left"></i>
                        Mô tả
                    </h2>
                    <div class="description-text">
                        <p>Homestay được thiết kế theo phong cách hiện đại, ấm cúng và gần gũi, mang đến không gian lưu trú thoải mái cho mọi du khách. Các phòng đều sạch sẽ, đầy đủ tiện nghi như máy lạnh, TV, WiFi và phòng tắm riêng. Khu vực sân vườn thoáng mát, thích hợp để thư giãn hoặc thưởng thức cà phê buổi sáng. Với vị trí thuận tiện và dịch vụ thân thiện, homestay là lựa chọn lý tưởng cho kỳ nghỉ nhẹ nhàng và trọn vẹn.</p>
                    </div>
                </div>

                <!-- Room Info Section -->
                <div class="content-section">
                    <h2 class="section-title">
                        <i class="fas fa-bed"></i>
                        Phòng có sẵn
                    </h2>
                    <div class="room-table-wrapper">
                        <table class="room-table">
                            <thead>
                                <tr>
                                    <th>Tên phòng</th>
                                    <th>Sức chứa</th>
                                    <th>Mô tả</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rooms as $room): ?>
                                    <tr>
                                        <td>
                                            <div class="room-name">
                                                <?= htmlspecialchars($room['room_name']) ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="room-capacity">
                                                <i class="fas fa-user"></i>
                                                <span><?= htmlspecialchars($room['capacity']) ?> người</span>
                                            </div>
                                        </td>
                                        <td><?= htmlspecialchars($room['description']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Amenities Section -->
                <div class="content-section">
                    <h2 class="section-title">
                        <i class="fas fa-star"></i>
                        Tiện nghi
                    </h2>
                    <div class="amenities-grid">
                        <?php foreach ($amenities_list as $amenity): ?>
                            <div class="amenity-item">
                                <i class="amenity-icon fas fa-check-circle"></i>
                                <span class="amenity-text"><?= htmlspecialchars($amenity) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Host Section -->
                <div class="content-section">
                    <h2 class="section-title">
                        <i class="fas fa-user-circle"></i>
                        Chủ nhà
                    </h2>
                    <div class="host-card">
                        <div class="host-avatar">NH</div>
                        <div class="host-info">
                            <h3>Nguyễn Hoàng</h3>
                            <p>Chủ nhà · Tham gia từ 2023</p>
                            <div class="host-stats">
                                <div class="host-stat">
                                    <i class="fas fa-star"></i>
                                    <span>15 đánh giá</span>
                                </div>
                                <div class="host-stat">
                                    <i class="fas fa-shield-alt"></i>
                                    <span>Đã xác minh</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reviews Section -->
                <div class="content-section">
                    <div class="reviews-header">
                        <h2 class="section-title">
                            <i class="fas fa-comments"></i>
                            Đánh giá từ khách
                        </h2>
                        <div class="review-summary">
                            <span class="review-score">5.0</span>
                            <div class="rating-stars">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="review-card">
                        <div class="review-header">
                            <div class="reviewer-info">
                                <div class="reviewer-avatar">TM</div>
                                <div>
                                    <div class="reviewer-name">Trần Minh Anh</div>
                                    <div class="review-date">Tháng 10, 2025</div>
                                </div>
                            </div>
                            <div class="review-stars">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                        <p class="review-text">
                            Chỗ ở tuyệt vời! View đẹp không tì vết, nhà cửa sạch sẽ và chủ nhà rất thân thiện. 
                            Chúng tôi đã có kỳ nghỉ tuyệt vời tại đây. Rất đáng để quay lại!
                        </p>
                    </div>
                    
                    <div class="review-card">
                        <div class="review-header">
                            <div class="reviewer-info">
                                <div class="reviewer-avatar">LV</div>
                                <div>
                                    <div class="reviewer-name">Lê Văn Bình</div>
                                    <div class="review-date">Tháng 9, 2025</div>
                                </div>
                            </div>
                            <div class="review-stars">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                        <p class="review-text">
                            Không gian yên tĩnh, thoáng mát. Phù hợp cho gia đình. Bếp đầy đủ tiện nghi, 
                            chúng tôi tự nấu ăn rất tiện. Sẽ giới thiệu cho bạn bè!
                        </p>
                    </div>
                </div>
            </div>

            <!-- Sidebar - Booking Card -->
            <div class="sidebar">
                <form action="./booking.php" method="POST" id="bookingForm">
                    <div class="booking-card">
                        <div class="price-section">
                            <span class="price"><?= number_format($homestay['price_per_night']) ?></span>
                            <span class="price-currency">đ</span>
                            <span class="price-label">/ đêm</span>
                        </div>

                        <div class="date-picker">
                            <div class="date-group">
                                <label for="checkin">Nhận phòng</label>
                                <input type="date" 
                                       class="date-input" 
                                       id="checkin" 
                                       name="checkin"
                                       value="<?= date('Y-m-d') ?>"
                                       min="<?= date('Y-m-d') ?>"
                                       required>
                            </div>
                            <div class="date-group">
                                <label for="checkout">Trả phòng</label>
                                <input type="date" 
                                       class="date-input" 
                                       id="checkout" 
                                       name="checkout"
                                       value="<?= date('Y-m-d', strtotime('+1 days')) ?>"
                                       min="<?= date('Y-m-d', strtotime('+1 days')) ?>"
                                       required>
                            </div>
                        </div>

                        <div class="guest-selector">
                            <label class="guest-label">Số khách</label>
                            <div class="guest-row">
                                <span class="guest-text">Khách</span>
                                <div class="guest-controls">
                                    <button type="button" class="guest-btn" id="decreaseGuests">−</button>
                                    <span class="guest-count" id="guestCount">1</span>
                                    <button type="button" class="guest-btn" id="increaseGuests">+</button>
                                </div>
                            </div>
                        </div>

                        <!-- Hidden inputs -->
                        <input type="hidden" name="homestay_id" value="<?= htmlspecialchars($homestay['id']) ?>">
                        <input type="hidden" name="homestay_name" value="<?= htmlspecialchars($homestay['homestay_name']) ?>">
                        <input type="hidden" name="price_per_night" value="<?= $homestay['price_per_night'] ?>">
                        <input type="hidden" name="guests" id="guestsInput" value="1">
                        <input type="hidden" name="nights" id="nightsInput" value="1">
                        <input type="hidden" name="subtotal" id="subtotalInput" value="<?= $homestay['price_per_night'] ?>">
                        <input type="hidden" name="total" id="totalInput" value="<?= $homestay['price_per_night'] ?>">

                        <button type="submit" class="btn-booking">
                            <i class="fas fa-calendar-check"></i>
                            <span>Đặt phòng ngay</span>
                        </button>

                        <div class="booking-note">
                            Bạn sẽ chưa bị tính phí
                        </div>

                        <div class="price-breakdown">
                            <div class="price-row">
                                <span class="price-row-label" id="priceCalc">
                                    <?= number_format($homestay['price_per_night']) ?>đ × 1 đêm
                                </span>
                                <span id="subtotalDisplay"><?= number_format($homestay['price_per_night']) ?>đ</span>
                            </div>
                            
                            <div class="price-total">
                                <span>Tổng cộng</span>
                                <span id="totalDisplay"><?= number_format($homestay['price_per_night']) ?>đ</span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include '../views/footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom Scripts -->
    <script>
        // Gallery Modal Functions
        function openGalleryModal() {
            const modal = document.getElementById('galleryModal');
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeGalleryModal() {
            const modal = document.getElementById('galleryModal');
            modal.classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        // Close modal when clicking outside
        document.getElementById('galleryModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeGalleryModal();
            }
        });

        // Close modal on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeGalleryModal();
            }
        });

        // Guest Counter
        let guestCount = 1;
        const pricePerNight = <?= $homestay['price_per_night'] ?>;
        const maxGuests = <?= $homestay['max_guests'] ?? 10 ?>;

        const decreaseBtn = document.getElementById('decreaseGuests');
        const increaseBtn = document.getElementById('increaseGuests');
        const guestCountDisplay = document.getElementById('guestCount');
        const guestsInput = document.getElementById('guestsInput');

        decreaseBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (guestCount > 1) {
                guestCount--;
                updateGuestDisplay();
            }
        });

        increaseBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (guestCount < maxGuests) {
                guestCount++;
                updateGuestDisplay();
            }
        });

        function updateGuestDisplay() {
            guestCountDisplay.textContent = guestCount;
            guestsInput.value = guestCount;
            decreaseBtn.disabled = guestCount <= 1;
            increaseBtn.disabled = guestCount >= maxGuests;
        }

        // Calculate nights
        function calculateNights() {
            const checkinDate = new Date(document.getElementById('checkin').value);
            const checkoutDate = new Date(document.getElementById('checkout').value);
            const timeDiff = checkoutDate - checkinDate;
            const nights = Math.ceil(timeDiff / (1000 * 60 * 60 * 24));
            return nights > 0 ? nights : 1;
        }

        // Update price display
        function updatePrice() {
            const nights = calculateNights();
            const subtotal = pricePerNight * nights;
            const total = subtotal;

            // Format number with Vietnamese locale
            const formatVND = (num) => num.toLocaleString('vi-VN');

            document.getElementById('priceCalc').textContent = 
                `${formatVND(pricePerNight)}đ × ${nights} đêm`;
            document.getElementById('subtotalDisplay').textContent = 
                `${formatVND(subtotal)}đ`;
            document.getElementById('totalDisplay').textContent = 
                `${formatVND(total)}đ`;

            // Update hidden inputs
            document.getElementById('nightsInput').value = nights;
            document.getElementById('subtotalInput').value = subtotal;
            document.getElementById('totalInput').value = total;
        }

        // Date change handlers
        document.getElementById('checkin').addEventListener('change', function() {
            const checkin = new Date(this.value);
            const checkout = document.getElementById('checkout');
            const minCheckout = new Date(checkin);
            minCheckout.setDate(minCheckout.getDate() + 1);
            checkout.min = minCheckout.toISOString().split('T')[0];
            
            if (new Date(checkout.value) <= checkin) {
                checkout.value = minCheckout.toISOString().split('T')[0];
            }
            updatePrice();
        });

        document.getElementById('checkout').addEventListener('change', updatePrice);

        // Form validation
        document.getElementById('bookingForm').addEventListener('submit', function(e) {
            const checkin = new Date(document.getElementById('checkin').value);
            const checkout = new Date(document.getElementById('checkout').value);
            
            if (checkout <= checkin) {
                e.preventDefault();
                alert('Ngày trả phòng phải sau ngày nhận phòng!');
                return false;
            }
            
            const nights = calculateNights();
            if (nights < 1) {
                e.preventDefault();
                alert('Vui lòng chọn ngày hợp lệ!');
                return false;
            }
        });

        // Initialize
        updateGuestDisplay();
        updatePrice();
    </script>
</body>
</html>