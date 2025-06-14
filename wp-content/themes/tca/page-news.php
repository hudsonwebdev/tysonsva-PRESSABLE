<?php
get_header(); // Include the header template
?>
<main id="primary" class="site-main">
<div class="news-page">
    <?php draw_no_image_banner("Tysons News"); ?>
    <div class="uk-container">
        <div class="filter-bar">
            <div class="filter-side">
            <button class="filter-toggle" onclick="toggleFilter()"><?php drawSVG('filter-toggle'); ?> Filter News</button>

            
                <div class="post-filter">
                    <form id="categoryFilterForm" method="GET" action="<?php echo get_the_permalink(); ?>">
                        <h3>Select Categories</h3>
                        <?php
                    
                        $categories = get_categories();
                       
                        $selected_categories = isset($_GET['category']) ? $_GET['category'] : array();

                        foreach ($categories as $category) : ?>
                            <label>
                                <input type="checkbox" name="category[]" value="<?php echo $category->term_id; ?>" 
                                <?php echo (in_array($category->term_id, $selected_categories)) ? 'checked' : ''; ?>>
                                <?php echo $category->name; ?>
                            </label><br>
                        <?php endforeach; ?>
                        <button type="submit" class="uk-button uk-button-primary">Apply Filter</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <hr>
    <div class="uk-container">
    <div class="news-container" uk-scrollspy="target: .news-card; cls: uk-animation-slide-bottom-medium; delay: 200;repeat:true;">
       

            <?php
            // Default WP Query for Posts
            $args = array(
                'post_type' => 'post',
                'posts_per_page' => 9, // 9 posts per page
                'paged' => get_query_var('paged', 1), // Handle pagination
            );

            // Check if category filter is applied
            if (isset($_GET['category']) && !empty($_GET['category'])) {
                $args['tax_query'] = array(
                    array(
                        'taxonomy' => 'category',
                        'field' => 'id',
                        'terms' => $_GET['category'],
                        'operator' => 'IN',
                    ),
                );
            }

            $query = new WP_Query($args);

            if ($query->have_posts()) :
                while ($query->have_posts()) : $query->the_post();

                $pid = get_the_ID();
                draw_news_card($pid,1);


                    ?>

                    

                    <?php
                endwhile;

                // Pagination Links
                $pagination_args = array(
                    'total' => $query->max_num_pages,
                    'current' => max(1, get_query_var('paged')),
                    'type' => 'list',
                    'prev_text' => __('<svg xmlns="http://www.w3.org/2000/svg" width="15" height="24" viewBox="0 0 15 24" fill="none">
  <path d="M13 22L3 11.994L13 2" stroke="#385DFF" stroke-width="3" stroke-miterlimit="10"/>
</svg>'),
                    'next_text' => __('<svg xmlns="http://www.w3.org/2000/svg" width="15" height="24" viewBox="0 0 15 24" fill="none">
  <path d="M2 2L12 12.006L2 22" stroke="#385DFF" stroke-width="3" stroke-miterlimit="10"/>
</svg>'),
                );
                echo '<div class="pagination uk-text-center">' . paginate_links($pagination_args) . '</div>';

                wp_reset_postdata();
            else :
                echo '<p>No posts found.</p>';
            endif;
            ?>
    </div>
     
    </div> <!-- /uk-container -->

  
</div>

<div class="uk-container deco-wrap uk-position-relative">
    <div class="section-decoration"><?php  echo drawSVG('section-graphic-1'); ?></div>
</div>
        </main>
<?php get_footer(); // Include the footer template ?>
