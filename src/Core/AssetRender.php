<?php

namespace Core;

class AssetRender
{

    public static function This(array $remote_data) {
        $cache_age = Commons::TimeElapsedString($remote_data['timestamp'], true);

        header('Content-Type: '.$remote_data['headers']['content-type']);
        header('X-Cache-Last-Update: '.$cache_age);

        fpassthru($remote_data['content']);
        fclose($remote_data['content']);
        exit;
    }

}