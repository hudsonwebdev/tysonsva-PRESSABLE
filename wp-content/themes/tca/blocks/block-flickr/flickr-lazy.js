/**
 * Load deferred Flickr images when they enter (or approach) the viewport.
 */
(function () {
	'use strict';

	function loadImage(img) {
		var src = img.getAttribute('data-src');
		if (!src) {
			return;
		}
		img.src = src;
		img.removeAttribute('data-src');
		img.classList.remove('flickr-photo__img--deferred');
	}

	function initAlbum(album) {
		var deferred = album.querySelectorAll('img.flickr-photo__img--deferred[data-src]');
		if (!deferred.length) {
			return;
		}

		if (!('IntersectionObserver' in window)) {
			deferred.forEach(loadImage);
			return;
		}

		var observer = new IntersectionObserver(
			function (entries) {
				entries.forEach(function (entry) {
					if (!entry.isIntersecting) {
						return;
					}
					loadImage(entry.target);
					observer.unobserve(entry.target);
				});
			},
			{ rootMargin: '300px 0px' }
		);

		deferred.forEach(function (img) {
			observer.observe(img);
		});
	}

	function initAll() {
		document.querySelectorAll('.flickr-album').forEach(initAlbum);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initAll);
	} else {
		initAll();
	}
})();
