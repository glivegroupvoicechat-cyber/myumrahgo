(() => {
  const scene=document.querySelector('.scene-card');
  if(scene && window.matchMedia('(pointer:fine)').matches){
    const wrap=scene.closest('.hero-scene');
    wrap?.addEventListener('mousemove',e=>{
      const r=wrap.getBoundingClientRect();
      const x=(e.clientX-r.left)/r.width-.5;
      const y=(e.clientY-r.top)/r.height-.5;
      scene.style.transform=`rotateY(${-5+x*8}deg) rotateX(${2-y*6}deg)`;
    });
    wrap?.addEventListener('mouseleave',()=>scene.style.transform='rotateY(-5deg) rotateX(2deg)');
  }
  document.querySelectorAll('[data-toast]').forEach(el=>el.addEventListener('click',e=>{
    e.preventDefault();
    const toast=document.getElementById('toast');
    if(!toast)return;
    toast.textContent=el.dataset.toast;
    toast.classList.add('show');
    clearTimeout(window.__enhToast);
    window.__enhToast=setTimeout(()=>toast.classList.remove('show'),2400);
  }));
})();
