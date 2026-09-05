/**
 * Events Location and List: accordion sidebar + map (no overlay drawer).
 */
(function () {
	'use strict';

	var ACCESS_TOKEN = (window.TCA_MAP && window.TCA_MAP.mapboxToken) || '';

	function getAccessToken() {
		return (window.TCA_MAP && window.TCA_MAP.mapboxToken) || ACCESS_TOKEN || '';
	}

	function parseMarkers(raw) {
		if (!raw) {
			return [];
		}
		try {
			var parsed = JSON.parse(raw);
			return Array.isArray(parsed) ? parsed : [];
		} catch (e) {
			return [];
		}
	}

	function setOpenItem(wrap, index, open) {
		wrap.querySelectorAll('.tca-evloc-list__item').forEach(function (item) {
			var itemIndex = parseInt(item.getAttribute('data-marker-index'), 10);
			var isTarget = itemIndex === index;
			var shouldOpen = open && isTarget;
			var btn = item.querySelector('.tca-evloc-list__title');
			var panel = item.querySelector('.tca-evloc-list__panel');

			item.classList.toggle('is-open', shouldOpen);
			if (btn) {
				btn.setAttribute('aria-expanded', shouldOpen ? 'true' : 'false');
			}
			if (panel) {
				panel.hidden = !shouldOpen;
			}
		});

		wrap.querySelectorAll('.tca-evloc-list-pin').forEach(function (pin) {
			var pinIndex = parseInt(pin.getAttribute('data-marker-index'), 10);
			var focused = open && pinIndex === index;
			pin.classList.toggle('is-focused', focused);
			pin.style.zIndex = focused ? '10' : '';
		});
	}

	function focusPin(map, data) {
		if (!map || typeof data.lng !== 'number' || typeof data.lat !== 'number') {
			return;
		}
		try {
			map.easeTo({
				center: [data.lng, data.lat],
				zoom: Math.max(map.getZoom(), 13),
				padding: { top: 40, bottom: 40, left: 40, right: 40 },
				duration: 500,
				essential: true,
			});
		} catch (err) {
			map.easeTo({
				center: [data.lng, data.lat],
				zoom: 13,
				duration: 500,
			});
		}
	}

	function selectLocation(wrap, map, data, index, forceOpen) {
		var item = wrap.querySelector('.tca-evloc-list__item[data-marker-index="' + index + '"]');
		var isOpen = item && item.classList.contains('is-open');
		var nextOpen = typeof forceOpen === 'boolean' ? forceOpen : !isOpen;

		setOpenItem(wrap, index, nextOpen);

		if (nextOpen && item) {
			try {
				item.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
			} catch (err) {
				/* ignore */
			}
		}

		if (nextOpen && map && data) {
			focusPin(map, data);
		}
	}

	function initMaps() {
		if (typeof mapboxgl === 'undefined') {
			return;
		}
		mapboxgl.accessToken = getAccessToken();

		document.querySelectorAll('[data-tca-evloc-list]').forEach(function (wrap) {
			if (wrap.getAttribute('data-tca-evloc-list-initialized') === '1') {
				return;
			}

			var canvas = wrap.querySelector('.tca-evloc-list-map-canvas');
			if (!canvas) {
				return;
			}

			var markers = parseMarkers(wrap.getAttribute('data-markers'))
				.map(function (m) {
					if (!m || typeof m !== 'object') {
						return null;
					}
					var lat = typeof m.lat === 'number' ? m.lat : parseFloat(m.lat);
					var lng = typeof m.lng === 'number' ? m.lng : parseFloat(m.lng);
					if (!isFinite(lat) || !isFinite(lng)) {
						return null;
					}
					m.lat = lat;
					m.lng = lng;
					return m;
				})
				.filter(Boolean);

			var style = wrap.getAttribute('data-map-style') || 'mapbox://styles/mapbox/streets-v12';
			var token = getAccessToken() || wrap.getAttribute('data-mapbox-token') || '';
			if (token) {
				mapboxgl.accessToken = token;
			}
			var pinShape = wrap.getAttribute('data-pin-shape') || 'teardrop';
			var pinSize = wrap.getAttribute('data-pin-size') || 15;
			if (!/^(teardrop|circle|square|diamond)$/.test(pinShape)) {
				pinShape = 'teardrop';
			}
			var pinLight = wrap.getAttribute('data-pin-light') === '1';

			if (!markers.length) {
				wrap.setAttribute('data-tca-evloc-list-initialized', '1');
				return;
			}

			var map = new mapboxgl.Map({
				container: canvas,
				style: style,
				center: [markers[0].lng, markers[0].lat],
				zoom: 12,
				cooperativeGestures: true,
			});
			wrap._tcaMapboxMap = map;

			map.on('error', function (e) {
				if (window.console && console.warn) {
					console.warn('[TCA events-location-list] Mapbox error', e && e.error ? e.error : e);
				}
			});

			map.addControl(new mapboxgl.NavigationControl({ showCompass: false }), 'top-right');

			var bounds = new mapboxgl.LngLatBounds();
			var bounded = 0;

			markers.forEach(function (m, index) {
				if (typeof m.lng !== 'number' || typeof m.lat !== 'number') {
					return;
				}
				bounds.extend([m.lng, m.lat]);
				bounded += 1;

				var pin = document.createElement('button');
				pin.style.width = pinSize + 'px';
				pin.style.height = pinSize + 'px';
				pin.type = 'button';
				pin.className = 'tca-evloc-list-pin tca-evloc-list-pin--' + pinShape;
				if (pinLight) {
					pin.classList.add('tca-evloc-list-pin--light');
				}
				pin.setAttribute('aria-label', m.locationName || 'Location');
				pin.setAttribute('data-marker-index', String(index));

				var pinAnchor = pinShape === 'diamond' ? 'center' : 'bottom';
				var marker = new mapboxgl.Marker({ element: pin, anchor: pinAnchor }).setLngLat([m.lng, m.lat]);
				if (typeof marker.removePopup === 'function') {
					marker.removePopup();
				}
				marker.addTo(map);

				pin.addEventListener('click', function (e) {
					e.preventDefault();
					e.stopPropagation();
					selectLocation(wrap, map, m, index, true);
				});
			});

			wrap.querySelectorAll('.tca-evloc-list__title').forEach(function (btn) {
				btn.addEventListener('click', function () {
					var index = parseInt(btn.getAttribute('data-marker-index'), 10);
					if (isNaN(index) || !markers[index]) {
						return;
					}
					selectLocation(wrap, map, markers[index], index);
				});
			});

			function fitToMarkers() {
				if (bounded === 1) {
					map.setZoom(13);
					return;
				}
				if (bounded > 1 && !bounds.isEmpty()) {
					map.fitBounds(bounds, { padding: 48, maxZoom: 14 });
				}
			}

			fitToMarkers();

			map.on('load', function () {
				map.resize();
				fitToMarkers();
			});
			map.on('idle', function onIdle() {
				map.off('idle', onIdle);
				map.resize();
			});
			setTimeout(function () {
				map.resize();
			}, 250);
			setTimeout(function () {
				map.resize();
			}, 600);

			if (typeof ResizeObserver !== 'undefined') {
				var ro = new ResizeObserver(function () {
					map.resize();
				});
				ro.observe(canvas);
			}

			wrap.setAttribute('data-tca-evloc-list-initialized', '1');
		});
	}

	function scheduleInit() {
		var attempts = 0;
		var maxAttempts = 40;

		function tryInit() {
			if (typeof mapboxgl === 'undefined') {
				attempts += 1;
				if (attempts < maxAttempts) {
					setTimeout(tryInit, 100);
				}
				return;
			}
			initMaps();
			setTimeout(initMaps, 100);
			setTimeout(initMaps, 400);
		}

		tryInit();
	}

	window.tcaEvlocListInitMaps = scheduleInit;

	function bindEditorPreviewHooks() {
		if (typeof acf !== 'undefined' && typeof acf.addAction === 'function') {
			acf.addAction('render_block_preview', function () {
				scheduleInit();
			});
			acf.addAction('render_block_preview/type=acf/events-location-list', function () {
				scheduleInit();
			});
			acf.addAction('render_block_preview/type=tca/events-location-list', function () {
				scheduleInit();
			});
		}
		document.addEventListener('acf-block-preview-ready', scheduleInit);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', function () {
			scheduleInit();
			bindEditorPreviewHooks();
		});
	} else {
		scheduleInit();
		bindEditorPreviewHooks();
	}
})();
