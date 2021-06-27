<?php

/**
 * NUKEVIET Content Management System
 * @version 5.x
 * @author VINADES.,JSC <contact@vinades.vn>
 * @copyright (C) 2009-2021 VINADES.,JSC. All rights reserved
 * @license GNU/GPL version 2 or any later version
 * @see https://github.com/nukeviet The NukeViet CMS GitHub project
 */

if (!defined('NV_IS_FILE_DATABASE')) {
    exit('Stop!!!');
}

$log_dir = NV_ROOTDIR . '/' . NV_LOGS_DIR . '/dump_backup';
if ($global_config['idsite']) {
    $log_dir .= '/' . $global_config['site_dir'];
}

$tpl = new \NukeViet\Template\Smarty();
$tpl->setTemplateDir(NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$tpl->assign('LANG', $nv_Lang);
$tpl->assign('BASE_URL', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE);

$tpl->registerPlugin('modifier', 'byte2text', 'nv_convertfromBytes');
$tpl->registerPlugin('modifier', 'date', 'nv_date');
$tpl->registerPlugin('modifier', 'md5', 'md5');

$array_content = $array_time = [];
$files = scandir($log_dir);

foreach ($files as $file) {
    if (preg_match('/^([a-zA-Z0-9]+)\_([a-zA-Z0-9\-\_]+)\.(sql|sql\.gz)+$/', $file, $mc)) {
        $filesize = filesize($log_dir . '/' . $file);
        $filetime = (int) (filemtime($log_dir . '/' . $file));
        $array_time[] = $filetime;

        $array_content[$filetime] = [
            'file' => $file,
            'mc' => $mc,
            'filesize' => $filesize
        ];
    }
}
sort($array_time);

$tpl->assign('DATA', $array_content);
$tpl->assign('ARRAY_TIME', $array_time);
$tpl->assign('NUM_FILES', sizeof($array_time) - 1);
$tpl->assign('NV_CHECK_SESSION', NV_CHECK_SESSION);
$tpl->assign('BACKUPNOW', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=download&amp;checkss=' . NV_CHECK_SESSION);

$page_title = $nv_Lang->getModule('file_backup');
$contents = $tpl->fetch('files.tpl');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
