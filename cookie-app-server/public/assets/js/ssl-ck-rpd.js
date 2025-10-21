
(async function syncTracker() {
  const origin = window.location.origin;
  const localKey = "ssl-ck-rpd-tracker_id";
  const baseUrl = "http://127.0.0.1:8001";

  function getCookie(name) {
    const match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
    return match ? match[2] : null;
  }

  if (getCookie(localKey)) return; // already synced

  try {
    const response = await fetch(`${baseUrl}/track`, {
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
  const syncUrl = `${baseUrl}/redirect-sync?origin=${encodeURIComponent(origin)}`;
  const trackerParam = new URLSearchParams(window.location.search).get("tracker_id");

  if (!trackerParam) {
    // window.location.href = syncUrl;
  } else {
    document.cookie = `${localKey}=${trackerParam}; path=/; Secure; SameSite=Lax;`;
    window.history.replaceState({}, document.title, window.location.pathname);
  }
})();