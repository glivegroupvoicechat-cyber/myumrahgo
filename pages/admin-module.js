(() => {
  const qs = new URLSearchParams(location.search);
  const module = qs.get('module') || 'agencies';
  const api = '../api/';
  const endpoint = module === 'agencies' ? 'agency-list.php' : module === 'inventory' ? 'inventory.php?type=hotels' : null;
  const content = document.getElementById('content');
  const cards = document.getElementById('cards');
  if (!content) return;

  const esc = v => String(v ?? '').replace(/[&<>"']/g, s => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[s]));
  const table = (headers, rows) => `<div style="overflow:auto"><table class="table"><thead><tr>${headers.map(h=>`<th>${esc(h)}</th>`).join('')}</tr></thead><tbody>${rows}</tbody></table></div>`;

  async function load() {
    if (!endpoint) return;
    content.innerHTML = '<div class="empty">Loading live records…</div>';
    try {
      const res = await fetch(api + endpoint, { credentials: 'include', headers: { Accept: 'application/json' } });
      const data = await res.json();
      if (!res.ok) throw new Error(data.error || 'API request failed');
      const items = Array.isArray(data.items) ? data.items : [];
      if (module === 'agencies') {
        cards.innerHTML = ['Pending','Active','Suspended','Total'].map((x,i)=>`<div class="card"><small>${x}</small><strong>${i===3?items.length:'—'}</strong></div>`).join('');
        content.innerHTML = items.length ? table(['Agency','Email','Status','Created','Action'], items.map(a=>`<tr><td><b>${esc(a.name || a.agency_name)}</b></td><td>${esc(a.email)}</td><td><span class="badge">${esc(a.status)}</span></td><td>${esc(a.created_at)}</td><td><button class="btn dark" data-id="${esc(a.id)}">Open</button></td></tr>`).join('')) : '<div class="empty">No agencies found.</div>';
      } else {
        content.innerHTML = items.length ? table(['Hotel','City','Distance','Stars','Status'], items.map(h=>`<tr><td><b>${esc(h.name)}</b></td><td>${esc(h.city)}</td><td>${esc(h.distance_m)} m</td><td>${esc(h.stars || '—')}</td><td><span class="badge">${h.active ? 'Active' : 'Inactive'}</span></td></tr>`).join('')) : '<div class="empty">No inventory found.</div>';
      }
    } catch (e) {
      content.innerHTML = `<div class="empty"><b>Live API not available in this preview.</b><br><br>${esc(e.message)}<br><small>Deploy the PHP/MySQL backend on Hostinger and configure api/config.php to enable live data.</small></div>`;
    }
  }

  load();
  document.getElementById('search')?.addEventListener('input', e => {
    const term = e.target.value.toLowerCase();
    document.querySelectorAll('#content tbody tr').forEach(row => { row.style.display = row.innerText.toLowerCase().includes(term) ? '' : 'none'; });
  });
})();
