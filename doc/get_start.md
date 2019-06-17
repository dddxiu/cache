# get_start

```
// 放入缓存
\Dddxiu\Cache::put('name', 'zhangsan_default', 5);
// 读取缓存
$ret = \Dddxiu\Cache::get('name', 'lisi');
var_dump($ret);


// 批量操作
$ret = \Dddxiu\Cache::puts([
    'name1'=>'lisi1',
    'name2'=>'lisi2',
]);
$ret = \Dddxiu\Cache::gets(['name1', 'name2']);
var_dump($ret);


// 增加,减少
\Dddxiu\Cache::inc('score', 5);
\Dddxiu\Cache::dec('score', 1);
$ret = \Dddxiu\Cache::get('score');
var_dump($ret);


// 删除
$ret = \Dddxiu\Cache::del('name');
$ret = \Dddxiu\Cache::get('name', 'deleted');
var_dump($ret);


// 清空,缓存目录
// $ret = \Dddxiu\Cache::flush();
// var_dump($ret);
```

