<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="renderer" content="webkit">
        <title></title>
        <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" />
        <meta name="apple-mobile-web-app-capable" content="yes" />
        <meta name="apple-mobile-web-app-status-bar-style" content="black" />
        <meta name="format-detection" content="telephone=no" />
        <link href="{$docroot}favicon.ico" rel="Shortcut Icon" />
        <link href="{$docroot}static/css/wshop_admin_style.css?v={$cssversion}" type="text/css" rel="Stylesheet" />
        <link href="{$docroot}static/script/zTree_v3/css/zTreeStyle/zTreeStyle.css" type="text/css" rel="Stylesheet" />
        <link href="{$docroot}static/script/umeditor/themes/default/css/umeditor.min.css" type="text/css" rel="Stylesheet" />
        <link href="{$docroot}static/script/timepicker/css/jquery-ui.css" type="text/css" rel="Stylesheet" />
        <link href="{$docroot}static/script/fancyBox/source/jquery.fancybox.css" type="text/css" rel="Stylesheet" />
    </head>
    <body>
        <i id="scriptTag">{$docroot}static/script/Wdmin/customers/customer_envsend.js</i>
        <div class='gmess-sending'></div>
        <form style="padding:15px 20px;padding-bottom: 70px;" id="settingFrom">

            <div class="fv2Field clearfix">
                <div class="fv2Left">
                    <span>发放对象</span>
                </div>
                <div class="fv2Right">
                    <select id="envsTarget">
                        <option value="0">全部用户</option>
                        <option value="1" data-hash="hashGroup">分组用户</option>
                        <option value="2" data-hash="hashPart">部分用户</option>
                    </select>
                    <div class='fv2Tip'>微店铺名称，显示在网页标题结尾</div>
                </div>
            </div>

            <div class="fv2Field typeHash clearfix hidden" id="hashGroup">
                <div class="fv2Left">
                    <span>分组选择</span>
                </div>
                <div class="fv2Right">
                    <select id="envsGroup">
                        {foreach from=$group item=g name=groupn}
                            <option value="{$g.id}">{$g.level_name} ({$g.count})</option>
                        {/foreach}
                    </select>
                    <div class='fv2Tip'>请选择发放的分组</div>
                </div>
            </div>

            <div class="fv2Field typeHash clearfix hidden" id="hashPart">
                <input type="hidden" value="{$settings.welcomegmess}" name="welcomegmess" id="welcomegmess" />
                <div class="fv2Left">
                    <span>用户选择</span>
                </div>
                <div class="fv2Right">
                    <a id="sGmess" href="{$docroot}?/WdminAjax/ajax_customer_select/" class="wd-btn primary fancybox.ajax" data-fancybox-type="ajax" style="margin:0;width:100%;" data-id="">选择用户</a>
                    <div id="GmessItem" class="clearfix">
                        {if $gm}
                            <div class="gmBlock" data-id="{$gm.id}">
                                <a class="sel hov"></a>
                                <p class="title Elipsis">{$gm.title}</p>
                                <img src="uploads/gmess/{$gm.catimg}" />
                                <p class="desc Elipsis">{$gm.desc}</p>
                            </div>
                        {/if}
                    </div>
                    <div id="ProductItem" class="clearfix" style="margin-top: 10px;">
                        {if $products}
                            {include file='../fancy/ajaxPdBlocks.tpl'}
                        {/if}
                    </div>
                    <div class='fv2Tip' id="gmessTip">请点击选择用户</div>
                </div>
            </div>

            <div class="fv2Field clearfix">
                <div class="fv2Left">
                    <span>红包选择</span>
                </div>
                <div class="fv2Right">
                    <select id="envsId">
                        {foreach from=$envs item=env}
                            <option value="{$env.id}">{$env.name} (满{$env.req_amount}减{$env.dis_amount})</option>
                        {/foreach}
                    </select>
                    <div class='fv2Tip'>请选择发放的红包类型</div>
                </div>
            </div>

            <div class="fv2Field clearfix">
                <div class="fv2Left">
                    <span>发放数量</span>
                </div>
                <div class="fv2Right">
                    <input type="text" class="gs-input" value="1" id="count" autofocus/>
                    <div class='fv2Tip'>每用户发放红包的数量</div>
                </div>
            </div>

            <div class="fv2Field clearfix">
                <div class="fv2Left">
                    <span>过期日期</span>
                </div>
                <div class="fv2Right">
                    <input type="text" class="gs-input" value="" id="dt" autofocus/>
                    <div class='fv2Tip'>红包过期日期，不包括当日</div>
                </div>
            </div>  
			<a class="wd-btn primary" id='saveBtn' style="width:150px;margin-left: 150px;margin-top: 20px;" href="javascript:;">开始发放</a>
        </form>

		<!--
        <div class="fix_bottom" style="position: fixed">
            <a class="wd-btn primary" id='saveBtn' style="width:150px" href="javascript:;">开始发放</a>
        </div>
 		-->
        <script type="text/javascript" src="{$docroot}static/script/jquery-1.7.2.min.js"></script>
        <script type="text/javascript" src="{$docroot}static/script/timepicker/js/jquery-ui.js"></script>
        <script type="text/javascript" src="{$docroot}static/script/timepicker/js/jquery-ui-slide.min.js"></script>
        <script type="text/javascript" src="{$docroot}static/script/timepicker/js/jquery-ui-timepicker-addon.js"></script>
        <script type="text/javascript">
            $(function () {
                $('#dt').datetimepicker();
            });
        </script>

        <script data-main="{$docroot}static/script/wdmin-frame.js?v={$cssversion}" src="{$docroot}static/script/require.min.js"></script>

    </body>
</html>