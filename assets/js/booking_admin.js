document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".status-select").forEach((select) => {
    select.addEventListener("change", async function () {
      const bookingId = this.dataset.bookingId;
      const newStatus = this.value;
      const originalStatus =
        this.querySelector("option[selected]")?.value || this.value;

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

        if (result.trim() === "success") {
          alert("Cập nhật trạng thái thành công!");
          this.querySelectorAll("option").forEach((opt) => {
            opt.removeAttribute("selected");
          });
          this.querySelector(`option[value="${newStatus}"]`).setAttribute(
            "selected",
            "selected"
          );
        } else {
          alert("Có lỗi khi cập nhật trạng thái!");
          this.value = originalStatus;
        }
      } catch (err) {
        console.error("Error:", err);
        alert("Lỗi kết nối đến server!");
        this.value = originalStatus;
      }
    });
  });
});
