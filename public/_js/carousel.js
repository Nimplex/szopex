document.addEventListener("DOMContentLoaded", () => {
  const carousel = document.querySelector(".carousel");
  const main = document.querySelector("#main-cover");
  const list = document.querySelector(".carousel ul");
  const btnLeft = document.querySelector("#cover-container .left");
  const btnRight = document.querySelector("#cover-container .right");

  if (!carousel || !main || !list || !btnLeft || !btnRight) return;

  const items = [...list.querySelectorAll("img")];
  if (!items.length) return;

  let currentIndex = 0;

  function updateMain(index) {
    currentIndex = (index + items.length) % items.length;
    main.src = items[currentIndex].src;
  }

  items.values().forEach((item, index) => item.addEventListener("click", () => updateMain(index)));

  btnLeft.addEventListener("click", () => updateMain(currentIndex - 1));
  btnRight.addEventListener("click", () => updateMain(currentIndex + 1));
});
