

// Tính số đêm
function calculateNights() {
    const checkinDate = new Date(document.getElementById('checkin').value);
    const checkoutDate = new Date(document.getElementById('checkout').value);
    const timeDiff = checkoutDate - checkinDate;
    const nights = Math.ceil(timeDiff / (1000 * 60 * 60 * 24));
    return nights > 0 ? nights : 1;
}

// Cập nhật giá
function updatePrice() {
    const nights = calculateNights();
    const subtotal = pricePerNight * nights;
    const serviceFee = subtotal * 0.1;
    const total = subtotal + serviceFee;

    // Cập nhật hiển thị
    document.getElementById('priceCalc').textContent = `${pricePerNight.toLocaleString('vi-VN')}đ x ${nights} đêm`;
    document.getElementById('subtotal').textContent = `${subtotal.toLocaleString('vi-VN')}đ`;
    document.getElementById('serviceFee').textContent = `${serviceFee.toLocaleString('vi-VN')}đ`;
    document.getElementById('totalPrice').textContent = `${total.toLocaleString('vi-VN')}đ`;

    // Cập nhật hidden inputs
    document.getElementById('nightsInput').value = nights;
    document.getElementById('subtotalInput').value = subtotal;
    document.getElementById('serviceFeeInput').value = serviceFee;
    document.getElementById('totalInput').value = total;
}

// Xác thực ngày trước khi submit
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

// Cập nhật giá khi thay đổi ngày
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

// Khởi tạo giá ban đầu
updatePrice();