<?php
/**
 * Template Name: Test
 */
get_header();
?>

<div style="max-height:500px;overflow-y:scroll">
	<?php
	/*
	 * API-Sports widgets (v3): timezone is set on the *config* widget and applies to all widgets on the page.
	 * Use IANA name America/New_York for US Eastern (handles EST in winter and EDT in summer).
	 * data-lang="en" is the English UI; the built-in "en" pack typically uses US-style dates for match lists.
	 * If any date still looks day-first, use a custom JSON via data-custom-lang (see API-Sports widget docs).
	 */
	?>
	<!-- Config must be present; put it first so the league widget inherits data-timezone / data-lang. -->
	<api-sports-widget
		data-type="config"
		data-key="336bd9a6753a3a08a2f09d7aaf2c196b"
		data-sport="football"
		data-lang="en"
		data-timezone="America/New_York"
		data-theme="white"
		data-show-logos="true"
		data-favorite="true"
	></api-sports-widget>

	<api-sports-widget
		data-type="league"
		data-league="1"
		data-season="2026"
	></api-sports-widget>
</div>

<?php
// v3 script (match version in https://api-sports.io/documentation/widgets/v3 if you upgrade)
?>
<script type="module" src="https://widgets.api-sports.io/3.1.0/widgets.js"></script>

<?php
get_footer();
