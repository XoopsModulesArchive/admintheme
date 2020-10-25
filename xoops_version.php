<?php

$modversion['name'] = 'AdminTheme';
$modversion['version'] = '0.20';
$modversion['author'] = 'original wanikoo';
$modversion['image'] = 'slogo.png';
$modversion['dirname'] = basename(__DIR__);

$modversion['cube_style'] = true;
$modversion['read_any'] = false;

$modversion['hasAdmin'] = 1;

$modversion['config'][0]['name'] = 'admintheme';
$modversion['config'][0]['title'] = '_MI_ADMINTHEME_CONF';
$modversion['config'][0]['description'] = '_MI_ADMINTHEME_DESC';
$modversion['config'][0]['formtype'] = 'textbox';
$modversion['config'][0]['valuetype'] = 'text';
$modversion['config'][0]['default'] = 'default';

$modversion['config'][1]['name'] = 'viewblock';
$modversion['config'][1]['title'] = '_MI_ADMINTHEME_BLOCKV';
$modversion['config'][1]['description'] = '_MI_ADMINTHEME_BLOCKV_DESC';
$modversion['config'][1]['formtype'] = 'yesno';
$modversion['config'][1]['valuetype'] = 'int';
$modversion['config'][1]['default'] = '1';

$modversion['config'][2]['name'] = 'themepath';
$modversion['config'][2]['title'] = '_MI_ADMINTHEME_PATH';
$modversion['config'][2]['description'] = '_MI_ADMINTHEME_PATH_DESC';
$modversion['config'][2]['formtype'] = 'select';
$modversion['config'][2]['valuetype'] = 'text';
$modversion['config'][2]['default'] = 'default';
$modversion['config'][2]['options'] = [
  '_MI_ADMINTHEME_PATH_DEFAULT' => 'default',
'_MI_ADMINTHEME_PATH_MODULE' => 'module',
];
