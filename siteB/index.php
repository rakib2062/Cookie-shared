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

  <script type="text/javascript" src="http://127.0.0.1:8001/assets/js/ssl-ck-rpd.js"></script>

</body>
</html>
