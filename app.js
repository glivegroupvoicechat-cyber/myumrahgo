const b2bBtn = document.getElementById('b2bBtn');

const goTo = (path) => {
  window.location.href = path;
};

b2bBtn?.addEventListener('click', () => goTo('pages/b2b.html'));

const storageKey = 'myumrahgo:saved-packages';
const readSaved = () => {
  try { return JSON.parse(localStorage.getItem(storageKey) || '[]'); }
  catch { return []; }
};
const writeSaved = (items) => localStorage.setItem(storageKey, JSON.stringify(items));

const saved = new Set(readSaved());

document.querySelectorAll('.package-card').forEach((card) => {
  const title = card.querySelector('h3')?.textContent?.trim() || '';
  const save = card.querySelector('.save');
  const cta = card.querySelector('.card-cta');
  if (!save) return;

  const syncSaveState = () => {
    const active = saved.has(title);
    save.textContent = active ? '♥' : '♡';
    save.setAttribute('aria-pressed', String(active));
    save.setAttribute('title', active ? 'Remove from saved packages' : 'Save package');
  };

  syncSaveState();
  save.addEventListener('click', (event) => {
    event.preventDefault();
    event.stopPropagation();
    if (saved.has(title)) saved.delete(title); else saved.add(title);
    writeSaved([...saved]);
    syncSaveState();
  });

  cta?.addEventListener('click', () => {
    const isBuilder = title === 'Build Your Own Umrah';
    const target = isBuilder
      ? 'pages/package.html?mode=builder'
      : `pages/package.html?package=${encodeURIComponent(title)}`;
    goTo(target);
  });
});

document.querySelectorAll('a[href^="#"]').forEach((link) => {
  link.addEventListener('click', (event) => {
    const id = link.getAttribute('href');
    if (!id || id === '#') return;
    const target = document.querySelector(id);
    if (!target) return;
    event.preventDefault();
    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
  });
});

// Respect reduced-motion preferences while keeping the experience accessible.
if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
  document.documentElement.style.scrollBehavior = 'auto';
}
