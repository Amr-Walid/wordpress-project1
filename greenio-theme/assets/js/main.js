/**
 * Greenio theme — front-end interactions (vanilla JS, no dependencies).
 *
 * - Sticky header on scroll
 * - Mobile drawer navigation
 * - Reveal-on-scroll animations
 * - Live count-up counters (with a subtle real-time CO2 ticker)
 * - Smooth anchor scrolling with fixed-header offset
 * - Parallax + active nav link tracking + button ripple micro-interaction
 */
(function () {
  'use strict';

  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* ---------- Sticky header ---------- */
  var header = document.getElementById('siteHeader');
  function onScroll() {
    if (header) header.classList.toggle('scrolled', window.scrollY > 40);
  }
  onScroll();
  window.addEventListener('scroll', onScroll, { passive: true });

  /* ---------- Mobile nav drawer ---------- */
  var toggle = document.getElementById('navToggle');
  var nav = document.getElementById('mainNav');
  if (toggle && nav) {
    toggle.addEventListener('click', function () {
      var open = nav.classList.toggle('open');
      toggle.classList.toggle('open', open);
      toggle.setAttribute('aria-expanded', open);
      document.body.style.overflow = open ? 'hidden' : '';
    });
    nav.querySelectorAll('a').forEach(function (a) {
      a.addEventListener('click', function () {
        nav.classList.remove('open');
        toggle.classList.remove('open');
        toggle.setAttribute('aria-expanded', false);
        document.body.style.overflow = '';
      });
    });
  }

  /* ---------- Reveal on scroll ---------- */
  var reveals = document.querySelectorAll('[data-reveal]');
  document.querySelectorAll('.float-row [data-reveal]').forEach(function (el, i) {
    el.style.setProperty('--i', i);
  });
  if ('IntersectionObserver' in window) {
    var io = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (e) {
          if (e.isIntersecting) {
            e.target.classList.add('in');
            io.unobserve(e.target);
          }
        });
      },
      { threshold: 0.15, rootMargin: '0px 0px -8% 0px' }
    );
    reveals.forEach(function (el) { io.observe(el); });
  } else {
    reveals.forEach(function (el) { el.classList.add('in'); });
  }

  /* ---------- Count-up counters ---------- */
  function formatNumber(n) {
    return Math.floor(n).toLocaleString('en-US');
  }
  function animateCounter(el) {
    var target = parseInt(el.dataset.target, 10) || 0;
    var suffix = el.dataset.suffix || '';
    var duration = 2000;
    var start = performance.now();
    if (reduceMotion) { el.textContent = formatNumber(target) + suffix; return; }
    function tick(now) {
      var p = Math.min((now - start) / duration, 1);
      var eased = p === 1 ? 1 : 1 - Math.pow(2, -10 * p); // easeOutExpo
      el.textContent = formatNumber(target * eased) + suffix;
      if (p < 1) requestAnimationFrame(tick);
      else el.textContent = formatNumber(target) + suffix;
    }
    requestAnimationFrame(tick);
  }
  var counters = document.querySelectorAll('.counter');
  if ('IntersectionObserver' in window) {
    var cObs = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (e) {
          if (e.isIntersecting) {
            animateCounter(e.target);
            cObs.unobserve(e.target);
          }
        });
      },
      { threshold: 0.5 }
    );
    counters.forEach(function (c) { cObs.observe(c); });
  } else {
    counters.forEach(animateCounter);
  }

  /* ---------- Live CO2 ticker ---------- */
  var co2 = document.querySelector('.stat-card .counter');
  if (co2 && !reduceMotion) {
    var started = false;
    setTimeout(function startLive() {
      if (started) return;
      started = true;
      setInterval(function () {
        var cur = parseInt(co2.textContent.replace(/[^\d]/g, ''), 10) || 0;
        co2.textContent = formatNumber(cur + Math.floor(Math.random() * 9 + 3));
      }, 1400);
    }, 3200);
  }

  /* ---------- Smooth anchor scroll (fixed-header offset) ---------- */
  document.querySelectorAll('a[href^="#"]').forEach(function (link) {
    link.addEventListener('click', function (e) {
      var id = link.getAttribute('href');
      if (id === '#' || id.length < 2) return;
      var target = document.querySelector(id);
      if (!target) return;
      e.preventDefault();
      var headerH = header ? header.offsetHeight : 0;
      var top = target.getBoundingClientRect().top + window.pageYOffset - headerH - 10;
      window.scrollTo({ top: top, behavior: reduceMotion ? 'auto' : 'smooth' });
    });
  });

  /* ---------- Parallax ---------- */
  var parallaxEls = document.querySelectorAll('.parallax');
  var ticking = false;
  function runParallax() {
    parallaxEls.forEach(function (el) {
      var rect = el.getBoundingClientRect();
      if (rect.bottom < 0 || rect.top > window.innerHeight) return;
      var speed = parseFloat(el.dataset.speed) || 0.1;
      var offset = (rect.top - window.innerHeight / 2) * -speed;
      el.style.transform = 'translateY(' + offset.toFixed(1) + 'px) scale(1.06)';
    });
    ticking = false;
  }
  if (!reduceMotion && parallaxEls.length) {
    window.addEventListener('scroll', function () {
      if (!ticking) { requestAnimationFrame(runParallax); ticking = true; }
    }, { passive: true });
    runParallax();
  }

  /* ---------- Active nav link tracking ---------- */
  var sectionIds = ['home', 'about', 'services', 'projects', 'contact'];
  if ('IntersectionObserver' in window) {
    var navLinks = document.querySelectorAll('.main-nav a');
    var sObs = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (e) {
          if (e.isIntersecting) {
            navLinks.forEach(function (a) { a.classList.remove('active'); });
            navLinks.forEach(function (a) {
              if (a.getAttribute('href') === '#' + e.target.id) a.classList.add('active');
            });
          }
        });
      },
      { threshold: 0.5 }
    );
    sectionIds.forEach(function (id) {
      var el = document.getElementById(id);
      if (el) sObs.observe(el);
    });
  }

  /* ---------- Button ripple micro-interaction ---------- */
  document.querySelectorAll('.btn').forEach(function (btn) {
    btn.addEventListener('pointerdown', function (e) {
      if (reduceMotion) return;
      var rect = btn.getBoundingClientRect();
      var span = document.createElement('span');
      var size = Math.max(rect.width, rect.height);
      span.style.cssText =
        'position:absolute;border-radius:50%;pointer-events:none;transform:translate(-50%,-50%) scale(0);' +
        'background:rgba(255,255,255,.45);width:' + size + 'px;height:' + size + 'px;' +
        'left:' + (e.clientX - rect.left) + 'px;top:' + (e.clientY - rect.top) + 'px;' +
        'transition:transform .5s ease,opacity .6s ease;opacity:1;';
      btn.appendChild(span);
      requestAnimationFrame(function () {
        span.style.transform = 'translate(-50%,-50%) scale(2.4)';
        span.style.opacity = '0';
      });
      setTimeout(function () { span.remove(); }, 650);
    });
  });
})();
