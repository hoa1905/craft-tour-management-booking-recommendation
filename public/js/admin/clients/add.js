function togglePassword(fieldId) {
  let field = document.getElementById(fieldId);
  let button = field.nextElementSibling;
  let icon = button.querySelector('i');

  if (field.type === "password") {
    field.type = "text";
    icon.classList.replace("bi-eye-fill", "bi-eye-slash-fill");
  } else {
    field.type = "password";
    icon.classList.replace("bi-eye-slash-fill", "bi-eye-fill");
  }
}