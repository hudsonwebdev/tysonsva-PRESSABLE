<?php
/**
 * Gravity Forms Usage Finder Admin Page
 */

add_action('admin_menu', function () {
    add_management_page(
        'Gravity Forms Usage',
        'Gravity Forms Usage',
        'manage_options',
        'gravity-forms-usage',
        'hwdev_gravity_forms_usage_page'
    );
});

function hwdev_gravity_forms_usage_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    $results = hwdev_find_gravity_forms_usage();

    echo '<div class="wrap">';
    echo '<h1>Gravity Forms Usage</h1>';
    echo '<p>This scans published posts, pages, and public custom post types for Gravity Forms shortcodes, blocks, and likely PHP template usage.</p>';

    if (empty($results)) {
        echo '<p><strong>No Gravity Forms usage found.</strong></p>';
        echo '</div>';
        return;
    }

    echo '<table class="widefat striped">';
    echo '<thead>
            <tr>
                <th>Form ID</th>
                <th>Found In</th>
                <th>Type</th>
                <th>Method</th>
                <th>Edit/View</th>
            </tr>
          </thead>';
    echo '<tbody>';

    foreach ($results as $row) {
        echo '<tr>';
        echo '<td>' . esc_html($row['form_id']) . '</td>';
        echo '<td>' . esc_html($row['title']) . '</td>';
        echo '<td>' . esc_html($row['source_type']) . '</td>';
        echo '<td>' . esc_html($row['method']) . '</td>';
        echo '<td>';

        if (!empty($row['edit_link'])) {
            echo '<a href="' . esc_url($row['edit_link']) . '">Edit</a> | ';
        }

        if (!empty($row['view_link'])) {
            echo '<a href="' . esc_url($row['view_link']) . '" target="_blank">View</a>';
        }

        echo '</td>';
        echo '</tr>';
    }

    echo '</tbody></table>';
    echo '</div>';
}

function hwdev_find_gravity_forms_usage() {
    $results = [];

    $post_types = get_post_types([
        'public' => true,
    ], 'names');

    $query = new WP_Query([
        'post_type'      => $post_types,
        'post_status'    => ['publish', 'private', 'draft'],
        'posts_per_page' => -1,
        'fields'         => 'ids',
    ]);

    foreach ($query->posts as $post_id) {
        $post = get_post($post_id);

        if (!$post) {
            continue;
        }

        $content = $post->post_content;

        /*
         * 1. Shortcode usage:
         * [gravityform id="1"]
         * [gravityform id=1]
         */
        if (preg_match_all('/\[gravityform[^\]]*id=[\'"]?(\d+)[\'"]?/i', $content, $matches)) {
            foreach ($matches[1] as $form_id) {
                $results[] = hwdev_gravity_forms_usage_row(
                    $form_id,
                    $post,
                    'Post Content',
                    'Shortcode'
                );
            }
        }

        /*
         * 2. Gravity Forms block usage.
         * Core block usually stores formId in block attrs.
         */
        if (has_blocks($content)) {
            $blocks = parse_blocks($content);
            $results = array_merge(
                $results,
                hwdev_scan_blocks_for_gravity_forms($blocks, $post)
            );
        }
    }

    /*
     * 3. PHP template usage scan.
     * Looks for gravity_form(1), gravity_form( '1' ), gravity_form( "1" )
     */
    $theme_dir = get_stylesheet_directory();
    $php_files = hwdev_get_php_files_recursive($theme_dir);

    foreach ($php_files as $file) {
        $contents = file_get_contents($file);

        if (!$contents) {
            continue;
        }

        if (preg_match_all('/gravity_form\s*\(\s*[\'"]?(\d+)[\'"]?/i', $contents, $matches)) {
            foreach ($matches[1] as $form_id) {
                $results[] = [
                    'form_id'     => $form_id,
                    'title'       => str_replace($theme_dir . '/', '', $file),
                    'source_type' => 'Theme PHP File',
                    'method'      => 'PHP gravity_form()',
                    'edit_link'   => '',
                    'view_link'   => '',
                ];
            }
        }
    }

    return hwdev_dedupe_gravity_forms_results($results);
}

function hwdev_scan_blocks_for_gravity_forms($blocks, $post) {
    $results = [];

    foreach ($blocks as $block) {
        $block_name = $block['blockName'] ?? '';

        if (
            $block_name === 'gravityforms/form' ||
            $block_name === 'gravityforms/formshortcode'
        ) {
            $attrs = $block['attrs'] ?? [];

            $form_id = $attrs['formId']
                ?? $attrs['form_id']
                ?? $attrs['id']
                ?? '';

            if ($form_id) {
                $results[] = hwdev_gravity_forms_usage_row(
                    $form_id,
                    $post,
                    'Post Content',
                    'Gravity Forms Block'
                );
            }
        }

        if (!empty($block['innerBlocks'])) {
            $results = array_merge(
                $results,
                hwdev_scan_blocks_for_gravity_forms($block['innerBlocks'], $post)
            );
        }
    }

    return $results;
}

function hwdev_gravity_forms_usage_row($form_id, $post, $source_type, $method) {
    return [
        'form_id'     => $form_id,
        'title'       => get_the_title($post),
        'source_type' => $source_type . ' — ' . get_post_type($post),
        'method'      => $method,
        'edit_link'   => get_edit_post_link($post->ID),
        'view_link'   => get_permalink($post),
    ];
}

function hwdev_get_php_files_recursive($dir) {
    $files = [];

    if (!is_dir($dir)) {
        return $files;
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS)
    );

    foreach ($iterator as $file) {
        if ($file->isFile() && strtolower($file->getExtension()) === 'php') {
            $files[] = $file->getPathname();
        }
    }

    return $files;
}

function hwdev_dedupe_gravity_forms_results($results) {
    $seen = [];
    $clean = [];

    foreach ($results as $row) {
        $key = md5(
            $row['form_id'] .
            $row['title'] .
            $row['source_type'] .
            $row['method']
        );

        if (!isset($seen[$key])) {
            $seen[$key] = true;
            $clean[] = $row;
        }
    }

    return $clean;
}