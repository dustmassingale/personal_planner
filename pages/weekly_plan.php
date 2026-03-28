<?php require_once __DIR__ . '/../components/header.php'; ?>

<main class="container">
  <h1>Weekly Plan — Guided</h1>
  <section class="card">
    <p>This guided flow helps you create a weekly plan aligned with your values across four areas: Social, Spiritual, Physical, Learning.</p>
  </section>

  <section id="wp-step1" class="card">
    <h2>Step 1 — Choose week start</h2>
    <input type="date" id="weekStart" />
    <button id="startPlan">Start Plan</button>
  </section>

  <section id="wp-step2" class="card" style="display:none">
    <h2>Step 2 — Plan Days</h2>
    <div id="daysContainer"></div>
    <div style="margin-top:12px"><button id="backStep1">Back</button> <button id="savePlan">Save Plan</button></div>
  </section>

  <section id="wp-step3" class="card" style="display:none">
    <h2>Step 3 — Review</h2>
    <div id="review"></div>
    <div style="margin-top:12px"><button id="backStep2">Back</button> <button id="confirmSave">Confirm & Finish</button></div>
  </section>

</main>

<script src="/assets/js/weekly_plan.js"></script>

<?php require_once __DIR__ . '/../components/footer.php'; ?>
