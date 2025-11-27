document.addEventListener("DOMContentLoaded", () => {
  document.getElementById("menu-toggle").addEventListener("click", (e) => {
    const btn = e.currentTarget;
    const menu = document.getElementById("mobile-container");
    const open = btn.getAttribute("aria-expanded") === "true";
    btn.innerText = open ? "≡" : "×";
    btn.setAttribute("aria-expanded", String(!open));
    menu.hidden = open;
  });
});
