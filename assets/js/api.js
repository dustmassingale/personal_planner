// assets/js/api.js

async function api(endpoint, method = "GET", data = null) {
  const options = {
    method,
    headers: { "Content-Type": "application/json", "X-Requested-With": "XMLHttpRequest" },
  };
  if (data) options.body = JSON.stringify(data);

  let res;
  try {
    res = await fetch(`/api/${endpoint}.php`, options);
  } catch (err) {
    console.error("Network error:", err);
    throw new Error("Network error — are you connected?");
  }

  if (!res.ok) {
    const text = await res.text();
    console.error(`API ${endpoint} ${method} failed (${res.status}):`, text);
    throw new Error(`Server error ${res.status}`);
  }

  return res.json();
}
