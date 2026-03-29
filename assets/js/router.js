// assets/js/router.js
// Router is loaded LAST so all component functions already exist.

const routes = {
  "":        ValuesPage,
  "#values": ValuesPage,
  "#roles":  RolesPage,
  "#goals":  GoalsPage,
  "#weekly": WeeklyPlanPage,
  "#export": ExportPage,
};

function router() {
  const hash = window.location.hash || "";
  const page = routes[hash] || ValuesPage;
  page();
}

window.addEventListener("hashchange", router);
window.addEventListener("load", router);
