// assets/js/modal.js
// Reusable modal used by all pages instead of prompt().

function openModal(title, bodyHTML, onConfirm) {
  document.getElementById("modal-title").textContent = title;
  document.getElementById("modal-body").innerHTML = bodyHTML;
  const btn = document.getElementById("modal-confirm");
  // Remove old listener by cloning
  const fresh = btn.cloneNode(true);
  btn.parentNode.replaceChild(fresh, btn);
  fresh.addEventListener("click", onConfirm);
  document.getElementById("modal").classList.add("open");
}

function closeModal() {
  document.getElementById("modal").classList.remove("open");
}

// Close on backdrop click
document.getElementById("modal").addEventListener("click", function (e) {
  if (e.target === this) closeModal();
});

// Escape key
document.addEventListener("keydown", function (e) {
  if (e.key === "Escape") closeModal();
});

// XSS-safe text encoder
function escHtml(str) {
  const d = document.createElement("div");
  d.appendChild(document.createTextNode(String(str || "")));
  return d.innerHTML;
}
