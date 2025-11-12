<?php
require_once '../../../functions/homestay_functions.php';

// Kiểm tra ID homestay
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: ../homestay.php?error=Không tìm thấy ID homestay");
    exit();
}

$homestay_id = $_GET['id'];

// Lấy thông tin đầy đủ homestay
$homestay = getHomestayFullDetails($homestay_id);

if (!$homestay) {
    header("Location: ../homestay.php?error=Homestay không tồn tại");
    exit();
}

// Chuyển đổi dữ liệu sang JSON để JavaScript xử lý
$rooms_json = json_encode($homestay['rooms']);
$amenities_json = json_encode($homestay['amenities']);
$images_json = json_encode($homestay['images']);
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa Homestay</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../../assets/css/create_homestay.css">
</head>
<body>
    <div class="container-custom">
        <div class="form-card">
            <!-- Header -->
            <div class="form-header">
                <a href="../homestay.php" class="back-button">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
                <h1><i class="fas fa-edit"></i> Chỉnh sửa Homestay</h1>
                <p>Cập nhật thông tin homestay</p>
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

            <!-- Alert Messages -->
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($_GET['error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Form Content -->
            <div class="form-content">
                <form method="POST" action="../../../handles/homestay_process.php" id="homestayForm">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" value="<?php echo $homestay['id']; ?>">
                    <input type="hidden" name="rooms" id="roomsData">
                    <input type="hidden" name="amenities" id="amenitiesData">
                    <input type="hidden" name="images" id="imagesData">

                    <!-- Step 1: Basic Info -->
                    <div class="form-step active" data-step="1">
                        <div class="info-card">
                            <h5><i class="fas fa-info-circle"></i> Thông tin cơ bản</h5>
                            <p>Cập nhật thông tin chi tiết về homestay của bạn</p>
                        </div>

                        <div class="mb-4">
                            <label for="homestay_name" class="form-label">
                                Tên Homestay <span class="text-danger">*</span>
                            </label>
                            <div class="input-icon">
                                <i class="fas fa-hotel"></i>
                                <input type="text" class="form-control" id="homestay_name" name="homestay_name" 
                                       value="<?php echo htmlspecialchars($homestay['homestay_name']); ?>" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="location" class="form-label">
                                Địa điểm <span class="text-danger">*</span>
                            </label>
                            <div class="input-icon">
                                <i class="fas fa-map-marker-alt"></i>
                                <input type="text" class="form-control" id="location" name="location" 
                                       value="<?php echo htmlspecialchars($homestay['location']); ?>" required>
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
                                           value="<?php echo $homestay['price_per_night']; ?>" min="0" step="1000" required>
                                </div>
                            </div>

                            <div class="col-md-4 mb-4">
                                <label for="num_room" class="form-label">
                                    Số phòng <span class="text-danger">*</span>
                                </label>
                                <div class="input-icon">
                                    <i class="fas fa-door-open"></i>
                                    <input type="number" class="form-control" id="num_room" name="num_room" 
                                           value="<?php echo $homestay['num_room']; ?>" min="1" required>
                                </div>
                            </div>

                            <div class="col-md-4 mb-4">
                                <label for="max_people" class="form-label">
                                    Sức chứa <span class="text-danger">*</span>
                                </label>
                                <div class="input-icon">
                                    <i class="fas fa-users"></i>
                                    <input type="number" class="form-control" id="max_people" name="max_people" 
                                           value="<?php echo $homestay['max_people']; ?>" min="1" required>
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
                                       value="<?php echo htmlspecialchars($homestay['image_url']); ?>">
                            </div>
                            <small class="text-muted">Hình ảnh đại diện cho homestay</small>
                            
                            <div class="image-preview <?php echo !empty($homestay['image_url']) ? 'show' : ''; ?>" id="imagePreview">
                                <img id="previewImg" src="<?php echo htmlspecialchars($homestay['image_url']); ?>" alt="Preview">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">
                                Trạng thái <span class="text-danger">*</span>
                            </label>
                            <div class="status-options">
                                <label class="status-option <?php echo $homestay['status'] === 'Hoạt động' ? 'checked' : ''; ?>" id="statusActive">
                                    <input type="radio" name="status" value="Hoạt động" 
                                           <?php echo $homestay['status'] === 'Hoạt động' ? 'checked' : ''; ?>>
                                    <div class="status-icon">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <div class="status-content">
                                        <h6>Hoạt động</h6>
                                        <p>Sẵn sàng đón khách</p>
                                    </div>
                                </label>

                                <label class="status-option <?php echo $homestay['status'] === 'Không hoạt động' ? 'checked' : ''; ?>" id="statusInactive">
                                    <input type="radio" name="status" value="Không hoạt động"
                                           <?php echo $homestay['status'] === 'Không hoạt động' ? 'checked' : ''; ?>>
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
                            <p>Cập nhật các phòng và tiện ích có trong homestay</p>
                        </div>

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
                                <p class="text-muted text-center py-3">Đang tải...</p>
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
                            <p>Cập nhật hình ảnh cho các phòng trong homestay</p>
                        </div>

                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <label class="form-label mb-0">Danh sách hình ảnh</label>
                                <button type="button" class="btn btn-primary btn-sm" onclick="openImageModal()">
                                    <i class="fas fa-plus"></i> Thêm ảnh
                                </button>
                            </div>
                            <div id="imagesList" class="image-list">
                                <p class="text-muted text-center py-3">Đang tải...</p>
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
                            Cập nhật Homestay
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
                <h4><i class="fas fa-bed"></i> <span id="roomModalTitle">Thêm phòng</span></h4>
                <button class="close-modal" onclick="closeRoomModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="roomForm" onsubmit="saveRoom(event)">
                <input type="hidden" id="editingRoomId">
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
    <script>
        // Load dữ liệu từ PHP
        let rooms = <?php echo $rooms_json; ?>;
        let selectedAmenities = <?php echo $amenities_json; ?>;
        let images = <?php echo $images_json; ?>;
        let currentStep = 1;
        const totalSteps = 3;

        // Initialize data khi load trang
        document.addEventListener('DOMContentLoaded', function() {
            // Load amenities
            selectedAmenities.forEach(amenity => {
                const card = document.querySelector(`.selection-card[data-amenity="${amenity}"]`);
                if (card) {
                    card.classList.add('selected');
                    card.querySelector('input[type="checkbox"]').checked = true;
                }
            });

            renderRooms();
            renderImages();
            updateImageRoomSelect();
        });

        // Multi-step form logic
        function showStep(step) {
            document.querySelectorAll('.form-step').forEach(el => {
                el.classList.remove('active');
            });
            
            document.querySelector(`.form-step[data-step="${step}"]`).classList.add('active');
            
            document.querySelectorAll('.step').forEach((el, index) => {
                const stepNum = index + 1;
                if (stepNum < step) {
                    el.classList.add('completed');
                    el.classList.remove('active');
                } else if (stepNum === step) {
                    el.classList.add('active');
                    el.classList.remove('completed');
                } else {
                    el.classList.remove('active', 'completed');
                }
            });
            
            document.getElementById('prevBtn').style.display = step === 1 ? 'none' : 'inline-flex';
            document.getElementById('nextBtn').style.display = step === totalSteps ? 'none' : 'inline-flex';
            document.getElementById('submitBtn').style.display = step === totalSteps ? 'inline-flex' : 'none';
        }

        function validateStep(step) {
            const currentStepEl = document.querySelector(`.form-step[data-step="${step}"]`);
            const inputs = currentStepEl.querySelectorAll('input[required]');
            
            for (let input of inputs) {
                if (!input.value.trim()) {
                    input.focus();
                    input.classList.add('is-invalid');
                    return false;
                }
                input.classList.remove('is-invalid');
            }

            if (step === 2 && rooms.length < 4) {
                alert('Vui lòng thêm ít nhất 4 phòng trước khi tiếp tục!');
                return false;
            }

            return true;
        }

        document.getElementById('nextBtn').addEventListener('click', function() {
            if (validateStep(currentStep)) {
                currentStep++;
                showStep(currentStep);
            }
        });

        document.getElementById('prevBtn').addEventListener('click', function() {
            currentStep--;
            showStep(currentStep);
        });

        // Image preview
        document.getElementById('image_url').addEventListener('input', function() {
            const url = this.value.trim();
            const preview = document.getElementById('imagePreview');
            const img = document.getElementById('previewImg');
            
            if (url) {
                img.src = url;
                preview.classList.add('show');
                
                img.onerror = function() {
                    preview.classList.remove('show');
                };
            } else {
                preview.classList.remove('show');
            }
        });

        // Status option selection
        document.querySelectorAll('.status-option').forEach(option => {
            option.addEventListener('click', function() {
                document.querySelectorAll('.status-option').forEach(opt => {
                    opt.classList.remove('checked');
                });
                this.classList.add('checked');
                this.querySelector('input[type="radio"]').checked = true;
            });
        });

        // Amenities selection
        document.querySelectorAll('.selection-card').forEach(card => {
            card.addEventListener('click', function() {
                const checkbox = this.querySelector('input[type="checkbox"]');
                checkbox.checked = !checkbox.checked;
                
                if (checkbox.checked) {
                    this.classList.add('selected');
                    if (!selectedAmenities.includes(checkbox.value)) {
                        selectedAmenities.push(checkbox.value);
                    }
                } else {
                    this.classList.remove('selected');
                    selectedAmenities = selectedAmenities.filter(a => a !== checkbox.value);
                }
            });
        });

        // Room Modal Functions
        function openRoomModal(roomId = null) {
            if (roomId) {
                const room = rooms.find(r => r.id == roomId);
                if (room) {
                    document.getElementById('roomModalTitle').textContent = 'Chỉnh sửa phòng';
                    document.getElementById('editingRoomId').value = roomId;
                    document.getElementById('roomName').value = room.name;
                    document.getElementById('roomDescription').value = room.description || '';
                    document.getElementById('roomCapacity').value = room.capacity;
                }
            } else {
                document.getElementById('roomModalTitle').textContent = 'Thêm phòng';
                document.getElementById('roomForm').reset();
                document.getElementById('editingRoomId').value = '';
            }
            document.getElementById('roomModal').classList.add('show');
        }

        function closeRoomModal() {
            document.getElementById('roomModal').classList.remove('show');
            document.getElementById('roomForm').reset();
        }

        function saveRoom(event) {
            event.preventDefault();
            
            const editingId = document.getElementById('editingRoomId').value;
            const roomData = {
                id: editingId || Date.now(),
                name: document.getElementById('roomName').value,
                description: document.getElementById('roomDescription').value,
                capacity: parseInt(document.getElementById('roomCapacity').value)
            };
            
            if (editingId) {
                const index = rooms.findIndex(r => r.id == editingId);
                if (index !== -1) {
                    rooms[index] = roomData;
                }
            } else {
                rooms.push(roomData);
            }
            
            renderRooms();
            updateImageRoomSelect();
            closeRoomModal();
        }

        function removeRoom(id) {
            if (confirm('Bạn có chắc muốn xóa phòng này? Các ảnh liên quan cũng sẽ bị xóa.')) {
                rooms = rooms.filter(r => r.id != id);
                images = images.filter(i => i.roomId != id);
                renderRooms();
                renderImages();
                updateImageRoomSelect();
            }
        }

        function renderRooms() {
            const container = document.getElementById('roomsList');
            
            if (rooms.length === 0) {
                container.innerHTML = '<p class="text-muted text-center py-3">Chưa có phòng nào. Nhấn "Thêm phòng" để bắt đầu.</p>';
                return;
            }
            
            const roomCountInfo = rooms.length < 4 
                ? `<div class="alert alert-warning mb-3"><i class="fas fa-exclamation-triangle"></i> Đã thêm ${rooms.length}/4 phòng (Còn thiếu ${4 - rooms.length} phòng)</div>`
                : `<div class="alert alert-success mb-3"><i class="fas fa-check-circle"></i> Đã đủ ${rooms.length} phòng</div>`;
            
            container.innerHTML = roomCountInfo + rooms.map(room => `
                <div class="room-item">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6><i class="fas fa-bed"></i> ${room.name}</h6>
                        <div>
                            <button type="button" class="btn btn-sm btn-info me-1" onclick="openRoomModal(${room.id})">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-danger" onclick="removeRoom(${room.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <p>${room.description || 'Không có mô tả'}</p>
                    <div class="d-flex gap-2">
                        <span class="badge-custom badge-capacity">
                            <i class="fas fa-users"></i> ${room.capacity} người
                        </span>
                    </div>
                </div>
            `).join('');
        }

        // Image Modal Functions
        function openImageModal() {
            if (rooms.length === 0) {
                alert('Vui lòng thêm phòng trước!');
                return;
            }
            document.getElementById('imageModal').classList.add('show');
        }

        function closeImageModal() {
            document.getElementById('imageModal').classList.remove('show');
            document.getElementById('imageForm').reset();
            document.getElementById('imageModalPreview').classList.remove('show');
        }

        function updateImageRoomSelect() {
            const select = document.getElementById('imageRoomSelect');
            select.innerHTML = '<option value="">-- Chọn phòng --</option>' +
                rooms.map(room => `<option value="${room.id}">${room.name}</option>`).join('');
        }

        document.getElementById('imageUrl').addEventListener('input', function() {
            const url = this.value.trim();
            const preview = document.getElementById('imageModalPreview');
            const img = document.getElementById('imageModalPreviewImg');
            
            if (url) {
                img.src = url;
                preview.classList.add('show');
                
                img.onerror = function() {
                    preview.classList.remove('show');
                };
            } else {
                preview.classList.remove('show');
            }
        });

        function addImage(event) {
            event.preventDefault();
            
            const image = {
                id: Date.now(),
                roomId: parseInt(document.getElementById('imageRoomSelect').value),
                url: document.getElementById('imageUrl').value
            };
            
            images.push(image);
            renderImages();
            closeImageModal();
        }

        function removeImage(id) {
            const imageToRemove = images.find(img => img.id === id);
            if (!imageToRemove) return;

            const room = rooms.find(r => r.id == imageToRemove.roomId);
            const roomName = room ? room.name : 'Unknown Room';

            if (confirm(`Bạn có chắc muốn xóa ảnh của phòng "${roomName}"?`)) {
                images = images.filter(img => img.id !== id);
                renderImages();
            }
        }

        function renderImages() {
            const container = document.getElementById('imagesList');
            if (images.length === 0) {
                container.innerHTML = '<p class="text-muted text-center py-3">Chưa có ảnh nào. Nhấn "Thêm ảnh" để bắt đầu.</p>';
                return;
            }

            container.innerHTML = images.map(image => {
                const room = rooms.find(r => r.id == image.roomId);
                return `
                    <div class="image-item">
                        <img src="${image.url}" alt="Room image" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22100%22 height=%22100%22%3E%3Crect fill=%22%23ddd%22 width=%22100%22 height=%22100%22/%3E%3Ctext fill=%22%23999%22 x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22%3ENo Image%3C/text%3E%3C/svg%3E'">
                        <div class="image-item-info">
                            <p class="mb-1"><strong>${room ? room.name : 'Unknown Room'}</strong></p>
                            
                        </div>
                        <button type="button" class="remove-btn" onclick="removeImage(${image.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                `;
            }).join('');
        }

                
                // Form submission
                document.getElementById('homestayForm').addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    // Validate minimum 4 rooms
                    if (rooms.length < 4) {
                        alert('Vui lòng thêm ít nhất 4 phòng!');
                        currentStep = 2;
                        showStep(2);
                        return;
                    }
                    
                    // Validate at least 1 image
                    if (images.length < 1) {
                        alert('Vui lòng thêm ít nhất 1 hình ảnh!');
                        currentStep = 3;
                        showStep(3);
                        return;
                    }
                    
                    const submitBtn = document.getElementById('submitBtn');
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<div class="spinner"></div> Đang cập nhật...';
                    
                    // Prepare data - QUAN TRỌNG: đảm bảo format đúng
                    console.log('Rooms before stringify:', rooms);
                    console.log('Amenities before stringify:', selectedAmenities);
                    console.log('Images before stringify:', images);
                    
                    document.getElementById('roomsData').value = JSON.stringify(rooms);
                    document.getElementById('amenitiesData').value = JSON.stringify(selectedAmenities);
                    document.getElementById('imagesData').value = JSON.stringify(images);
                    
                    console.log('Final roomsData:', document.getElementById('roomsData').value);
                    console.log('Final amenitiesData:', document.getElementById('amenitiesData').value);
                    console.log('Final imagesData:', document.getElementById('imagesData').value);
                    
                    // Submit form
                    this.submit();
                });

                // Auto hide alerts
                setTimeout(function() {
                    const alerts = document.querySelectorAll('.alert');
                    alerts.forEach(alert => {
                        const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                        bsAlert.close();
                    });
                }, 5000);

                // Initialize
                showStep(1);
                
    </script>
</body>
</html>