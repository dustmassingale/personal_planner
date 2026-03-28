document.addEventListener('DOMContentLoaded', function () {
  const list = document.getElementById('valuesList');
  const addBtn = document.getElementById('addBtn');
  const nameInput = document.getElementById('valueName');
  const descInput = document.getElementById('valueDesc');

  function fetchList() {
    fetch('/api/values.php')
      .then(r => r.json())
      .then(render)
      .catch(err => console.error(err));
  }

  function render(items) {
    list.innerHTML = '';
    items.forEach(item => {
      const li = document.createElement('li');
      li.innerHTML = `<strong>${escapeHtml(item.name)}</strong> - ${escapeHtml(item.description || '')}`;
      const editBtn = document.createElement('button'); editBtn.textContent = 'Edit'; editBtn.dataset.id = item.id; editBtn.className = 'edit';
      const delBtn = document.createElement('button'); delBtn.textContent = 'Delete'; delBtn.dataset.id = item.id; delBtn.className = 'del';
      li.appendChild(editBtn); li.appendChild(delBtn);
      list.appendChild(li);
    });
  }

  addBtn.addEventListener('click', function () {
    const name = nameInput.value.trim();
    const description = descInput.value.trim();
    if (!name) return alert('Name is required');
    fetch('/api/values.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ name: name, description: description })
    }).then(() => { nameInput.value = ''; descInput.value = ''; fetchList(); });
  });

  list.addEventListener('click', function (e) {
    if (e.target.classList.contains('del')) {
      const id = e.target.dataset.id;
      if (!confirm('Delete this value?')) return;
      fetch('/api/values.php?id=' + encodeURIComponent(id), { method: 'DELETE' }).then(() => fetchList());
    }

    if (e.target.classList.contains('edit')) {
      const id = e.target.dataset.id;
      const li = e.target.closest('li');
      const currentName = li.querySelector('strong').textContent;
      const currentDesc = li.childNodes[2] ? li.childNodes[2].textContent.replace(' - ', '') : '';
      const newName = prompt('Edit value name', currentName);
      if (newName === null) return;
      const newDesc = prompt('Edit description', currentDesc);
      fetch('/api/values.php?id=' + encodeURIComponent(id), {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ name: newName, description: newDesc })
      }).then(() => fetchList());
    }
  });

  function escapeHtml(s) { return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;'); }

  fetchList();
});
