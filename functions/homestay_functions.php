<?php
require_once 'db_connection.php';

/**
 * Lấy tất cả danh sách homestays từ database
 */
function getAllHomestays() {
    $conn = getDbConnection();
    
    $sql = "SELECT id, homestay_name, location, price_per_night, num_room, max_people, image_url, status 
            FROM homestays ORDER BY id DESC";
    $result = mysqli_query($conn, $sql);
    
    $homestays = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) { 
            $homestays[] = $row;
        }
    }
    
    mysqli_close($conn);
    return $homestays;
}

/**
 * Thêm homestay mới với đầy đủ rooms, amenities và images
 */
function addHomestayComplete($homestay_name, $location, $price_per_night, $num_room, $max_people, $image_url, $status, $rooms, $amenities, $images) {
    $conn = getDbConnection();
    if (!$conn) {
        error_log("❌ Cannot connect to database");
        return false;
    }

    mysqli_begin_transaction($conn);
    try {
        error_log("=== ADD HOMESTAY START ===");
        
        // 1️⃣ Thêm homestay
        $sql = "INSERT INTO homestays (homestay_name, location, price_per_night, num_room, max_people, image_url, status)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssdiiss", $homestay_name, $location, $price_per_night, $num_room, $max_people, $image_url, $status);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Insert homestay error: " . mysqli_stmt_error($stmt));
        }
        
        $homestay_id = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);
        error_log("✓ Created homestay #$homestay_id");

        // 2️⃣ ✅ FIXED: Validate số phòng phải khớp với num_room (không cứng >= 4)
        $num_room_int = (int)$num_room;
        if (count($rooms) !== $num_room_int) {
            throw new Exception("Số phòng không khớp! Yêu cầu: {$num_room_int}, Thực tế: " . count($rooms));
        }
        
        $room_ids = [];
        foreach ($rooms as $r) {
            if (!isset($r['name']) || !isset($r['capacity'])) {
                throw new Exception("Dữ liệu phòng không hợp lệ: thiếu name hoặc capacity");
            }
            
            $sql = "INSERT INTO rooms (room_name, description, capacity) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            $desc = isset($r['description']) ? $r['description'] : '';
            mysqli_stmt_bind_param($stmt, "ssi", $r['name'], $desc, $r['capacity']);
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Lỗi thêm phòng: " . mysqli_stmt_error($stmt));
            }
            
            $room_ids[] = mysqli_insert_id($conn);
            mysqli_stmt_close($stmt);
        }
        error_log("✓ Created " . count($room_ids) . " rooms");

        // 3️⃣ Xử lý tiện ích
        $amenity_ids = [];
        if (!empty($amenities) && is_array($amenities)) {
            foreach ($amenities as $a) {
                if (empty($a)) continue; // Bỏ qua amenity rỗng
                
                // Kiểm tra amenity đã tồn tại chưa
                $sql = "SELECT id FROM amenities WHERE name=? LIMIT 1";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "s", $a);
                mysqli_stmt_execute($stmt);
                $res = mysqli_stmt_get_result($stmt);
                
                if ($row = mysqli_fetch_assoc($res)) {
                    $amenity_ids[] = $row['id'];
                } else {
                    // Tạo amenity mới
                    mysqli_stmt_close($stmt);
                    $sql2 = "INSERT INTO amenities (name) VALUES (?)";
                    $stmt2 = mysqli_prepare($conn, $sql2);
                    mysqli_stmt_bind_param($stmt2, "s", $a);
                    mysqli_stmt_execute($stmt2);
                    $amenity_ids[] = mysqli_insert_id($conn);
                    mysqli_stmt_close($stmt2);
                    continue;
                }
                mysqli_stmt_close($stmt);
            }
        }
        
        // Nếu không có amenity nào, dùng NULL
        if (empty($amenity_ids)) {
            $amenity_ids[] = null;
        }
        error_log("✓ Processed " . count($amenity_ids) . " amenities");

        // 4️⃣ Validate và thêm ảnh
        if (count($images) < 1) {
            throw new Exception("Cần ít nhất 1 ảnh");
        }
        
        $image_ids = [];
        foreach ($images as $img) {
            if (!isset($img['url'])) {
                throw new Exception("Dữ liệu ảnh không hợp lệ: thiếu url");
            }
            
            $sql = "INSERT INTO room_images (room_image_url) VALUES (?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "s", $img['url']);
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Lỗi thêm ảnh: " . mysqli_stmt_error($stmt));
            }
            
            $image_ids[] = mysqli_insert_id($conn);
            mysqli_stmt_close($stmt);
        }
        error_log("✓ Created " . count($image_ids) . " images");

        // 5️⃣ Thêm vào homestay_details - XỬ LÝ NULL AMENITY
        $total_insert = 0;
        for ($i = 0; $i < count($room_ids); $i++) {
            $room_id = $room_ids[$i];
            $amenity_id = $amenity_ids[$i % count($amenity_ids)];
            $image_id = isset($image_ids[$i]) ? $image_ids[$i] : $image_ids[0];

            // XỬ LÝ NULL CHO AMENITY
            if ($amenity_id === null) {
                $sql = "INSERT INTO homestay_details (homestay_id, room_id, amenities_id, image_id)
                        VALUES (?, ?, NULL, ?)";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "iii", $homestay_id, $room_id, $image_id);
            } else {
                $sql = "INSERT INTO homestay_details (homestay_id, room_id, amenities_id, image_id)
                        VALUES (?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "iiii", $homestay_id, $room_id, $amenity_id, $image_id);
            }
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Lỗi thêm chi tiết homestay: " . mysqli_stmt_error($stmt));
            }
            $total_insert++;
            mysqli_stmt_close($stmt);
        }

        // ✅ FIXED: Validate số bản ghi phải khớp với num_room
        if ($total_insert !== $num_room_int) {
            throw new Exception("Số homestay_details không khớp! Yêu cầu: {$num_room_int}, Thực tế: $total_insert");
        }
        
        error_log("✓ Created $total_insert homestay_details records");

        mysqli_commit($conn);
        mysqli_close($conn);
        error_log("✅ Successfully created Homestay #$homestay_id");
        return $homestay_id;

    } catch (Exception $e) {
        mysqli_rollback($conn);
        mysqli_close($conn);
        error_log("❌ Error in addHomestayComplete: " . $e->getMessage());
        return false;
    }
}

/**
 * Lấy thông tin homestay theo ID (chỉ bảng homestays)
 */
function getHomestayById($id) {
    $conn = getDbConnection();
    
    $sql = "SELECT id, homestay_name, location, price_per_night, num_room, max_people, image_url, status 
            FROM homestays WHERE id = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) > 0) {
            $homestay = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return $homestay;
        }
        
        mysqli_stmt_close($stmt);
    }
    
    mysqli_close($conn);
    return null;
}

/**
 * Lấy đầy đủ thông tin homestay kèm rooms, amenities, images
 * FIXED: Xử lý tốt hơn, trả về đúng format
 */
function getHomestayFullDetails($homestay_id) {
    $conn = getDbConnection();
    
    // Lấy thông tin homestay
    $homestay = getHomestayById($homestay_id);
    if (!$homestay) {
        mysqli_close($conn);
        return null;
    }
    
    // Lấy rooms
    $sql_rooms = "SELECT DISTINCT r.id, r.room_name, r.description, r.capacity
                  FROM homestay_details hd
                  JOIN rooms r ON hd.room_id = r.id
                  WHERE hd.homestay_id = ?
                  ORDER BY r.id";
    
    $stmt = mysqli_prepare($conn, $sql_rooms);
    mysqli_stmt_bind_param($stmt, "i", $homestay_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $rooms = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rooms[] = [
            'id' => (int)$row['id'],
            'name' => $row['room_name'],
            'description' => $row['description'] ?? '',
            'capacity' => (int)$row['capacity']
        ];
    }
    mysqli_stmt_close($stmt);
    
    // Lấy amenities - DISTINCT để tránh trùng
    $sql_amenities = "SELECT DISTINCT a.name
                      FROM homestay_details hd
                      JOIN amenities a ON hd.amenities_id = a.id
                      WHERE hd.homestay_id = ?
                      ORDER BY a.name";
    
    $stmt = mysqli_prepare($conn, $sql_amenities);
    mysqli_stmt_bind_param($stmt, "i", $homestay_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $amenities = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $amenities[] = $row['name'];
    }
    mysqli_stmt_close($stmt);
    
    // Lấy images với roomId
    $sql_images = "SELECT DISTINCT hd.room_id, ri.id, ri.room_image_url
                   FROM homestay_details hd
                   JOIN room_images ri ON hd.image_id = ri.id
                   WHERE hd.homestay_id = ?
                   ORDER BY hd.room_id, ri.id";
    
    $stmt = mysqli_prepare($conn, $sql_images);
    mysqli_stmt_bind_param($stmt, "i", $homestay_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $images = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $images[] = [
            'id' => (int)$row['id'],
            'roomId' => (int)$row['room_id'],
            'url' => $row['room_image_url']
        ];
    }
    mysqli_stmt_close($stmt);
    
    mysqli_close($conn);
    
    // Gộp tất cả thông tin
    $homestay['rooms'] = $rooms;
    $homestay['amenities'] = $amenities;
    $homestay['images'] = $images;
    
    return $homestay;
}

/**
 * Lấy rooms theo homestay ID
 */
function getRoomsByHomestayId($homestay_id) {
    $conn = getDbConnection();

    $sql = "SELECT DISTINCT
                r.id AS room_id,
                r.room_name,
                r.description,
                r.capacity
            FROM homestay_details hd
            JOIN rooms r ON hd.room_id = r.id
            WHERE hd.homestay_id = ?
            ORDER BY r.id";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $homestay_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $rooms = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rooms[] = $row;
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $rooms;
}

/**
 * Lấy hình ảnh theo homestay ID
 */
function getHomestayImages($homestay_id) {
    $conn = getDbConnection();
    
    $sql = "SELECT DISTINCT i.id, i.room_image_url
            FROM homestay_details hd
            JOIN room_images i ON hd.image_id = i.id
            WHERE hd.homestay_id = ?
            ORDER BY i.id";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $homestay_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $images = mysqli_fetch_all($result, MYSQLI_ASSOC);
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    
    return $images;
}

/**
 * Cập nhật homestay (chỉ bảng homestays)
 */
function updateHomestay($id, $homestay_name, $location, $price_per_night, $num_room, $max_people, $image_url, $status) {
    $conn = getDbConnection();
    
    $sql = "UPDATE homestays 
            SET homestay_name = ?, location = ?, price_per_night = ?, num_room = ?, max_people = ?, image_url = ?, status = ? 
            WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssdiissi", $homestay_name, $location, $price_per_night, $num_room, $max_people, $image_url, $status, $id);
        $success = mysqli_stmt_execute($stmt);
        
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }
    
    mysqli_close($conn);
    return false;
}

/**
 * Cập nhật homestay đầy đủ với rooms, amenities và images
 */
function updateHomestayComplete($id, $homestay_name, $location, $price_per_night, $num_room, $max_people, $image_url, $status, $rooms, $amenities, $images) {
    $conn = getDbConnection();
    
    if (!$conn) {
        error_log("❌ Cannot connect to database");
        return false;
    }
    
    mysqli_begin_transaction($conn);
    
    try {
        error_log("=== UPDATE HOMESTAY ID: $id ===");
        error_log("Rooms: " . count($rooms) . ", Amenities: " . count($amenities) . ", Images: " . count($images));
        
        // 1. Cập nhật thông tin homestay
        $sql = "UPDATE homestays 
                SET homestay_name = ?, location = ?, price_per_night = ?, num_room = ?, max_people = ?, image_url = ?, status = ? 
                WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssdiissi", $homestay_name, $location, $price_per_night, $num_room, $max_people, $image_url, $status, $id);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Lỗi update homestay: " . mysqli_stmt_error($stmt));
        }
        mysqli_stmt_close($stmt);
        error_log("✓ Updated homestay info");
        
        // 2. Lấy danh sách room_id và image_id cũ
        $sql = "SELECT DISTINCT room_id, image_id FROM homestay_details WHERE homestay_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $old_room_ids = [];
        $old_image_ids = [];
        while ($row = mysqli_fetch_assoc($result)) {
            if ($row['room_id']) $old_room_ids[] = $row['room_id'];
            if ($row['image_id']) $old_image_ids[] = $row['image_id'];
        }
        mysqli_stmt_close($stmt);
        
        $old_room_ids = array_unique($old_room_ids);
        $old_image_ids = array_unique($old_image_ids);
        error_log("✓ Found " . count($old_room_ids) . " old rooms, " . count($old_image_ids) . " old images");
        
        // 3. Xóa homestay_details cũ
        $sql = "DELETE FROM homestay_details WHERE homestay_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        error_log("✓ Deleted old homestay_details");
        
        // 4. Xóa rooms cũ
        if (!empty($old_room_ids)) {
            foreach ($old_room_ids as $room_id) {
                $sql = "DELETE FROM rooms WHERE id = ?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "i", $room_id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
            error_log("✓ Deleted " . count($old_room_ids) . " old rooms");
        }
        
        // 5. Xóa images cũ
        if (!empty($old_image_ids)) {
            foreach ($old_image_ids as $image_id) {
                $sql = "DELETE FROM room_images WHERE id = ?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "i", $image_id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
            error_log("✓ Deleted " . count($old_image_ids) . " old images");
        }
        
        // 6. ✅ FIXED: Validate số phòng phải khớp với num_room
        $num_room_int = (int)$num_room;
        if (count($rooms) !== $num_room_int) {
            throw new Exception("Số phòng không khớp! Yêu cầu: {$num_room_int}, Thực tế: " . count($rooms));
        }
        
        $room_id_mapping = [];
        foreach ($rooms as $room) {
            if (!isset($room['name']) || !isset($room['capacity'])) {
                throw new Exception("Dữ liệu phòng không hợp lệ");
            }
            
            $sql = "INSERT INTO rooms (room_name, description, capacity) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            $room_desc = isset($room['description']) ? $room['description'] : '';
            mysqli_stmt_bind_param($stmt, "ssi", $room['name'], $room_desc, $room['capacity']);
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Lỗi insert room: " . mysqli_stmt_error($stmt));
            }
            
            $new_room_id = mysqli_insert_id($conn);
            $room_id_mapping[$room['id']] = $new_room_id;
            mysqli_stmt_close($stmt);
        }
        error_log("✓ Created " . count($room_id_mapping) . " new rooms");
        
        // 7. Xử lý amenities
        $amenity_ids = [];
        if (!empty($amenities) && is_array($amenities)) {
            foreach ($amenities as $amenity_name) {
                if (empty($amenity_name)) continue;
                
                $sql = "SELECT id FROM amenities WHERE name = ? LIMIT 1";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "s", $amenity_name);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                
                if (mysqli_num_rows($result) > 0) {
                    $row = mysqli_fetch_assoc($result);
                    $amenity_ids[] = $row['id'];
                } else {
                    mysqli_stmt_close($stmt);
                    $sql = "INSERT INTO amenities (name) VALUES (?)";
                    $stmt = mysqli_prepare($conn, $sql);
                    mysqli_stmt_bind_param($stmt, "s", $amenity_name);
                    mysqli_stmt_execute($stmt);
                    $amenity_ids[] = mysqli_insert_id($conn);
                }
                mysqli_stmt_close($stmt);
            }
        }
        
        if (empty($amenity_ids)) {
            $amenity_ids[] = null;
        }
        error_log("✓ Processed " . count($amenity_ids) . " amenities");
        
        // 8. Validate và thêm images + homestay_details
        if (count($images) < 1) {
            throw new Exception("Cần ít nhất 1 ảnh");
        }
        
        $detail_count = 0;
        foreach ($images as $image) {
            if (!isset($image['url']) || !isset($image['roomId'])) {
                throw new Exception("Dữ liệu ảnh không hợp lệ");
            }
            
            if (!isset($room_id_mapping[$image['roomId']])) {
                error_log("⚠️ WARNING: roomId {$image['roomId']} not found in mapping, skipping");
                continue;
            }
            
            $real_room_id = $room_id_mapping[$image['roomId']];
            
            // Insert image
            $sql = "INSERT INTO room_images (room_image_url) VALUES (?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "s", $image['url']);
            mysqli_stmt_execute($stmt);
            $image_id = mysqli_insert_id($conn);
            mysqli_stmt_close($stmt);
            
            // Lấy amenity_id
            $amenity_id = $amenity_ids[0];
            
            // Insert homestay_details - XỬ LÝ NULL
            if ($amenity_id !== null) {
                $sql = "INSERT INTO homestay_details (homestay_id, room_id, amenities_id, image_id) 
                        VALUES (?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "iiii", $id, $real_room_id, $amenity_id, $image_id);
            } else {
                $sql = "INSERT INTO homestay_details (homestay_id, room_id, amenities_id, image_id) 
                        VALUES (?, ?, NULL, ?)";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "iii", $id, $real_room_id, $image_id);
            }
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Lỗi insert homestay_details: " . mysqli_stmt_error($stmt));
            }
            mysqli_stmt_close($stmt);
            $detail_count++;
        }
        
        // ✅ FIXED: Validate số homestay_details phải khớp với num_room
        if ($detail_count !== $num_room_int) {
            throw new Exception("Số homestay_details không khớp! Yêu cầu: {$num_room_int}, Thực tế: $detail_count");
        }
        
        error_log("✓ Created $detail_count homestay_details records");
        
        mysqli_commit($conn);
        mysqli_close($conn);
        
        error_log("✅ Successfully updated Homestay #$id");
        return true;
        
    } catch (Exception $e) {
        mysqli_rollback($conn);
        mysqli_close($conn);
        error_log("❌ Error in updateHomestayComplete: " . $e->getMessage());
        return false;
    }
}

/**
 * Xóa homestay
 * FIXED: Xóa đúng thứ tự để tránh foreign key constraint
 */
function deleteHomestay($id) {
    $conn = getDbConnection();
    
    mysqli_begin_transaction($conn);
    
    try {
        // Lấy room_id và image_id trước khi xóa
        $sql = "SELECT DISTINCT room_id, image_id FROM homestay_details WHERE homestay_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $room_ids = [];
        $image_ids = [];
        while ($row = mysqli_fetch_assoc($result)) {
            if ($row['room_id']) $room_ids[] = $row['room_id'];
            if ($row['image_id']) $image_ids[] = $row['image_id'];
        }
        mysqli_stmt_close($stmt);
        
        $room_ids = array_unique($room_ids);
        $image_ids = array_unique($image_ids);
        
        // 1. Xóa homestay_details
        $sql = "DELETE FROM homestay_details WHERE homestay_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        // 2. Xóa rooms
        foreach ($room_ids as $room_id) {
            $sql = "DELETE FROM rooms WHERE id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $room_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
        
        // 3. Xóa images
        foreach ($image_ids as $image_id) {
            $sql = "DELETE FROM room_images WHERE id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $image_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
        
        // 4. Xóa homestay
        $sql = "DELETE FROM homestays WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        if ($success) {
            mysqli_commit($conn);
            mysqli_close($conn);
            return true;
        }
        
        throw new Exception("Failed to delete homestay");
        
    } catch (Exception $e) {
        mysqli_rollback($conn);
        mysqli_close($conn);
        error_log("❌ Error deleting homestay: " . $e->getMessage());
        return false;
    }
}
function getAllAmenities() {
    $conn = getDbConnection();
    $sql = "SELECT * FROM amenities ORDER BY name ASC";
    $result = mysqli_query($conn, $sql);

    $amenities = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $amenities[] = $row;
        }
    }

    mysqli_close($conn);
    return $amenities;
}

/**
 * Lấy danh sách amenities theo homestay_id
 * Trả về mảng tên amenities
 */
function getAmenitiesByHomestayId($homestay_id) {
    $conn = getDbConnection();
    if (!$conn) return [];

    $sql = "SELECT DISTINCT a.name
            FROM homestay_details hd
            INNER JOIN amenities a ON hd.amenities_id = a.id
            WHERE hd.homestay_id = ?
            ORDER BY a.name";

    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        mysqli_close($conn);
        return [];
    }

    mysqli_stmt_bind_param($stmt, "i", $homestay_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $amenities = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $amenities[] = $row['name'];
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $amenities;
}
?>