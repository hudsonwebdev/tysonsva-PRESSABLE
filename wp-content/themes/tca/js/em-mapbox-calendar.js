/**
 * Initialize the TCA Mapbox listing map when Events Manager switches to Map view.
 */
(function ($) {
	'use strict';

	function initTcaMap() {
		if (typeof window.tcaEventsLocationsInitMaps === 'function') {
			window.tcaEventsLocationsInitMaps();
		}
	}

	function viewHasTcaMap(view) {
		if (!view || !view.find) {
			return !!document.querySelector('[data-tca-events-locations-map]');
		}
		return view.find('[data-tca-events-locations-map]').length > 0;
	}

	function patchGoogleMapsSkip() {
		if (typeof window.em_maps_load !== 'function' || window.em_maps_load._tcaPatched) {
			return;
		}
		var original = window.em_maps_load;
		window.em_maps_load = function () {
			if (document.querySelector('[data-tca-events-locations-map]')) {
				initTcaMap();
				return;
			}
			return original.apply(this, arguments);
		};
		window.em_maps_load._tcaPatched = true;
	}

	$(document).on('em_view_loaded_map em_search_loaded em_search_result', function (e, view) {
		if (viewHasTcaMap(view)) {
			initTcaMap();
		}
	});

	$(function () {
		patchGoogleMapsSkip();
		if (document.querySelector('[data-tca-events-locations-map]')) {
			initTcaMap();
		}
	});
})(jQuery);
