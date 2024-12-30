<?php

namespace Wzj177\WebmanViewLayout;

use support\view\Raw;
use Throwable;

class LayoutRaw extends Raw
{
    public static function render(string $template, array $vars, ?string $app = null, ?string $plugin = null, ?string $layout = null): string
    {
        $request = request();
        $plugin = $plugin === null ? ($request->plugin ?? '') : $plugin;
        $configPrefix = $plugin ? "plugin.$plugin." : '';
        $viewSuffix = config("{$configPrefix}view.options.view_suffix", 'html');
        $app = $app === null ? ($request->app ?? '') : $app;
        $baseViewPath = $plugin ? base_path() . "/plugin/$plugin/app" : app_path();
        $__template_path__ = $template[0] === '/' ? base_path() . "$template.$viewSuffix" : ($app === '' ? "$baseViewPath/view/$template.$viewSuffix" : "$baseViewPath/$app/view/$template.$viewSuffix");
        $layoutPath = $layout ? $baseViewPath . "/view/$layout" : null;
        $viewCache = config("{$configPrefix}view.options.view_cache", true);
        if ($viewCache) {
            $cacheKey = md5($__template_path__);
            $viewPath = runtime_path('views');
            !is_dir($viewPath) && mkdir($viewPath, 0755, true);
            $cachePhpFile = runtime_path('views') . "/$cacheKey.php";
            $cachePhpTimeFile = runtime_path('views') . "/$cacheKey.php.txt";
            if (is_file($cachePhpFile) && is_file($cachePhpTimeFile)) {
                $cachePhpTime = intval(file_get_contents($cachePhpTimeFile));
                // 缓存文件是否过期
                if ($cachePhpTime === filemtime($__template_path__)) {
                    ob_start();
                    try {
                        include $cachePhpFile;
                    } catch (Throwable $e) {
                        ob_end_clean();
                        throw $e;
                    }

                    return ob_get_clean();
                }
            }
        }

        if (isset($request->_view_vars)) {
            extract((array)$request->_view_vars);
        }

        extract($vars);

        ob_start();
        try {
            include $__template_path__;
        } catch (Throwable $e) {
            ob_end_clean();
            throw $e;
        }

        $page_body_content = ob_get_clean();
        if ($layout) {
            if (!is_file($layoutPath) && 'admin' !== $plugin) {
                $layoutPath = base_path() . "/plugin/admin/app/view/$layout";
            }
            if (is_file($layoutPath)) {
                preg_match_all('/<style.*?>(.*?)<\/style>/is', $page_body_content, $styleMatches);
                preg_match_all('/<script\s+src=["\'](.*?)["\'].*?><\/script>/is', $page_body_content, $scriptMatches);
                preg_match_all('/<link\s+rel=["\'].*?["\'].*?href=["\'](.*?)["\'].*?>/is', $page_body_content, $linkMatches);
                $block_link = implode("\n", $linkMatches[0]) . "\n";
                $block_style = implode("\n", $styleMatches[0]) . "\n";
                $block_script = implode("\n", $scriptMatches[0]);
                $page_body_content = preg_replace('/<style.*?<\/style>/is', '', $page_body_content);
                $page_body_content = preg_replace('/<script\s+src=["\'].*?["\'].*?><\/script>/is', '', $page_body_content);
                $page_body_content = preg_replace('/<link\s+rel=["\'].*?["\'].*?href=["\'].*?["\'].*?>/is', '', $page_body_content);

                ob_start();
                // 提取布局和视图的内容
                extract(compact('page_body_content', 'block_link', 'block_style', 'block_script'));
                include $layoutPath;
                $renderedContent = ob_get_clean();
                if ($viewCache) {
                    file_put_contents($cachePhpFile, $renderedContent);
                    file_put_contents($cachePhpTimeFile, filemtime($__template_path__));
                }

                return $renderedContent;
            }
        }

        return $page_body_content;
    }
}