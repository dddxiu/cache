<?php

namespace Dddxiu\adapter;

use Dddxiu\Cache;

/**
 * Memcache缓存
 * 仅支持该客户端
 * https://github.com/php-memcached-dev/php-memcached
 */
class Memcached implements AdapterImp
{

    // memcache 实例
    private $memc = NULL;

    // 前缀
    private $prefix = NULL;


    /**
     * 设置值
     * @param [type] $key       [description]
     * @param [type] $value     [description]
     * @param [type] $expire_at [description]
     */
    public function put(string $key, $value, $expire_at=NULL)
    {
        if ($expire_at === NULL) {
            $expire_at = $this->expire_at;
        }
        $key = $this->k($key);
        if ($expire_at == Cache::CACHE_FOREVER) {
            $expire_at = 0;
        }
        // 3600*30*24 = 2592000 大于30天认为是unix时间戳
        return $this->memc->set($key, $value, $expire_at);
    }


    /**
     * 批量取值
     * @param  array  $kvs       [description]
     * @param  [type] $expire_at [description]
     * @return [type]            [description]
     */
    public function puts(array $kvs, $expire_at=NULL)
    {
        foreach ($kvs as $key => $value) {
            $this->put($key, $value, $expire_at);
        }
    }


    /**
     * 取值
     * @param  [type] $key     [description]
     * @param  [type] $default [description]
     * @return [type]          [description]
     */
    public function get(string $key, $default=NULL)
    {
        $key = $this->k($key);
        $val = $this->memc->get($key);
        if (0 === $this->memc->getResultCode()) {
            return $val ?? $default;;
        }
        return NULL;
    }


    /**
     * 批量取值
     * @param  [type] $key [description]
     * @return [type]      [description]
     */
    public function gets(array $keys)
    {
        $values = [];
        foreach ($keys as $key) {
            $values[$key] = $this->get($key);
        }
        return $values;
    }


    /**
     * 自增
     * @param  string  $key  [description]
     * @param  integer $step [description]
     * @return [type]        [description]
     */
    public function inc(string $key, $step=1)
    {
        $key = $this->k($key);
        $val = $this->memc->get($key);
        if (intval($val) === 0) {
            $this->memc->set($key, 0);
        }
        return $this->memc->increment($key, $step);
    }


    /**
     * 减少,最少为0
     * @param  string  $key  [description]
     * @param  integer $step [description]
     * @return [type]        [description]
     */
    public function dec(string $key, $step=1)
    {
        $key = $this->k($key);
        $val = $this->memc->get($key);
        if (intval($val) === 0) {
            $this->memc->set($key, 0);
        }
        return $this->memc->decrement($key, $step);
    }


    /**
     * 删除一个值
     * @param  string $key [description]
     * @return [type]      [description]
     */
    public function del(string $key)
    {
        $key = $this->k($key);
        return $this->memc->delete($key);
    }


    /**
     * 清空缓存
     * @return [type] [description]
     */
    public function flush()
    {
        return $this->memc->flush();
    }


    /**
     * load配置
     * @param  array  $conf [description]
     * @return [type]       [description]
     */
    public function load(array $conf)
    {
        $this->expire_at = $conf['expire_at'];
        if ($this->expire_at < -1) {
            throw new \Exception("file expire_at:{$this->expire_at} value error", 1);
        }
        $this->prefix = $conf['prefix'];
        $this->memc   = $conf['conf']['memc'];
    }


    /**
     * 格式化key
     * @param  [type] $key [description]
     * @return [type]      [description]
     */
    private function k($key)
    {
        if (empty($this->prefix)) {
            return $key;
        }
        $key = "{$this->prefix}{$key}";
        return $key;
    }
}
