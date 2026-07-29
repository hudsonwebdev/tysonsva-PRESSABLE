/**
 * Football Live Scores: poll server for updated in-play fixtures.
 */
(function () {
	'use strict';

	function poll(block) {
		if (!window.tcaLiveScores || !tcaLiveScores.ajaxUrl || !tcaLiveScores.nonce) {
			return;
		}

		var body = new FormData();
		body.append('action', 'tca_api_football_live_scores');
		body.append('nonce', tcaLiveScores.nonce);
		body.append('league', block.getAttribute('data-league') || '0');
		body.append('season', block.getAttribute('data-season') || '0');
		body.append('icons', block.getAttribute('data-icons') || 'flags_small');
		body.append('scope', block.getAttribute('data-scope') || 'league');

		fetch(tcaLiveScores.ajaxUrl, {
			method: 'POST',
			body: body,
			credentials: 'same-origin',
		})
			.then(function (response) {
				return response.json();
			})
			.then(function (payload) {
				if (!payload || !payload.success || !payload.data) {
					return;
				}

				var bodyEl = block.querySelector('[data-live-body]');
				var count = typeof payload.data.count === 'number' ? payload.data.count : 0;
				var layout = payload.data.layout || 'upcoming_only';
				var bodyHtml = payload.data.body_html || '';

				if (bodyEl) {
					var wasAtTop = bodyEl.scrollTop === 0;
					bodyEl.innerHTML = bodyHtml;
					bodyEl.setAttribute('data-layout', layout);
					bodyEl.setAttribute('data-live-count', String(count));
					if (wasAtTop && window.tcaFootballScheduleScroll) {
						window.tcaFootballScheduleScroll(bodyEl, true);
					}
				}

				block.setAttribute('data-live-layout', layout);
				block.setAttribute('data-live-updated', String(Date.now()));
			})
			.catch(function () {});
	}

	function initBlock(block) {
		var seconds = parseInt(block.getAttribute('data-poll'), 10);
		if (!seconds || seconds < 15) {
			seconds = 45;
		}

		poll(block);
		window.setInterval(function () {
			poll(block);
		}, seconds * 1000);
	}

	function init() {
		var blocks = document.querySelectorAll('[data-tca-live-scores]');
		for (var i = 0; i < blocks.length; i++) {
			initBlock(blocks[i]);
		}
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
