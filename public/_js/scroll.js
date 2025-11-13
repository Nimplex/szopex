document.addEventListener("DOMContentLoaded", () => {
  const offers = document.getElementById("offers");
  let loading = false;

  function clearSettling() {
    setTimeout(() => [...document.getElementsByClassName("settling")].forEach(x => x.classList.remove("settling")), 100);
  }

  clearSettling();

  async function loadNextPage(page) {
    const throbber = document.getElementById("throbber");
    loading = true;
    const res = await fetch(`/listings/all.php?page=${page}`, {
      headers: {
        RAW_REQUEST: true,
      },
    });
    const html = await res.text();
    offers.insertAdjacentHTML("beforeend", html);
    clearSettling();
    throbber.remove();
    loading = false;
  }

  document.addEventListener("scroll", async () => {
    if (loading) return;
    const sentinel = document.getElementById("sentinel");
    if (!sentinel) return;

    const rect = sentinel.getBoundingClientRect();
    if (rect.top <= window.innerHeight) {
      const nextPage = sentinel.dataset.nextPage;
      sentinel.remove();
      await loadNextPage(nextPage);
    }
  });
});
