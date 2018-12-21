<?php

namespace Core;

class Commons
{

    public static function RemoteRequestURL(array $remote_id, object $config_data) {
        if (!isset($remote_id['remote_host_id'])) {
            $remote_request_uri = $remote_id['remote_request_uri'];
            $remote_host = $config_data->remote_origin;
        } else {
            $remote_host_id = $remote_id['remote_host_id'];
            $remote_request_uri = $remote_id['remote_request_uri'];
            $remote_host = $config_data->remote_origin->$remote_host_id;
        }

        return $remote_host.$remote_request_uri;
    }

    public static function HeadersParser(array $headers, string $header_fetch = '') {
        $redirect_count = 0;
        foreach ($headers as $header) {
            preg_match('/(HTTP\/[^\ ]+)\s+([0-9]+)\s+(.*)/i', $header, $status);
            if (!empty($status[0])) {
                unset($output);

                $redirect_count += 1;
                ErrorHandler::InterruptProcess(
                    $redirect_count > 1,
                    403,
                    [
                        'context' => 'remote-origin',
                        'info' => 'requested resource exceeds maximum allowed redirects'
                    ]
                );
                
                $output['status_full'] = $status[0];
                $output['status_code'] = $status[2];
                $output['status_desc'] = $status[3];
                continue;
            }

            $key_value_pair = preg_split('/:\s*/', $header);
            $key = strtolower($key_value_pair[0]);
            $value = $key_value_pair[1];
            $output[$key] = $value;
        }

        if (!empty($header_fetch)) {
            if (isset($output[strtolower($header_fetch)])) {
                $output = $output[strtolower($header_fetch)];
            }
            $output = false;
        }

        return $output;
    }

    public static function ObjectToArray($object_data) {
        if (!is_object($object_data) && !is_array($object_data)) {
            return $object_data;
        }

        foreach ($object_data as $key => $value) {
            $arr[$key] = self::ObjectToArray($value);
        }

        return $arr;
    }

    public static function TimeElapsedString(int $timestamp, bool $full = false) {
        $now = new \DateTime;
        $ago = new \DateTime('@'.($timestamp-10));
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );

        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }

}