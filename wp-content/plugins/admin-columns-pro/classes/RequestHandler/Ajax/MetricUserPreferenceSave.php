<?php

declare(strict_types=1);

namespace ACP\RequestHandler\Ajax;

use AC\Nonce;
use AC\Request;
use AC\RequestAjaxHandler;
use AC\Type\ListScreenId;
use ACP\Metric;
use InvalidArgumentException;

class MetricUserPreferenceSave implements RequestAjaxHandler
{
    private Metric\UserPreferences $preferences;

    private Nonce\Ajax $nonce;

    public function __construct(Metric\UserPreferences $preferences, Nonce\Ajax $nonce)
    {
        $this->preferences = $preferences;
        $this->nonce = $nonce;
    }

    public function handle(): void
    {
        $request = new Request();

        if (! $this->nonce->verify($request)) {
            wp_send_json_error();
        }

        try {
            $list_id = new ListScreenId((string)$request->get('list_id'));
        } catch (InvalidArgumentException $e) {
            wp_send_json_error();
        }

        $key = (string)$request->get('key');

        if (! Metric\UserPreferences::is_valid_key($key)) {
            wp_send_json_error();
        }

        $value = $request->get('value');

        $this->preferences->set($list_id, $key, $this->cast_value($key, $value));

        wp_send_json_success();
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    private function cast_value(string $key, $value)
    {
        if ($key === Metric\UserPreferences::KEY_STICKY) {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }

        return $value;
    }

}
