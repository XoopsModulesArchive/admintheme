<?php

require_once XOOPS_ROOT_PATH . '/modules/legacyRender/kernel/Legacy_AdminRenderSystem.class.php';

class AdminthemeSmarty extends Legacy_AdminSmarty
{
    public $theme;

    public function __construct($theme)
    {
        $this->theme = $theme;

        parent::Legacy_AdminSmarty();
    }

    public function _fetch_resource_info(&$params)
    {
        $_return = false;

        $theme = $this->theme;

        $dirname = $this->mModulePrefix;

        if (null != $dirname) {
            $params['resource_base_path'] = _ADMIN_THME_PATH . '/' . $theme . '/modules/' . $dirname;

            $params['quiet'] = true;

            $_return = parent::_fetch_resource_info($params);
        }

        if (!$_return) {
            unset($params['resource_base_path']);

            $params['quiet'] = false;

            $_return = parent::_fetch_resource_info($params);
        }

        return $_return;
    }
}

class AdminthemeRender extends Legacy_AdminRenderSystem
{
    public $theme;

    public function __construct()
    {
        $this->theme = getAdminThemeName();
    }

    public function prepare(&$controller)
    {
        $this->mController = &$controller;

        $this->mSmarty = new AdminthemeSmarty($this->theme);

        $this->mSmarty->register_modifier('theme', 'admintheme_modifier_theme');

        $this->mSmarty->register_function('stylesheet', 'admintheme_function_stylesheet');

        $this->mSmarty->assign(
            [
      'xoops_url' => XOOPS_URL,
'xoops_rootpath' => XOOPS_ROOT_PATH,
'xoops_langcode' => _LANGCODE,
'xoops_charset' => _CHARSET,
'xoops_version' => XOOPS_VERSION,
'xoops_upload_url' => XOOPS_UPLOAD_URL,
]
        );

        if (true === $controller->mRoot->mSiteConfig['Legacy_AdminRenderSystem']['ThemeDevelopmentMode']) {
            $this->mSmarty->force_compile = true;
        }

        $this->mSmarty->compile_id = $this->theme;
    }

    public function renderTheme($target)
    {
        foreach ($target->getAttributes() as $key => $value) {
            $this->mSmarty->assign($key, $value);
        }

        $this->mSmarty->assign('stdout_buffer', $this->_mStdoutBuffer);

        $moduleObject = &$this->mController->getVirtualCurrentModule();

        $this->mSmarty->assign('currentModule', $moduleObject);

        $this->mSmarty->assign('legacy_sitename', $this->mController->mRoot->mContext->getAttribute('legacy_sitename'));

        $this->mSmarty->assign('legacy_pagetitle', $this->mController->mRoot->mContext->getAttribute('legacy_pagetitle'));

        $this->mSmarty->assign('legacy_slogan', $this->mController->mRoot->mContext->getAttribute('legacy_slogan'));

        $blocks = [];

        foreach ($this->mController->mRoot->mContext->mAttributes['legacy_BlockContents'][0] as $key => $result) {
            $blocks[$result['name']] = $result;
        }

        $this->mSmarty->assign('xoops_lblocks', $blocks);

        if (is_file(_ADMIN_THME_PATH . '/' . $this->theme . '/admin_theme.html')) {
            $this->mSmarty->template_dir = _ADMIN_THME_PATH . '/' . $this->theme;
        } else {
            $this->mSmarty->template_dir = LEGACY_ADMIN_RENDER_FALLBACK_PATH;
        }

        $this->mSmarty->setModulePrefix('');

        $result = $this->mSmarty->fetch('file:admin_theme.html');

        $target->setResult($result);
    }
}

function getAdminThemeName()
{
    $moduleHandler = xoops_getHandler('module');

    $admintheme = $moduleHandler->getByDirname('admintheme');

    $configHandler = xoops_getHandler('config');

    $configs = $configHandler->getConfigsByCat(0, $admintheme->get('mid'));

    if ('default' == $configs['admintheme']) {
        $root = &XCube_Root::getSingleton();

        return $root->mSiteConfig['Legacy']['Theme'];
    }

    return $configs['admintheme'];
}

function admintheme_modifier_theme($string)
{
    $infoArr = admintheme_get_override_file($string);

    if (null != $infoArr['theme'] && null != $infoArr['dirname']) {
        return _ADMIN_THME_URL . '/' . $infoArr['theme'] . '/modules/' . $infoArr['dirname'] . '/' . $string;
    } elseif (null != $infoArr['theme']) {
        return _ADMIN_THME_URL . '/' . $infoArr['theme'] . '/' . $string;
    } elseif (null != $infoArr['dirname']) {
        return XOOPS_MODULE_URL . '/' . $infoArr['dirname'] . '/admin/templates/' . $string;
    }

    return LEGACY_ADMIN_RENDER_FALLBACK_URL . '/' . $string;
}

function admintheme_function_stylesheet($params, $smarty)
{
    if (!isset($params['file'])) {
        $smarty->trigger_error('stylesheet: missing file parameter.');

        return;
    }

    $file = $params['file'];

    if (false !== mb_strstr($file, '..')) {
        $smarty->trigger_error('stylesheet: missing file parameter.');

        return;
    }

    $media = $params['media'] ?? 'all';

    $infoArr = admintheme_get_override_file($file, 'stylesheets/');

    // TEMP

    // TODO We must return FALLBACK_URL here.

    if (null != $infoArr['file']) {
        $request = [];

        foreach ($infoArr as $key => $value) {
            if (null != $value) {
                $request[] = $key . '=' . $value;
            }
        }

        $url = XOOPS_MODULE_URL . '/admintheme/admin/css.php?' . implode('&amp;', $request);

        echo '<link rel="stylesheet" type="text/css" media="' . $media . '" href="' . $url . '">';
    }
}

function admintheme_get_override_file($file, $prefix = null, $isSpDirname = false)
{
    $root = &XCube_Root::getSingleton();

    $moduleObject = &$root->mContext->mXoopsModule;

    if ($isSpDirname && is_object($moduleObject) && 'legacy' == $moduleObject->get('dirname') && isset($_REQUEST['dirname'])) {
        if (preg_match('/^[a-z0-9_]+$/i', xoops_getrequest('dirname'))) {
            $handler = xoops_getHandler('module');

            $moduleObject = $handler->getByDirname(xoops_getrequest('dirname'));
        }
    }

    $theme = getAdminThemeName();

    $ret = [];

    $ret['theme'] = $theme;

    $ret['file'] = $file;

    $file = $prefix . $file;

    if (!is_object($moduleObject)) {
        $themePath = _ADMIN_THME_PATH . '/' . $theme . '/' . $file;

        if (is_file($themePath)) {
            return $ret;
        }

        return $ret;
    }

    $dirname = $moduleObject->get('dirname');

    $ret['dirname'] = $dirname;

    $themePath = _ADMIN_THME_PATH . '/' . $theme . '/modules/' . $dirname . '/' . $file;

    if (is_file($themePath)) {
        return $ret;
    }

    $themePath = _ADMIN_THME_PATH . '/' . $theme . '/' . $file;

    if (is_file($themePath)) {
        $ret['dirname'] = null;

        return $ret;
    }

    $ret['theme'] = null;

    $modulePath = XOOPS_MODULE_PATH . '/' . $dirname . '/admin/templates/' . $file;

    if (is_file($modulePath)) {
        return $ret;
    }

    $ret['dirname'] = null;

    if (is_file(LEGACY_ADMIN_RENDER_FALLBACK_PATH . '/' . $file)) {
        return $ret;
    }

    $ret['file'] = null;

    return $ret;
}
