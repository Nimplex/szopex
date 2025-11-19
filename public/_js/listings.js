async function favourite(event) {
  event.target.blur();

  const { listingId } = event.target.dataset;

  if (!listingId || listingId === "")
    return console.warn("No listingId found!");

  const res = await fetch("/api/listings/favourite", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: new URLSearchParams({ listingId }),
  });

  const contentType = res.headers.get("Content-Type");

  if (!contentType.startsWith("text/html"))
    return console.error("API returned invalid response!");

  const body = await res.text();
  const isFavourited = body == "yes";

  if (isFavourited) {
    event.target.classList.add("btn-red");
    event.target.innerHTML = "Usuń z ulubionych";
  } else {
    event.target.classList.remove("btn-red");
    event.target.innerHTML = "Dodaj do ulubionych";
  }
}

function message(event) {
  const { listingId } = event.target.dataset;

  if (!listingId || listingId === "")
    return console.warn("No listingId found!");
}
