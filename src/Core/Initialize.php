<?php

namespace Core;

class Initialize
{

    public function __construct() {
        $config_load = Config::Load();

        $remote_origin_id = Route::RemoteID($config_load);

        $self_filter = SelfFilter::Origin($remote_origin_id, $config_load);

        $remote_origin_full_url = Commons::RemoteRequestURL($self_filter, $config_load);

        $cache_fetch = Fresco::CacheFetch($remote_origin_full_url, $config_load);

        $remote_origin_fetch = RemoteFetch::Origin($cache_fetch);

        $caching_layer = Fresco::CacheData($remote_origin_fetch, $config_load);

        return $caching_layer;
    }

}