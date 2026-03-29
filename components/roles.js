// components/roles.js

async function RolesPage() {
  const app = document.getElementById("app");
    // Load the roles.html partial using fetch (replaces jQuery .load)
    try {
      const resp = await fetch('components/partials/roles.html');
      if (!resp.ok) throw new Error('Failed to load roles partial: ' + resp.status + ' ' + resp.statusText);
      app.innerHTML = await resp.text();
    } catch (err) {
      // Surface a descriptive error to the caller (loadRoles will not run)
      throw new Error('Failed to load roles partial: ' + err.message);
    }
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
          <button class="w3-button w3-tiny w3-red delete-role" data-id="${r.id}">✕</button>
        </div>
      </div>
    </div>
  `).join("");
  attachRoleHandlers();
}

function attachRoleHandlers() {
  const buttons = document.querySelectorAll('#rolesList .delete-role');
  buttons.forEach(btn => {
    // remove any previously attached handler to avoid duplicates
    if (btn._handler) btn.removeEventListener('click', btn._handler);
    const id = btn.getAttribute('data-id');
    const handler = function () { deleteRole(id); };
    btn.addEventListener('click', handler);
    btn._handler = handler;
  });
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
