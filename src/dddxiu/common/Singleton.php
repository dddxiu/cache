<?php

namespace Dddxiu\common;

/**
 * 单例
 */
class Singleton
{

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
        };
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