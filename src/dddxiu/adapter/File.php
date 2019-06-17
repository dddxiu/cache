<?php

namespace Dddxiu\adapter;

use Dddxiu\Cache;

/**
 * 文件缓存
 *     只适用于几百个几千个文件
 */
class File implements AdapterImp
{
    // 文件缓存需要路径
    private $path = './';

    // 默认超时
    private $expire_at = 108000;

    // 会出现延时问题
    private $now  = NULL;

    // 时间戳长度
    private $ts_len = 12;

    /**
     * 设置值
     * @param [type] $key       [description]
     * @param [type] $value     [description]
     * @param [type] $expire_at [description]
     */
    public function put(string $key, $value, $expire_at=NULL)
    {
        $key = $this->base($key);
        if ($expire_at === NULL) {
            $expire_at = $this->expire_at;
        }
        if ($expire_at === Cache::CACHE_FOREVER) {
            $expire_at = 99999999999;
        }
        $time = (string)($this->now + $expire_at);
        if (strlen($time) !== $this->ts_len) {
            $time = str_pad($time, $this->ts_len, '0', STR_PAD_LEFT);
        }
        $value = "{$time}".$value;
        file_put_contents($key, $value);
        return true;
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
        $path = $this->base($key);
        if (!file_exists($path)) {
            return $default;
        }
        $update_at = filemtime($path);
        $info_meta = file_get_contents($path);
        $timestamp = substr($info_meta, 0, $this->ts_len);
        if ($timestamp < $this->now) {
            return $default;
        }
        $ret = substr($info_meta, $this->ts_len);
        return $ret ?? NULL;
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
        $val = $this->get($key, -1);
        if ($val === -1) {
            return $this->put($key, $step, Cache::CACHE_FOREVER);
        }
        return $this->put($key, $val+$step);
    }


    /**
     * 减少
     * @param  string  $key  [description]
     * @param  integer $step [description]
     * @return [type]        [description]
     */
    public function dec(string $key, $step=1)
    {
        $val = $this->get($key, -1);
        if ($val === -1) {
            return $this->put($key, $val, Cache::CACHE_FOREVER);
        }
        return $this->put($key, $val-$step);
    }


    /**
     * 删除一个值
     * @param  string $key [description]
     * @return [type]      [description]
     */
    public function del(string $key)
    {
        $key = $this->base($key);
        if (file_exists($key)) {
            return unlink($key);
        }
        return true;
    }


    /**
     * 清空缓存:清空缓存目录
     * @return [type] [description]
     */
    public function flush()
    {
        return $this->deleteDir($this->path);
    }


    /**
     * 删除文件夹
     * @param  [type] $dir [description]
     * @return [type]      [description]
     */
    private function deleteDir($dir)
    {
        if (!$handle = @opendir($dir)) {
            return false;
        }
        while (false !== ($file = readdir($handle))) {
            if ($file !== "." && $file !== "..") {
                $file = $dir . '/' . $file;
                if (is_dir($file)) {
                    deleteDir($file);
                } else {
                    @unlink($file);
                }
            }
        }
    }


    /**
     * 生成配置
     * @param  array  $conf [description]
     * @return [type]       [description]
     */
    public function load(array $conf)
    {
        $file_conf  = $conf['conf'];
        $this->path = $file_conf['cache_path'];
        if (!is_dir($this->path)) {
            throw new \Exception("file path:{$this->path} not exists", 1);
        }
        $this->expire_at = $conf['expire_at'];
        if ($this->expire_at < -1) {
            throw new \Exception("file expire_at:{$this->expire_at} value error", 1);
        }
        $this->prefix = $conf['prefix'];

    }


    /**
     * 基本信息
     * @param  [type] $key [description]
     * @return [type]      [description]
     */
    public function base($key)
    {
        if (empty($key)) {
            throw new \Exception("cache key:{$key} error", 1);
        }
        $this->now = time();
        return $this->path.DIRECTORY_SEPARATOR.$this->prefix.md5($key);
    }
}
