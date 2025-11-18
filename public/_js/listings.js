async function favourite(event) {
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

  if (!contentType.startsWith("application/json"))
    return console.error("API returned invalid response!");

  const body = await res.json();

  console.log(body);
}

function message(event) {
  const { listingId } = event.target.dataset;

  if (!listingId || listingId === "")
    return console.warn("No listingId found!");
}
