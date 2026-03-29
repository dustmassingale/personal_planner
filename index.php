<?php
// index.php — SPA Shell
// All routes handled client-side via hash routing
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Personal Improvement Devotional</title>
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    .nav-brand { font-weight: bold; font-size: 1.1em; padding: 12px 16px; color: #fff; }
    .page-fade { animation: fadeIn 0.2s ease; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: none; } }
    .loading { color: #999; padding: 20px; text-align: center; }
    .error-msg { color: #c0392b; padding: 10px; }
    .modal-overlay {
      display: none; position: fixed; top: 0; left: 0;
      width: 100%; height: 100%; background: rgba(0,0,0,0.4); z-index: 100;
      justify-content: center; align-items: center;
    }
    .modal-overlay.open { display: flex; }
    .modal-box { background: #fff; border-radius: 6px; padding: 24px; width: 90%; max-width: 420px; }
    .modal-box label { display: block; margin-top: 12px; font-size: 0.9em; color: #555; }
    .modal-box input, .modal-box textarea, .modal-box select {
      width: 100%; padding: 8px; margin-top: 4px;
      border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;
    }
    .modal-box textarea { height: 80px; resize: vertical; }
    .badge { display: inline-block; padding: 2px 8px; border-radius: 12px; font-size: 0.75em; }
  </style>
</head>
<body class="w3-light-grey">

  <!-- Navigation -->
  <div class="w3-bar w3-blue w3-card">
    <span class="nav-brand">📘 Devotional</span>
    <a href="#values"  class="w3-bar-item w3-button">Values</a>
    <a href="#roles"   class="w3-bar-item w3-button">Roles</a>
    <a href="#goals"   class="w3-bar-item w3-button">Goals</a>
    <a href="#weekly"  class="w3-bar-item w3-button">Weekly Plan</a>
    <a href="#export"  class="w3-bar-item w3-button">Export</a>
  </div>

  <!-- Main App Container -->
  <div id="app" class="w3-container" style="margin-top:24px; max-width:800px; margin-left:auto; margin-right:auto;"></div>

  <!-- Modal (reused by all pages) -->
  <div class="modal-overlay" id="modal">
    <div class="modal-box">
      <h3 id="modal-title" style="margin-top:0;"></h3>
      <div id="modal-body"></div>
      <div style="margin-top:16px; text-align:right;">
        <button class="w3-button w3-grey w3-margin-right" onclick="closeModal()">Cancel</button>
        <button class="w3-button w3-blue" id="modal-confirm">Save</button>
      </div>
    </div>
  </div>

  <!-- JS: load order matters -->
  <script src="assets/js/state.js"></script>
  <script src="assets/js/api.js"></script>
  <script src="assets/js/modal.js"></script>
  <script src="components/values.js"></script>
  <script src="components/roles.js"></script>
  <script src="components/goals.js"></script>
  <script src="components/weekly_plan.js"></script>
  <script src="components/export.js"></script>
  <!-- Router last: components must exist before routes are registered -->
  <script src="assets/js/router.js"></script>

</body>
</html>
