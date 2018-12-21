<?php

namespace Core;

class Fresco
{

    public static function CacheData(array $remote_data, object $config_data, string $cache_directory = '../cache') {
        self::Init($cache_directory);

        $cache_signature = md5($remote_data['origin']);
        $meta_signature = $cache_directory.'/meta/'.$cache_signature;
        $data_signature = $cache_directory.'/data/'.$cache_signature;

        $cache_meta = new \stdClass();
        $cache_meta->origin = $remote_data['origin'];
        $cache_meta->headers = $remote_data['headers'];
        $cache_meta->timestamp = $remote_data['timestamp'];
        $cache_meta = json_encode($cache_meta);

        $max_bytes = $config_data->max_remote_bytes;
        $byte_counter = $config_data->byte_counter;
        $remote_data['content'] = DataStream::ByteCounter($remote_data['content'], $byte_counter, $max_bytes);

        file_put_contents($meta_signature, $cache_meta);
        file_put_contents($data_signature, $remote_data['content']);

        fclose($remote_data['content']);
        $remote_data['content'] = fopen($data_signature, 'r');

        AssetRender::This($remote_data);
    }

    public static function CacheFetch(string $origin_link, object $config_data, string $cache_directory = '../cache') {
        self::Init($cache_directory);

        $cache_signature = md5($origin_link);
        $meta_signature = $cache_directory.'/meta/'.$cache_signature;
        $data_signature = $cache_directory.'/data/'.$cache_signature;

        if (file_exists($meta_signature) && file_exists($data_signature)) {
            $meta_fopen = fopen($meta_signature, 'r');
            $data_fopen = fopen($data_signature, 'r');

            $meta_parsed = json_decode(fread($meta_fopen, filesize($meta_signature)));
            $meta_parsed = Commons::ObjectToArray($meta_parsed);
            $meta_parsed['content'] = $data_fopen;

            fclose($meta_fopen);

            if (time()-((int)$meta_parsed['timestamp']) > $config_data->cache_lifespan_seconds) {
                return $origin_link;
            }

            touch($meta_signature);
            touch($data_signature);

            AssetRender::This($meta_parsed);
        } else {
            return $origin_link;
        }
    }

    private static function Init(string $cache_directory) {
        $meta_dir = $cache_directory.'/meta';
        $data_dir = $cache_directory.'/data';
        if (!file_exists($meta_dir) || !file_exists($data_dir)) {
            self::RecursiveDelete($cache_directory);
            mkdir($meta_dir, 0777, true);
            mkdir($data_dir, 0777, true);
        } elseif (!is_dir($meta_dir) || !is_dir($data_dir)) {
            self::RecursiveDelete($cache_directory);
            self::Init($cache_directory);
        }
    }

    private static function RecursiveDelete(string $directory) {
        if (is_dir($directory)) {
            $objects = scandir($directory);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($directory."/".$object)) {
                        self::RecursiveDelete($directory."/".$object);
                    } else {
                        unlink($directory."/".$object);
                    }
                }
            }
            rmdir($directory);
        }
    }

}