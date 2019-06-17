<?php

include dirname(__DIR__).'/vendor/autoload.php';

test_redis();

function test_redis()
{
    $redis = new Redis();
    $redis->connect('127.0.0.1', 6379);
    \Dddxiu\Cache::conf([
        'type'      => 'redis',   // 缓存类型
        'expire_at' => 100000,    // 默认超时(秒)
        'prefix'    => 'prefix_', // 前缀
        'conf'      => [
            'redis' => $redis,   // redis资源
            'client'=> Dddxiu\adapter\Redis::REDIS_TYPE_PHP
        ]
    ]);

    cache(['name'=>'zhangsan123'], 100000);
    var_dump(cache('name'));
    // \Dddxiu\Cache::inc('score', 5);
    // \Dddxiu\Cache::dec('score', 1);
    // $ret = \Dddxiu\Cache::get('score');
    // var_dump($ret);
    $ret = \Dddxiu\Cache::del('name');
    $ret = \Dddxiu\Cache::get('name', 'deleted');
    var_dump($ret);
    $ret = \Dddxiu\Cache::flush();
    var_dump(cache('name'));
}


function test_file()
{
    \Dddxiu\Cache::conf([
        'type'      => 'file',    // 缓存类型
        'expire_at' => 100000,    // 默认超时(秒)
        'prefix'    => 'prefix_', // 前缀
        'conf'      => [
            'cache_path' => dirname(__DIR__).'/tmp/', // 缓存类型配置项
        ],
    ]);

    // 覆盖全局超时
    // \Dddxiu\Cache::put('name', 'zhangsan_default', 5);
    // $ret = \Dddxiu\Cache::get('name', 'lisi');
    // var_dump($ret);
    // $ret = \Dddxiu\Cache::puts([
    //     'name1'=>'lisi1',
    //     'name2'=>'lisi2',
    // ]);
    // $ret = \Dddxiu\Cache::gets(['name1', 'name2']);
    // var_dump($ret);
    // \Dddxiu\Cache::inc('score', 5);
    // \Dddxiu\Cache::dec('score', 1);
    // $ret = \Dddxiu\Cache::get('score');
    // var_dump($ret);
    // $ret = \Dddxiu\Cache::del('name');
    // $ret = \Dddxiu\Cache::get('name', 'deleted');
    // var_dump($ret);
    // $ret = \Dddxiu\Cache::flush();
    // var_dump($ret);


    // cache(['name'=>'zhangsan123'], 10);
    // var_dump(cache('name'));
}
