<?php

namespace Dddxiu\adapter;

/**
 * 适配器接口
 */
interface AdapterImp
{

    /**
     * 设置值
     * @param [type] $key       [description]
     * @param [type] $value     [description]
     * @param [type] $expire_at [description]
     */
    public function put(string $key, $value, $expire_at=NULL);


    /**
     * 批量取值
     * @param  array  $kvs       [description]
     * @param  [type] $expire_at [description]
     * @return [type]            [description]
     */
    public function puts(array $kvs, $expire_at=NULL);


    /**
     * 取值
     * @param  [type] $key     [description]
     * @param  [type] $default [description]
     * @return [type]          [description]
     */
    public function get(string $key, $default=NULL);


    /**
     * 批量取值
     * @param  [type] $key [description]
     * @return [type]      [description]
     */
    public function gets(array $key);


    /**
     * 自增
     * @param  string  $key  [description]
     * @param  integer $step [description]
     * @return [type]        [description]
     */
    public function inc(string $key, $step=1);


    /**
     * 减少
     * @param  string  $key  [description]
     * @param  integer $step [description]
     * @return [type]        [description]
     */
    public function dec(string $key, $step=1);


    /**
     * 删除一个值
     * @param  string $key [description]
     * @return [type]      [description]
     */
    public function del(string $key);


    /**
     * 亲空缓存
     * @return [type] [description]
     */
    public function flush();


    /**
     * load配置
     * @param  array  $conf [description]
     * @return [type]       [description]
     */
    public function load(array $conf);
}