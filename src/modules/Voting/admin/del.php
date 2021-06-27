<?php

/**
 * NUKEVIET Content Management System
 * @version 5.x
 * @author VINADES.,JSC <contact@vinades.vn>
 * @copyright (C) 2009-2021 VINADES.,JSC. All rights reserved
 * @license GNU/GPL version 2 or any later version
 * @see https://github.com/nukeviet The NukeViet CMS GitHub project
 */

if (!defined('NV_IS_FILE_ADMIN')) {
    exit('Stop!!!');
}

if (!defined('NV_IS_AJAX')) {
    exit('Wrong URL');
}

$checkss = $nv_Request->get_string('checkss', 'post');
$vid = $nv_Request->get_int('vid', 'post', 0);
$contents = '';

if ($vid > 0 and $checkss == md5($vid . NV_CHECK_SESSION)) {
    nv_insert_logs(NV_LANG_DATA, $module_name, 'log_del_vote', 'votingid ' . $vid, $admin_info['userid']);
    $sql = 'DELETE FROM ' . NV_PREFIXLANG . '_' . $module_data . ' WHERE vid=' . $vid;
    if ($db->exec($sql)) {
        $db->query('DELETE FROM ' . NV_PREFIXLANG . '_' . $module_data . '_rows WHERE vid=' . $vid);
        $nv_Cache->delMod($module_name);

        $contents = 'OK_' . $vid;
    } else {
        $contents = 'ERR_' . $nv_Lang->getModule('voting_delete_unsuccess');
    }
}

include NV_ROOTDIR . '/includes/header.php';
echo $contents;
include NV_ROOTDIR . '/includes/footer.php';
