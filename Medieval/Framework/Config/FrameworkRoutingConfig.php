<?php

namespace Medieval\Framework\Config;

use Medieval\Config\RoutingConfig;

class FrameworkRoutingConfig {

    const ROUTING_TYPE = RoutingConfig::ROUTING_TYPE;

    const MAX_REQUEST_PARAMS = 10;

    const UNAUTHORIZED_REDIRECT = RoutingConfig::UNAUTHORIZED_REDIRECT;
    const AUTHORIZED_REDIRECT = RoutingConfig::AUTHORIZED_REDIRECT;

    /**
     * This method returns custom defined routes that map to existing ones.
     * 'uri' key keeps the existing route
     * 'params' keeps the needed uri params to call the action.
     * See below for example.
     * @return array
     */
    public static function getCustomMappings() {
        return RoutingConfig::getCustomMappings();
    }
}