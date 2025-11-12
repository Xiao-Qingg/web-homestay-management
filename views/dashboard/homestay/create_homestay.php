<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Homestay Mới</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../../assets/css/create_homestay.css">
</head>
<body>
    <script></script>
    <div class="container-custom">
        <div class="form-card">
            <!-- Header -->
            <div class="form-header">
                <a href="../homestay.php" class="back-button">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
                <h1><i class="fas fa-home"></i> Thêm Homestay Mới</h1>
                <p>Điền thông tin chi tiết để tạo homestay mới</p>
            </div>

            <!-- Progress Steps -->
            <div class="progress-steps">
                <div class="step active" data-step="1">
                    <div class="step-number">1</div>
                    <div class="step-label">Thông tin cơ bản</div>
                </div>
                <div class="step" data-step="2">
                    <div class="step-number">2</div>
                    <div class="step-label">Phòng & Tiện ích</div>
                </div>
                <div class="step" data-step="3">
                    <div class="step-number">3</div>
                    <div class="step-label">Hình ảnh</div>
                </div>
            </div>

            <!-- Form Content -->
            <div class="form-content">
                <form method="POST" action="../../../handles/homestay_process.php" id="homestayForm">
                    <input type="hidden" name="action" value="create">
                    <input type="hidden" name="rooms" id="roomsData">
                    <input type="hidden" name="amenities" id="amenitiesData">
                    <input type="hidden" name="images" id="imagesData">

                    <!-- Step 1: Basic Info -->
                    <div class="form-step active" data-step="1">
                        <div class="info-card">
                            <h5><i class="fas fa-info-circle"></i> Thông tin cơ bản</h5>
                            <p>Nhập thông tin chi tiết về homestay của bạn</p>
                        </div>

                        <div class="mb-4">
                            <label for="homestay_name" class="form-label">
                                Tên Homestay <span class="text-danger">*</span>
                            </label>
                            <div class="input-icon">
                                <i class="fas fa-hotel"></i>
                                <input type="text" class="form-control" id="homestay_name" name="homestay_name" 
                                       placeholder="Ví dụ: Homestay Hà Nội View Đẹp" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="location" class="form-label">
                                Địa điểm <span class="text-danger">*</span>
                            </label>
                            <div class="input-icon">
                                <i class="fas fa-map-marker-alt"></i>
                                <input type="text" class="form-control" id="location" name="location" 
                                       placeholder="Ví dụ: Hoàn Kiếm, Hà Nội" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-4">
                                <label for="price_per_night" class="form-label">
                                    Giá/đêm (VNĐ) <span class="text-danger">*</span>
                                </label>
                                <div class="input-icon">
                                    <i class="fas fa-dollar-sign"></i>
                                    <input type="number" class="form-control" id="price_per_night" name="price_per_night" 
                                           placeholder="500000" min="0" step="1000" required>
                                </div>
                            </div>

                            <div class="col-md-4 mb-4">
                                <label for="num_room" class="form-label">
                                    Số phòng <span class="text-danger">*</span>
                                </label>
                                <div class="input-icon">
                                    <i class="fas fa-door-open"></i>
                                    <input type="number" class="form-control" id="num_room" name="num_room" 
                                           placeholder="3" min="1" required>
                                </div>
                            </div>

                            <div class="col-md-4 mb-4">
                                <label for="max_people" class="form-label">
                                    Sức chứa <span class="text-danger">*</span>
                                </label>
                                <div class="input-icon">
                                    <i class="fas fa-users"></i>
                                    <input type="number" class="form-control" id="max_people" name="max_people" 
                                           placeholder="6" min="1" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="image_url" class="form-label">
                                URL Hình ảnh chính
                            </label>
                            <div class="input-icon">
                                <i class="fas fa-link"></i>
                                <input type="url" class="form-control" id="image_url" name="image_url" 
                                       placeholder="https://example.com/image.jpg">
                            </div>
                            <small class="text-muted">Hình ảnh đại diện cho homestay</small>
                            
                            <div class="image-preview" id="imagePreview">
                                <img id="previewImg" src="" alt="Preview">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">
                                Trạng thái <span class="text-danger">*</span>
                            </label>
                            <div class="status-options">
                                <label class="status-option checked" id="statusActive">
                                    <input type="radio" name="status" value="Hoạt động" checked>
                                    <div class="status-icon">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <div class="status-content">
                                        <h6>Hoạt động</h6>
                                        <p>Sẵn sàng đón khách</p>
                                    </div>
                                </label>

                                <label class="status-option" id="statusInactive">
                                    <input type="radio" name="status" value="Không hoạt động">
                                    <div class="status-icon">
                                        <i class="fas fa-times-circle"></i>
                                    </div>
                                    <div class="status-content">
                                        <h6>Không hoạt động</h6>
                                        <p>Tạm ngưng</p>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Rooms & Amenities -->
                    <div class="form-step" data-step="2">
                        <div class="info-card">
                            <h5><i class="fas fa-bed"></i> Chi tiết phòng & Tiện ích</h5>
                            <p>Thêm các phòng và tiện ích có trong homestay</p>
                        </div>

                        <!-- Rooms Section -->
                       <!-- Thay thế phần Rooms Section trong Step 2 -->

                        <!-- Rooms Section -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <label class="form-label mb-0">
                                    Danh sách phòng 
                                    <span class="text-danger">* (Tối thiểu 4 phòng)</span>
                                </label>
                                <button type="button" class="btn btn-primary btn-sm" onclick="openRoomModal()">
                                    <i class="fas fa-plus"></i> Thêm phòng
                                </button>
                            </div>
                            <div id="roomsList">
                                <p class="text-muted text-center py-3">Chưa có phòng nào. Nhấn "Thêm phòng" để bắt đầu.</p>
                            </div>
                        </div>

                        <!-- Amenities Section -->
                        <div class="mb-4">
                            <label class="form-label">Tiện ích</label>
                            <div class="selection-grid" id="amenitiesGrid">
                                <div class="selection-card" data-amenity="wifi">
                                    <input type="checkbox" value="wifi">
                                    <div class="selection-icon">
                                        <i class="fas fa-wifi"></i>
                                    </div>
                                    <h6>WiFi miễn phí</h6>
                                </div>
                                <div class="selection-card" data-amenity="parking">
                                    <input type="checkbox" value="parking">
                                    <div class="selection-icon">
                                        <i class="fas fa-parking"></i>
                                    </div>
                                    <h6>Chỗ đậu xe</h6>
                                </div>
                                <div class="selection-card" data-amenity="ac">
                                    <input type="checkbox" value="ac">
                                    <div class="selection-icon">
                                        <i class="fas fa-snowflake"></i>
                                    </div>
                                    <h6>Điều hòa</h6>
                                </div>
                                <div class="selection-card" data-amenity="kitchen">
                                    <input type="checkbox" value="kitchen">
                                    <div class="selection-icon">
                                        <i class="fas fa-utensils"></i>
                                    </div>
                                    <h6>Bếp</h6>
                                </div>
                                <div class="selection-card" data-amenity="tv">
                                    <input type="checkbox" value="tv">
                                    <div class="selection-icon">
                                        <i class="fas fa-tv"></i>
                                    </div>
                                    <h6>TV</h6>
                                </div>
                                <div class="selection-card" data-amenity="pool">
                                    <input type="checkbox" value="pool">
                                    <div class="selection-icon">
                                        <i class="fas fa-swimming-pool"></i>
                                    </div>
                                    <h6>Hồ bơi</h6>
                                </div>
                                <div class="selection-card" data-amenity="balcony">
                                    <input type="checkbox" value="balcony">
                                    <div class="selection-icon">
                                        <i class="fas fa-door-open"></i>
                                    </div>
                                    <h6>Ban công</h6>
                                </div>
                                <div class="selection-card" data-amenity="bbq">
                                    <input type="checkbox" value="bbq">
                                    <div class="selection-icon">
                                        <i class="fas fa-fire"></i>
                                    </div>
                                    <h6>BBQ ngoài trời</h6>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Images -->
                    <div class="form-step" data-step="3">
                        <div class="info-card">
                            <h5><i class="fas fa-image"></i> Hình ảnh phòng</h5>
                            <p>Thêm hình ảnh cho các phòng trong homestay</p>
                        </div>

                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <label class="form-label mb-0">Danh sách hình ảnh</label>
                                <button type="button" class="btn btn-primary btn-sm" onclick="openImageModal()">
                                    <i class="fas fa-plus"></i> Thêm ảnh
                                </button>
                            </div>
                            <div id="imagesList" class="image-list">
                                <p class="text-muted text-center py-3">Chưa có ảnh nào. Nhấn "Thêm ảnh" để bắt đầu.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="form-navigation">
                        <button type="button" class="btn-custom btn-secondary-custom" id="prevBtn" style="display: none;">
                            <i class="fas fa-arrow-left"></i>
                            Quay lại
                        </button>
                        <div style="flex: 1;"></div>
                        <button type="button" class="btn-custom btn-primary-custom" id="nextBtn">
                            Tiếp theo
                            <i class="fas fa-arrow-right"></i>
                        </button>
                        <button type="submit" class="btn-custom btn-success-custom" id="submitBtn" style="display: none;">
                            <i class="fas fa-save"></i>
                            Lưu Homestay
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Room Modal -->
    <div class="modal-custom" id="roomModal">
        <div class="modal-content-custom">
            <div class="modal-header-custom">
                <h4><i class="fas fa-bed"></i> Thêm phòng</h4>
                <button class="close-modal" onclick="closeRoomModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="roomForm" onsubmit="addRoom(event)">
                <div class="mb-3">
                    <label class="form-label">Tên phòng <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="roomName" placeholder="Ví dụ: Studio" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Mô tả</label>
                    <textarea class="form-control" id="roomDescription" rows="3" placeholder="Mô tả phòng..."></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Sức chứa (người) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="roomCapacity" placeholder="2" min="1" required>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill">
                        <i class="fas fa-save"></i> Lưu
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="closeRoomModal()">Hủy</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Image Modal -->
    <div class="modal-custom" id="imageModal">
        <div class="modal-content-custom">
            <div class="modal-header-custom">
                <h4><i class="fas fa-image"></i> Thêm hình ảnh</h4>
                <button class="close-modal" onclick="closeImageModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="imageForm" onsubmit="addImage(event)">
                <div class="mb-3">
                    <label class="form-label">Chọn phòng <span class="text-danger">*</span></label>
                    <select class="form-select" id="imageRoomSelect" required>
                        <option value="">-- Chọn phòng --</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">URL Hình ảnh <span class="text-danger">*</span></label>
                    <input type="url" class="form-control" id="imageUrl" 
                           placeholder="https://example.com/image.jpg" required>
                    <small class="text-muted">Nhập đường dẫn URL của ảnh</small>
                </div>
                <div class="mb-3">
                    <div id="imageModalPreview" class="image-preview">
                        <img id="imageModalPreviewImg" src="" alt="Preview">
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill">
                        <i class="fas fa-save"></i> Lưu
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="closeImageModal()">Hủy</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../../assets/js/create_homestay.js"></script>
</body>
</html>