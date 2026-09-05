/**
 * Arrows and dots for the FileBird gallery carousel.
 *
 * The scrolling itself is left to CSS scroll-snap, so touch dragging, momentum
 * and keyboard scrolling come from the browser. This only adds the controls and
 * keeps them in sync, which is why there is no slider library involved.
 */
(function () {
  var PAGE_EPSILON = 2;

  function pageCount(track) {
    if (track.clientWidth <= 0) {
      return 1;
    }

    return Math.max(1, Math.ceil((track.scrollWidth - PAGE_EPSILON) / track.clientWidth));
  }

  function currentPage(track) {
    if (track.clientWidth <= 0) {
      return 0;
    }

    return Math.round(track.scrollLeft / track.clientWidth);
  }

  function Carousel(root) {
    this.root = root;
    this.track = root.querySelector('.filebird-gallery');

    if (!this.track) {
      return;
    }

    this.prev = root.querySelector('.filebird-carousel__nav--prev');
    this.next = root.querySelector('.filebird-carousel__nav--next');
    this.dots = root.querySelector('.filebird-carousel__dots');
    this.dotLabel = root.getAttribute('data-dot-label') || 'Go to slide %d';

    this.bind();
    this.refresh();
  }

  Carousel.prototype.bind = function () {
    var self = this;

    if (this.prev) {
      this.prev.addEventListener('click', function () {
        self.scrollToPage(currentPage(self.track) - 1);
      });
    }

    if (this.next) {
      this.next.addEventListener('click', function () {
        self.scrollToPage(currentPage(self.track) + 1);
      });
    }

    var scrollTimer = null;
    this.track.addEventListener('scroll', function () {
      if (scrollTimer) {
        window.clearTimeout(scrollTimer);
      }
      scrollTimer = window.setTimeout(function () {
        self.syncState();
      }, 60);
    });

    if (window.ResizeObserver) {
      new window.ResizeObserver(function () {
        self.refresh();
      }).observe(this.track);
    } else {
      window.addEventListener('resize', function () {
        self.refresh();
      });
    }
  };

  Carousel.prototype.scrollToPage = function (page) {
    var total = pageCount(this.track);
    var target = Math.min(Math.max(page, 0), total - 1);

    this.track.scrollTo({ left: target * this.track.clientWidth, behavior: 'smooth' });
  };

  Carousel.prototype.refresh = function () {
    var total = pageCount(this.track);
    var scrollable = this.track.scrollWidth - this.track.clientWidth > PAGE_EPSILON;

    // Nothing to page through: hide the controls rather than leave dead buttons.
    this.root.classList.toggle('filebird-carousel--static', !scrollable);

    if (this.dots) {
      this.buildDots(scrollable ? total : 0);
    }

    this.syncState();
  };

  Carousel.prototype.buildDots = function (total) {
    if (this.dots.children.length === total) {
      return;
    }

    var self = this;
    this.dots.innerHTML = '';

    for (var i = 0; i < total; i++) {
      (function (index) {
        var dot = document.createElement('button');
        dot.type = 'button';
        dot.className = 'filebird-carousel__dot';
        dot.setAttribute('aria-label', self.dotLabel.replace('%d', index + 1));
        dot.addEventListener('click', function () {
          self.scrollToPage(index);
        });
        self.dots.appendChild(dot);
      })(i);
    }
  };

  Carousel.prototype.syncState = function () {
    var total = pageCount(this.track);
    var page = currentPage(this.track);

    if (this.prev) {
      this.prev.disabled = page <= 0;
    }

    if (this.next) {
      this.next.disabled = page >= total - 1;
    }

    if (this.dots) {
      for (var i = 0; i < this.dots.children.length; i++) {
        var dot = this.dots.children[i];
        var active = i === page;
        dot.classList.toggle('is-active', active);
        dot.setAttribute('aria-current', active ? 'true' : 'false');
      }
    }
  };

  function init() {
    var roots = document.querySelectorAll('[data-filebird-carousel]');

    for (var i = 0; i < roots.length; i++) {
      if (!roots[i].filebirdCarousel) {
        roots[i].filebirdCarousel = new Carousel(roots[i]);
      }
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  // The Divi builder swaps module markup in without a page load.
  window.filebirdGalleryCarousel = { init: init };
})();
