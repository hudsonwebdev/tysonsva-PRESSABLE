
    <div uk-slider="autoplay: true">
        <div class="uk-slider-items uk-child-width-1-2 uk-child-width-1-3@s uk-child-width-1-4@m uk-light uk-grid uk-grid-small">
            <?php foreach ($photos as $photo):
                $photo_url = "https://live.staticflickr.com/{$photo->server}/{$photo->id}_{$photo->secret}_b.jpg"; // Get the URL for the large image
                ?>
                <div class="flickr-photo">
                    <img src="<?php echo esc_url($photo_url); ?>" alt="<?php echo esc_attr($photo->title); ?>" width="400" height="600" />
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Slider controls (previous/next) -->
        <a class="uk-position-center-left uk-position-small uk-hidden-hover" href="#" uk-slidenav-previous uk-slider-item="previous"></a>
        <a class="uk-position-center-right uk-position-small uk-hidden-hover" href="#" uk-slidenav-next uk-slider-item="next"></a>

        <!-- Dot navigation -->
        <ul class="uk-slider-nav uk-dotnav uk-flex-center uk-margin"></ul>
    </div>

