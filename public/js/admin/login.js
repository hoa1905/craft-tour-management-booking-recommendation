// Chức năng ẩn/hiện mật khẩu
document.addEventListener('DOMContentLoaded', function () {
  const togglePassword = document.getElementById('togglePassword');
  const passwordField = document.getElementById('password');

  togglePassword.addEventListener('click', function () {
    if (passwordField.type === 'password') {
      passwordField.type = 'text'; // Hiện mật khẩu
      this.classList.remove('fa-eye');
      this.classList.add('fa-eye-slash'); // Đổi icon thành "ẩn mật khẩu"
    } else {
      passwordField.type = 'password'; // Ẩn mật khẩu
      this.classList.remove('fa-eye-slash');
      this.classList.add('fa-eye'); // Đổi icon thành "hiện mật khẩu"
    }
    // Thêm hiệu ứng phóng to khi click
    this.style.transform = 'scale(1.2)';
    setTimeout(() => {
      this.style.transform = 'translateY(-50%)'; // Đưa icon về vị trí ban đầu
    }, 200);
  });
});

document.addEventListener('DOMContentLoaded', function () {
  const form = document.querySelector('form');
  const email = document.getElementById('email');
  const password = document.getElementById('password');

  form.addEventListener('submit', function (e) {
    let valid = true;
    let errorMessages = [];

    // Xóa lỗi cũ
    document.querySelectorAll('.error-message-js').forEach(el => el.remove());

    // Kiểm tra email
    if (!email.value.trim()) {
      valid = false;
      errorMessages.push('Vui lòng nhập email.');
      showError(email, 'Vui lòng nhập email.');
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
      valid = false;
      errorMessages.push('Email không hợp lệ.');
      showError(email, 'Email không hợp lệ.');
    }

    // Kiểm tra mật khẩu
    if (!password.value.trim()) {
      valid = false;
      errorMessages.push('Vui lòng nhập mật khẩu.');
      showError(password, 'Vui lòng nhập mật khẩu.');
    } else if (password.value.trim().length < 6) {
      valid = false;
      errorMessages.push('Mật khẩu phải có ít nhất 6 ký tự.');
      showError(password, 'Mật khẩu phải có ít nhất 6 ký tự.');
    }

    // Nếu có lỗi, chặn gửi form
    if (!valid) {
      e.preventDefault();
    }
  });

  function showError(input, message) {
    const div = document.createElement('div');
    div.classList.add('error-message-js');
    div.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;
    input.closest('.form-group').appendChild(div);
  }
});
