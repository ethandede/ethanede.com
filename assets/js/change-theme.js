document.addEventListener("DOMContentLoaded", function() {
  const primaryInput = document.getElementById('primaryColor');
  const secondaryInput = document.getElementById('secondaryColor');

  if (primaryInput) {
    primaryInput.addEventListener('input', function() {
      document.documentElement.style.setProperty('--primary-color', this.value);
      updateSquaresColor();
    });
  }

  if (secondaryInput) {
    secondaryInput.addEventListener('input', function() {
      document.documentElement.style.setProperty('--secondary-color', this.value);
    });
  }
});
