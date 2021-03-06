/**
 * Desc
 * @description Holp You Do Good But Not Evil
 * @copyright   Copyright 2014-2015 <ycchen@iwshop.cn>
 * @license     LGPL (http=>//www.gnu.org/licenses/lgpl.html)
 * @author      Chenyong Cai <ycchen@iwshop.cn>
 * @package     Wshop
 * @link        http=>//www.iwshop.cn
 */
requirejs(['jquery', 'util', 'echarts', 'echarts/chart/line', 'echarts/chart/pie', 'echarts/chart/bar'], function($, util, echarts) {

    var  currentMonthDayNums= function(){
        var  day = new Date();
        var year = day.getFullYear();
        var month = day.getMonth()+1;
        return MonthOfDays(year,month);
    }

    var MonthOfDays = function(year,month){
        var d = new Date(year,month,0);
        return d.getDate();
    }
    var getDays = function(str, day_count, format) {
        if (typeof str === "number") {
            format = day_count;
            day_count = str;
            str = new Date();
        }
        var date = new Date();
        //var date = new Date(2015,11,28);
        var month = 0;
        var month_arr_30_days = [4,6,9,11];
        var month_arr_28_days = [2];
        var dates = [];
        var totalDays = 0;
        for (var i = 0; i < day_count + 1; i++) {
            var d = null;
            if (format) {
                var fmt = format;
                fmt = fmt.replace(/y{4}/, date.getFullYear());
                fmt = fmt.replace(/M{2}/, date.getMonth()%12==0?12:date.getMonth()%12);
                fmt = fmt.replace(/d{2}/, date.getDate() >= 10 ? date.getDate() : '0' + date.getDate());
                d = fmt;
            } else {
                d = date.getFullYear() + "-" + date.getMonth() + "-" + date.getDate();
            }
            month = date.getMonth();
            if($.inArray(month,month_arr_30_days)>-1){  //只有30天的情况
                if(date.getDate()!= 31){
                    dates.push(d);
                }
            }else if($.inArray(month,month_arr_28_days)>-1){ //只有28天的情况
                if(date.getDate()<=28){
                    dates.push(d);
                }
            }else{
                dates.push(d);
            }
            date.setDate(date.getDate() + 1);
        }
        return dates;
    };

    util.onresize(function() {
        var h = $(window).height() - 2;
        $('.stat-h-50').height(h / 2);
        $('#stat-wrap').height(h);
        $('.fLeft50').height((h / 2) - 3);
    });

    var Days = getDays(currentMonthDayNums(), "MM-dd");
    console.log(Days);

    $.post(shoproot + '?/WdminStat/getSaleStat/', {}, function(r) {
        var data = [];
        for (var i in Days) {
            if (r[Days[i]] !== undefined) {
                data.push(r[Days[i]]);
            } else {
                data.push(0);
            }
        }
        // 销售情况面积图
        var option = {
            title: {
                text: '近30天销售情况',
                //subtext: '纯属虚构',
                x: 'center',
                padding: 10,
                textStyle: {
                    fontFamily: 'Verdana, Microsoft YaHei,Helvetica',
                    align: 'center',
                    baseline: 'middle',
                    fontWeight: 'normal'
                }
            },
            tooltip: {
                trigger: 'axis'
            },
            toolbox: {
                show: false
            },
            calculable: false,
            xAxis: [
                {
                    type: 'category',
                    boundaryGap: false,
                    data: Days,
                    axisLine: {
                        show: false
                    }
                }
            ],
            yAxis: [
                {
                    type: 'value',
                    axisLabel: {
                        formatter: '{value} 元'
                    }
                }
            ],
            series: [
                {
                    name: '销售额',
                    type: 'line',
                    data: data,
                    markLine: {
                        data: [
                            {type: 'average', name: '平均值'}
                        ]
                    }
                }
            ]
        };

        echarts.init($('.stat-h-50').get(0)).setOption(option);
    });

    // 销售占比图
    $.get(shoproot + '?/WdminStat/getSalePercent/', function(res) {
        var option2 = {
            title: {
                text: '销售产品占比',
                x: 'center',
                padding: 10,
                textStyle: {
                    fontFamily: 'Verdana, Microsoft YaHei,Helvetica',
                    align: 'center',
                    baseline: 'middle',
                    fontWeight: 'normal'
                }
            },
            tooltip: {
                trigger: 'item',
                formatter: "{a} <br/>{b} : {c} ({d}%)"
            },
            toolbox: {
                show: false
            },
            calculable: true,
            series: [
                {
                    name: '访问来源',
                    type: 'pie',
                    radius: '45%',
                    center: ['50%', '55%'],
                    data: res
                }
            ]
        };
        echarts.init($('.fLeft50').get(0)).setOption(option2);
    });

    // 销售占比图
    $.get(shoproot + '?/WdminStat/getHotSaleProduct/', function(res) {
        var option2 = {
            title: {
                text: '热销产品占比',
                x: 'center',
                padding: 10,
                textStyle: {
                    fontFamily: 'Verdana, Microsoft YaHei,Helvetica',
                    align: 'center',
                    baseline: 'middle',
                    fontWeight: 'normal'
                }
            },
            tooltip: {
                trigger: 'item',
                formatter: "{a} <br/>{b} : {c} ({d}%)"
            },
            toolbox: {
                show: false
            },
            calculable: true,
            series: [
                {
                    name: '访问来源',
                    type: 'pie',
                    radius: '45%',
                    center: ['50%', '55%'],
                    data: res
                }
            ]
        };
        echarts.init($('.fLeft50').get(1)).setOption(option2);
    });

    // 
//
//    $.get(shoproot + '?/WdminStat/getSalePercent/', function(res) {
//
//        $('.fLeft50').eq(0).highcharts({
//            credits: {
//                enabled: false
//            },
//            chart: {
//                type: 'pie',
//                options3d: {
//                    enabled: true,
//                    alpha: 45,
//                    beta: 0
//                }, style: {
//                    fontFamily: '"Microsoft YaHei"',
//                    fontSize: '12px'
//                },
//                width: $(window).width() / 2,
//                height: $(window).height() / 2
//            },
//            title: {
//                text: '本月产品销售占比',
//                style: {
//                    fontSize: '15px',
//                    color: '#666',
//                    fontWeight: 'lighter'
//                }
//            },
//            tooltip: {
//                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
//            },
//            plotOptions: {
//                pie: {
//                    allowPointSelect: true,
//                    cursor: 'pointer',
//                    depth: 35,
//                    dataLabels: {
//                        enabled: true,
//                        format: '<b>{point.name}</b>: {point.percentage:.1f} %'
//                    },
//                    size: '60%'
//                }
//            },
//            series: [{
//                    type: 'pie',
//                    name: '占比',
//                    data: res
//                }]
//        });
//    });
//
//    $.get(shoproot + '?/WdminStat/getHotSaleProduct/', function(r) {
//        $('.fLeft50').eq(1).highcharts({
//            credits: {
//                enabled: false
//            },
//            chart: {
//                type: 'column',
//                margin: [30, 35, 90, 70],
//                style: {
//                    fontFamily: '"Microsoft YaHei"',
//                    fontSize: '12px'
//                },
//                height: $(window).height() / 2
//            },
//            title: {
//                text: '本月热销商品',
//                style: {
//                    fontSize: '15px',
//                    color: '#666',
//                    fontWeight: 'lighter'
//                }
//            },
//            xAxis: {
//                categories: r.x,
//                labels: {
//                    rotation: -45,
//                    align: 'right',
//                    style: {
//                        fontSize: '12px',
//                        fontFamily: 'Verdana, sans-serif'
//                    }
//                }
//            },
//            yAxis: {
//                min: 0,
//                title: {
//                    text: '销售数量 (件)'
//                }
//            },
//            legend: {
//                enabled: false
//            },
//            tooltip: {
//                pointFormat: '销售数量: <b>{point.y}</b> 件'
//            },
//            series: [{
//                    name: ' ',
//                    data: r.y,
//                    dataLabels: {
//                        enabled: true,
//                        rotation: -90,
//                        color: '#FFFFFF',
//                        align: 'right',
//                        x: 4,
//                        y: -5,
//                        style: {
//                            fontSize: '12px',
//                            fontFamily: 'Verdana, sans-serif',
//                            textShadow: '0 0 3px black'
//                        }
//                    }
//                }]});
//    });
});