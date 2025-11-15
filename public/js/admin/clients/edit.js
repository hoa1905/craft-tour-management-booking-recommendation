function togglePassword(fieldId) {
  const field = document.getElementById(fieldId);
  const button = field.nextElementSibling;
  const icon = button.querySelector('i');

  if (field.type === "password") {
    field.type = "text";
    icon.classList.replace("bi-eye", "bi-eye-slash");
  } else {
    field.type = "password";
    icon.classList.replace("bi-eye-slash", "bi-eye");
  }
}