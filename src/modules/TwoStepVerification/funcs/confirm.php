<?php

/**
 * NUKEVIET Content Management System
 * @version 5.x
 * @author VINADES.,JSC <contact@vinades.vn>
 * @copyright (C) 2009-2021 VINADES.,JSC. All rights reserved
 * @license GNU/GPL version 2 or any later version
 * @see https://github.com/nukeviet The NukeViet CMS GitHub project
 */

if (!defined('NV_MOD_2STEP_VERIFICATION')) {
    exit('Stop!!!');
}

$page_title = $module_info['site_title'];
$key_words = $module_info['keywords'];

$nv_redirect = '';
if ($nv_Request->isset_request('nv_redirect', 'post,get')) {
    $nv_redirect = nv_get_redirect();
}

/**
 * nv_json_result()
 *
 * @param mixed $array
 * @return
 */
function nv_json_result($array)
{
    global $nv_redirect;

    $array['redirect'] = $nv_redirect ? nv_redirect_decrypt($nv_redirect) : '';
    nv_jsonOutput($array);
}

if ($tokend_confirm_password != $tokend) {
    $checkss = $nv_Request->get_title('checkss', 'post', '');

    $blocker = new NukeViet\Core\Blocker(NV_ROOTDIR . '/' . NV_LOGS_DIR . '/ip_logs', NV_CLIENT_IP);
    $rules = [
        $global_config['login_number_tracking'],
        $global_config['login_time_tracking'],
        $global_config['login_time_ban']
    ];
    $blocker->trackLogin($rules);

    if ($checkss == NV_CHECK_SESSION) {
        if ($global_config['login_number_tracking'] and $blocker->is_blocklogin($user_info['username'])) {
            nv_json_result([
                'status' => 'error',
                'input' => '',
                'mess' => sprintf($nv_Lang->getGlobal('userlogin_blocked'), $global_config['login_number_tracking'], nv_date('H:i d/m/Y', $blocker->login_block_end))
            ]);
        }

        $nv_password = $nv_Request->get_title('password', 'post', '');
        $db_password = $db->query('SELECT password FROM ' . $db_config['prefix'] . '_' . $site_mods[NV_BRIDGE_USER_MODULE]['module_data'] . ' WHERE userid=' . $user_info['userid'])->fetchColumn();

        if ($crypt->validate_password($nv_password, $db_password)) {
            $blocker->reset_trackLogin($user_info['username']);
            $nv_Request->set_Session($tokend_key, $tokend);
            nv_json_result([
                'status' => 'ok',
                'input' => '',
                'mess' => ''
            ]);
        }

        if ($global_config['login_number_tracking'] and !empty($nv_password)) {
            $blocker->set_loginFailed($user_info['username'], NV_CURRENTTIME);
        }

        nv_json_result([
            'status' => 'error',
            'input' => 'password',
            'mess' => $nv_Lang->getGlobal('incorrect_password')
        ]);
    }

    $contents = nv_theme_confirm_password();
} else {
    if (empty($nv_redirect)) {
        header('Location: ' . nv_url_rewrite(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name, true));
        exit();
    }
    header('Location: ' . nv_redirect_decrypt($nv_redirect));
    exit();
}

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';