document.addEventListener('DOMContentLoaded', function() {
  const startBtn = document.getElementById('startPlan');
  const weekStartInput = document.getElementById('weekStart');
  const step1 = document.getElementById('wp-step1');
  const step2 = document.getElementById('wp-step2');
  const step3 = document.getElementById('wp-step3');
  const daysContainer = document.getElementById('daysContainer');
  const review = document.getElementById('review');
  const savePlan = document.getElementById('savePlan');
  const confirmSave = document.getElementById('confirmSave');
  const exportIcs = document.getElementById('exportIcs');
  let planEntries = [];

  function setStep(n) { step1.style.display = n===1? '': 'none'; step2.style.display = n===2? '': 'none'; step3.style.display = n===3? '': 'none'; }

  function startPlanning() {
    const ws = weekStartInput.value;
    if (!ws) return alert('Pick a week start date');
    // generate 7 days starting from ws
    planEntries = [];
    const start = new Date(ws);
    for (let i=0;i<7;i++) {
      const d = new Date(start); d.setDate(start.getDate()+i);
      const iso = d.toISOString().slice(0,10);
      planEntries.push({ week_start: ws, day: iso, social:'', spiritual:'', physical:'', learning:'' });
    }
    renderDayEditors(); setStep(2);
  }

  function renderDayEditors() {
    daysContainer.innerHTML = '';
    planEntries.forEach((e, idx) => {
      const card = document.createElement('div'); card.className='card';
      const h = document.createElement('h3'); h.textContent = new Date(e.day).toDateString();
      const social = document.createElement('input'); social.placeholder='Social activity'; social.value=e.social;
      const spiritual = document.createElement('input'); spiritual.placeholder='Spiritual activity'; spiritual.value=e.spiritual;
      const physical = document.createElement('input'); physical.placeholder='Physical activity'; physical.value=e.physical;
      const learning = document.createElement('input'); learning.placeholder='Learning activity'; learning.value=e.learning;
      social.onchange = ()=> planEntries[idx].social = social.value;
      spiritual.onchange = ()=> planEntries[idx].spiritual = spiritual.value;
      physical.onchange = ()=> planEntries[idx].physical = physical.value;
      learning.onchange = ()=> planEntries[idx].learning = learning.value;
      card.appendChild(h); card.appendChild(social); card.appendChild(document.createElement('br'));
      card.appendChild(spiritual); card.appendChild(document.createElement('br'));
      card.appendChild(physical); card.appendChild(document.createElement('br'));
      card.appendChild(learning);
      daysContainer.appendChild(card);
    });
  }

  function reviewPlan() {
    review.innerHTML = '';
    planEntries.forEach(e=>{
      const div = document.createElement('div'); div.className='card';
      div.innerHTML = `<strong>${new Date(e.day).toDateString()}</strong><br>Social: ${e.social}<br>Spiritual: ${e.spiritual}<br>Physical: ${e.physical}<br>Learning: ${e.learning}`;
      review.appendChild(div);
    });
    setStep(3);
  }

  function saveToApi() {
    // POST array of entries
    fetch('/api/weekly_plan.php', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(planEntries) })
      .then(r=>r.json()).then(data=>{ alert('Plan saved'); console.log(data); setStep(1); });
  }

  function exportCalendar() {
    const ws = (planEntries[0] && planEntries[0].week_start) || weekStartInput.value;
    if (!ws) return alert('No week selected');
    const url = '/api/export_calendar.php?week_start=' + encodeURIComponent(ws);
    window.open(url, '_blank');
  }

  document.getElementById('backStep1').addEventListener('click', ()=> setStep(1));
  document.getElementById('backStep2').addEventListener('click', ()=> setStep(2));
  startBtn.addEventListener('click', startPlanning);
  savePlan.addEventListener('click', reviewPlan);
  confirmSave.addEventListener('click', saveToApi);
  if (exportIcs) exportIcs.addEventListener('click', exportCalendar);
  setStep(1);
});
