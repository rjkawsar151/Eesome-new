import './bootstrap';
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

const initialiseProductShowcase = (root) => {
 const slides=[...root.querySelectorAll('[data-carousel-slide]')],details=[...root.querySelectorAll('[data-carousel-details]')],stage=root.querySelector('[data-carousel-stage]'),current=root.querySelector('[data-carousel-current]'),count=slides.length;
 if(!count)return;
 let active=0,start=null,timer=null;
 const reduced=window.matchMedia('(prefers-reduced-motion: reduce)'),mod=n=>(n+count)%count;
 const render=n=>{active=mod(n);const prev=mod(active-1),next=mod(active+1);slides.forEach((slide,i)=>{let position='hidden';if(i===active)position='active';else if(count===2)position='next';else if(count>2&&i===prev)position='previous';else if(count>2&&i===next)position='next';slide.dataset.position=position;slide.setAttribute('aria-current',position==='active'?'true':'false');slide.setAttribute('aria-hidden',position==='hidden'?'true':'false')});details.forEach((item,i)=>item.dataset.active=i===active?'true':'false');if(current)current.textContent=String(active+1)};
 const previous=()=>render(active-1),next=()=>render(active+1),stop=()=>{clearInterval(timer);timer=null},play=()=>{stop();if(count<3||reduced.matches||document.hidden||root.matches(':hover,:focus-within'))return;timer=setInterval(next,3000)};
 root.querySelector('[data-carousel-previous]')?.addEventListener('click',previous);root.querySelector('[data-carousel-next]')?.addEventListener('click',next);
 slides.forEach((slide,i)=>slide.addEventListener('click',()=>{if(i!==active)render(i)}));
 root.addEventListener('keydown',e=>{if(e.key==='ArrowLeft'){e.preventDefault();previous()}if(e.key==='ArrowRight'){e.preventDefault();next()}});
 stage?.addEventListener('pointerdown',e=>{if(e.pointerType==='mouse'&&e.button!==0)return;start=e.clientX;stage.setPointerCapture?.(e.pointerId)});
 stage?.addEventListener('pointerup',e=>{if(start===null)return;const distance=e.clientX-start;start=null;if(Math.abs(distance)>=45)(distance>0?previous:next)()});stage?.addEventListener('pointercancel',()=>start=null);
 root.addEventListener('mouseenter',stop);root.addEventListener('mouseleave',play);root.addEventListener('focusin',stop);root.addEventListener('focusout',play);document.addEventListener('visibilitychange',()=>document.hidden?stop():play());reduced.addEventListener?.('change',play);render(0);play();
};
document.querySelectorAll('[data-product-showcase]').forEach(initialiseProductShowcase);