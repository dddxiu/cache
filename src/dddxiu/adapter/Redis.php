<?php

namespace Dddxiu\adapter;

use Dddxiu\Cache;

/**
 * Redis缓存
 */
class Redis implements AdapterImp
{
    // PHP开发的客户端
    const REDIS_TYPE_PHP = 'predis';

    // 扩展开发的客户端
    const REDIS_TYPE_EXT = 'phpredis';

    // 客户端类型
    private $client_type = NULL;

    // 默认超时
    private $expire_at = 108000;


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
        if ($expire_at == Cache::CACHE_FOREVER) {
            return $this->redis->set($key, $value);
        }
        // 10秒
        $this->redis->set($key, $value, ['nx', 'ex'=>$expire_at]);
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
        $ret = $this->redis->get($key);
        if ($ret === false) {
            return $default;
        }
        return $ret ?? $default;
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
            $values[] = $this->get($key);
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
        return $this->redis->incr($key, $step);
    }


    /**
     * 减少
     * @param  string  $key  [description]
     * @param  integer $step [description]
     * @return [type]        [description]
     */
    public function dec(string $key, $step=1)
    {
        return $this->redis->decr($key, $step);
    }


    /**
     * 删除一个值
     * @param  string $key [description]
     * @return [type]      [description]
     */
    public function del(string $key)
    {
        return $this->redis->del($key);
    }


    /**
     * 清空缓存
     * @return [type] [description]
     */
    public function flush()
    {
        return $this->redis->flushDb();
    }


    public function load(array $conf)
    {
        $file_conf = $conf['conf'];
        $this->expire_at = $conf['expire_at'];
        if ($this->expire_at < -1) {
            throw new \Exception("file expire_at:{$this->expire_at} value error", 1);
        }
        $this->prefix = $conf['prefix'];
        $this->redis  = $file_conf['redis'];
        $this->client_type = $file_conf['client'];
    }
}
