// components/export.js

function ExportPage() {
  const today = new Date();
  const dayOfWeek = today.getDay();
  const diff = dayOfWeek === 0 ? -6 : 1 - dayOfWeek;
  const monday = new Date(today);
  monday.setDate(today.getDate() + diff);
  const weekStart = monday.toISOString().slice(0, 10);

  document.getElementById("app").innerHTML = `
    <div class="page-fade">
      <h2 class="w3-text-blue">Export Weekly Plan</h2>
      <p class="w3-text-grey">Download your current week as a <strong>.ics</strong> calendar file
         compatible with Google Calendar, Apple Calendar, and Outlook.</p>

      <div class="w3-card w3-white w3-padding w3-margin-bottom">
        <h4 style="margin-top:0;">Current Week</h4>
        <p>Week of <strong>${weekStart}</strong></p>
        <a href="/api/export.php?week_start=${weekStart}"
           class="w3-button w3-blue" download="weekly_plan_${weekStart}.ics">
          ⬇ Download .ics
        </a>
      </div>

      <div class="w3-card w3-white w3-padding">
        <h4 style="margin-top:0;">Custom Week</h4>
        <label style="display:block; margin-bottom:6px;">Select week start (Monday):</label>
        <input type="date" id="export-date" value="${weekStart}"
               style="padding:8px; border:1px solid #ccc; border-radius:4px;">
        <br><br>
        <button class="w3-button w3-blue" onclick="downloadCustomExport()">⬇ Download .ics</button>
      </div>
    </div>
  `;
}

function downloadCustomExport() {
  const date = document.getElementById("export-date").value;
  if (!date) { alert("Please select a date."); return; }
  window.location.href = `/api/export.php?week_start=${encodeURIComponent(date)}`;
}
