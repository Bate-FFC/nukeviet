<?php

/**
 * NUKEVIET Content Management System
 * @version 5.x
 * @author VINADES.,JSC <contact@vinades.vn>
 * @copyright (C) 2009-2021 VINADES.,JSC. All rights reserved
 * @license GNU/GPL version 2 or any later version
 * @see https://github.com/nukeviet The NukeViet CMS GitHub project
 */

if (!defined('NV_ADMIN')) {
    exit('Stop!!!');
}

if (defined('NV_IS_SPADMIN')) {
    $submenu['thumbconfig'] = $nv_Lang->getModule('thumbconfig');
    $submenu['config'] = $nv_Lang->getModule('configlogo');
    if (defined('NV_IS_GODADMIN')) {
        $submenu['uploadconfig'] = $nv_Lang->getModule('uploadconfig');
    }
}