/**
 * Events Manager map view: skip locations whose lat/lng are not finite numbers.
 * Google Maps throws InvalidValueError (NaN) and aborts the whole map.
 */
(function ($) {
	'use strict';

	function isValidLatLng(lat, lng) {
		var la = parseFloat(lat);
		var ln = parseFloat(lng);
		if (!isFinite(la) || !isFinite(ln)) {
			return false;
		}
		if (Math.abs(la) < 1e-8 && Math.abs(ln) < 1e-8) {
			return false;
		}
		if (la < -90 || la > 90 || ln < -180 || ln > 180) {
			return false;
		}
		return true;
	}

	function sanitizeLocations(data) {
		var out = [];
		if (!data) {
			return out;
		}
		$.each(data, function (_, loc) {
			if (!loc || typeof loc !== 'object') {
				return;
			}
			if (!isValidLatLng(loc.location_latitude, loc.location_longitude)) {
				return;
			}
			loc.location_latitude = parseFloat(loc.location_latitude);
			loc.location_longitude = parseFloat(loc.location_longitude);
			out.push(loc);
		});
		return out;
	}

	function isEmMapQuery(data) {
		if (!data) {
			return false;
		}
		if (typeof data === 'string') {
			return data.indexOf('GlobalMapData') !== -1 || data.indexOf('GlobalEventsMapData') !== -1;
		}
		var q = data.query;
		return q === 'GlobalMapData' || q === 'GlobalEventsMapData';
	}

	$.ajaxPrefilter('json', function (options, originalOptions) {
		if (!isEmMapQuery(originalOptions.data) && !isEmMapQuery(options.data)) {
			return;
		}
		var converters = options.converters || {};
		var orig = converters['text json'];
		options.converters = converters;
		converters['text json'] = function (text) {
			var data = typeof orig === 'function' ? orig(text) : JSON.parse(text);
			return sanitizeLocations(data);
		};
	});

	function wrapSingleLocationLoader() {
		if (typeof window.em_maps_load_location !== 'function' || window.em_maps_load_location._tcaGuarded) {
			return;
		}
		var orig = window.em_maps_load_location;
		window.em_maps_load_location = async function (el) {
			var $el = $(el);
			var mapId = ($el.attr('id') || '').replace('em-location-map-', '');
			var lat = $('#em-location-map-coords-' + mapId + ' .lat').text();
			var lng = $('#em-location-map-coords-' + mapId + ' .lng').text();
			if (!isValidLatLng(lat, lng)) {
				return;
			}
			return orig.apply(this, arguments);
		};
		window.em_maps_load_location._tcaGuarded = true;
	}

	if (typeof window.em_maps === 'function' && !window.em_maps._tcaGuarded) {
		var origMaps = window.em_maps;
		window.em_maps = async function () {
			wrapSingleLocationLoader();
			return origMaps.apply(this, arguments);
		};
		window.em_maps._tcaGuarded = true;
	}

	$(function () {
		wrapSingleLocationLoader();
	});
})(jQuery);
