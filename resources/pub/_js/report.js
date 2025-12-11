window.report = async function(event) {
  const { target } = event;

  if (!target)
    return console.error("No target found");

  const { userId, listingId } = target.dataset;

  if (!userId && !listingId)
    return console.error("No data found");

  const reason = prompt("Powód zgłoszenia (nie może być pusty, max. 255 znaków)");

  if (reason.length <= 0 || reason.length > 255)
    return alert("Błąd długości");

  const params = new URLSearchParams();
  if (listingId) params.append('listing_id', listingId);
  if (userId) params.append('user_id', userId);
  params.append('reason', reason);
  
  const res = await fetch("/api/report", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: params,
  });

  const contentType = res.headers.get("Content-Type");

  if (!contentType.startsWith("text/html"))
    return alert("Wystąpił błąd serwera");

  const body = await res.text();
  const isReported = body == "yes";

  if (isReported) alert("Przesłano zgłoszenie");
  else alert("Wystąpił błąd zgłaszania");
}
