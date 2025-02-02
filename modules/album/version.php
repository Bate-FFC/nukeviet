<?php

/**
 * NukeViet Content Management System
 * @version 4.x
 * @author VINADES.,JSC <contact@vinades.vn>
 * @copyright (C) 2009-2021 VINADES.,JSC. All rights reserved
 * @license GNU/GPL version 2 or any later version
 * @see https://github.com/nukeviet The NukeViet CMS GitHub project
 */

if (!defined('NV_ADMIN') or !defined('NV_MAINFILE')) {
    exit('Stop!!!');
}

$module_version = [
    'name' => 'album', // Tieu de module
    'modfuncs' => 'main, album', // Cac function co block
    'is_sysmod' => 0, // 1:0 => Co phai la module he thong hay khong
    'virtual' => 1, // 1:0 => Co cho phep ao hao module hay khong
    'version' => '4.5.00', // Phien ban cua modle
    'date' => 'Saturday, July 17, 2021 4:00:00 PM GMT+07:00', // Ngay phat hanh phien ban
    'author' => 'VINADES.,JSC <contact@vinades.vn>', // Tac gia
    'note' => '', // Ghi chu
    'uploads_dir' => [
        $module_upload
    ],
];
