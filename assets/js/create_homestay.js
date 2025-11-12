// Thay thế phần JavaScript trong create_homestay.php từ dòng <script> đến

// Data storage
let rooms = [];
let images = [];
let selectedAmenities = [];
let currentStep = 1;
const totalSteps = 3;

// Multi-step form logic
function showStep(step) {
  document.querySelectorAll(".form-step").forEach((el) => {
    el.classList.remove("active");
  });

  document
    .querySelector(`.form-step[data-step="${step}"]`)
    .classList.add("active");

  document.querySelectorAll(".step").forEach((el, index) => {
    const stepNum = index + 1;
    if (stepNum < step) {
      el.classList.add("completed");
      el.classList.remove("active");
    } else if (stepNum === step) {
      el.classList.add("active");
      el.classList.remove("completed");
    } else {
      el.classList.remove("active", "completed");
    }
  });

  document.getElementById("prevBtn").style.display =
    step === 1 ? "none" : "inline-flex";
  document.getElementById("nextBtn").style.display =
    step === totalSteps ? "none" : "inline-flex";
  document.getElementById("submitBtn").style.display =
    step === totalSteps ? "inline-flex" : "none";
}

function validateStep(step) {
  const currentStepEl = document.querySelector(
    `.form-step[data-step="${step}"]`
  );
  const inputs = currentStepEl.querySelectorAll("input[required]");

  for (let input of inputs) {
    if (!input.value.trim()) {
      input.focus();
      input.classList.add("is-invalid");
      return false;
    }
    input.classList.remove("is-invalid");
  }

  // Validate Step 2: At least 4 rooms required
  if (step === 2 && rooms.length < 4) {
    alert("Vui lòng thêm ít nhất 4 phòng trước khi tiếp tục!");
    return false;
  }

  return true;
}

document.getElementById("nextBtn").addEventListener("click", function () {
  if (validateStep(currentStep)) {
    currentStep++;
    showStep(currentStep);
  }
});

document.getElementById("prevBtn").addEventListener("click", function () {
  currentStep--;
  showStep(currentStep);
});

// Image preview
document.getElementById("image_url").addEventListener("input", function () {
  const url = this.value.trim();
  const preview = document.getElementById("imagePreview");
  const img = document.getElementById("previewImg");

  if (url) {
    img.src = url;
    preview.classList.add("show");

    img.onerror = function () {
      preview.classList.remove("show");
    };
  } else {
    preview.classList.remove("show");
  }
});

// Status option selection
document.querySelectorAll(".status-option").forEach((option) => {
  option.addEventListener("click", function () {
    document.querySelectorAll(".status-option").forEach((opt) => {
      opt.classList.remove("checked");
    });
    this.classList.add("checked");
    this.querySelector('input[type="radio"]').checked = true;
  });
});

// Amenities selection
document.querySelectorAll(".selection-card").forEach((card) => {
  card.addEventListener("click", function () {
    const checkbox = this.querySelector('input[type="checkbox"]');
    checkbox.checked = !checkbox.checked;

    if (checkbox.checked) {
      this.classList.add("selected");
      if (!selectedAmenities.includes(checkbox.value)) {
        selectedAmenities.push(checkbox.value);
      }
    } else {
      this.classList.remove("selected");
      selectedAmenities = selectedAmenities.filter((a) => a !== checkbox.value);
    }
  });
});

// Room Modal
function openRoomModal() {
  document.getElementById("roomModal").classList.add("show");
}

function closeRoomModal() {
  document.getElementById("roomModal").classList.remove("show");
  document.getElementById("roomForm").reset();
}

function addRoom(event) {
  event.preventDefault();

  const room = {
    id: Date.now(),
    name: document.getElementById("roomName").value,
    description: document.getElementById("roomDescription").value,
    capacity: parseInt(document.getElementById("roomCapacity").value),
  };

  rooms.push(room);
  renderRooms();
  updateImageRoomSelect();
  closeRoomModal();
}

function removeRoom(id) {
  if (confirm("Bạn có chắc muốn xóa phòng này?")) {
    rooms = rooms.filter((r) => r.id !== id);
    images = images.filter((i) => i.roomId !== id);
    renderRooms();
    renderImages();
    updateImageRoomSelect();
  }
}

function renderRooms() {
  const container = document.getElementById("roomsList");

  if (rooms.length === 0) {
    container.innerHTML =
      '<p class="text-muted text-center py-3">Chưa có phòng nào. Nhấn "Thêm phòng" để bắt đầu.</p>';
    return;
  }

  // Show room count status
  const roomCountInfo =
    rooms.length < 4
      ? `<div class="alert alert-warning mb-3"><i class="fas fa-exclamation-triangle"></i> Đã thêm ${
          rooms.length
        }/4 phòng (Còn thiếu ${4 - rooms.length} phòng)</div>`
      : `<div class="alert alert-success mb-3"><i class="fas fa-check-circle"></i> Đã đủ ${rooms.length} phòng</div>`;

  container.innerHTML =
    roomCountInfo +
    rooms
      .map(
        (room) => `
                <div class="room-item">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6><i class="fas fa-bed"></i> ${room.name}</h6>
                        <button type="button" class="btn btn-sm btn-danger" onclick="removeRoom(${
                          room.id
                        })">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    <p>${room.description || "Không có mô tả"}</p>
                    <div class="d-flex gap-2">
                        <span class="badge-custom badge-capacity">
                            <i class="fas fa-users"></i> ${room.capacity} người
                        </span>
                    </div>
                </div>
            `
      )
      .join("");
}

// Image Modal
function openImageModal() {
  if (rooms.length === 0) {
    alert("Vui lòng thêm phòng trước!");
    return;
  }
  document.getElementById("imageModal").classList.add("show");
}

function closeImageModal() {
  document.getElementById("imageModal").classList.remove("show");
  document.getElementById("imageForm").reset();
  document.getElementById("imageModalPreview").classList.remove("show");
}

function updateImageRoomSelect() {
  const select = document.getElementById("imageRoomSelect");
  select.innerHTML =
    '<option value="">-- Chọn phòng --</option>' +
    rooms
      .map((room) => `<option value="${room.id}">${room.name}</option>`)
      .join("");
}

document.getElementById("imageUrl").addEventListener("input", function () {
  const url = this.value.trim();
  const preview = document.getElementById("imageModalPreview");
  const img = document.getElementById("imageModalPreviewImg");

  if (url) {
    img.src = url;
    preview.classList.add("show");

    img.onerror = function () {
      preview.classList.remove("show");
    };
  } else {
    preview.classList.remove("show");
  }
});

function addImage(event) {
  event.preventDefault();

  const image = {
    id: Date.now(),
    roomId: parseInt(document.getElementById("imageRoomSelect").value),
    url: document.getElementById("imageUrl").value,
  };

  images.push(image);
  renderImages();
  closeImageModal();
}

function removeImage(id) {
  if (confirm("Bạn có chắc muốn xóa ảnh này?")) {
    images = images.filter((i) => i.id !== id);
    renderImages();
  }
}

function renderImages() {
  const container = document.getElementById("imagesList");

  if (images.length === 0) {
    container.innerHTML =
      '<p class="text-muted text-center py-3">Chưa có ảnh nào. Nhấn "Thêm ảnh" để bắt đầu.</p>';
    return;
  }

  container.innerHTML = images
    .map((image) => {
      const room = rooms.find((r) => r.id === image.roomId);
      return `
                    <div class="image-item">
                        <img src="${
                          image.url
                        }" alt="Room image" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22100%22 height=%22100%22%3E%3Crect fill=%22%23ddd%22 width=%22100%22 height=%22100%22/%3E%3Ctext fill=%22%23999%22 x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22%3ENo Image%3C/text%3E%3C/svg%3E'">
                        <div class="image-item-info">
                            <p class="mb-1"><strong>${
                              room ? room.name : "Unknown Room"
                            }</strong></p>
                            <p class="text-truncate mb-0">${image.url}</p>
                        </div>
                        <button type="button" class="remove-btn" onclick="removeImage(${
                          image.id
                        })">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                `;
    })
    .join("");
}

// Form submission
document
  .getElementById("homestayForm")
  .addEventListener("submit", function (e) {
    e.preventDefault();

    // Validate minimum 4 rooms before submit
    if (rooms.length < 4) {
      alert("Vui lòng thêm ít nhất 4 phòng!");
      currentStep = 2;
      showStep(2);
      return;
    }

    const submitBtn = document.getElementById("submitBtn");
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<div class="spinner"></div> Đang lưu...';

    // Prepare data
    document.getElementById("roomsData").value = JSON.stringify(rooms);
    document.getElementById("amenitiesData").value =
      JSON.stringify(selectedAmenities);
    document.getElementById("imagesData").value = JSON.stringify(images);

    // Submit form
    this.submit();
  });

// Auto hide alerts
setTimeout(function () {
  const alerts = document.querySelectorAll(".alert");
  alerts.forEach((alert) => {
    const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
    bsAlert.close();
  });
}, 5000);

// Initialize
showStep(1);
