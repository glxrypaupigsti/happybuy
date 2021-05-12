/**
 * Desc
 * @description Holp You Do Good But Not Evil
 * @copyright   Copyright 2014-2015 <ycchen@iwshop.cn>
 * @license     LGPL (http=>//www.gnu.org/licenses/lgpl.html)
 * @author      Chenyong Cai <ycchen@iwshop.cn>
 * @package     Wshop
 * @link        http=>//www.iwshop.cn
 */
requirejs(['jquery', 'util', 'highcharts'], function($, util, highcharts) {
    $(function() {

        $(function() {
            $('#right_charts1').highcharts({
                credits: {
                    enabled: false
                },
                chart: {
                    type: 'area',
                    inverted: true,
                    style: {
                        fontFamily: '"Microsoft YaHei"',
                        fontSize: '12px'
                    }
                },
                title: {
                    text: ' '
                },
                xAxis: {
                    categories: [
                        '粉丝数',
                        '会员数',
                        '代理数'
                    ]
                },
                yAxis: {
                    title: {
                        text: ''
                    }
                },
                plotOptions: {
                    area: {
                        fillOpacity: 0.5,
                        dataLabels: {
                            enabled: true,
                            x: 20,
                            y: 12
                        },
                        color: '#44b549'
                    }
                },
                series: [{
                        name: '',
                        data: [parseInt($('#allfanscount').html()), parseInt($('#usersum').html()), parseInt($('#comsum').html())]
                    }],
                exporting: {
                    enabled: false
                },
                legend: {
                    enabled: false
                },
                tooltip: {
                    enabled: false,
                    pointFormat: '<b>{point.y}</b>'
                }
            });

            $('#right_charts2').highcharts({
                credits: {
                    enabled: false
                },
                chart: {
                    type: 'area',
                    inverted: true,
                    style: {
                        fontFamily: '"Microsoft YaHei"',
                        fontSize: '12px'
                    }
                },
                title: {
                    text: ' '
                },
                xAxis: {
                    categories: [
                        '下订单',
                        '已支付',
                        '快递中',
                        '未发货'
                    ]
                },
                yAxis: {
                    title: {
                        text: ''
                    }
                },
                plotOptions: {
                    area: {
                        fillOpacity: 0.5,
                        dataLabels: {
                            enabled: true,
                            x: 20,
                            y: 15
                        },
                        color: '#44b549'
                    }
                },
                series: [{
                        name: '',
                        data: [parseInt($('#neworder_month').val()), parseInt($('#valorder_month').val()), parseInt($('#orderdelivering').html()), parseInt($('#ordertoexp').html())]
                    }],
                exporting: {
                    enabled: false
                },
                legend: {
                    enabled: false
                },
                tooltip: {
                    enabled: false,
                    pointFormat: '<b>{point.y}</b>'
                }
            });
        });

        util.onresize(function() {
            $('#ovw-left,#ovw-right').height($(window).height());
            $('#right_charts').height($('#dTb').eq(0).height() - 2);
            $('#right_charts1').height($('#right_charts').height() / 2);
            $('#right_charts2').height($('#right_charts').height() / 2);
        });
    });
});