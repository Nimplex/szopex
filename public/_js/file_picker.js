function previewFile(input) {
  const file = input.files[0];
  const reader = new FileReader();

  reader.onloadend = () => {
    const label = input.parentNode;
    const wrapper = label.parentNode;
    const grid = document.getElementById('image-input');
    const img = document.createElement('img');
    label.replaceChild(img, label.firstChild);

    img.src = reader.result;

    if (wrapper.className === "has-img") return;

    // new input field
    const new_field = document.createElement('div');
    new_field.innerHTML = `
<label>
    +
    <input
        type="file"
        class="sr-only"
        onchange="previewFile(this)"
        name="images[]"
        accept="image/jpeg,image/png"
    >
</label>
<button onclick="removeFile(this); event.preventDefault(); event.stopPropagation();">×</button>`;
    grid.insertBefore(new_field, wrapper.nextSibling);
    wrapper.className = "has-img";
    new_field.firstElementChild.focus();
  }

  if (file) {
    reader.readAsDataURL(file);
  } else {
    img.src = "";
  }
}

function removeFile(close) {
  close.parentNode.remove();
}
