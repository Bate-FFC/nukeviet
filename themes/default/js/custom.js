/**
 * NukeViet Content Management System
 * @version 4.x
 * @author VINADES.,JSC <contact@vinades.vn>
 * @copyright (C) 2009-2021 VINADES.,JSC. All rights reserved
 * @license GNU/GPL version 2 or any later version
 * @see https://github.com/nukeviet The NukeViet CMS GitHub project
 */

/* Các tùy chỉnh JS của giao diện nên để vào đây */
$('.laber_trai').on('click',function(){
    $('.dieuhuong-trai').click();
});
$('.laber_phai').on('click',function(){
    $('.dieuhuong-phai').click();
});
if ($(window).height()<700) {
    $('.menu_video').removeClass('col-md-8')
    $('.menu_video').addClass('col-md-24')

    $('.noidung').removeClass('col-md-16')
    $('.noidung').addClass('col-md-24')

    $('.menu').removeClass('col-md-24')
    $('.menu').addClass('col-md-12')

    $('.video').removeClass('col-md-24')
    $('.video').addClass('col-md-11')
}