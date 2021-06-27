/**
 * NUKEVIET Content Management System
 * @version 5.x
 * @author VINADES.,JSC <contact@vinades.vn>
 * @copyright (C) 2009-2021 VINADES.,JSC. All rights reserved
 * @license GNU/GPL version 2 or any later version
 * @see https://github.com/nukeviet The NukeViet CMS GitHub project
 */

$(document).ready(function() {
    // Thumbconfig
    $('[data-toggle="thumbCfgViewEx"]').click(function(e) {
        e.preventDefault();

        if (typeof $(this).data('busy') == "undefined" || !$(this).data('busy')) {
            var $this = $(this);
            var ctn = $this.parent().parent();
            var did = $this.data('did') != -1 ? $this.data('did') : $('[name="other_dir"]', ctn).val(),
                thumbType = $('[name="other_type"]', ctn).length ? $('[name="other_type"]', ctn).val() : $('[name="thumb_type[' + did + ']"]', ctn).val(),
                thumbW = $('[name="other_thumb_width"]', ctn).length ? $('[name="other_thumb_width"]', ctn).val() : $('[name="thumb_width[' + did + ']"]', ctn).val(),
                thumbH = $('[name="other_thumb_height"]', ctn).length ? $('[name="other_thumb_height"]', ctn).val() : $('[name="thumb_height[' + did + ']"]', ctn).val(),
                thumbQuality = $('[name="other_thumb_quality"]', ctn).length ? $('[name="other_thumb_quality"]', ctn).val() : $('[name="thumb_quality[' + did + ']"]', ctn).val();

            if ((!did && $this.data('did') == -1) || thumbType == 0 || !thumbW || !thumbH || !thumbQuality || thumbW == 0 || thumbH == 0 || thumbQuality == 0) {
                alert($this.data('errmsg'));
                return false;
            }

            $this.data('busy', true);
            $this.find('i').removeClass('fa-search');
            $this.find('i').addClass('fa-cog');
            $this.find('i').addClass('fa-spin');

            $.post(
                script_name + '?' + nv_lang_variable + '=' + nv_lang_data + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=thumbconfig&nocache=' + new Date().getTime(),
                'getexample=1&did=' + did + '&t=' + thumbType + '&w=' + thumbW + '&h=' + thumbH + '&q=' + thumbQuality,
                function(res) {
                    $this.data('busy', false);
                    $this.find('i').removeClass('fa-cog');
                    $this.find('i').removeClass('fa-spin');
                    $this.find('i').addClass('fa-search');
                    if (res.status != 'success') {
                        $('#thumbprewiew').html(res.message);
                        return false;
                    }
                    $('#thumbprewiewtmp .imgorg').attr('src', res.src);
                    $('#thumbprewiewtmp .imgthumb').attr('src', res.thumbsrc);
                    $('#thumbprewiew').html($('#thumbprewiewtmp').html());
                    $('html, body').animate({
                        scrollTop: $('#thumbprewiew').offset().top - 10
                    }, 'slow');
                }
            );
        }
    });
});
