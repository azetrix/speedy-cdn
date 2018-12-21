<?php

namespace Core;

class RemoteFetch
{

    const ALLOWED_STATUS_CODES = [
        200
    ];

    const ALLOWED_CONTENT_TYPES = [
        '/^image\/(?:(?:(?:x-(?:citrix-)?)?png)|(?:x-(?:citrix-)?|p)?jpeg|gif|x-icon|bmp|psd|svg\+xml|webp)/i',
        '/^text\/(?:css|plain|yaml|xml|xsl)/i',
        '/^application\/(?:javascript|json|(?:rss\+|xhtml\+|atom\+)?xml)/i'
    ];

    public static function Origin(string $origin_link) {
        $data = fopen($origin_link, 'r', false,
            stream_context_create(
                array(
                    'http' => array(
                        'ignore_errors' => true,
                        'timeout' => 4,
                        'method'=>"GET",
                        'header'  => "User-Agent: speedy-cdn\r\n"
                    )
                )
            )
        );

        if (!empty($http_response_header)) {
            $origin_return['origin'] = $origin_link;
            $origin_return['headers'] = $http_response_header;
            $origin_return['timestamp'] = time();
            if (!empty($data)) {
                $origin_return['content'] = $data;
            }
        };

        ErrorHandler::InterruptProcess(
            empty($origin_return),
            400,
            [
                'context' => 'remote-origin',
                'info' => 'remote origin unreachable'
            ]
        );

        return self::Filter($origin_return);
    }

    public static function Filter(array $remote_data) {
        $headers = Commons::HeadersParser($remote_data['headers']);
        $remote_data['headers'] = $headers;

        foreach (self::ALLOWED_CONTENT_TYPES as $content_type) {
            preg_match($content_type, $headers['content-type'], $matches);
            if (!empty($matches)) {
                break;
            }
        }

        ErrorHandler::InterruptProcess(
            false && (empty($matches) && $headers['status_code'] != 404),
            400,
            [
                'context' => 'speedy-cdn',
                'info' => 'you have requested an unsupported resource'
            ]
        );

        ErrorHandler::InterruptProcess(
            !in_array(
                $headers['status_code'],
                self::ALLOWED_STATUS_CODES
            ),
            $headers['status_code'],
            [
                'context' => 'remote-origin'
            ]
        );

        return $remote_data;
    }

}