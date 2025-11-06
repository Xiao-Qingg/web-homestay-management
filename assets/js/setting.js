function showSection(sectionId) {
  // Hide all sections
  document.querySelectorAll(".settings-section").forEach((section) => {
    section.classList.remove("active");
  });

  // Remove active class from nav items
  document.querySelectorAll(".settings-nav-item").forEach((item) => {
    item.classList.remove("active");
  });

  // Show selected section
  document.getElementById(sectionId).classList.add("active");

  // Add active class to clicked nav item
  event.target.closest(".settings-nav-item").classList.add("active");
}

function previewAvatar(input) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = function (e) {
      document.getElementById(
        "avatarPreview"
      ).innerHTML = `<img src="${e.target.result}" alt="Avatar">`;
    };
    reader.readAsDataURL(input.files[0]);
  }
}

function backupData() {
  if (confirm("Bạn có muốn sao lưu dữ liệu hệ thống?")) {
    alert("Đang tiến hành sao lưu dữ liệu...");
    // Thêm code sao lưu ở đây
  }
}

function confirmReset() {
  if (
    confirm(
      "CẢNH BÁO: Hành động này sẽ xóa toàn bộ dữ liệu hệ thống. Bạn có chắc chắn muốn tiếp tục?"
    )
  ) {
    if (
      confirm("Xác nhận lần cuối: Dữ liệu không thể khôi phục sau khi xóa!")
    ) {
      alert("Chức năng đang được phát triển!");
    }
  }
}

function toggleDarkMode(checkbox) {
  if (checkbox.checked) {
    document.body.style.background = "#1a1a1a";
    alert("Chế độ tối đang được phát triển!");
    checkbox.checked = false;
  }
}

function changeThemeColor(color) {
  document.querySelectorAll(".theme-color").forEach((el) => {
    el.style.border = "3px solid transparent";
  });
  event.target.style.border = "3px solid #333";
  alert("Đổi màu chủ đạo sang: " + color);
}

function changeFontSize(size) {
  document.body.style.fontSize = size;
}
