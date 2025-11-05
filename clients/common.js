const API_BASE = 'http://localhost:8080';

function saveToken(token) {
  localStorage.setItem('token', token);
}
function getToken() {
  return localStorage.getItem('token');
}
function authHeaders() {
  const t = getToken();
  return t ? { 'Authorization': 'Bearer ' + t } : {};
}
async function api(path, options = {}) {
  const headers = Object.assign({ 'Content-Type': 'application/json' }, authHeaders(), options.headers || {});
  const res = await fetch(API_BASE + path, { ...options, headers });
  if (!res.ok) {
    let msg = 'Request failed';
    try {
      const data = await res.json();
      msg = data.error || JSON.stringify(data);
    } catch {}
    throw new Error(msg);
  }
  const contentType = res.headers.get('content-type') || '';
  if (contentType.includes('application/json')) return res.json();
  return res.text();
}
