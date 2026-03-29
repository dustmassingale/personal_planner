// components/goals.js
// Goals must link to a value_id (US3 requirement).

function GoalsPage() {
  document.getElementById("app").innerHTML = `
    <div class="page-fade">
      <div class="w3-row" style="margin-bottom:16px;">
        <div class="w3-col s8"><h2 class="w3-text-blue" style="margin:0;">Your Goals</h2></div>
        <div class="w3-col s4" style="text-align:right;">
          <button class="w3-button w3-green" onclick="showAddGoal()">+ Add Goal</button>
        </div>
      </div>
      <p class="w3-text-grey">Goals aligned to your values. Every goal must trace back to what matters.</p>
      <div id="goalsList"><div class="loading">Loading…</div></div>
    </div>
  `;
  loadGoals();
}

async function loadGoals() {
  try {
    // Load both in parallel
    const [goals, values] = await Promise.all([api("goals"), api("values")]);
    AppState.goals  = goals;
    AppState.values = values;
    renderGoals();
  } catch (e) {
    document.getElementById("goalsList").innerHTML =
      `<p class="error-msg">⚠ ${escHtml(e.message)}</p>`;
  }
}

const STATUS_COLORS = { active: "w3-blue", complete: "w3-green", paused: "w3-orange" };

function renderGoals() {
  const list = document.getElementById("goalsList");
  if (!AppState.goals.length) {
    list.innerHTML = `<p class="w3-text-grey">No goals yet. Add one above.</p>`;
    return;
  }
  const valueMap = Object.fromEntries(AppState.values.map(v => [v.id, v.name]));
  list.innerHTML = AppState.goals.map(g => {
    const valueName = valueMap[g.value_id] || "Unlinked";
    const statusColor = STATUS_COLORS[g.status] || "w3-grey";
    return `
      <div class="w3-card w3-white w3-margin-bottom" style="border-left:4px solid #9C27B0;">
        <div class="w3-padding">
          <div style="display:flex; justify-content:space-between; align-items:start;">
            <div>
              <span class="badge ${statusColor} w3-text-white" style="margin-bottom:6px;">${escHtml(g.status || "active")}</span>
              <span class="badge w3-light-grey" style="margin-left:4px;">${escHtml(g.category || "General")}</span><br>
              <strong>${escHtml(g.description)}</strong><br>
              <small class="w3-text-grey">↳ Value: ${escHtml(valueName)}</small>
            </div>
            <button class="w3-button w3-tiny w3-red" onclick="deleteGoal(${g.id})">✕</button>
          </div>
        </div>
      </div>
    `;
  }).join("");
}

async function showAddGoal() {
  // Ensure values are loaded for the dropdown
  if (!AppState.values.length) {
    AppState.values = await api("values");
  }
  const valueOptions = AppState.values.length
    ? AppState.values.map(v => `<option value="${v.id}">${escHtml(v.name)}</option>`).join("")
    : `<option disabled>No values yet — add one first</option>`;

  openModal("Add a Goal",
    `<label>Description <span style="color:red">*</span></label>
     <input id="f-goal-desc" placeholder="e.g. Read scripture daily" maxlength="255">
     <label>Category</label>
     <select id="f-goal-cat">
       <option value="Spiritual">Spiritual</option>
       <option value="Physical">Physical</option>
       <option value="Social">Social</option>
       <option value="Learning">Learning</option>
       <option value="Professional">Professional</option>
       <option value="Other">Other</option>
     </select>
     <label>Linked Value <span style="color:red">*</span></label>
     <select id="f-goal-value">${valueOptions}</select>
     <label>Status</label>
     <select id="f-goal-status">
       <option value="active">Active</option>
       <option value="paused">Paused</option>
       <option value="complete">Complete</option>
     </select>`,
    async () => {
      const description = document.getElementById("f-goal-desc").value.trim();
      const value_id    = document.getElementById("f-goal-value").value;
      if (!description) { alert("Description is required."); return; }
      if (!value_id)    { alert("Please link this goal to a value."); return; }
      await api("goals", "POST", {
        description,
        value_id:  parseInt(value_id),
        category:  document.getElementById("f-goal-cat").value,
        status:    document.getElementById("f-goal-status").value,
      });
      closeModal();
      loadGoals();
    }
  );
}

async function deleteGoal(id) {
  if (!confirm("Delete this goal?")) return;
  await api("goals", "DELETE", { id });
  loadGoals();
}
