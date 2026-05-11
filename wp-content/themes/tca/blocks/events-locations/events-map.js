/**
 * Events locations map: pins open a left drawer that slides over the map.
 */
(function () {
	'use strict';

	var ACCESS_TOKEN =
		'pk.eyJ1IjoidGNhc29mdHdhcmUiLCJhIjoiY2xzdWd6MHVpMTFxajJ2cjA4cnh6cmZ5cCJ9.e9dAUpCID8DqXU5WD0QOxw';
	var TRANSITION_MS = 400;

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

	function parseJsonStringAttr(el, attrName) {
		var raw = el.getAttribute(attrName);
		if (raw == null || raw === '') {
			return '';
		}
		try {
			var v = JSON.parse(raw);
			return typeof v === 'string' ? v : '';
		} catch (err) {
			return '';
		}
	}

	function fillDrawer(wrap, data) {
		var drawer = wrap.querySelector('.tca-events-locations-drawer');
		if (!drawer) {
			return;
		}

		var titleEl = drawer.querySelector('.tca-events-locations-drawer__title');
		var addrEl = drawer.querySelector('.tca-events-locations-drawer__address');
		var webEl = drawer.querySelector('.tca-events-locations-drawer__website');
		var imgWrap = drawer.querySelector('.tca-events-locations-drawer__image-wrap');
		var img = imgWrap && imgWrap.querySelector('img');
		var headingEl = drawer.querySelector('.tca-events-locations-drawer__events-heading');
		var listEl = drawer.querySelector('.tca-events-locations-drawer__events');
		var emptyEl = drawer.querySelector('.tca-events-locations-drawer__empty');
		var eventsCol = drawer.querySelector('.tca-events-locations-drawer__events-col');
		var colsEl = drawer.querySelector('.tca-events-locations-drawer__cols');

		if (titleEl) {
			titleEl.textContent = data.locationName || '';
		}

		if (addrEl) {
			if (data.address) {
				addrEl.textContent = data.address;
				addrEl.hidden = false;
			} else {
				addrEl.textContent = '';
				addrEl.hidden = true;
			}
		}

		if (webEl) {
			while (webEl.firstChild) {
				webEl.removeChild(webEl.firstChild);
			}
			webEl.hidden = true;
			var wUrl = typeof data.websiteUrl === 'string' ? data.websiteUrl.trim() : '';
			var wLabel = typeof data.websiteLabel === 'string' ? data.websiteLabel.trim() : '';
			if (wUrl && /^https?:\/\//i.test(wUrl)) {
				webEl.hidden = false;
				var a = document.createElement('a');
				a.href = wUrl;
				a.textContent = 'Website »';
				a.target = '_blank';
				a.rel = 'noopener noreferrer';
				webEl.appendChild(a);
			} else if (wLabel) {
				webEl.hidden = false;
				webEl.textContent = wLabel;
			}
		}

		if (img && imgWrap) {
			if (data.imageUrl) {
				img.src = data.imageUrl;
				img.alt = data.locationName || '';
				img.hidden = false;
				imgWrap.classList.remove('tca-events-locations-drawer__image-wrap--empty');
			} else {
				img.removeAttribute('src');
				img.alt = '';
				img.hidden = true;
				imgWrap.classList.add('tca-events-locations-drawer__image-wrap--empty');
			}
		}

		var showUpcoming = wrap.getAttribute('data-show-upcoming-events') === '1';
		var noEvtBehavior = wrap.getAttribute('data-no-events-behavior') || 'hide';
		var noEvtMessage = parseJsonStringAttr(wrap, 'data-no-events-message');
		var upcomingLabel = wrap.getAttribute('data-label-upcoming') || 'Upcoming events';

		if (!showUpcoming) {
			if (headingEl) {
				headingEl.textContent = '';
				headingEl.classList.remove('tca-events-locations-drawer__events-heading--message-only');
			}
			if (listEl) {
				listEl.innerHTML = '';
			}
			if (emptyEl) {
				emptyEl.hidden = true;
			}
			if (colsEl) {
				colsEl.classList.add('tca-events-locations-drawer__cols--no-events');
			}
			if (eventsCol) {
				eventsCol.hidden = true;
			}
			if (drawer) {
				drawer.classList.add('tca-events-locations-drawer--no-events');
			}
			return;
		}

		var events = data.events || [];
		var isEmpty = events.length === 0;

		if (isEmpty && noEvtBehavior === 'hide') {
			if (colsEl) {
				colsEl.classList.add('tca-events-locations-drawer__cols--no-events');
			}
			if (drawer) {
				drawer.classList.add('tca-events-locations-drawer--no-events');
			}
			if (eventsCol) {
				eventsCol.hidden = true;
			}
			if (headingEl) {
				headingEl.textContent = '';
				headingEl.classList.remove('tca-events-locations-drawer__events-heading--message-only');
			}
			if (listEl) {
				listEl.innerHTML = '';
			}
			if (emptyEl) {
				emptyEl.hidden = true;
			}
		} else if (isEmpty && noEvtBehavior === 'show_message') {
			if (colsEl) {
				colsEl.classList.remove('tca-events-locations-drawer__cols--no-events');
			}
			if (drawer) {
				drawer.classList.remove('tca-events-locations-drawer--no-events');
			}
			if (eventsCol) {
				eventsCol.hidden = false;
			}
			var msg = noEvtMessage.trim() ? noEvtMessage : upcomingLabel;
			if (headingEl) {
				headingEl.textContent = msg;
				headingEl.classList.add('tca-events-locations-drawer__events-heading--message-only');
			}
			if (listEl) {
				listEl.innerHTML = '';
			}
			if (emptyEl) {
				emptyEl.hidden = true;
			}
		} else {
			if (colsEl) {
				colsEl.classList.remove('tca-events-locations-drawer__cols--no-events');
			}
			if (drawer) {
				drawer.classList.remove('tca-events-locations-drawer--no-events');
			}
			if (eventsCol) {
				eventsCol.hidden = false;
			}
			if (headingEl) {
				headingEl.textContent = upcomingLabel;
				headingEl.classList.remove('tca-events-locations-drawer__events-heading--message-only');
			}
			if (listEl) {
				listEl.innerHTML = '';
				events.forEach(function (ev) {
					var li = document.createElement('li');
					var a = document.createElement('a');
					a.href = ev.url || '#';
					a.textContent = ev.title || '';
					li.appendChild(a);
					if (ev.date) {
						var span = document.createElement('span');
						span.className = 'tca-events-locations-drawer__event-date';
						span.textContent = ev.date;
						li.appendChild(span);
					}
					listEl.appendChild(li);
				});
			}
			if (emptyEl) {
				emptyEl.hidden = events.length > 0;
			}
		}
	}

	function openDrawer(wrap, map, data) {
		var drawer = wrap.querySelector('.tca-events-locations-drawer');
		var scrim = wrap.querySelector('.tca-events-locations-map-scrim');
		if (!drawer) {
			return;
		}

		fillDrawer(wrap, data);
		drawer.hidden = false;
		drawer.setAttribute('aria-hidden', 'false');
		void drawer.offsetWidth;
		wrap.classList.add('is-drawer-open');
		if (scrim) {
			scrim.hidden = false;
		}

		setTimeout(function () {
			if (!map) {
				return;
			}
			map.resize();
			if (typeof data.lng === 'number' && typeof data.lat === 'number') {
				try {
					map.easeTo({ center: [data.lng, data.lat], duration: 400, essential: true });
				} catch (err) {
					map.easeTo({ center: [data.lng, data.lat], duration: 400 });
				}
			}
		}, TRANSITION_MS);
	}

	function closeDrawer(wrap, map) {
		var drawer = wrap.querySelector('.tca-events-locations-drawer');
		var scrim = wrap.querySelector('.tca-events-locations-map-scrim');
		wrap.classList.remove('is-drawer-open');
		if (scrim) {
			scrim.hidden = true;
		}
		setTimeout(function () {
			if (drawer) {
				drawer.hidden = true;
				drawer.setAttribute('aria-hidden', 'true');
			}
			if (map) {
				map.resize();
			}
		}, TRANSITION_MS);
	}

	var escapeBound = false;
	function bindEscape() {
		if (escapeBound) {
			return;
		}
		escapeBound = true;
		document.addEventListener('keydown', function (e) {
			if (e.key !== 'Escape') {
				return;
			}
			document.querySelectorAll('[data-tca-events-locations-map].is-drawer-open').forEach(function (w) {
				closeDrawer(w, w._tcaMapboxMap);
			});
		});
	}

	function initMaps() {
		if (typeof mapboxgl === 'undefined') {
			return;
		}
		mapboxgl.accessToken = ACCESS_TOKEN;
		bindEscape();

		document.querySelectorAll('[data-tca-events-locations-map]').forEach(function (wrap) {
			if (wrap.getAttribute('data-tca-events-map-initialized') === '1') {
				return;
			}
			var canvas = wrap.querySelector('.tca-events-locations-map-canvas');
			if (!canvas) {
				return;
			}

			var markers = parseMarkers(wrap.getAttribute('data-markers'));
			var style = wrap.getAttribute('data-map-style') || 'mapbox://styles/mapbox/streets-v12';
			var pinShape = wrap.getAttribute('data-pin-shape') || 'teardrop';
			var pinSize = wrap.getAttribute('data-pin-size') || 15;
			if (!/^(teardrop|circle|square|diamond)$/.test(pinShape)) {
				pinShape = 'teardrop';
			}
			var pinLight = wrap.getAttribute('data-pin-light') === '1';

			if (!markers.length) {
				wrap.setAttribute('data-tca-events-map-initialized', '1');
				return;
			}

			var drawer = wrap.querySelector('.tca-events-locations-drawer');
			var closeBtn = drawer && drawer.querySelector('.tca-events-locations-drawer__close');
			var scrim = wrap.querySelector('.tca-events-locations-map-scrim');

			if (closeBtn) {
				closeBtn.addEventListener('click', function () {
					closeDrawer(wrap, wrap._tcaMapboxMap);
				});
			}
			if (scrim) {
				scrim.addEventListener('click', function () {
					closeDrawer(wrap, wrap._tcaMapboxMap);
				});
			}

			var map = new mapboxgl.Map({
				container: canvas,
				style: style,
				center: [markers[0].lng, markers[0].lat],
				zoom: 12,
			});
			wrap._tcaMapboxMap = map;

			map.addControl(new mapboxgl.NavigationControl({ showCompass: false }), 'top-right');

			var bounds = new mapboxgl.LngLatBounds();
			markers.forEach(function (m) {
				if (typeof m.lng !== 'number' || typeof m.lat !== 'number') {
					return;
				}
				bounds.extend([m.lng, m.lat]);

				var pin = document.createElement('button');

				pin.style.width = pinSize+'px';  // Set custom width
				pin.style.height = pinSize+'px';

				pin.type = 'button';
				pin.className = 'tca-events-locations-pin tca-events-locations-pin--' + pinShape;
				if (pinLight) {
					pin.classList.add('tca-events-locations-pin--light');
				}
				pin.setAttribute('aria-label', m.locationName || 'Location');

				var pinAnchor = pinShape === 'diamond' ? 'center' : 'bottom';
				var marker = new mapboxgl.Marker({ element: pin, anchor: pinAnchor }).setLngLat([m.lng, m.lat]);
				if (typeof marker.removePopup === 'function') {
					marker.removePopup();
				}
				marker.addTo(map);

				pin.addEventListener('click', function (e) {
					e.preventDefault();
					e.stopPropagation();
					openDrawer(wrap, map, m);
				});
			});

			if (markers.length === 1) {
				map.setZoom(13);
			} else {
				map.fitBounds(bounds, { padding: 48, maxZoom: 14 });
			}

			wrap.setAttribute('data-tca-events-map-initialized', '1');
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initMaps);
	} else {
		initMaps();
	}
})();
