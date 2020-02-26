<?php

namespace App\Traits;

trait Securable
{
    /**
     * @param string $url
     * @param string $secret
     * @param int    $ttl
     * @param string $id
     * @param string $ip
     *
     * @return string
     */
    public static function getSecureExpireLink(
        string $url, string $secret, int $ttl, string $id, string $ip
    ) {
        $expires = time() + $ttl;

        $md5 = md5("{$expires}{$id}{$ip} {$secret}", true);
        $md5 = base64_encode($md5);
        $md5 = strtr($md5, '+/', '-_');
        $md5 = str_replace('=', '', $md5);

        return "{$url}?md5={$md5}&id={$id}&expires={$expires}";
    }
}
