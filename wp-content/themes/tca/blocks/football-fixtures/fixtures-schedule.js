/**
 * Scroll capped football schedule lists to today's fixtures on load.
 */
(function () {
	'use strict';

	function localTodayKey(tz) {
		try {
			return new Intl.DateTimeFormat('en-CA', {
				timeZone: tz,
				year: 'numeric',
				month: '2-digit',
				day: '2-digit',
			}).format(new Date());
		} catch (e) {
			var d = new Date();
			var month = String(d.getMonth() + 1).padStart(2, '0');
			var day = String(d.getDate()).padStart(2, '0');
			return d.getFullYear() + '-' + month + '-' + day;
		}
	}

	function findTargetRow(list, today) {
		var rows = list.querySelectorAll('[data-fixture-date]');
		var i;
		var date;
		var fallback = null;

		for (i = 0; i < rows.length; i++) {
			date = rows[i].getAttribute('data-fixture-date');
			if (!date) {
				continue;
			}
			if (date === today) {
				return rows[i];
			}
			if (!fallback && date >= today) {
				fallback = rows[i];
			}
		}

		return fallback;
	}

	function scrollListToToday(list, onlyIfAtTop) {
		if (!list || !list.classList.contains('tca-football-fixtures__list--scroll')) {
			return;
		}

		if (onlyIfAtTop && list.scrollTop > 0) {
			return;
		}

		var tz = list.getAttribute('data-display-tz') || '';
		if (!tz) {
			try {
				tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
			} catch (e2) {
				tz = 'UTC';
			}
		}

		var target = findTargetRow(list, localTodayKey(tz));
		if (!target) {
			return;
		}

		var offset = target.offsetTop - list.offsetTop;
		if (offset < 0) {
			offset = 0;
		}
		list.scrollTop = offset;
	}

	function initList(list) {
		scrollListToToday(list, false);
	}

	function initAll() {
		var lists = document.querySelectorAll('[data-tca-football-schedule]');
		for (var i = 0; i < lists.length; i++) {
			initList(lists[i]);
		}
	}

	window.tcaFootballScheduleScroll = function (list, onlyIfAtTop) {
		if (list) {
			scrollListToToday(list, !!onlyIfAtTop);
			return;
		}
		initAll();
	};

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initAll);
	} else {
		initAll();
	}
})();
