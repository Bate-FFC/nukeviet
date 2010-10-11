<?php
/**
 * @Project NUKEVIET 3.0
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2010 VINADES., JSC. All rights reserved
 * @Createdate 3/9/2010 23:25
 */

if ( ! defined( 'NV_IS_MOD_NEWS' ) ) die( 'Stop!!!' );

$blocknewsid = 2;

global $global_config, $module_name, $module_data, $module_file, $global_array_cat, $module_config, $module_info;
$array_block_news = array();

$cache_file = NV_LANG_DATA . "_" . $module_name . "_block_news_" . NV_CACHE_PREFIX . ".cache";
if ( ( $cache = nv_get_cache( $cache_file ) ) != false )
{
    $array_block_news = unserialize( $cache );
}
else
{
    $sql = "SELECT id, listcatid, publtime, exptime, title, alias, homeimgthumb FROM `" . NV_PREFIXLANG . "_" . $module_data . "_rows` WHERE `status`= 1 AND `publtime` < " . NV_CURRENTTIME . " AND (`exptime`=0 OR `exptime`>" . NV_CURRENTTIME . ") ORDER BY `publtime` DESC LIMIT 0 , 20";
    $result = $db->sql_query( $sql );
    while ( list( $id, $listcatid, $publtime, $exptime, $title, $alias, $homeimgthumb ) = $db->sql_fetchrow( $result ) )
    {
        $catid = end( explode( ",", $listcatid ) );
        $imgurl = "";
        $imagesizex = $imagesizey = 0;
        $link = NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=" . $module_name . "&amp;" . NV_OP_VARIABLE . "=" . $global_array_cat[$catid]['alias'] . "/" . $alias . "-" . $id;
        if ( $homeimgthumb != "" )
        {
            $arr_homeimgthumb = explode( "|", $homeimgthumb );
            if ( isset( $arr_homeimgthumb[1] ) and ! empty( $arr_homeimgthumb[1] ) and file_exists( NV_UPLOADS_REAL_DIR . '/' . $module_name . '/' . $arr_homeimgthumb[1] ) )
            {
                $imgurl = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_name . '/' . $arr_homeimgthumb[1];
                $size = @getimagesize( NV_UPLOADS_REAL_DIR . '/' . $module_name . '/' . $arr_homeimgthumb[1] );
                $imagesizex = $size[0];
                $imagesizey = $size[1];
            }
        }
        $array_block_news[] = array( 
            'id' => $id, 'listcatid' => $listcatid, 'title' => $title, 'link' => $link, 'imgurl' => $imgurl, 'width' => $imagesizex, 'height' => $imagesizey 
        );
    }
    $cache = serialize( $array_block_news );
    nv_set_cache( $cache_file, $cache );
}

$blockwidth = $module_config[$module_name]['blockwidth'];

$xtpl = new XTemplate( "block_news.tpl", NV_ROOTDIR . "/themes/" . $module_info['template'] . "/modules/" . $module_file );
$a = 1;
foreach ( $array_block_news as $array_news )
{
    if ( $array_news['width'] > $blockwidth )
    {
        $array_news['height'] = round( ( $blockwidth / $array_news['width'] ) * $array_news['height'] );
        $array_news['width'] = $blockwidth;
    }
    $xtpl->assign( 'blocknews', $array_news );
    if ( $array_news['width'] > 0 )
    {
        $xtpl->parse( 'main.newloop.imgblock' );
    }
    $xtpl->parse( 'main.newloop' );
    $xtpl->assign( 'BACKGROUND', ( $a % 2 ) ? 'bg ' : '' );
    $a ++;
}
$xtpl->parse( 'main' );
$content = $xtpl->text( 'main' );

?>