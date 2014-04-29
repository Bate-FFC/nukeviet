<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 2-2-2010 12:55
 */

if( ! defined( 'NV_IS_FILE_ADMIN' ) ) die( 'Stop!!!' );

$path = nv_check_path_upload( $nv_Request->get_string( 'path', 'post,get' ) );
$check_allow_upload_dir = nv_check_allow_upload_dir( $path );

if( ! isset( $check_allow_upload_dir['delete_file'] ) )
{
	die( 'ERROR#' . $lang_module['notlevel'] );
}

$file = htmlspecialchars( trim( $nv_Request->get_string( 'file', 'post,get' ) ), ENT_QUOTES );
$file = basename( $file );

if( empty( $file ) or ! is_file( NV_ROOTDIR . '/' . $path . '/' . $file ) )
{
	die( 'ERROR#' . $lang_module['errorNotSelectFile'] . NV_ROOTDIR . '/' . $path . '/' . $file );
}

if( $nv_Request->isset_request( 'path', 'post' ) and $nv_Request->isset_request( 'x', 'post' ) and $nv_Request->isset_request( 'y', 'post' ) )
{

	$config_logo = array();
	$config_logo['x'] = $nv_Request->get_int( 'x', 'post', 0 );
	$config_logo['y'] = $nv_Request->get_int( 'y', 'post', 0 );
	$config_logo['w'] = $nv_Request->get_int( 'w', 'post', 0 );
	$config_logo['h'] = $nv_Request->get_int( 'h', 'post', 0 );
    
	if( $config_logo['w'] > 0 and $config_logo['h'] > 0 )
	{
		require_once NV_ROOTDIR . '/includes/class/image.class.php';
		$createImage = new image( NV_ROOTDIR . '/' . $path . '/' . $file, NV_MAX_WIDTH, NV_MAX_HEIGHT );
        $createImage->cropFromLeft( $config_logo['x'], $config_logo['y'], $config_logo['w'], $config_logo['h'] );
		$createImage->save( NV_ROOTDIR . '/' . $path, $file );
        
        if( preg_match( "/^" . nv_preg_quote( NV_UPLOADS_DIR ) . "\/([a-z0-9\-\_\/]+)$/i", $path, $m ) )
		{
			@nv_deletefile( NV_ROOTDIR . '/' . NV_FILES_DIR . '/' . $m[1] . '/' . $image );
			$createImage->resizeXY( 80, 80 );
			$createImage->save( NV_ROOTDIR . '/' . NV_FILES_DIR . '/' . $m[1], $file, 75 );
			$create_Image_info = $createImage->create_Image_info;
		}

		$createImage->close();

		if( isset( $array_dirname[$path] ) )
		{
			$did = $array_dirname[$path];
			$info = nv_getFileInfo( $path, $file );
			$info['userid'] = $admin_info['userid'];
			$db->query( "REPLACE INTO " . NV_UPLOAD_GLOBALTABLE . "_file
							(name, ext, type, filesize, src, srcwidth, srcheight, sizes, userid, mtime, did, title) VALUES
							('" . $info['name'] . "', '" . $info['ext'] . "', '" . $info['type'] . "', " . $info['filesize'] . ", '" . $info['src'] . "', " . $info['srcwidth'] . ", " . $info['srcheight'] . ", '" . $info['size'] . "', " . $info['userid'] . ", " . $info['mtime'] . ", " . $did . ", '" . $file . "')" );
		}
    
		die( 'OK' );
	}
	else
	{
		die( 'ERROR#' . $lang_module['notlevel'] );
	}
}

$xtpl = new XTemplate( 'cropimg.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file );
$xtpl->assign( 'LANG', $lang_module );
$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
$xtpl->assign( 'NV_OP_URL', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op );
$xtpl->assign( 'NV_MODULE_URL', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' );
$xtpl->assign( 'IMG_PATH', $path );
$xtpl->assign( 'IMG_FILE', $file );
$xtpl->assign( 'IMG_MTIME', filemtime( NV_ROOTDIR . '/' . $path . '/' . $file ) );

$xtpl->parse( 'main' );
$contents = $xtpl->text( 'main' );

include NV_ROOTDIR . '/includes/header.php';
echo $contents;
include NV_ROOTDIR . '/includes/footer.php';