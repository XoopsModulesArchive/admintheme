<?php

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class Admintheme_Module extends Legacy_ModuleAdapter
{
    public function __construct(&$xoopsModule)
    {
        parent::Legacy_ModuleAdapter($xoopsModule);
    }

    public function hasAdminIndex()
    {
        return true;
    }

    public function getAdminIndex()
    {
        return XOOPS_MODULE_URL . '/' . $this->mXoopsModule->get('dirname') . '/admin/index.php';
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

    public function getAdminMenu()
    {
        if ($this->_mAdminMenuLoadedFlag) {
            return $this->mAdminMenu;
        }

        $root = &XCube_Root::getSingleton();

        $this->mAdminMenu[] = [
      'link' => $root->mController->getPreferenceEditUrl($this->mXoopsModule),
'title' => _PREFERENCES,
'absolute' => true,
    ];

        $this->mAdminMenu[] = [
      'link' => 'javascript:void(0);return false;',
'title' => '---',
'absolute' => true,
    ];

        $thisurl = XOOPS_MODULE_URL . '/' . $this->mXoopsModule->get('dirname') . '/admin/index.php?';

        $this->mAdminMenu[] = [
      'link' => $thisurl . 'theme=default&url=' . $GLOBALS['xoopsRequestUri'],
'title' => 'default',
'show' => true,
    ];

        foreach ($this->getAdminTheme() as $name) {
            $this->mAdminMenu[] = [
        'link' => $thisurl . 'theme=' . $name . '&url=' . $GLOBALS['xoopsRequestUri'],
'title' => $name,
'show' => true,
      ];
        }

        $this->_mAdminMenuLoadedFlag = true;

        return $this->mAdminMenu;
    }
}
