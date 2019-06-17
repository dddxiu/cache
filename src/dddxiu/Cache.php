<?php

namespace Dddxiu;

/**
 * 缓存
 */
class Cache
{
    protected static $instance;

    const CACHE_FOREVER = -1;


    /**
     * 项目配置,需要重新加载
     * @param  array  $conf [description]
     * @return [type]       [description]
     */
    protected function conf(array $conf)
    {
        $this->type      = $conf['type'] ?? 'file';
        $this->type_conf = $conf['type_conf'] ?? [];
        $this->expire_at = $conf['expire_at'] ?? 108000;
        $this->conf      = $conf;
        $this->load_adapter();
    }


    /**
     * 加载适配器
     * @return [type] [description]
     */
    protected function load_adapter()
    {
        $cls = __NAMESPACE__.'\\adapter\\'.$this->type;
        if (!class_exists($cls)) {
            throw new \Exception("{$cls} adapter not exists", 1);
        }
        $drive = new $cls();
        $drive->load($this->conf);
        $this->drive = $drive;
    }

    /**
     * 限制引用传值
     * 
     * @param  [type] $method [description]
     * @param  [type] $args   [description]
     * @return [type]         [description]
     */
    public static function __callStatic($method, $args)
    {
        $instance = static::getInstance();
        if (method_exists($instance, $method)) {
            return call_user_func_array([$instance, $method], $args);
        }
        if (empty($instance->drive)) {
            throw new \Exception("cache drive can't use", 1);
        }
        if (method_exists($instance->drive, $method)) {
            return call_user_func_array([$instance->drive, $method], $args);
        }
        throw new \Exception("{$method} not exists", 1);
    }


    /**
     * 限制了使用引用传值
     * 
     * @param  [type] $method [description]
     * @param  [type] $args   [description]
     * @return [type]         [description]
     */
    public function __call($method, $args)
    {
        if (method_exists($this, $method)) {
            return call_user_func_array([$this, $method], $args);
        };
        if (empty($this->drive)) {
            throw new \Exception("cache drive can't use", 1);
        }
        if (method_exists($this->drive, $method)) {
            return call_user_func_array([$this->drive, $method], $args);
        }
        throw new \Exception("{$method} not exists", 1);
    }

    
    /**
     * 类静态常量保持单例
     * 
     * @return [type] [description]
     */
    public static function getInstance()
    {
        $cls = get_called_class();
        if ($cls::$instance == NULL) {
            $cls::$instance = new static();
        }
        return $cls::$instance;
    }
}
