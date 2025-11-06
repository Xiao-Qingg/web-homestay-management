function recalculate() {
  const checkin = new Date(document.getElementById("checkinInput").value);
  const checkout = new Date(document.getElementById("checkoutInput").value);
  const nights = Math.ceil((checkout - checkin) / (1000 * 60 * 60 * 24));

  if (nights > 0) {
    const subtotal = pricePerNight * nights;

    document.getElementById("nightsDisplay").textContent = `${nights} đêm`;
    document.getElementById("nightsCalc").textContent = nights;
    document.getElementById("total").textContent = `${total.toLocaleString()}đ`;

    document.querySelector('input[name="nights"]').value = nights;
    document.querySelector('input[name="total"]').value = total;
  }
}

document.getElementById("checkinInput").addEventListener("change", recalculate);
document
  .getElementById("checkoutInput")
  .addEventListener("change", recalculate);

document.getElementById("bookingForm").addEventListener("submit", function (e) {
  if (!document.getElementById("terms").checked) {
    e.preventDefault();
    alert("Vui lòng đồng ý với điều khoản dịch vụ!");
    return false;
  }
});
document.querySelectorAll(".status-select").forEach((select) => {
  select.addEventListener("change", async function () {
    const bookingId = this.dataset.bookingId;
    const newStatus = this.value;

    try {
      const res = await fetch("../../handles/booking_process.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams({
          action: "update_status",
          booking_id: bookingId,
          status: newStatus,
        }),
      });

      const result = await res.text();
      console.log(result);

      if (res.ok) {
        alert("Cập nhật trạng thái thành công!");
      } else {
        alert("Có lỗi khi cập nhật trạng thái!");
      }
    } catch (err) {
      console.error(err);
      alert("Lỗi kết nối đến server!");
    }
  });
});