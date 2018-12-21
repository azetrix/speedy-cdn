<?php

namespace Core;

class ErrorHandler
{

    const ERROR_MESSAGES = [
        200 => 'ok',
        400 => 'bad request',
        418 => 'i\'m a teapot',
        403 => 'access denied',
        404 => 'resource not found',
        422 => 'unprocessable entity',
        500 => 'server error'
    ];

    public static function InterruptProcess(bool $proceed, int $return_code, array $details = [null]) {
        if ($proceed) {
            http_response_code($return_code);
            header('Content-Type: application/json');
            header('X-Robots-Tag: noindex, nofollow');

            $return_data['status_code'] = $return_code;
            if (!empty(self::ERROR_MESSAGES[$return_code])) {
                $return_data['response_description'] = self::ERROR_MESSAGES[$return_code];
            }
            $return_data['timestamp'] = time();
            if (!empty($details)) {
                $return_data['response_details'] = $details;
            }

            die(json_encode($return_data));
        }
    }

}