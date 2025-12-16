window.openChat = function (event) {
  const target = event.target.closest("button");
  const { chatId } = target.dataset;
  console.log(target.dataset, chatId);

  const locationUrl = new URL(window.location);
  locationUrl.search = "";
  locationUrl.pathname = `/messages/${chatId}`;

  window.location.assign(locationUrl.toString());
};

window.openSidebar = function (event) {
  const sidebarToggle = document.getElementById("sidebar-toggle");
  const sidebar = document.getElementById("chats-sidebar");
  const messageBox = document.getElementById("message-box");
  const rightDash = document.getElementById("right-dash");
  const isOpen = sidebar.dataset.open == "true";

  sidebar.dataset.open = isOpen ? "false" : "true";
  rightDash.style.transform = isOpen ? "" : "rotate(-180deg) translateX(1rem)";
  sidebar.style.display = isOpen ? "none" : "flex";
  messageBox.style.display = isOpen ? "flex" : "none";
}

window.addEventListener("resize", (event) => {
  const sidebarToggle = document.getElementById("sidebar-toggle");
  const sidebar = document.getElementById("chats-sidebar");
  const messageBox = document.getElementById("message-box");
  const isOpen = sidebar.dataset.open == "true";

  if (window.innerWidth > 768) {
    sidebar.style.display = "flex";
    messageBox.style.display = "flex";
  } else if (isOpen) {
    sidebar.style.display = "flex";
    messageBox.style.display = "none";
  } else {
    sidebar.style.display = "none";
    messageBox.style.display = "flex";
  }
});

document.addEventListener("DOMContentLoaded", () => {
  const sidebar = document.getElementById("chats-sidebar");
  const messageList = document.getElementById("message-list");

  messageList.scrollTop = messageList.scrollHeight;

  let loading = false;

  function clearSettling() {
    setTimeout(
      () =>
        [...document.getElementsByClassName("settling")].forEach((x) =>
          x.classList.remove("settling"),
        ),
      100,
    );
  }

  clearSettling();

  async function loadNextPage(page, chatId) {
    const throbber = document.getElementById("throbber");
    loading = true;
    const res = await fetch(`/messages/${chatId}?page=${page}`, {
      headers: {
        PARTIAL_REQ: true,
      },
    });
    const html = await res.text();
    messageList.insertAdjacentHTML("beforeend", html);
    clearSettling();
    throbber.remove();
    loading = false;
  }

  messageList.addEventListener("scroll", async () => {
    if (loading) return;
    const sentinel = document.getElementById("sentinel");
    if (!sentinel) return;

    const sentinelTop = sentinel.getBoundingClientRect().top;
    const listTop = messageList.getBoundingClientRect().top;
    
    if (sentinelTop >= listTop) {
      const { nextPage, chatId } = sentinel.dataset;
      sentinel.remove();
      await loadNextPage(nextPage, chatId);
    }
  });
});
