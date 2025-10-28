document.addEventListener("DOMContentLoaded", () => {
  const offers = document.getElementById("offers");
  const throbber = document.getElementById("throbber");
  let loading = false;

  async function loadNextPage(page) {
    loading = true;
    throbber.style.display = "block";
    const res = await fetch(`/listings/all.php?page=${page}`, {
      headers: {
        RAW_REQUEST: true,
      },
    });
    const html = await res.text();
    offers.insertAdjacentHTML("beforeend", html);
    throbber.style.display = "none";
    window.history.pushState({"html": document.html, "pageTitle": document.title}, "", `/listings/all.php?page=${page}`);
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
