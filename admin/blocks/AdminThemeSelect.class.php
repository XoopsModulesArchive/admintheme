<?php

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class AdminThemeSelect extends Legacy_AbstractBlockProcedure
{
    public function getName()
    {
        return 'themeselect';
    }

    public function getTitle()
    {
        return 'TEST: AdminThemeSelect';
    }

    public function getEntryIndex()
    {
        return 0;
    }

    public function isEnableCache()
    {
        return false;
    }

    public function getAdminTheme()
    {
        $results = [];

        if ($handler = opendir(_ADMIN_THME_PATH)) {
            while (false !== ($dirname = readdir($handler))) {
                if ('.' == $dirname || '..' == $dirname) {
                    continue;
                }

                $themeDir = _ADMIN_THME_PATH . '/' . $dirname;

                if (is_file($themeDir . '/admin_theme.html')) {
                    $results[] = $dirname;
                }
            }

            closedir($handler);
        }

        return $results;
    }

    public function execute()
    {
        $root = &XCube_Root::getSingleton();

        $root->mLanguageManager->loadModuleAdminMessageCatalog('admintheme');

        $root->mLanguageManager->loadBlockMessageCatalog('legacy');

        $render = &$this->getRenderTarget();

        $render->setTemplateName('../../../../admintheme/admin/templates/blocks/admin_block_themes.html');

        $moduleHandler = xoops_getHandler('module');

        $admintheme = $moduleHandler->getByDirname('admintheme');

        $configHandler = xoops_getHandler('config');

        $configs = $configHandler->getConfigsByCat(0, $admintheme->get('mid'));

        $admintheme_options = [];

        $i = 1;

        $admintheme_options[0]['name'] = 'default';

        if ('default' == $configs['admintheme']) {
            $admintheme_options[0]['selected'] = 'selected="selected"';
        } else {
            $admintheme_options[0]['selected'] = '';
        }

        foreach ($this->getAdminTheme() as $theme) {
            $admintheme_options[$i]['name'] = $theme;

            if ($theme == $configs['admintheme']) {
                $admintheme_options[$i]['selected'] = 'selected="selected"';
            } else {
                $admintheme_options[$i]['selected'] = '';
            }

            $i++;
        }

        $admintheme_count = $i;

        $render->setAttribute('admintheme_options', $admintheme_options);

        $render->setAttribute('admintheme_count', $admintheme_count);

        $render->setAttribute('blockid', $this->getName());

        $renderSystem = &$root->getRenderSystem($this->getRenderSystemName());

        $renderSystem->renderBlock($render);
    }
}
