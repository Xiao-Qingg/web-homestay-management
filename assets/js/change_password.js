function togglePassword(inputId) {
  const input = document.getElementById(inputId);
  const icon = input.nextElementSibling;

  if (input.type === "password") {
    input.type = "text";
    icon.classList.remove("fa-eye");
    icon.classList.add("fa-eye-slash");
  } else {
    input.type = "password";
    icon.classList.remove("fa-eye-slash");
    icon.classList.add("fa-eye");
  }
}

// Kiểm tra độ mạnh mật khẩu
const newPasswordInput = document.getElementById("new-password");
const strengthBar = document.getElementById("password-strength");

newPasswordInput.addEventListener("input", function () {
  const password = this.value;

  if (password.length > 0) {
    strengthBar.style.display = "block";

    // Kiểm tra các tiêu chí
    const hasLength = password.length >= 8;
    const hasUppercase = /[A-Z]/.test(password);
    const hasLowercase = /[a-z]/.test(password);
    const hasNumber = /[0-9]/.test(password);

    // Cập nhật checklist
    updateCheck("length-check", hasLength);
    updateCheck("uppercase-check", hasUppercase);
    updateCheck("lowercase-check", hasLowercase);
    updateCheck("number-check", hasNumber);

    // Tính điểm mạnh
    const score = [hasLength, hasUppercase, hasLowercase, hasNumber].filter(
      Boolean
    ).length;

    strengthBar.classList.remove("weak", "medium", "strong");

    if (score <= 2) {
      strengthBar.classList.add("weak");
    } else if (score === 3) {
      strengthBar.classList.add("medium");
    } else {
      strengthBar.classList.add("strong");
    }
  } else {
    strengthBar.style.display = "none";
  }
});

function updateCheck(id, isValid) {
  const element = document.getElementById(id);
  if (isValid) {
    element.classList.add("valid");
    element.querySelector("i").classList.remove("fa-circle");
    element.querySelector("i").classList.add("fa-check-circle");
  } else {
    element.classList.remove("valid");
    element.querySelector("i").classList.remove("fa-check-circle");
    element.querySelector("i").classList.add("fa-circle");
  }
}

// Kiểm tra mật khẩu khớp
const confirmPasswordInput = document.getElementById("confirm-password");
const matchHint = document.getElementById("match-hint");

confirmPasswordInput.addEventListener("input", function () {
  if (this.value.length > 0) {
    if (this.value !== newPasswordInput.value) {
      matchHint.style.display = "block";
      matchHint.style.color = "#f5576c";
      matchHint.innerHTML =
        '<i class="fas fa-exclamation-circle"></i> Mật khẩu không khớp';
    } else {
      matchHint.style.display = "block";
      matchHint.style.color = "#38ef7d";
      matchHint.innerHTML = '<i class="fas fa-check-circle"></i> Mật khẩu khớp';
    }
  } else {
    matchHint.style.display = "none";
  }
});

// Validate form trước khi submit
document
  .getElementById("change-password-form")
  .addEventListener("submit", function (e) {
    const newPassword = newPasswordInput.value;
    const confirmPassword = confirmPasswordInput.value;

    if (newPassword !== confirmPassword) {
      e.preventDefault();
      alert("Mật khẩu xác nhận không khớp!");
      return false;
    }

    // Kiểm tra độ mạnh tối thiểu
    const hasLength = newPassword.length >= 8;
    const hasUppercase = /[A-Z]/.test(newPassword);
    const hasLowercase = /[a-z]/.test(newPassword);
    const hasNumber = /[0-9]/.test(newPassword);

    if (!hasLength || !hasUppercase || !hasLowercase || !hasNumber) {
      e.preventDefault();
      alert("Mật khẩu mới chưa đủ mạnh! Vui lòng đáp ứng tất cả yêu cầu.");
      return false;
    }
  });
