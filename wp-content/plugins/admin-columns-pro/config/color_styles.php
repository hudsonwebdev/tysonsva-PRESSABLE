<?php

declare(strict_types=1);

$purple = '#9b51e0';
$orange = '#ff7043';
$grey = '#8896a3';

return [
    // row 1
    'success' => [
        'color'            => '#fff',
        'background_color' => 'var(--ac-color-success)',
    ],
    'warning' => [
        'color'            => '#fff',
        'background_color' => 'var(--ac-color-warning)',
    ],
    'error' => [
        'color'            => '#fff',
        'background_color' => 'var(--ac-color-error)',
    ],
    'info' => [
        'color'            => '#fff',
        'background_color' => 'var(--ac-color-info)',
    ],
    'purple' => [
        'color'            => '#fff',
        'background_color' => $purple,
    ],
    'orange' => [
        'color'            => '#fff',
        'background_color' => $orange,
    ],
    'grey' => [
        'color'            => '#fff',
        'background_color' => $grey,
    ],

    // row ,
    'success_pattern' => [
        'background'       => 'repeating-linear-gradient(-45deg,rgba(255,255,255,.2),rgba(255,255,255,.2) 5px,transparent 5px,transparent 10px)',
        'background_color' => 'var(--ac-color-success)',
        'color'            => '#fff',
    ],
    'warning_pattern' => [
        'background'       => 'repeating-linear-gradient(90deg,rgba(255,255,255,.2),rgba(255,255,255,.2) 5px,transparent 5px,transparent 10px)',
        'background_color' => 'var(--ac-color-warning)',
        'color'            => '#fff',
    ],
    'error_pattern' => [
        'background'       => 'repeating-linear-gradient(45deg,rgba(255,255,255,.2),rgba(255,255,255,.2) 5px,transparent 5px,transparent 10px)',
        'background_color' => 'var(--ac-color-error)',
        'color'            => '#fff',
    ],
    'info_pattern' => [
        'background'       => 'linear-gradient(45deg, rgba(255,255,255,.2) 25%, transparent 25%) 0 0 / 20px 20px, linear-gradient(-45deg, rgba(255,255,255,.2) 25%, transparent 25%) 0 10px / 20px 20px, linear-gradient(45deg, transparent 75%, rgba(255,255,255,.2) 75%) 10px -10px / 20px 20px, linear-gradient(-45deg, transparent 75%, rgba(255,255,255,.2) 75%) -10px 0 / 20px 20px',
        'background_color' => 'var(--ac-primary-color)',
        'color'            => '#fff',
    ],
    'purple-pattern' => [
        'background'       => 'radial-gradient(circle, rgba(255,255,255,.2) 40%, transparent 40%) 0 0 / 20px 20px, radial-gradient(circle, rgba(255,255,255,.2) 30%, transparent 30%) 10px 10px / 20px 20px',
        'background_color' => $purple,
        'color'            => '#fff',
    ],
    'orange-pattern' => [
        'background'       => 'linear-gradient(45deg, rgba(255,255,255,.2) 25%, transparent 25%, transparent 75%, rgba(255,255,255,.2) 75%, rgba(255,255,255,.4)) 0 0 / 10px 10px, linear-gradient(45deg, rgba(255,255,255,.2) 25%, transparent 25%, transparent 75%, rgba(255,255,255,.4) 75%, rgba(255,255,255,.4)) 5px 5px / 10px 10px',
        'background_color' => $orange,
        'color'            => '#fff',
    ],
    'grey-pattern' => [
        'background'       => 'repeating-linear-gradient(0deg,rgba(255,255,255,.2),rgba(255,255,255,.2) 2px,transparent 2px,transparent 10px), repeating-linear-gradient(90deg,rgba(255,255,255,.2),rgba(255,255,255,.2) 2px,transparent 2px,transparent 10px)',
        'background_color' => $grey,
        'color'            => '#fff',
    ],
    // row 3
    'success-invert' => [
        'color' => 'var(--ac-color-success)',
    ],
    'warning-invert' => [
        'color' => 'var(--ac-color-warning)',
    ],
    'error-invert' => [
        'color' => 'var(--ac-color-error)',
    ],
    'info-invert' => [
        'color' => 'var(--ac-color-info)',
    ],
    'purple-invert' => [
        'color' => $purple,
    ],
    'orange-invert' => [
        'color' => $orange,
    ],
    'grey-invert' => [
        'color' => $grey,
    ],
];
