<?php

declare(strict_types=1);

namespace ACP\Metric;

use AC\Response;
use ACP\Query\Bindings;

abstract class Strategy
{
    protected Formatter $formatter;

    public function __construct(Formatter $formatter)
    {
        $this->formatter = $formatter;
    }

    abstract public function handle(Bindings $bindings): void;

    public function send_response(string $value): void
    {
        $response = new Response\Json([
            'result' => $this->formatter->format($value),
        ]);

        $response->send();
    }

}
