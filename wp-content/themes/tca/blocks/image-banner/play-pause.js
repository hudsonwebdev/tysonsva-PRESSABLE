/**
 * Image Banner: accessible play/pause control for background video.
 *
 * Loaded once on any page that renders the tca/image-banner block.
 * The button markup is emitted by render.php inside .video-banner. This
 * script wires up its behaviour and keeps the aria state in sync with the
 * <video> element.
 *
 * Styles live in src/scss/blocks/_image-banner.scss (.tca-video-pp-btn and
 * .video-banner.tca-pp-ready).
 */
(function () {
	'use strict';

	var INIT_ATTR = 'data-tca-pp-init';
	var LABEL_PLAY = 'Play background video';
	var LABEL_PAUSE = 'Pause background video';

	function attach(banner) {
		if (banner.getAttribute(INIT_ATTR)) return;
		var video = banner.querySelector('video');
		var btn = banner.querySelector('.tca-video-pp-btn');
		if (!video || !btn) return;
		banner.setAttribute(INIT_ATTR, '1');
		banner.classList.add('tca-pp-ready');

		function sync() {
			var paused = video.paused || video.ended;
			btn.setAttribute('aria-pressed', paused ? 'true' : 'false');
			btn.setAttribute('aria-label', paused ? LABEL_PLAY : LABEL_PAUSE);
		}

		btn.addEventListener('click', function () {
			if (video.paused || video.ended) {
				var p = video.play();
				if (p && typeof p.catch === 'function') {
					p.catch(function () {});
				}
			} else {
				video.pause();
			}
		});

		video.addEventListener('play', sync);
		video.addEventListener('playing', sync);
		video.addEventListener('pause', sync);
		video.addEventListener('ended', sync);

		sync();
	}

	function init() {
		var banners = document.querySelectorAll('.video-banner');
		for (var i = 0; i < banners.length; i++) {
			attach(banners[i]);
		}
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
