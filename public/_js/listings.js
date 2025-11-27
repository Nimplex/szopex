async function favourite(event) {
  let target = event.target.closest("button");

  const { listingId } = target.dataset;

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
    target.classList.add("btn-red");
    target.querySelector("span").innerHTML = "Usuń z ulubionych";
  } else {
    target.classList.remove("btn-red");
    target.querySelector("span").innerHTML = "Dodaj do ulubionych";
  }
}

function message(event) {
  const { listingId } = event.target.dataset;

  if (!listingId || listingId === "")
    return console.warn("No listingId found!");
}
