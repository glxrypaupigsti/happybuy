/**
 * Desc
 * @description Holp You Do Good But Not Evil
 * @copyright   Copyright 2014-2015 <ycchen@iwshop.cn>
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Chenyong Cai <ycchen@iwshop.cn>
 * @package     Wshop
 * @link        http://www.iwshop.cn
 */

requirejs(['jquery', 'util', 'fancyBox', 'Spinner'], function ($, util, fancyBox, Spinner) {

    var gmessId = 0;

    $('#saveBtn').click(function () {
        var data = $('#settingFrom').serializeArray();
        $.post('?/wSettings/updateSettings/', {
            data: data
        }, function (r) {
            if (r > 0) {
                util.Alert('保存成功');
            } else {
                util.Alert('保存失败', true);
            }
        });
    });

    // 2> 选择图文素材
    fnFancyBox('#sGmess', function () {
        $('.gmBlock').bind('click', function () {
            $('#welcomegmess').val(parseInt($(this).attr('data-id')));
            var block = $(this).clone();
            block.find('.sel').remove();
            block.find('.title').width($('#sGmess').width() - 28);
            block.find('.desc').width($('#sGmess').width() - 28);
            block.find('img').width($('#sGmess').width() - 28).height(($('#sGmess').width() - 28) / 1.8125);
            $('#GmessItem').empty().append(block).css({
                marginTop: '10px'
            });
            $('#gmessTip').hide();
            $.fancybox.close();
        });
    });

    /**
     * 图文选择自适应调整
     * @returns {undefined}
     */
    function gmBlockAdjust() {
        var block = $('.gmBlock').eq(0);
        block.find('.sel').remove();
        block.find('.title').width($('#sGmess').width() - 28);
        block.find('.desc').width($('#sGmess').width() - 28);
        block.find('img').width($('#sGmess').width() - 28).height(($('#sGmess').width() - 28) / 1.8125);
        $('#GmessItem').css({
            marginTop: '10px'
        });
        $('#gmessTip').hide();
    }

    /**
     * 清除抢红包记录
     */
    $('#clearRecord').click(function () {
        $.post('?/wSettings/clearEnvsRobRecord/', {}, function () {
            util.Alert('记录已清空');
        });
    });

    gmBlockAdjust();

});