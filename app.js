const storageKey='myumrahgo:saved-packages';
const readSaved=()=>{try{return JSON.parse(localStorage.getItem(storageKey)||'[]')}catch{return[]}};
const writeSaved=v=>localStorage.setItem(storageKey,JSON.stringify(v));
const saved=new Set(readSaved());

// Mobile navigation
const menuBtn=document.querySelector('.menu-btn');
const mobileMenu=document.querySelector('.mobile-menu');
menuBtn?.addEventListener('click',()=>mobileMenu?.classList.toggle('open'));
mobileMenu?.querySelectorAll('a').forEach(a=>a.addEventListener('click',()=>mobileMenu.classList.remove('open')));

// Saved package buttons
const toast=document.getElementById('toast');
const showToast=(message)=>{if(!toast)return;toast.textContent=message;toast.classList.add('show');clearTimeout(window.__toastTimer);window.__toastTimer=setTimeout(()=>toast.classList.remove('show'),2200)};
document.querySelectorAll('.package-card').forEach(card=>{
 const title=card.querySelector('h3')?.textContent?.trim()||''; const save=card.querySelector('.save'); if(!save)return;
 const sync=()=>{const active=saved.has(title);save.textContent=active?'♥':'♡';save.setAttribute('aria-pressed',String(active));}; sync();
 save.addEventListener('click',e=>{e.preventDefault();e.stopPropagation();saved.has(title)?saved.delete(title):saved.add(title);writeSaved([...saved]);sync();showToast(saved.has(title)?'Package saved to your favourites':'Package removed from favourites')});
});

// Smooth anchor navigation
 document.querySelectorAll('a[href^="#"]').forEach(link=>link.addEventListener('click',e=>{const id=link.getAttribute('href');if(!id||id==='#')return;const target=document.querySelector(id);if(!target)return;e.preventDefault();target.scrollIntoView({behavior:matchMedia('(prefers-reduced-motion: reduce)').matches?'auto':'smooth',block:'start'});}));

// Small reveal animation without a framework
const revealItems=document.querySelectorAll('.reveal,.feature-grid a,.trust-grid>div,.package-card,.quick-links a');
if('IntersectionObserver' in window){const io=new IntersectionObserver(entries=>entries.forEach(entry=>{if(entry.isIntersecting){entry.target.classList.add('is-visible');io.unobserve(entry.target)}}),{threshold:.08});revealItems.forEach(el=>{el.classList.add('reveal-ready');io.observe(el)})}

// Make external-looking support links easy to replace from the CMS later.
document.querySelectorAll('[data-toast]').forEach(el=>el.addEventListener('click',()=>showToast(el.dataset.toast)));
