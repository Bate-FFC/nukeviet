<?php

/**
 * NUKEVIET Content Management System
 * @version 5.x
 * @author VINADES.,JSC <contact@vinades.vn>
 * @copyright (C) 2009-2021 VINADES.,JSC. All rights reserved
 * @license GNU/GPL version 2 or any later version
 * @see https://github.com/nukeviet The NukeViet CMS GitHub project
 */

if (!defined('NV_IS_MOD_STATISTICS')) {
    exit('Stop!!!');
}

$page_title = $nv_Lang->getModule('referer');
$key_words = $module_info['keywords'];
$mod_title = $nv_Lang->getModule('referer');

$sql = 'SELECT COUNT(*), SUM(total), MAX(total) FROM ' . NV_REFSTAT_TABLE;
$result = $db->query($sql);
list($num_items, $total, $max) = $result->fetch(3);

if ($num_items) {
    $page = $nv_Request->get_int('page', 'get', 1);
    $per_page = 50;
    $base_url = NV_BASE_MOD_URL . '&amp;' . NV_OP_VARIABLE . '=' . $module_info['alias']['allreferers'];

    $db->sqlreset()
        ->select('host, total, last_update')
        ->from(NV_REFSTAT_TABLE)
        ->where('total!=0')
        ->order('total DESC')
        ->limit($per_page)
        ->offset(($page - 1) * $per_page);
    $result = $db->query($db->sql());

    $host_list = [];
    while (list($host, $count, $last_visit) = $result->fetch(3)) {
        $last_visit = !empty($last_visit) ? nv_date('l, d F Y H:i', $last_visit) : '';
        $bymonth = '<a href="' . NV_BASE_MOD_URL . '&amp;' . NV_OP_VARIABLE . '=' . $module_info['alias']['referer'] . '&amp;host=' . $host . '">' . $nv_Lang->getModule('statbymoth2') . '</a>';
        $host_list[$host] = [$count, $last_visit, $bymonth];
    }

    if (!empty($host_list)) {
        $cts = [];
        $cts['thead'] = [$nv_Lang->getModule('referer'), $nv_Lang->getModule('hits'), $nv_Lang->getModule('last_visit')];
        $cts['rows'] = $host_list;
        $cts['max'] = $max;
        $cts['generate_page'] = nv_generate_page($base_url, $num_items, $per_page, $page);
    }
    if ($page > 1) {
        $page_title .= ' ' . NV_TITLEBAR_DEFIS . ' ' . $nv_Lang->getGlobal('page') . ' ' . $page;
    }
    $contents = nv_theme_statistics_allreferers($num_items, $cts, $host_list);
}

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
