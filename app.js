const b2bBtn = document.getElementById('b2bBtn');

// Keep the prototype navigable without requiring a backend.
b2bBtn?.addEventListener('click', () => {
  window.location.href = 'pages/b2b.html';
});

document.querySelectorAll('.save').forEach((button) => {
  button.setAttribute('aria-label', 'Save package');
  button.addEventListener('click', () => {
    const saved = button.textContent.trim() === '♥';
    button.textContent = saved ? '♡' : '♥';
    button.setAttribute('aria-pressed', String(!saved));
  });
});

document.querySelectorAll('.package-card').forEach((card) => {
  const cta = card.querySelector('.card-cta');
  if (!cta) return;

  cta.addEventListener('click', () => {
    const title = card.querySelector('h3')?.textContent?.trim();
    const isBuilder = title === 'Build Your Own Umrah';
    const target = isBuilder ? 'pages/package.html?mode=builder' : `pages/package.html?package=${encodeURIComponent(title || '')}`;
    window.location.href = target;
  });
});

// Gentle in-page motion for anchor navigation.
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
