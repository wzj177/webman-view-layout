<div style="padding:18px;max-width: 1024px;margin:0 auto;background-color:#fff;color:#333">
<h1>webman view layout </h1>

为webman 原始视图提供模板布局功能。


# 特性
- 支持模板布局
- 支持模板缓存

## 使用

- 在`config/view.php`或在应用插件的`plugin/{name}/config/view.php`里配置:
```php
return [
    'handler' => \Wzj177\WebmanViewLayout\LayoutRaw::class,
    'options' => [
    ]
];
```

- 新建_layout.html

  默认_layout.html模板文件放在`{resources}/views/_layout.html`(`app/view`或者`plugin/{name}/view`)，demo参考下面。

## 注意
1. 如果应用插件里admin下的view目录下没有`_layout.html`模板文件，则默认使用webman admin 插件里的 `_layout.html`模板文件。如果都没有没有就使用原始的视图。

## `_layout.html` demo
```html
<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <title><?= $page_title ?? '管理后台' ?></title>
    <link rel="stylesheet" href="/app/admin/component/pear/css/pear.css"/>
    <link rel="stylesheet" href="/app/admin/component/jsoneditor/css/jsoneditor.css" />
    <link rel="stylesheet" href="/app/admin/admin/css/reset.css"/>
    <?= isset($block_link) ? $block_link : '' ?>
    <?= isset($block_style) ? $block_style : '' ?>
</head>
<body class="pear-container">
<?= $page_body_content ?? '<div class="content"><img src="/app/admin/admin/images/404.svg" alt=""><div class="content-r"><h1>404</h1><p>抱歉，你访问的页面不存在或仍在开发中</p></div></div>' ?>
<script src="/app/admin/component/layui/layui.js?v=2.8.12"></script>
<script src="/app/admin/component/pear/pear.js"></script>
<script src="/app/admin/component/jsoneditor/jsoneditor.js"></script>
<script src="/app/admin/admin/js/common.js"></script>
<?= isset($block_script) ? $block_script : '' ?>
</body>
</html>
```