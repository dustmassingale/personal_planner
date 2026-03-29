// components/values.js

function ValuesPage() {
  document.getElementById("app").innerHTML = `
    <div class="page-fade">
      <div class="w3-row" style="margin-bottom:16px;">
        <div class="w3-col s8"><h2 class="w3-text-blue" style="margin:0;">Your Values</h2></div>
        <div class="w3-col s4" style="text-align:right;">
          <button class="w3-button w3-green" onclick="showAddValue()">+ Add Value</button>
        </div>
      </div>
      <p class="w3-text-grey">Define what matters most. Your goals and weekly plan flow from these.</p>
      <div id="valuesList"><div class="loading">Loading…</div></div>
    </div>
  `;
  loadValues();
}

async function loadValues() {
  try {
    AppState.values = await api("values");
    renderValues();
  } catch (e) {
    document.getElementById("valuesList").innerHTML =
      `<p class="error-msg">⚠ ${escHtml(e.message)}</p>`;
  }
}

function renderValues() {
  const list = document.getElementById("valuesList");
  if (!AppState.values.length) {
    list.innerHTML = `<p class="w3-text-grey">No values yet. Add one to get started.</p>`;
    return;
  }
  list.innerHTML = AppState.values.map(v => `
    <div class="w3-card w3-white w3-margin-bottom" style="border-left:4px solid #2196F3;">
      <div class="w3-padding">
        <div style="display:flex; justify-content:space-between; align-items:start;">
          <div>
            <strong>${escHtml(v.name)}</strong><br>
            <small class="w3-text-grey">${escHtml(v.description || "")}</small>
          </div>
          <button class="w3-button w3-tiny w3-red" onclick="deleteValue(${v.id})">✕</button>
        </div>
      </div>
    </div>
  `).join("");
}

function showAddValue() {
  openModal("Add a Value",
    `<label>Name <span style="color:red">*</span></label>
     <input id="f-val-name" placeholder="e.g. Family" maxlength="100">
     <label>Description</label>
     <textarea id="f-val-desc" placeholder="Why does this value matter to you?"></textarea>`,
    async () => {
      const name = document.getElementById("f-val-name").value.trim();
      if (!name) { alert("Name is required."); return; }
      await api("values", "POST", { name, description: document.getElementById("f-val-desc").value.trim() });
      closeModal();
      loadValues();
    }
  );
}

async function deleteValue(id) {
  if (!confirm("Delete this value? Goals linked to it will become unlinked.")) return;
  await api("values", "DELETE", { id });
  loadValues();
}
