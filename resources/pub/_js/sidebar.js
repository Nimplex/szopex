document.addEventListener("DOMContentLoaded", () => {
  const animQuery = matchMedia("(prefers-reduced-motion: no-preference)");
  let hasAnim = animQuery.matches;

  animQuery.onchange = q => { hasAnim = q.matches };

  const sidebar = document.getElementById("sidebar");
  const fadeout = document.getElementById("sidebar-bg-fadeout");
  const content = document.getElementById("sidebar-pane");
  const buttonList = [
    "sidebar-open-button",
    "sidebar-close-button",
  ];

  let contInert = false;
  
  window.addEventListener("resize", () => {
    if (window.innerWidth > 1000) {
      content.inert = false;
    } else {
      // fix for the close animation playing every time
      // the user switches from desktop to mobile mode
      if (hasAnim && sidebar.hidden) {
        sidebar.style.display = "none";
        fadeout.style.display = "none";
        setTimeout(() => {
          sidebar.style.display = "";
          fadeout.style.display = "";
        }, 10);
      }

      content.inert = contInert;
    }
  })

  window.openSidebar = () => {
    if (!sidebar.hidden) return;

    buttonList.forEach(x => document.getElementById(x).ariaExpanded = true);
    sidebar.hidden = false;
    contInert = content.inert = true;
  }

  window.closeSidebar = () => {
    if (sidebar.hidden) return;

    buttonList.forEach(x => document.getElementById(x).ariaExpanded = false);
    sidebar.hidden = true;
    contInert = content.inert = false;
  }
})



