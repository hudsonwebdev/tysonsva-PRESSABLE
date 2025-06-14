<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package tca
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="single-header-background">
		<div class="inner">
			<?php echo drawSVG('single-news-header'); ?>
		</div>
	</div>
	<header class="entry-header">
		<div class="uk-container uk-container-small">

		<div class="card-header">
			<div class="card-type">NEWS</div>
			<div class="card-date"><?php the_date(); ?></div>
		</div>
		<?php
		if ( is_singular() ) :
			the_title( '<h1 class="entry-title">', '</h1>' );
		else :
			the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
		endif;
		
		?>
		</div>

	    <?php if(get_the_post_thumbnail()){ ?>
		<div class="uk-container">
			<div class="featured-image-wrap">
				<div class="single-featured-image">
					<?php tca_post_thumbnail('large'); ?>
					
				</div>
				<div class="single-featured-caption">
					<?php  the_post_thumbnail_caption(); ?>
				</div>
			</div>
		</div>
		<?php } ?>
		
	</header><!-- .entry-header -->

	

	<div class="entry-content">
		<div class="uk-container uk-container-small">
		<?php
		the_content(
			sprintf(
				wp_kses(
					/* translators: %s: Name of current post. Only visible to screen readers */
					__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'tca' ),
					array(
						'span' => array(
							'class' => array(),
						),
					)
				),
				wp_kses_post( get_the_title() )
			)
		);


		?>
		<hr>
		<div class="share">
		<?php drawSocialShare(get_the_title(),get_the_permalink()); ?>
		</div>
		</div>
	</div><!-- .entry-content -->


</article><!-- #post-<?php the_ID(); ?> -->
