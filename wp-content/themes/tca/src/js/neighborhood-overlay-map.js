/**
 * Alternate Neighborhood Guide map: click a neighborhood to open image + description overlay.
 * Pretty URLs: /neighborhood-guide/{slug}/
 */
(function () {
	'use strict';

	var cfg = window.TCA_NG_OVERLAY || {};
	var canvas = document.getElementById('tca-ng-overlay-map-canvas');
	if (!canvas || typeof mapboxgl === 'undefined') {
		return;
	}

	var token = cfg.mapboxToken || (window.TCA_MAP && window.TCA_MAP.mapboxToken) || '';
	if (!token) {
		return;
	}

	mapboxgl.accessToken = token;

	var wrap = canvas.closest('[data-tca-ng-overlay-map]');
	var panel = wrap && wrap.querySelector('.tca-ng-overlay-map__panel');
	var scrim = wrap && wrap.querySelector('.tca-ng-overlay-map__scrim');
	var closeBtn = wrap && wrap.querySelector('.tca-ng-overlay-map__close');
	var headingEl = wrap && wrap.querySelector('.tca-ng-overlay-map__heading');
	var descEl = wrap && wrap.querySelector('.tca-ng-overlay-map__description');
	var imgWrap = wrap && wrap.querySelector('.tca-ng-overlay-map__image-wrap');
	var imgEl = imgWrap && imgWrap.querySelector('img');
	var featuresBySlug = {};
	var featuresById = {};

	var isMobile = function () {
		return document.documentElement.clientWidth <= 768;
	};

	function basePath() {
		var base = cfg.basePath || '/neighborhood-guide/';
		try {
			if (/^https?:\/\//i.test(base)) {
				base = new URL(base).pathname;
			}
		} catch (err) {
			base = '/neighborhood-guide/';
		}
		return base.replace(/\/?$/, '/');
	}

	function overlayUrl(slug) {
		return slug ? basePath() + slug + '/' : basePath();
	}

	function slugFromPath() {
		if (cfg.activeSlug) {
			return cfg.activeSlug;
		}
		var path = window.location.pathname.replace(/\/?$/, '/');
		var base = basePath();
		if (path.indexOf(base) !== 0) {
			return '';
		}
		return path.slice(base.length).replace(/\/$/, '').split('/')[0] || '';
	}

	var map = new mapboxgl.Map({
		container: canvas,
		style: cfg.style || 'mapbox://styles/mapbox/streets-v12',
		center: cfg.center || [-77.227, 38.926],
		zoom: isMobile() ? (cfg.mobileZoom || 12.3) : (cfg.zoom || 13.2),
		dragPan: true,
	});

	map.addControl(new mapboxgl.NavigationControl({ showCompass: false }), 'top-right');
	map.scrollZoom.disable();

	var layerIds = [];
	var selectedId = '';
	var FILL_COLOR = '#00B852';
	var FILL_IDLE = 0;
	var FILL_ACTIVE = 0.8;

	function setFill(id, opacity) {
		if (map.getLayer(id)) {
			map.setPaintProperty(id, 'fill-opacity', opacity);
		}
	}

	function highlight(id) {
		layerIds.forEach(function (layerId) {
			setFill(layerId, layerId === id ? FILL_ACTIVE : FILL_IDLE);
		});
		selectedId = id || '';
	}

	function clearSelection() {
		layerIds.forEach(function (id) {
			setFill(id, FILL_IDLE);
		});
		selectedId = '';
	}

	function fillPanel(props) {
		if (headingEl) {
			headingEl.textContent = props.title || '';
		}
		if (descEl) {
			descEl.textContent = props.description || '';
			descEl.hidden = !props.description;
		}
		if (imgEl && imgWrap) {
			if (props.image) {
				imgEl.src = props.image;
				imgEl.alt = props.title || '';
				imgEl.hidden = false;
				imgWrap.classList.remove('tca-ng-overlay-map__image-wrap--empty');
			} else {
				imgEl.removeAttribute('src');
				imgEl.alt = '';
				imgEl.hidden = true;
				imgWrap.classList.add('tca-ng-overlay-map__image-wrap--empty');
			}
		}
	}

	function openPanel(props, updateUrl) {
		if (!panel || !wrap || !props) {
			return;
		}

		fillPanel(props);
		highlight(String(props.id));

		panel.hidden = false;
		panel.setAttribute('aria-hidden', 'false');
		wrap.classList.add('is-open');
		if (scrim) {
			scrim.hidden = false;
		}

		if (updateUrl !== false && props.slug) {
			var next = overlayUrl(props.slug);
			if (window.location.pathname.replace(/\/?$/, '/') !== next) {
				history.pushState({ tcaNgSlug: props.slug }, '', next);
			}
		}
	}

	function closePanel(updateUrl) {
		if (!panel || !wrap) {
			return;
		}
		wrap.classList.remove('is-open');
		panel.hidden = true;
		panel.setAttribute('aria-hidden', 'true');
		if (scrim) {
			scrim.hidden = true;
		}
		clearSelection();
		if (updateUrl !== false) {
			var next = overlayUrl('');
			if (window.location.pathname.replace(/\/?$/, '/') !== next) {
				history.pushState({ tcaNgSlug: '' }, '', next);
			}
		}
	}

	function openBySlug(slug, updateUrl) {
		var shape = slug && featuresBySlug[slug];
		if (shape) {
			openPanel(shape.properties, updateUrl);
			return;
		}
		closePanel(false);
	}

	if (closeBtn) {
		closeBtn.addEventListener('click', function () {
			closePanel(true);
		});
	}
	if (scrim) {
		scrim.addEventListener('click', function () {
			closePanel(true);
		});
	}
	document.addEventListener('keydown', function (e) {
		if (e.key === 'Escape' && wrap && wrap.classList.contains('is-open')) {
			closePanel(true);
		}
	});
	window.addEventListener('popstate', function () {
		cfg.activeSlug = '';
		openBySlug(slugFromPath(), false);
	});

	map.on('load', function () {
		var firstSymbolId;
		var layers = map.getStyle().layers || [];
		for (var i = 0; i < layers.length; i++) {
			if (layers[i].type === 'symbol') {
				firstSymbolId = layers[i].id;
				break;
			}
		}

		var features = (cfg.data && cfg.data.features) || [];
		features.forEach(function (shape) {
			if (!shape || !shape.coords || !shape.properties) {
				return;
			}
			var thisid = String(shape.properties.id);
			var geom = {
				type: 'Feature',
				properties: shape.properties,
				geometry: {
					type: 'Polygon',
					coordinates: shape.coords,
				},
			};

			featuresById[thisid] = shape;
			if (shape.properties.slug) {
				featuresBySlug[shape.properties.slug] = shape;
			}

			map.addLayer(
				{
					id: thisid,
					type: 'fill',
					source: { type: 'geojson', data: geom },
					paint: {
						'fill-color': FILL_COLOR,
						'fill-opacity': FILL_IDLE,
					},
				},
				firstSymbolId
			);

			map.addLayer(
				{
					id: thisid + 'line',
					type: 'line',
					source: { type: 'geojson', data: geom },
					paint: {
						'line-color': '#ffffff',
						'line-width': 2,
						'line-dasharray': [1, 2],
					},
				},
				firstSymbolId
			);

			layerIds.push(thisid);

			map.on('click', thisid, function () {
				openPanel(shape.properties, true);
			});

			map.on('mouseenter', thisid, function () {
				map.getCanvas().style.cursor = 'pointer';
				setFill(thisid, FILL_ACTIVE);
			});

			map.on('mouseleave', thisid, function () {
				map.getCanvas().style.cursor = '';
				if (thisid !== selectedId) {
					setFill(thisid, FILL_IDLE);
				}
			});
		});

		var startSlug = slugFromPath();
		if (startSlug) {
			openBySlug(startSlug, false);
		}
	});
})();
