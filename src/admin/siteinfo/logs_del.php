<?php

/**
 * NUKEVIET Content Management System
 * @version 5.x
 * @author VINADES.,JSC <contact@vinades.vn>
 * @copyright (C) 2009-2021 VINADES.,JSC. All rights reserved
 * @license GNU/GPL version 2 or any later version
 * @see https://github.com/nukeviet The NukeViet CMS GitHub project
 */

if (!defined('NV_IS_FILE_SITEINFO')) {
    exit('Stop!!!');
}

// Xóa toàn bộ nhật ký hệ thống
if ($nv_Request->get_title('logempty', 'post', '') == md5('siteinfo_' . NV_CHECK_SESSION . '_' . $admin_info['userid'])) {
    $filtersql = $nv_Request->get_title('filtersql', 'post', '');
    if (!empty($filtersql)) {
        $where_str = $crypt->decrypt($filtersql, md5(NV_CHECK_SESSION . '-del-all-logs'));
        try {
            $db->query('DELETE FROM ' . $db_config['prefix'] . '_logs WHERE ' . $where_str);
            $nv_Cache->delMod($module_name);
            nv_insert_logs(NV_LANG_DATA, $module_name, $nv_Lang->getModule('log_empty_log_filter'), str_replace('AND', ',', $where_str), $admin_info['userid']);
            nv_htmlOutput('OK');
        } catch (Exception $e) {
            trigger_error(print_r($e, true));
            nv_htmlOutput($nv_Lang->getModule('log_del_error'));
        }
    } else {
        if ($db->query('TRUNCATE TABLE ' . $db_config['prefix'] . '_logs')) {
            $nv_Cache->delMod($module_name);
            nv_insert_logs(NV_LANG_DATA, $module_name, $nv_Lang->getModule('log_empty_log'), 'All', $admin_info['userid']);
            nv_htmlOutput('OK');
        } else {
            nv_htmlOutput($nv_Lang->getModule('log_del_error'));
        }
    }
}

$id = $nv_Request->get_int('id', 'post,get', 0);
$contents = 'NO_' . $nv_Lang->getModule('log_del_error');
$number_del = 0;
if ($id > 0) {
    if ($db->exec('DELETE FROM ' . $db_config['prefix'] . '_logs WHERE id=' . $id)) {
        $contents = 'OK_' . $nv_Lang->getModule('log_del_ok');
        ++$number_del;
    }
} else {
    $listall = $nv_Request->get_string('listall', 'post,get');
    $array_id = explode(',', $listall);
    $array_id = array_map('intval', $array_id);
    foreach ($array_id as $id) {
        if ($id > 0) {
            $db->query('DELETE FROM ' . $db_config['prefix'] . '_logs WHERE id=' . $id);
            ++$number_del;
        }
    }
    $contents = 'OK_' . $nv_Lang->getModule('log_del_ok');
}

nv_insert_logs(NV_LANG_DATA, $module_name, $nv_Lang->getGlobal('delete') . ' ' . $nv_Lang->getModule('logs_title'), $number_del, $admin_info['userid']);

nv_htmlOutput($contents);