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

    init();

    var lastInput = false;

    var pHash = [];

    pHash[1] = "北京";
    pHash[2] = "上海";
    pHash[3] = "天津";
    pHash[4] = "重庆";
    pHash[5] = "河北";
    pHash[6] = "山西";
    pHash[8] = "辽宁";
    pHash[9] = "吉林";
    pHash[11] = "江苏";
    pHash[12] = "浙江";
    pHash[13] = "安徽";
    pHash[14] = "福建";
    pHash[15] = "江西";
    pHash[16] = "山东";
    pHash[17] = "河南";
    pHash[18] = "湖北";
    pHash[19] = "湖南";
    pHash[20] = "广东";
    pHash[21] = "广西";
    pHash[22] = "海南";
    pHash[23] = "四川";
    pHash[24] = "贵州";
    pHash[25] = "云南";
    pHash[26] = "西藏";
    pHash[27] = "陕西";
    pHash[28] = "甘肃";
    pHash[29] = "宁夏";
    pHash[30] = "青海";
    pHash[31] = "新疆";
    pHash[32] = "香港";
    pHash[33] = "澳门";
    pHash[34] = "台湾";
    pHash[35] = "黑龙江";
    pHash[36] = "内蒙古";

    fnProvinceLis();

    fnFancyBox('#invoke', function () {
        var testStr = lastInput.val();
        var disable = getSelectedStr();
        $('#expprovince #in input').each(function () {
            if (disable.indexOf($(this).val()) !== -1) {
                $(this).get(0).checked = false;
                $(this).get(0).disabled = true;
                $(this).parent().find('label').css('color', '#eee');
            } else {
                $(this).parent().find('label').css('color', '#000');
                if (testStr.indexOf($(this).val()) !== -1) {
                    $(this).get(0).checked = true;
                } else {
                    $(this).get(0).checked = false;
                }
            }
        });

    });

    function getSelectedStr() {
        var m = [];
        $('.inputprovince').each(function () {
            if ($(this).val() !== '' && (!lastInput || lastInput.attr('data-index') !== $(this).attr('data-index'))) {
                m.push($(this).val());
            }
        });
        return m.join('|');
    }

    getSelectedStr();

    for (var k in pHash) {
        $('#expprovince #in').append('<a href="javascript:;"><label>' + pHash[k] + '</label><input type="checkbox" value="' + pHash[k] + '" /></a>');
    }

    $('#expprovince #in label').click(function () {
        $(this).parent().find('input').click();
    });

    $('#saveBtnEx').on('click', function () {
        if (lastInput) {
            var str = [];
            $('#expprovince #in input:checked').each(function () {
                str.push($(this).val());
            });
            lastInput.val(str.join('|'));
            $.fancybox.close();
        }
    });

    $('#addBtn').on('click', function () {
        var node = $('#expfieldTmplate').clone(false);
        node.addClass('expfield');
        node.find('input').val(0);
        node.find('input').eq(0).val('');
        $("#exps").append(node);
        fnProvinceLis();
        init();
    });

    function fnProvinceLis() {
        $('.inputprovince').unbind('click').bind('click', function () {
            lastInput = $(this);
            $('#invoke').click();
        });
    }

    $('#saveBtn').click(function () {
        var data = [];
        $('.expfield').each(function () {
            data.push([$(this).find('.inputprovince').val(), $(this).find('.inputffee').val(), $(this).find('.inputffeeadd').val()]);
        });
        $.post('?/wSettings/updateSettings/', {
            data: [
                {
                    'name': 'exp_weight1',
                    'value': $('#expWeight1').val()
                },
                {
                    'name': 'exp_weight2',
                    'value': $('#expWeight2').val()
                }
            ]
        }, function (r) {
            $.post('?/wSettings/updateExpTemplate/', {
                data: data
            }, function (r) {
                util.Alert('保存成功');
            });
        });
    });

    function init() {
        $('.inputprovince').each(function (index, node) {
            $(this).attr('data-index', index);
        });
    }

});