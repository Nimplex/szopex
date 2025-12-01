window.openChat = function (event) {
  const target = event.target.closest("button");
  const { chatId } = target.dataset;
  console.log(target.dataset, chatId);

  const locationUrl = new URL(window.location);
  locationUrl.search = "";
  locationUrl.pathname = `/messages/${chatId}`;

  window.location.assign(locationUrl.toString());
};
