<?php

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class Admintheme_Preload extends XCube_ActionFilter
{
    public function _createBlock($controller)
    {
        require_once XOOPS_MODULE_PATH . '/admintheme/admin/blocks/AdminThemeSelect.class.php';

        $controller->_mBlockChain[] = new AdminThemeSelect();
    }

    public function postFilter()
    {
        $moduleHandler = xoops_getHandler('module');

        $admintheme = $moduleHandler->getByDirname('admintheme');

        $configHandler = xoops_getHandler('config');

        $configs = $configHandler->getConfigsByCat(0, $admintheme->get('mid'));

        if (0 != $configs['viewblock']) {
            $this->mRoot->mDelegateManager->add('Legacy_AdminControllerStrategy.SetupBlock', 'Admintheme_Preload::_createBlock');
        }

        if ('default' != $configs['admintheme']) {
            $this->mRoot->mSiteConfig['RenderSystems']['Legacy_AdminRenderSystem'] = 'AdminthemeRender';

            $this->mRoot->mSiteConfig['AdminthemeRender']['class'] = 'AdminthemeRender';

            $this->mRoot->mSiteConfig['AdminthemeRender']['path'] = '/modules/admintheme/kernel';
        }

        switch ($configs['themepath']) {
      case 'default':
        define('_ADMIN_THME_PATH', XOOPS_THEME_PATH);
        define('_ADMIN_THME_URL', XOOPS_THEME_URL);
        break;
      case 'module':
        define('_ADMIN_THME_PATH', XOOPS_MODULE_PATH . '/admintheme/theme');
        define('_ADMIN_THME_URL', XOOPS_MODULE_URL . '/admintheme/theme');
        break;
    }
    }
}
