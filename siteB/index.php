<?php
// Simple demo page to show the tracking cookie value
$cookieName = 'ssl-ck-rpd-tracker_id';
$userId = $_COOKIE[$cookieName] ?? 'No cookie set yet';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Tracking Cookie Demo</title>
</head>
<body>
  <h2>Tracking Test Page</h2>

  <p><strong>Current Cookie (userId):</strong> 
    <span id="cookieValue"><?= htmlspecialchars($userId) ?></span>
  </p>

<script>
(async function syncTracker() {
  const origin = window.location.origin;
  const localKey = "ssl-ck-rpd-tracker_id";

  function getCookie(name) {
    const match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
    return match ? match[2] : null;
  }

  if (getCookie(localKey)) return; // already synced

  try {
    const response = await fetch("http://127.0.0.1:8001/track", {
      credentials: "include",
    });
    if (response.ok) {
      const data = await response.json();
      if (data.tracker_id) {
        document.cookie = `${localKey}=${data.tracker_id}; path=/; Secure; SameSite=Lax;`;
        return;
      }
    }
  } catch (e) {
    console.warn("Tracker fetch failed:", e);
  }

  // Fallback redirect-based sync
  const syncUrl = `http://127.0.0.1:8001/redirect-sync?origin=${encodeURIComponent(origin)}`;
  const trackerParam = new URLSearchParams(window.location.search).get("tracker_id");

  if (!trackerParam) {
    window.location.href = syncUrl;
  } else {
    document.cookie = `${localKey}=${trackerParam}; path=/; Secure; SameSite=Lax;`;
    window.history.replaceState({}, document.title, window.location.pathname);
  }
})();
</script>

</body>
</html>
