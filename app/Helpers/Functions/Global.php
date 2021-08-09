<?php

if (!function_exists('getHost')) {
    function getHost($url)
    {
        $url = parse_url($url);

        if (isset($url['host'])) {
            return $url['host'];
        }

        return false;
    }
}
