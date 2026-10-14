<?php

declare(strict_types=1);

namespace ACA\WC\Value\Formatter;

use AC\Formatter;
use AC\Helper\Dashicon;
use AC\Type\Value;

class Stars implements Formatter
{
    private int $max_stars;

    public function __construct(int $max_stars = 5)
    {
        $this->max_stars = $max_stars;
    }

    public function format(Value $value)
    {
        $stars = $this->stars((int)$value->get_value(), $this->max_stars);

        return $value->with_value($stars);
    }

    /**
     * Returns star rating based on X stars from $max count. Does support decimals.
     */
    private function stars(int $count, int $max = 0): string
    {
        $stars = [
            'filled' => floor($count),
            'half'   => floor(round(($count * 2)) - (floor($count) * 2)) ? 1 : 0,
            'empty'  => 0,
        ];

        $max = absint($max);

        if ($max > 0) {
            $star_count = $stars['filled'] + $stars['half'];

            $stars['empty'] = $max - $star_count;

            if ($star_count > $max) {
                $stars['filled'] = $max;
                $stars['half'] = 0;
            }
        }

        $icons = [];

        foreach ($stars as $type => $_count) {
            for ($i = 1; $i <= $_count; $i++) {
                $icons[] = Dashicon::create('star-' . $type, 'ac-value-star')->render();
            }
        }

        return sprintf(
            '<span class="ac-value-stars">%s</span>',
            implode(' ', $icons)
        );
    }

}
