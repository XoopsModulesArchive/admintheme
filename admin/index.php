<?php

require_once dirname(__DIR__, 3) . '/mainfile.php';
$root = &XCube_Root::getSingleton();

$theme = $root->mContext->mRequest->getRequest('theme');
if (!in_array($theme, $root->mContext->mModule->getAdminTheme(), true)) {
    $theme = 'default';
}

$db = &$root->mController->mDB;
$sql = 'UPDATE `' . $db->prefix('config') . '` ';
$sql .= 'SET `conf_value` = ' . $db->quoteString($theme) . ' ';
$sql .= 'WHERE `conf_modid` = ' . $root->mContext->mXoopsModule->get('mid') . ' ';
$sql .= 'AND `conf_catid` = 0 ';
$sql .= "AND `conf_name` = 'admintheme'";
if ($db->queryF($sql)) {
    $msg = 'Change Theme';
} else {
    $msg = 'Error';
}

$url = $root->mContext->mRequest->getRequest('url');
if ('' == $url) {
    $url = XOOPS_URL . '/admin.php';
}
$root->mController->executeRedirect($url, 2, $msg);
