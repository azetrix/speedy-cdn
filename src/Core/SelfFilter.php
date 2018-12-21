<?php

namespace Core;

class SelfFilter
{

    public static function Origin(array $origin_id, object $config_data) {
        if (!isset($origin_id['remote_host_id'])) {
            $remote_origin = $config_data->remote_origin;
        } else {
            $remote_host_id = $origin_id['remote_host_id'];
            $remote_origin = $config_data->remote_origin->$remote_host_id;
        }

        $remote_host = strtolower(parse_url($remote_origin)['host']);
        $http_host = strtolower($_SERVER['HTTP_HOST']);
        ErrorHandler::InterruptProcess(
            ($remote_host == $http_host),
            500,
            [
                'context' => 'speedy-cdn',
                'info' => 'reference loop detected'
            ]
        );

        return $origin_id;
    }

}