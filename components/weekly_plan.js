// components/weekly_plan.js

const DAYS = ["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"];

function WeeklyPlanPage() {
  // Default week_start = most recent Monday
  const today = new Date();
  const dayOfWeek = today.getDay(); // 0=Sun
  const diff = (dayOfWeek === 0) ? -6 : 1 - dayOfWeek;
  const monday = new Date(today);
  monday.setDate(today.getDate() + diff);
  const weekStart = monday.toISOString().slice(0, 10);

  document.getElementById("app").innerHTML = `
    <div class="page-fade">
      <div class="w3-row" style="margin-bottom:16px;">
        <div class="w3-col s8">
          <h2 class="w3-text-blue" style="margin:0;">Weekly Plan</h2>
          <small class="w3-text-grey">Week of ${weekStart}</small>
        </div>
        <div class="w3-col s4" style="text-align:right;">
          <button class="w3-button w3-green" onclick="showAddWeekly('${weekStart}')">+ Add Entry</button>
        </div>
      </div>
      <p class="w3-text-grey">Plan intentional time for each life dimension, every day.</p>
      <div id="weeklyList"><div class="loading">Loading…</div></div>
    </div>
  `;
  loadWeekly(weekStart);
}

async function loadWeekly(weekStart) {
  try {
    AppState.weekly = await api(`weekly_plan?week_start=${weekStart}`);
    renderWeekly();
  } catch (e) {
    document.getElementById("weeklyList").innerHTML =
      `<p class="error-msg">⚠ ${escHtml(e.message)}</p>`;
  }
}

function renderWeekly() {
  const list = document.getElementById("weeklyList");
  if (!AppState.weekly.length) {
    list.innerHTML = `<p class="w3-text-grey">No entries yet for this week. Add one above.</p>`;
    return;
  }
  // Group by day
  const byDay = {};
  AppState.weekly.forEach(w => {
    byDay[w.day] = byDay[w.day] || [];
    byDay[w.day].push(w);
  });

  list.innerHTML = DAYS.map(day => {
    const entries = byDay[day] || [];
    if (!entries.length) return "";
    return `
      <div class="w3-card w3-white w3-margin-bottom" style="border-left:4px solid #FF9800;">
        <div class="w3-padding">
          <strong>${day}</strong>
          ${entries.map(w => `
            <div style="margin-top:8px; padding-top:8px; border-top:1px solid #f0f0f0;">
              ${w.social    ? `<div>🤝 Social: ${escHtml(w.social)}</div>`    : ""}
              ${w.spiritual ? `<div>🙏 Spiritual: ${escHtml(w.spiritual)}</div>` : ""}
              ${w.physical  ? `<div>💪 Physical: ${escHtml(w.physical)}</div>`  : ""}
              ${w.learning  ? `<div>📚 Learning: ${escHtml(w.learning)}</div>`  : ""}
              <button class="w3-button w3-tiny w3-red" style="margin-top:4px;" onclick="deleteWeekly(${w.id})">✕ Remove</button>
            </div>
          `).join("")}
        </div>
      </div>
    `;
  }).join("");
}

function showAddWeekly(weekStart) {
  openModal("Add Weekly Entry",
    `<label>Day <span style="color:red">*</span></label>
     <select id="f-w-day">
       ${DAYS.map(d => `<option value="${d}">${d}</option>`).join("")}
     </select>
     <label>Social</label>
     <input id="f-w-social" placeholder="e.g. Call a friend">
     <label>Spiritual</label>
     <input id="f-w-spiritual" placeholder="e.g. Morning prayer, scripture">
     <label>Physical</label>
     <input id="f-w-physical" placeholder="e.g. 30-min walk">
     <label>Learning</label>
     <input id="f-w-learning" placeholder="e.g. Read 20 pages">`,
    async () => {
      const day = document.getElementById("f-w-day").value;
      await api("weekly_plan", "POST", {
        week_start: weekStart,
        day,
        social:    document.getElementById("f-w-social").value.trim(),
        spiritual: document.getElementById("f-w-spiritual").value.trim(),
        physical:  document.getElementById("f-w-physical").value.trim(),
        learning:  document.getElementById("f-w-learning").value.trim(),
      });
      closeModal();
      loadWeekly(weekStart);
    }
  );
}

async function deleteWeekly(id) {
  if (!confirm("Remove this entry?")) return;
  await api("weekly_plan", "DELETE", { id });
  // Re-derive weekStart from current page render
  const small = document.querySelector("small.w3-text-grey");
  const weekStart = small ? small.textContent.replace("Week of ", "").trim() : "";
  loadWeekly(weekStart);
}
