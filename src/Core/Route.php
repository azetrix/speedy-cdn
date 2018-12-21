<?php

namespace Core;

class Route
{

    const URI_PARSER = '/^(?:\/+([^\/]+?)\/*)([^\/].*)?$/mU';
    const DEDICATED_CDN_URI_PARSER = '/^(?:\/*)([^\/].*)?$/mU';

    public static function RemoteID(object $config_data) {
        if (is_string($config_data->remote_origins)) {
            preg_match_all(self::DEDICATED_CDN_URI_PARSER, $_SERVER['REQUEST_URI'], $matches);

            $matches = array_filter($matches);
            $match_full = $matches[0][0];
            $match_first = $matches[1][0];

            $format = [
                'request_uri' => $match_full,
                'remote_request_uri' => $match_first
            ];
        } else {
            preg_match_all(self::URI_PARSER, $_SERVER['REQUEST_URI'], $matches);

            $matches = array_filter($matches);
            $match_full = $matches[0][0];
            $match_first = $matches[1][0];
            $match_second = $matches[2][0];

            ErrorHandler::InterruptProcess(
                empty($match_full),
                200,
                [
                    'context' => 'speedy-cdn',
                    'info' => 'greetings from speedy'
                ]
            );

            ErrorHandler::InterruptProcess(
                !isset($config_data->remote_origins->$match_first),
                404,
                [
                    'context' => 'speedy-cdn',
                    'info' => 'remote-origin id could not be found'
                ]
            );

            $format = [
                'request_uri' => $match_full,
                'remote_host_id' => $match_first,
                'remote_request_uri' => $match_second
            ];
        }

        return $format;
    }

}