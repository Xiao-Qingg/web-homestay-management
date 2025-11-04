<?php
require_once 'db_connection.php';


/**
 * Lấy tất cả users có role_id = 2 (khách hàng)
 */
function getAllUsers() {
    $conn = getDbConnection();
    
    // Truy vấn chỉ lấy users có role_id = 2
    $sql = "SELECT id, fullname, username, phone, address, created_at, role_id, status 
            FROM users 
            WHERE role_id = 2 
            ORDER BY id DESC";
    $result = mysqli_query($conn, $sql);
    
    $users = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) { 
            $users[] = $row;
        }
    }
    
    mysqli_close($conn);
    return $users;
}


/**
 * Lấy users theo role_id
 */
function getUsersByRole($role_id) {
    $conn = getDbConnection();
    
    $sql = "SELECT id, fullname, username, phone, address, created_at, role_id, status
            FROM users 
            WHERE role_id = ? 
            ORDER BY id DESC";
    $stmt = mysqli_prepare($conn, $sql);
    
    $users = [];
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $role_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        while ($row = mysqli_fetch_assoc($result)) {
            $users[] = $row;
        }
        
        mysqli_stmt_close($stmt);
    }
    
    mysqli_close($conn);
    return $users;
}

/**
 * Thêm user mới
 */
function addUser($fullname, $username, $password, $phone, $address) {
    $conn = getDbConnection();
    
    // Mặc định role_id = 2 (user thường)
    $sql = "INSERT INTO users (fullname, username, password, phone, address, role_id, created_at, status) 
            VALUES (?, ?, ?, ?, ?, 2, NOW(), 'Hoạt động')";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sssss", $fullname, $username, $password, $phone, $address);
        $success = mysqli_stmt_execute($stmt);
        
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }
    
    mysqli_close($conn);
    return false;
}

/**
 * Lấy thông tin một user theo ID
 */
function getUserById($id) {
    $conn = getDbConnection();
    
    $sql = "SELECT id, fullname, username, phone, address, created_at, role_id, status 
            FROM users 
            WHERE id = ? 
            LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return $user;
        }
        
        mysqli_stmt_close($stmt);
    }
    
    mysqli_close($conn);
    return null;
}

/**
 * Cập nhật thông tin user
 */
function updateUser($id, $fullname, $username, $phone, $address) {
    $conn = getDbConnection();
    
    $sql = "UPDATE users 
            SET fullname = ?, username = ?, phone = ?, address = ? 
            WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssssi", $fullname, $username, $phone, $address, $id);
        $success = mysqli_stmt_execute($stmt);
        
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }
    
    mysqli_close($conn);
    return false;
}

/**
 * Xóa user theo ID
 */
function deleteUser($id) {
    $conn = getDbConnection();
    
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        $success = mysqli_stmt_execute($stmt);
        
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }
    
    mysqli_close($conn);
    return false;
}

/**
 * Khóa/Mở khóa tài khoản user
 */
function toggleUserStatus($id) {
    $conn = getDbConnection();
    
    // Lấy status hiện tại
    $sql_get = "SELECT status FROM users WHERE id = ?";
    $stmt_get = mysqli_prepare($conn, $sql_get);
    
    if ($stmt_get) {
        mysqli_stmt_bind_param($stmt_get, "i", $id);
        mysqli_stmt_execute($stmt_get);
        $result = mysqli_stmt_get_result($stmt_get);
        $user = mysqli_fetch_assoc($result);
        
        // Đổi status
        $new_status = ($user['status'] === 'Hoạt động') ? 'Bị khóa' : 'Hoạt động';
        
        $sql_update = "UPDATE users SET status = ? WHERE id = ?";
        $stmt_update = mysqli_prepare($conn, $sql_update);
        mysqli_stmt_bind_param($stmt_update, "si", $new_status, $id);
        $success = mysqli_stmt_execute($stmt_update);
        
        mysqli_stmt_close($stmt_get);
        mysqli_stmt_close($stmt_update);
        mysqli_close($conn);
        return $success;
    }
    
    mysqli_close($conn);
    return false;
}

function updateUserProfile($id, $fullname, $phone, $address) {
    $conn = getDbConnection();

    $sql = "UPDATE users SET fullname = ?, phone = ?, address = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sssi", $fullname, $phone, $address, $id);
        $success = mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }

    mysqli_close($conn);
    return false;
}


?>