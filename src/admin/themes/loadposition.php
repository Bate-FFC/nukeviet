<?php

/**
 * NUKEVIET Content Management System
 * @version 5.x
 * @author VINADES.,JSC <contact@vinades.vn>
 * @copyright (C) 2009-2021 VINADES.,JSC. All rights reserved
 * @license GNU/GPL version 2 or any later version
 * @see https://github.com/nukeviet The NukeViet CMS GitHub project
 */

if (!defined('NV_IS_FILE_THEMES')) {
    exit('Stop!!!');
}

$theme1 = $nv_Request->get_title('theme1', 'get');
$theme2 = $nv_Request->get_title('theme2', 'get');

$position1 = $position2 = [];

if (preg_match($global_config['check_theme'], $theme1) and preg_match($global_config['check_theme'], $theme2) and $theme1 != $theme2 and file_exists(NV_ROOTDIR . '/themes/' . $theme1 . '/config.ini') and file_exists(NV_ROOTDIR . '/themes/' . $theme2 . '/config.ini')) {
    // theme 1
    $xml = @simplexml_load_file(NV_ROOTDIR . '/themes/' . $theme1 . '/config.ini') or nv_info_die($nv_Lang->getGlobal('error_404_title'), $nv_Lang->getModule('block_error_fileconfig_title'), $nv_Lang->getModule('block_error_fileconfig_content'), 404);

    $content = $xml->xpath('positions');
    //array
    $positions = $content[0]->position;
    //object

    for ($i = 0, $count = sizeof($positions); $i < $count; ++$i) {
        $position1[] = $positions[$i]->tag;
    }

    // theme 2
    $xml = @simplexml_load_file(NV_ROOTDIR . '/themes/' . $theme2 . '/config.ini') or nv_info_die($nv_Lang->getGlobal('error_404_title'), $nv_Lang->getModule('block_error_fileconfig_title'), $nv_Lang->getModule('block_error_fileconfig_content'), 404);

    $content = $xml->xpath('positions');
    //array
    $positions = $content[0]->position;
    //object

    for ($i = 0, $count = sizeof($positions); $i < $count; ++$i) {
        $position2[] = $positions[$i]->tag;
    }

    $diffarray = array_diff($position1, $position2);
    $diffarray = array_diff($position1, $diffarray);

    $tpl = new \NukeViet\Template\Smarty();
    $tpl->setTemplateDir(NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
    $tpl->assign('LANG', $nv_Lang);

    $array_position = [];
    for ($i = 0, $count = sizeof($diffarray); $i < $count; ++$i) {
        $array_position[] = [
            'name' => (string) $positions[$i]->tag,
            'value' => (string) $positions[$i]->name,
        ];
    }

    $tpl->assign('ARRAY_POSITION', $array_position);

    $contents = $tpl->fetch('loadposition.tpl');

    include NV_ROOTDIR . '/includes/header.php';
    echo $contents;
    include NV_ROOTDIR . '/includes/footer.php';
}