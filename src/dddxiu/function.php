<?php

/**
 * 缓存操作
 *   1.当仅给定$key值时取操作
 *   2.
 * @param  mix $key       缓存key|缓存值
 * @param  [type] $default|$expire_at   默认值|或则超时时间
 */
function cache($key, $default=NULL)
{
    if (is_array($key)) {
        return \Dddxiu\Cache::puts($key, $default);
    }
    return \Dddxiu\Cache::get($key, $default);
}
