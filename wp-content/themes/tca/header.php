<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package tca
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

		<!-- Google Tag Manager -->

	<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':

	new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],

	j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=

	'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);

	})(window,document,'script','dataLayer','GTM-5SGV8GQ');</script>

	<!-- End Google Tag Manager -->

	<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
	<link rel="manifest" href="/site.webmanifest">

	<?php /* <link rel="preload" href="<?php echo get_stylesheet_directory_uri(); ?>/src/scss/silka/Silka-Roman-Webfont/silka-bold-webfont.woff2" as="font" type="font/woff2" crossorigin="anonymous"> */ ?>

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
	<!-- Google Tag Manager (noscript) -->

<noscript><iframe src=https://www.googletagmanager.com/ns.html?id=GTM-5SGV8GQ

height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>

<!-- End Google Tag Manager (noscript) -->
<?php wp_body_open(); ?>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'tca' ); ?></a>
	<div class="header-wrap">
		<div class="uk-container header-container">
			<header id="masthead" class="site-header">
				<div class="mobile-header">
				
					<div class="site-branding">
						<a href="<?php echo site_url(); ?>"><?php drawSVG('tcalogo'); ?></a>
					</div><!-- .site-branding -->
					<a href="#" role="button" class="mobile-toggle"  aria-label="Mobile Menu Toggle"><div class="t"><span></span><span></span><span></span></div></a>
					
				</div>
				<nav id="site-navigation" class="main-navigation">
					<?php drawMegaMenu(); ?>
				</nav><!-- #site-navigation -->
			</header><!-- #masthead -->
		</div>
	</div>
	<div class="nav-margin"></div>
