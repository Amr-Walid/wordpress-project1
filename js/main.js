/* =========================================================
   GREENIO — interactions
   ========================================================= */
(function () {
  'use strict';

  /* ---------- Sticky header ---------- */
  const header = document.getElementById('siteHeader');
  const onScroll = () => {
    header.classList.toggle('scrolled', window.scrollY > 40);
  };
  onScroll();
  window.addEventListener('scroll', onScroll, { passive: true });

  /* ---------- Mobile nav ---------- */
  const toggle = document.getElementById('navToggle');
  const nav = document.getElementById('mainNav');
  toggle.addEventListener('click', () => {
    const open = nav.classList.toggle('open');
    toggle.classList.toggle('open', open);
    toggle.setAttribute('aria-expanded', open);
    document.body.style.overflow = open ? 'hidden' : '';
  });
  nav.querySelectorAll('a').forEach((a) =>
    a.addEventListener('click', () => {
      nav.classList.remove('open');
      toggle.classList.remove('open');
      toggle.setAttribute('aria-expanded', false);
      document.body.style.overflow = '';
    })
  );

  /* ---------- Reveal on scroll ---------- */
  const reveals = document.querySelectorAll('[data-reveal]');
  // stagger index within float row
  document.querySelectorAll('.float-row [data-reveal]').forEach((el, i) => {
    el.style.setProperty('--i', i);
  });

  if ('IntersectionObserver' in window) {
    const io = new IntersectionObserver(
      (entries) => {
        entries.forEach((e) => {
          if (e.isIntersecting) {
            e.target.classList.add('in');
            io.unobserve(e.target);
          }
        });
      },
      { threshold: 0.15, rootMargin: '0px 0px -8% 0px' }
    );
    reveals.forEach((el) => io.observe(el));
  } else {
    reveals.forEach((el) => el.classList.add('in'));
  }

  /* ---------- Count-up counters ---------- */
  function formatNumber(n) {
    return Math.floor(n).toLocaleString('en-US');
  }

  function animateCounter(el) {
    const target = +el.dataset.target;
    const suffix = el.dataset.suffix || '';
    const duration = 2000;
    const start = performance.now();

    function tick(now) {
      const p = Math.min((now - start) / duration, 1);
      // easeOutExpo
      const eased = p === 1 ? 1 : 1 - Math.pow(2, -10 * p);
      const val = target * eased;
      el.textContent = formatNumber(val) + suffix;
      if (p < 1) requestAnimationFrame(tick);
      else el.textContent = formatNumber(target) + suffix;
    }
    requestAnimationFrame(tick);
  }

  const counters = document.querySelectorAll('.counter');
  if ('IntersectionObserver' in window) {
    const cObs = new IntersectionObserver(
      (entries) => {
        entries.forEach((e) => {
          if (e.isIntersecting) {
            animateCounter(e.target);
            cObs.unobserve(e.target);
          }
        });
      },
      { threshold: 0.5 }
    );
    counters.forEach((c) => cObs.observe(c));
  } else {
    counters.forEach(animateCounter);
  }

  /* ---------- Live CO2 ticker (subtle real-time increment) ---------- */
  const co2 = document.querySelector('.stat-card .counter');
  if (co2) {
    let ticking = false;
    const startLive = () => {
      if (ticking) return;
      ticking = true;
      setInterval(() => {
        const cur = parseInt(co2.textContent.replace(/[^\d]/g, ''), 10) || 0;
        co2.textContent = formatNumber(cur + Math.floor(Math.random() * 9 + 3));
      }, 1400);
    };
    // begin live ticking a moment after the count-up finishes
    setTimeout(startLive, 3200);
  }

  /* ---------- Parallax ---------- */
  const parallaxEls = document.querySelectorAll('.parallax');
  let ticking = false;
  const runParallax = () => {
    parallaxEls.forEach((el) => {
      const rect = el.getBoundingClientRect();
      if (rect.bottom < 0 || rect.top > window.innerHeight) return;
      const speed = parseFloat(el.dataset.speed) || 0.1;
      const offset = (rect.top - window.innerHeight / 2) * -speed;
      el.style.transform = `translateY(${offset.toFixed(1)}px) scale(1.06)`;
    });
    ticking = false;
  };
  if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    window.addEventListener(
      'scroll',
      () => {
        if (!ticking) {
          requestAnimationFrame(runParallax);
          ticking = true;
        }
      },
      { passive: true }
    );
    runParallax();
  }

  /* ---------- Active nav link tracking ---------- */
  const sections = ['home', 'about', 'services', 'projects', 'contact'];
  const links = new Map();
  document.querySelectorAll('.main-nav a').forEach((a) => {
    const id = a.getAttribute('href').replace('#', '');
    links.set(id, a);
  });
  if ('IntersectionObserver' in window) {
    const sObs = new IntersectionObserver(
      (entries) => {
        entries.forEach((e) => {
          if (e.isIntersecting) {
            document
              .querySelectorAll('.main-nav a')
              .forEach((a) => a.classList.remove('active'));
            const link = links.get(e.target.id);
            if (link) link.classList.add('active');
          }
        });
      },
      { threshold: 0.5 }
    );
    sections.forEach((id) => {
      const el = document.getElementById(id);
      if (el) sObs.observe(el);
    });
  }
})();
