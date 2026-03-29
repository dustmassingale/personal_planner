// components/roles.js

function RolesPage() {
  document.getElementById("app").innerHTML = `
    <div class="page-fade">
      <div class="w3-row" style="margin-bottom:16px;">
        <div class="w3-col s8"><h2 class="w3-text-blue" style="margin:0;">Your Roles</h2></div>
        <div class="w3-col s4" style="text-align:right;">
          <button class="w3-button w3-green" onclick="showAddRole()">+ Add Role</button>
        </div>
      </div>
      <p class="w3-text-grey">Who are you responsible to? Parent, spouse, leader, professional…</p>
      <div id="rolesList"><div class="loading">Loading…</div></div>
    </div>
  `;
  loadRoles();
}

async function loadRoles() {
  try {
    AppState.roles = await api("roles");
    renderRoles();
  } catch (e) {
    document.getElementById("rolesList").innerHTML =
      `<p class="error-msg">⚠ ${escHtml(e.message)}</p>`;
  }
}

function renderRoles() {
  const list = document.getElementById("rolesList");
  if (!AppState.roles.length) {
    list.innerHTML = `<p class="w3-text-grey">No roles yet. Add one to get started.</p>`;
    return;
  }
  list.innerHTML = AppState.roles.map(r => `
    <div class="w3-card w3-white w3-margin-bottom" style="border-left:4px solid #4CAF50;">
      <div class="w3-padding">
        <div style="display:flex; justify-content:space-between; align-items:start;">
          <div>
            <strong>${escHtml(r.name)}</strong><br>
            <small class="w3-text-grey">${escHtml(r.description || "")}</small><br>
            ${r.impact ? `<small><em>Impact: ${escHtml(r.impact)}</em></small>` : ""}
          </div>
          <button class="w3-button w3-tiny w3-red" onclick="deleteRole(${r.id})">✕</button>
        </div>
      </div>
    </div>
  `).join("");
}

function showAddRole() {
  openModal("Add a Role",
    `<label>Role Name <span style="color:red">*</span></label>
     <input id="f-role-name" placeholder="e.g. Parent" maxlength="100">
     <label>Description</label>
     <textarea id="f-role-desc" placeholder="What does this role mean to you?"></textarea>
     <label>Desired Impact</label>
     <input id="f-role-impact" placeholder="e.g. Raise confident, kind children" maxlength="200">`,
    async () => {
      const name = document.getElementById("f-role-name").value.trim();
      if (!name) { alert("Role name is required."); return; }
      await api("roles", "POST", {
        name,
        description: document.getElementById("f-role-desc").value.trim(),
        impact: document.getElementById("f-role-impact").value.trim(),
      });
      closeModal();
      loadRoles();
    }
  );
}

async function deleteRole(id) {
  if (!confirm("Delete this role?")) return;
  await api("roles", "DELETE", { id });
  loadRoles();
}
