<?php

declare(strict_types=1);

namespace ACP\API\Request;

use AC\Integration\IntegrationRepository;
use AC\Plugin\Version;
use ACP\API\Request;
use ACP\Type\ActivationToken;
use ACP\Type\SiteUrl;

/**
 * Used for updating subscription information, such as expiration date.
 */
class SubscriptionDetails extends Request
{
    public function __construct(SiteUrl $site_url, ActivationToken $activation_token, IntegrationRepository $repository, Version $version)
    {
        $args = [
            'command'        => 'subscription_details',
            'activation_url' => $site_url->get_url(),
        ];

        $args[$activation_token->get_type()] = $activation_token->get_token();

        foreach ($repository->find_all_active() as $integration) {
            $args['meta'][$integration->get_slug()] = (string)$version;
        }

        parent::__construct($args);
    }

}
