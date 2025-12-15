import { pluralize } from './utils.js';

document.addEventListener("DOMContentLoaded", () => {
  let modified_fields = new Map();
  const fields = document.getElementsByClassName("check-updates");
  const modified_counter = document.getElementById("update-counter");
  const orig_display_style = modified_counter.style.display;
  
  for (const f of fields) {
    const input_arr = [...f.getElementsByTagName("input"), ...f.getElementsByTagName("textarea")];
    const indicator_arr = f.getElementsByClassName("edit-indicator");

    if (input_arr.length != 1 || indicator_arr.length != 1) continue;

    const input = input_arr[0];
    const indicator = indicator_arr[0];

    const orig_content = input.value;
    input.addEventListener("input", () => {
      if (input.value == orig_content) {
        indicator.classList.remove("show");
        modified_fields.delete(input.name);
      } else {
        indicator.classList.add("show");
        modified_fields.set(input.name, true);
      }

      const amount_modified = modified_fields.size;

      if (amount_modified > 0) {
        modified_counter.style.display = orig_display_style;
        modified_counter.innerText = `Masz ${amount_modified} ${pluralize(amount_modified, "niezapisaną zmianę", "niezapisane zmiany", "niezapisanych zmian")}`;
      } else {
        modified_counter.style.display = 'none';
      }
    })
  }
})

