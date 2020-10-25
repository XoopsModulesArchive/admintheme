<?php

define('_LEGACY_PREVENT_LOAD_CORE_', true);
require_once dirname(__DIR__, 3) . '/mainfile.php';
require_once XOOPS_ROOT_PATH . '/settings/definition.inc.php';
require_once XOOPS_MODULE_PATH . '/admintheme/kernel/CssRender.class.php';

$theme = isset($_GET['theme']) ? trim($_GET['theme']) : null;
$dirname = isset($_GET['dirname']) ? trim($_GET['dirname']) : null;
$file = 'stylesheets/' . trim($_GET['file']);

if (false !== mb_strstr($theme, '..') || false !== mb_strstr($dirname, '..') || false !== mb_strstr($file, '..')) {
    exit();
}
define('_ADMIN_THME_URL', XOOPS_URL . '/modules/admintheme/theme/' . $theme);

$smarty = new AdminCssSmarty($theme);

if (null != $theme && null != $dirname) {
    $path = XOOPS_THEME_PATH . "/${theme}/modules/${dirname}";
} elseif (null != $theme) {
    $path = XOOPS_ROOT_PATH . '/modules/admintheme/theme/' . $theme;
} elseif (null != $dirname) {
    $path = XOOPS_MODULE_PATH . "/${dirname}/admin/templates";
} else {
    $path = LEGACY_ADMIN_RENDER_FALLBACK_PATH;
}
$smarty->template_dir = $path;

$result = '';
if (is_file($path . '/' . $file)) {
    $result = $smarty->fetch('file:' . $file);
}

header('Content-Type:text/css;');
echo $result;
