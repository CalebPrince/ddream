/**
 * DDREAM front-end behaviour.
 * No dependencies. Every enhancement degrades to working HTML without it.
 */
(function () {
  'use strict';

  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* ---------------------------------------------------------------- Mobile nav */
  function initMobileNav() {
    var toggle = document.getElementById('navToggle');
    var drawer = document.getElementById('mobileNav');
    if (!toggle || !drawer) return;

    function open() {
      drawer.classList.remove('hidden');
      toggle.setAttribute('aria-expanded', 'true');
      document.body.style.overflow = 'hidden';
      var firstLink = drawer.querySelector('a, button');
      if (firstLink) firstLink.focus();
    }

    function close() {
      drawer.classList.add('hidden');
      toggle.setAttribute('aria-expanded', 'false');
      document.body.style.overflow = '';
      toggle.focus();
    }

    toggle.addEventListener('click', open);
    drawer.querySelectorAll('[data-nav-close]').forEach(function (el) {
      el.addEventListener('click', close);
    });
    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape' && !drawer.classList.contains('hidden')) close();
    });
  }

  /* ------------------------------------------------------------ Header shadow */
  function initHeaderShadow() {
    var header = document.getElementById('siteHeader');
    if (!header) return;

    var scrolled = false;
    function update() {
      var next = window.scrollY > 8;
      if (next === scrolled) return;
      scrolled = next;
      header.classList.toggle('shadow-panel', next);
      // Collapses the hanging brand plaque. See .brand-plaque in app.css.
      document.documentElement.classList.toggle('is-scrolled', next);
    }
    update();
    window.addEventListener('scroll', update, { passive: true });
  }

  /* --------------------------------------------------------- Hero slideshow */
  function initSlideshow() {
    var root = document.getElementById('heroSlideshow');
    if (!root) return;

    var slides = Array.prototype.slice.call(root.querySelectorAll('[data-slide]'));
    var dots = Array.prototype.slice.call(root.querySelectorAll('[data-slide-dot]'));
    var captionEl = root.querySelector('[data-slide-caption]');
    var kickerEl = root.querySelector('[data-slide-kicker]');
    var toggleBtn = root.querySelector('[data-slide-toggle]');
    var iconPause = root.querySelector('[data-icon-pause]');
    var iconPlay = root.querySelector('[data-icon-play]');
    if (slides.length < 2) return;

    var captions = slides.map(function (slide) {
      return slide.getAttribute('data-caption') || '';
    });

    var interval = parseInt(root.dataset.interval, 10) || 6000;
    var current = 0;
    var timer = null;
    var paused = reduceMotion;

    function show(next) {
      next = (next + slides.length) % slides.length;
      if (next === current) return;

      slides[current].classList.replace('opacity-100', 'opacity-0');
      slides[current].setAttribute('aria-hidden', 'true');
      slides[next].classList.replace('opacity-0', 'opacity-100');
      slides[next].removeAttribute('aria-hidden');

      dots.forEach(function (dot, i) {
        dot.setAttribute('aria-selected', i === next ? 'true' : 'false');
        dot.classList.toggle('bg-gold-500', i === next);
        dot.classList.toggle('bg-white/30', i !== next);
        dot.classList.toggle('hover:bg-white/60', i !== next);
      });

      if (captionEl) captionEl.textContent = captions[next] || '';
      var kicker = slides[next].getAttribute('data-kicker');
      if (kickerEl && kicker) kickerEl.textContent = kicker;

      current = next;
    }

    function start() {
      stop();
      if (paused) return;
      timer = window.setInterval(function () { show(current + 1); }, interval);
    }

    function stop() {
      if (timer) window.clearInterval(timer);
      timer = null;
    }

    function setPaused(state) {
      paused = state;
      if (toggleBtn) {
        toggleBtn.setAttribute('aria-pressed', state ? 'true' : 'false');
        toggleBtn.setAttribute('aria-label', state ? 'Play slideshow' : 'Pause slideshow');
        if (iconPause) iconPause.classList.toggle('hidden', state);
        if (iconPlay) iconPlay.classList.toggle('hidden', !state);
      }
      state ? stop() : start();
    }

    root.querySelector('[data-slide-next]').addEventListener('click', function () {
      show(current + 1); if (!paused) start();
    });
    root.querySelector('[data-slide-prev]').addEventListener('click', function () {
      show(current - 1); if (!paused) start();
    });
    if (toggleBtn) {
      toggleBtn.addEventListener('click', function () { setPaused(!paused); });
    }
    dots.forEach(function (dot, i) {
      dot.addEventListener('click', function () { show(i); if (!paused) start(); });
    });

    root.addEventListener('mouseenter', stop);
    root.addEventListener('mouseleave', function () { if (!paused) start(); });
    root.addEventListener('focusin', stop);
    root.addEventListener('focusout', function () { if (!paused) start(); });

    document.addEventListener('visibilitychange', function () {
      document.hidden ? stop() : (paused ? null : start());
    });

    // Keyboard support on the carousel itself.
    root.addEventListener('keydown', function (event) {
      if (event.key === 'ArrowRight') { show(current + 1); event.preventDefault(); }
      if (event.key === 'ArrowLeft') { show(current - 1); event.preventDefault(); }
    });

    setPaused(reduceMotion);
  }

  /* -------------------------------------------------------- Search tab group */
  function initSearchTabs() {
    var tabs = Array.prototype.slice.call(document.querySelectorAll('[data-search-tab]'));
    var intent = document.getElementById('searchIntent');
    var ctaLabel = document.querySelector('[data-cta-label]');
    if (!tabs.length) return;

    tabs.forEach(function (tab) {
      tab.addEventListener('click', function () {
        tabs.forEach(function (other) {
          var active = other === tab;
          other.setAttribute('aria-selected', active ? 'true' : 'false');
          other.classList.toggle('text-navy-700', active);
          other.classList.toggle('text-muted', !active);
          other.classList.toggle('hover:text-navy-600', !active);
          var underline = other.querySelector('[data-tab-underline]');
          if (underline) {
            underline.classList.toggle('bg-gold-500', active);
            underline.classList.toggle('bg-transparent', !active);
          }
        });
        if (intent) intent.value = tab.dataset.searchTab;
        if (ctaLabel) ctaLabel.textContent = tab.dataset.cta;
      });
    });
  }

  /* --------------------------------------------------------- Save (shortlist) */
  function initSaveButtons() {
    document.querySelectorAll('[data-save]').forEach(function (button) {
      button.addEventListener('click', function (event) {
        event.preventDefault();
        var saved = button.getAttribute('aria-pressed') === 'true';
        button.setAttribute('aria-pressed', saved ? 'false' : 'true');
        button.dataset.saved = saved ? 'false' : 'true';
        var svg = button.querySelector('svg');
        if (svg) svg.setAttribute('fill', saved ? 'none' : 'currentColor');
      });
    });
  }

  /* ------------------------------------------------------------ Scroll reveal */
  function initReveal() {
    var targets = document.querySelectorAll('.reveal');
    if (!targets.length) return;

    if (reduceMotion || !('IntersectionObserver' in window)) {
      targets.forEach(function (el) { el.classList.add('reveal-in'); });
      return;
    }

    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        var siblings = Array.prototype.slice.call(
          entry.target.parentElement ? entry.target.parentElement.children : []
        );
        var delay = Math.min(siblings.indexOf(entry.target), 5) * 60;
        window.setTimeout(function () { entry.target.classList.add('reveal-in'); }, delay);
        observer.unobserve(entry.target);
      });
    }, { rootMargin: '0px 0px -8% 0px', threshold: 0.08 });

    targets.forEach(function (el) { observer.observe(el); });
  }

  /* ------------------------------------------------- Listing filter panel */
  function initFilterPanel() {
    var panel = document.getElementById('filterPanel');
    if (!panel) return;

    var wide = window.matchMedia('(min-width: 1024px)');

    function sync(event) {
      // Above lg the panel is always shown; below it, collapse it so the
      // results are not pushed off the page. The markup ships open, so this
      // is the only thing that closes it.
      if (event.matches) {
        panel.open = true;
      } else if (!panel.dataset.touched) {
        panel.open = false;
      }
    }

    panel.addEventListener('toggle', function () {
      if (!wide.matches) panel.dataset.touched = 'true';
    });

    sync(wide);
    wide.addEventListener
      ? wide.addEventListener('change', sync)
      : wide.addListener(sync);
  }

  function init() {
    initMobileNav();
    initFilterPanel();
    initHeaderShadow();
    initSlideshow();
    initSearchTabs();
    initSaveButtons();
    initReveal();
  }

  document.readyState === 'loading'
    ? document.addEventListener('DOMContentLoaded', init)
    : init();
})();
