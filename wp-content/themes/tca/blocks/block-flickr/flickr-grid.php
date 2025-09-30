
    <div class="uk-grid-small uk-child-width-1-2 uk-child-width-1-3@s uk-child-width-1-4@m uk-light uk-grid" uk-grid>
        <?php foreach ($photos as $photo):
            $photo_url = "https://live.staticflickr.com/{$photo->server}/{$photo->id}_{$photo->secret}_b.jpg"; // Get the URL for the large image
            ?>
            <div class="uk-width-1-4">
                <img src="<?php echo esc_url($photo_url); ?>" alt="<?php echo esc_attr($photo->title); ?>" width="400" height="400" />
            </div>
        <?php endforeach; ?>
    </div>

