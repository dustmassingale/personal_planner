document.addEventListener('DOMContentLoaded', function () {
  const stepLabel = document.getElementById('stepLabel');
  const stepHint = document.getElementById('stepHint');

  const phase1 = document.getElementById('phase1');
  const phase2 = document.getElementById('phase2');
  const phase3 = document.getElementById('phase3');

  const nameInput = document.getElementById('valueName');
  const addBtn = document.getElementById('addBtn');
  const namesList = document.getElementById('namesList');
  const nameCount = document.getElementById('nameCount');
  const toPhase2 = document.getElementById('toPhase2');
  const toPhase2Msg = document.createElement('div'); toPhase2Msg.className='hint'; toPhase2.parentNode.appendChild(toPhase2Msg);
  const descContainer = document.getElementById('descContainer');
  const backTo1 = document.getElementById('backTo1');
  const toPhase3 = document.getElementById('toPhase3');

  const selectList = document.getElementById('selectList');
  const backTo2 = document.getElementById('backTo2');
  const finishBtn = document.getElementById('finishBtn');
  const finishMsg = document.getElementById('finishMsg');

  let values = [];

  function setStep(n, hint) {
    stepLabel.textContent = n;
    stepHint.textContent = hint || '';
    phase1.style.display = n===1?'' : 'none';
    phase2.style.display = n===2?'' : 'none';
    phase3.style.display = n===3?'' : 'none';
  }

  function fetchValues() {
    return fetch('/api/values.php').then(r => r.json()).then(list => { values = list||[]; return values; });
  }

  function renderNames() {
    namesList.innerHTML = '';
    values.forEach(v => {
      const li = document.createElement('li');
      li.textContent = v.name;
      const del = document.createElement('button'); del.textContent='Delete'; del.onclick = () => removeValue(v.id);
      li.appendChild(del);
      namesList.appendChild(li);
    });
    nameCount.textContent = values.length;
    // update toPhase2 state
    if (values.length >= 5) {
      toPhase2.classList.remove('btn-disabled'); toPhase2.disabled = false; toPhase2Msg.textContent='Good — you can continue to add descriptions.';
    } else {
      toPhase2.classList.add('btn-disabled'); toPhase2.disabled = true; toPhase2Msg.textContent='Enter at least 5 values before continuing.';
    }
  }

  function renderDescEditor() {
    descContainer.innerHTML = '';
    values.forEach(v => {
      const div = document.createElement('div');
      div.className = 'card';
      const label = document.createElement('label'); label.textContent = v.name;
      const input = document.createElement('input'); input.value = v.description || ''; input.style.width='100%';
      const save = document.createElement('button'); save.textContent='Save';
      save.onclick = () => updateValue(v.id, { name: v.name, description: input.value });
      div.appendChild(label); div.appendChild(input); div.appendChild(save);
      descContainer.appendChild(div);
    });
  }

  function renderSelector() {
    selectList.innerHTML = '';
    values.forEach(v => {
      const li = document.createElement('li');
      const cb = document.createElement('input'); cb.type='checkbox'; cb.checked = !!v.selected; cb.dataset.id = v.id;
      const span = document.createElement('span'); span.textContent = `${v.name} — ${v.description||''}`;
      li.appendChild(cb); li.appendChild(span);
      selectList.appendChild(li);
    });
    updateFinishState();
  }

  function updateFinishState() {
    const cbs = Array.from(selectList.querySelectorAll('input[type=checkbox]'));
    const selectedCount = cbs.filter(cb => cb.checked).length;
    finishBtn.disabled = !(selectedCount >=3 && selectedCount <=5);
    if (finishBtn.disabled) finishBtn.classList.add('btn-disabled'); else finishBtn.classList.remove('btn-disabled');
    finishMsg.textContent = `Selected: ${selectedCount} (require 3–5)`;
  }

  function addValue(name) {
    return fetch('/api/values.php', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({ name }) }).then(()=>fetchValues()).then(renderNames);
  }

  function updateValue(id, body) {
    return fetch('/api/values.php?id=' + encodeURIComponent(id), { method:'PUT', headers:{'Content-Type':'application/json'}, body: JSON.stringify(body) }).then(()=>fetchValues()).then(()=>{ renderDescEditor(); renderSelector(); renderNames(); });
  }

  function removeValue(id) {
    if (!confirm('Delete this value?')) return;
    return fetch('/api/values.php?id=' + encodeURIComponent(id), { method:'DELETE' }).then(()=>fetchValues()).then(()=>{ renderNames(); renderDescEditor(); renderSelector(); });
  }

  addBtn.addEventListener('click', () => {
    const name = nameInput.value.trim();
    if (!name) return alert('Name required');
    addValue(name).then(()=>{ nameInput.value=''; });
  });

  toPhase2.addEventListener('click', () => {
    if (values.length < 5) return alert('Please enter at least 5 values before continuing.');
    if (values.length > 10) {
      if (!confirm('You entered more than 10 values. Continue anyway?')) return;
    }
    setStep(2, 'Add a short personal description for each value.');
    renderDescEditor();
  });

  backTo1.addEventListener('click', () => { setStep(1, 'Enter 5–10 value names to start.'); });
  toPhase3.addEventListener('click', ()=>{
    setStep(3, 'Select 3–5 core values.');
    renderSelector();
  });

  backTo2.addEventListener('click', ()=>{ setStep(2, 'Add descriptions.'); });

  selectList.addEventListener('change', (e)=>{ if (e.target && e.target.type === 'checkbox') updateFinishState(); });

  finishBtn.addEventListener('click', ()=>{
    const cbs = Array.from(selectList.querySelectorAll('input[type=checkbox]'));
    const selected = cbs.filter(cb => cb.checked).map(cb => cb.dataset.id);
    if (selected.length < 3 || selected.length > 5) return alert('Please select between 3 and 5 core values.');
    const updates = values.map(v => ({ id: v.id, selected: selected.includes(String(v.id)) }));
    Promise.all(updates.map(u => updateValue(u.id, { name: values.find(x=>x.id==u.id).name, description: values.find(x=>x.id==u.id).description||'', selected: u.selected })))
      .then(()=>{ finishMsg.textContent = 'Core values saved.'; });
  });

  // initial load
  fetchValues().then(()=>{ renderNames(); setStep(1, 'Enter 5–10 value names to start.'); });

});
