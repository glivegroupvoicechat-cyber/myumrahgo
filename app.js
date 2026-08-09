const goTo=(path)=>{window.location.href=path};
const b2bBtn=document.getElementById('b2bBtn');
b2bBtn?.addEventListener('click',()=>goTo('pages/b2b-login.html'));
const storageKey='myumrahgo:saved-packages';
const readSaved=()=>{try{return JSON.parse(localStorage.getItem(storageKey)||'[]')}catch{return[]}};
const writeSaved=(items)=>localStorage.setItem(storageKey,JSON.stringify(items));
const saved=new Set(readSaved());
document.querySelectorAll('.package-card').forEach(card=>{const title=card.querySelector('h3')?.textContent?.trim()||'';const save=card.querySelector('.save');const cta=card.querySelector('.card-cta');if(!save)return;const sync=()=>{const active=saved.has(title);save.textContent=active?'♥':'♡';save.setAttribute('aria-pressed',String(active));save.title=active?'Remove from saved packages':'Save package'};sync();save.addEventListener('click',e=>{e.preventDefault();e.stopPropagation();saved.has(title)?saved.delete(title):saved.add(title);writeSaved([...saved]);sync()});cta?.addEventListener('click',()=>goTo(title==='Build Your Own Umrah'?'pages/package.html?mode=builder':`pages/package.html?package=${encodeURIComponent(title)}`))});
document.querySelectorAll('a[href^="#"]').forEach(link=>link.addEventListener('click',e=>{const id=link.getAttribute('href');if(!id||id==='#')return;const target=document.querySelector(id);if(!target)return;e.preventDefault();target.scrollIntoView({behavior:window.matchMedia('(prefers-reduced-motion: reduce)').matches?'auto':'smooth',block:'start'})});