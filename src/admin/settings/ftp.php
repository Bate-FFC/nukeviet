<?php

/**
 * NUKEVIET Content Management System
 * @version 5.x
 * @author VINADES.,JSC <contact@vinades.vn>
 * @copyright (C) 2009-2021 VINADES.,JSC. All rights reserved
 * @license GNU/GPL version 2 or any later version
 * @see https://github.com/nukeviet The NukeViet CMS GitHub project
 */

if (!defined('NV_ADMIN') or !defined('NV_MAINFILE') or !defined('NV_IS_MODADMIN')) {
    exit('Stop!!!');
}

$error = '';
$page_title = $nv_Lang->getModule('ftp_config');

$tpl = new \NukeViet\Template\Smarty();
$tpl->setTemplateDir(NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$tpl->assign('LANG', $nv_Lang);
$tpl->assign('SYS_INFO', $sys_info);
$tpl->assign('FORM_ACTION', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op);

if ($sys_info['ftp_support']) {
    $array_config = [];

    $array_config['ftp_server'] = $nv_Request->get_title('ftp_server', 'post', $global_config['ftp_server'], 1);
    $array_config['ftp_port'] = $nv_Request->get_title('ftp_port', 'post', $global_config['ftp_port'], 1);
    $array_config['ftp_user_name'] = $nv_Request->get_title('ftp_user_name', 'post', $global_config['ftp_user_name'], 1);
    $array_config['ftp_user_pass'] = $nv_Request->get_title('ftp_user_pass', 'post', $global_config['ftp_user_pass'], 0);
    $array_config['ftp_path'] = $nv_Request->get_title('ftp_path', 'post', $global_config['ftp_path'], 1);
    $array_config['ftp_check_login'] = $global_config['ftp_check_login'];

    // Tu dong nhan dang Remove Path
    if ($nv_Request->isset_request('tetectftp', 'post')) {
        $ftp_server = nv_unhtmlspecialchars($array_config['ftp_server']);
        $ftp_port = (int) $array_config['ftp_port'];
        $ftp_user_name = nv_unhtmlspecialchars($array_config['ftp_user_name']);
        $ftp_user_pass = nv_unhtmlspecialchars($array_config['ftp_user_pass']);

        if (!$ftp_server or !$ftp_user_name or !$ftp_user_pass) {
            exit('ERROR|' . $nv_Lang->getModule('ftp_error_full'));
        }

        $ftp = new NukeViet\Ftp\Ftp($ftp_server, $ftp_user_name, $ftp_user_pass, ['timeout' => 10], $ftp_port);

        if (!empty($ftp->error)) {
            $ftp->close();
            exit('ERROR|' . (string) $ftp->error);
        }
        $list_valid = [NV_ASSETS_DIR, 'includes', 'index.php', 'modules', 'themes', 'vendor'];
        $ftp_root = $ftp->detectFtpRoot($list_valid, NV_ROOTDIR);

        if ($ftp_root === false) {
            $ftp->close();
            exit('ERROR|' . (empty($ftp->error) ? $nv_Lang->getModule('ftp_error_detect_root') : (string) $ftp->error));
        }

        $ftp->close();
        exit('OK|' . $ftp_root);

        $ftp->close();
        exit('ERROR|' . $nv_Lang->getModule('ftp_error_detect_root'));
    }

    if ($nv_Request->isset_request('ftp_server', 'post')) {
        $array_config['ftp_check_login'] = 0;

        if (!empty($array_config['ftp_server']) and !empty($array_config['ftp_user_name']) and !empty($array_config['ftp_user_pass'])) {
            $ftp_server = nv_unhtmlspecialchars($array_config['ftp_server']);
            $ftp_port = (int) $array_config['ftp_port'];
            $ftp_user_name = nv_unhtmlspecialchars($array_config['ftp_user_name']);
            $ftp_user_pass = nv_unhtmlspecialchars($array_config['ftp_user_pass']);
            $ftp_path = nv_unhtmlspecialchars($array_config['ftp_path']);

            $ftp = new NukeViet\Ftp\Ftp($ftp_server, $ftp_user_name, $ftp_user_pass, ['timeout' => 10], $ftp_port);

            if (!empty($ftp->error)) {
                $array_config['ftp_check_login'] = 3;
                $error = (string) $ftp->error;
            } elseif ($ftp->chdir($ftp_path) === false) {
                $array_config['ftp_check_login'] = 2;
                $error = $nv_Lang->getGlobal('ftp_error_path');
            } else {
                $check_files = [NV_ASSETS_DIR, 'includes', 'index.php', 'modules', 'themes', 'vendor'];
                $list_files = $ftp->listDetail($ftp_path, 'all');

                $a = 0;
                if (!empty($list_files)) {
                    foreach ($list_files as $filename) {
                        if (in_array($filename['name'], $check_files, true)) {
                            ++$a;
                        }
                    }
                }

                if ($a == sizeof($check_files)) {
                    $array_config['ftp_check_login'] = 1;
                } else {
                    $array_config['ftp_check_login'] = 2;
                    $error = $nv_Lang->getGlobal('ftp_error_path');
                }
            }
            $ftp->close();
        }

        if (empty($error)) {
            $array_config['ftp_user_pass'] = $crypt->encrypt($ftp_user_pass);

            $sth = $db->prepare('UPDATE ' . NV_CONFIG_GLOBALTABLE . " SET config_value= :config_value WHERE config_name = :config_name AND lang = 'sys' AND module='global'");
            foreach ($array_config as $config_name => $config_value) {
                $sth->bindParam(':config_name', $config_name, PDO::PARAM_STR, 30);
                $sth->bindParam(':config_value', $config_value, PDO::PARAM_STR);
                $sth->execute();
            }

            nv_save_file_config_global();
            nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op . '&rand=' . nv_genpass());
        }
        $array_config['ftp_user_pass'] = $ftp_user_pass;
    }

    $tpl->assign('DATA', $array_config);
    $tpl->assign('ERROR', $error);
}

$contents = $tpl->fetch('ftp.tpl');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
