// Lấy tất cả các hàng homestay
const allRows = document.querySelectorAll(".homestay-row");

// Hàm áp dụng filter
function applyFilter() {
  const searchTerm = document
    .getElementById("searchInput")
    .value.toLowerCase()
    .trim();
  const minPrice = parseFloat(document.getElementById("minPrice").value) || 0;
  const maxPrice =
    parseFloat(document.getElementById("maxPrice").value) || Infinity;

  let visibleCount = 0;

  allRows.forEach((row, index) => {
    const name = row.getAttribute("data-name");
    const location = row.getAttribute("data-location");
    const price = parseFloat(row.getAttribute("data-price"));

    // Kiểm tra điều kiện tìm kiếm
    const matchSearch =
      !searchTerm || name.includes(searchTerm) || location.includes(searchTerm);

    // Kiểm tra điều kiện giá
    const matchPrice = price >= minPrice && price <= maxPrice;

    // Hiển thị hoặc ẩn hàng
    if (matchSearch && matchPrice) {
      row.style.display = "";
      // Cập nhật STT
      row.querySelector("td:first-child").textContent = ++visibleCount;
    } else {
      row.style.display = "none";
    }
  });

  // Cập nhật số lượng kết quả
  document.getElementById("resultCount").textContent = visibleCount;

  // Hiển thị thông báo nếu không có kết quả
  const tbody = document.getElementById("homestayTableBody");
  const noResultRow = tbody.querySelector(".no-result-message");

  if (visibleCount === 0 && allRows.length > 0) {
    if (!noResultRow) {
      const newRow = document.createElement("tr");
      newRow.className = "no-result-message";
      newRow.innerHTML = `
                        <td colspan="9" class="no-results">
                            <i class="fas fa-search"></i>
                            <div>Không tìm thấy kết quả phù hợp</div>
                            <small style="color: #999; margin-top: 10px; display: block;">
                                Thử thay đổi tiêu chí tìm kiếm
                            </small>
                        </td>
                    `;
      tbody.appendChild(newRow);
    }
  } else if (noResultRow) {
    noResultRow.remove();
  }
}

// Hàm reset filter
function resetFilter() {
  document.getElementById("searchInput").value = "";
  document.getElementById("minPrice").value = "";
  document.getElementById("maxPrice").value = "";

  // Hiển thị tất cả các hàng
  allRows.forEach((row, index) => {
    row.style.display = "";
    row.querySelector("td:first-child").textContent = index + 1;
  });

  // Xóa thông báo không có kết quả
  const noResultRow = document
    .getElementById("homestayTableBody")
    .querySelector(".no-result-message");
  if (noResultRow) {
    noResultRow.remove();
  }

  // Cập nhật số lượng
  document.getElementById("resultCount").textContent = allRows.length;
}

// Tìm kiếm khi nhấn Enter
document
  .getElementById("searchInput")
  .addEventListener("keypress", function (e) {
    if (e.key === "Enter") {
      applyFilter();
    }
  });

// Tìm kiếm khi thay đổi giá
document.getElementById("minPrice").addEventListener("keypress", function (e) {
  if (e.key === "Enter") {
    applyFilter();
  }
});

document.getElementById("maxPrice").addEventListener("keypress", function (e) {
  if (e.key === "Enter") {
    applyFilter();
  }
});

// Khởi tạo số lượng ban đầu
document.addEventListener("DOMContentLoaded", function () {
  document.getElementById("resultCount").textContent = allRows.length;
});
