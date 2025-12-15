window.setPreview = input => {
  const file = input.files[0];
  const reader = new FileReader();
  const img = document.getElementById("profile-picture");

  reader.onloadend = () => img.src = reader.result;

  if (file) {
    reader.readAsDataURL(file);
  } else {
    img.src = "";
  }
}
