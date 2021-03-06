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
        <script data-main="{$docroot}static/script/wdmin-frame.js?v={$cssversion}" src="{$docroot}static/script/require.min.js"></script>
        <script type="text/javascript" src="{$docroot}static/script/jquery-1.7.2.min.js"></script>
        <script type="text/javascript" src="{$docroot}static/script/timepicker/js/jquery-ui.js"></script>
        <script type="text/javascript" src="{$docroot}static/script/timepicker/js/jquery-ui-slide.min.js"></script>
        <script type="text/javascript" src="{$docroot}static/script/timepicker/js/jquery-ui-timepicker-addon.js"></script>
    </head>
    <body>
        <i id="scriptTag">{$docroot}static/script/Wdmin/settings/banner_edit.js</i>
        <input type="hidden" value="{$smarty.server.HTTP_REFERER}" id="http_referer" /> 
        <input type="hidden" value="{$banner.relid}" id="relid" /> 
        <input type="hidden" value="{$banner.reltype}" id="relType" /> 
        <div class="clearfix" style="padding:10px 20px;padding-bottom: 65px;">
            <div class="fv2Field clearfix">
                <div class="fv2Left">
                    <span>???????????????</span>
                </div>
                <div class="fv2Right">
                    <input class='gs-input' type="text" name="cat_name" id='cat_name' value="{$banner.banner_name}" />
                    <div class='fv2Tip'>??????????????????</div>
                </div>
            </div>
            <div class="fv2Field clearfix">
                <div class="fv2Left">
                    <span>??????</span>
                </div>
                <div class="fv2Right">
                    <input class='gs-input' type="text" name="cat_order" id='cat_order' value="{$banner.sort}" />
                    <div class='fv2Tip'>????????????????????????</div>
                </div>
            </div>
            <div class="fv2Field clearfix">
                <div class="fv2Left">
                    <span>????????????</span>
                </div>
                <div class="fv2Right">
                    <select id="bn-type" style="color:#666">
                        <option value="0" data-hash="hashCat" {if $banner.reltype eq 0}selected{/if}>????????????</option>
                        <option value="1" data-hash="hashProduct" {if $banner.reltype eq 1}selected{/if}>????????????</option>
                        <option value="2" data-hash="hashGmess" {if $banner.reltype eq 2}selected{/if}>????????????</option>
                        <option value="3" data-hash="hashLink" {if $banner.reltype eq 3}selected{/if}>?????????</option>
                    </select>
                    <div class='fv2Tip'>??????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????</div>
                </div>
            </div>

            <!-- ????????? -->
            <div class="typeHash hidden" id="hashLink">
                <div class="fv2Field clearfix">
                    <div class="fv2Left">
                        <span>????????????</span>
                    </div>
                    <div class="fv2Right">
                        <input class='gs-input' type="text" name="link_address" id='link_address' value="{$banner.banner_href}" />
                        <div class='fv2Tip'>????????????????????????</div>
                    </div>
                </div>
            </div>

            <!-- ???????????? -->
            <div class="fv2Field typeHash clearfix hidden" id="hashGmess">
                <div class="fv2Left">
                    <span>????????????</span>
                </div>
                <div class="fv2Right">
                    <a id="sGmess" href="?/WdminPage/ajax_gmess_list/" class="wd-btn primary fancybox.ajax" data-fancybox-type="ajax" style="margin:0;width:100%;" data-id="">????????????</a>
                    <div id="GmessItem" class="clearfix">
                        {if $gm}
                            <div class="gmBlock" data-id="{$gm.id}">
                                <a class="sel hov"></a>
                                <p class="title Elipsis">{$gm.title}</p>
                                <img src="{$docroot}uploads/gmess/{$gm.catimg}" />
                                <p class="desc Elipsis">{$gm.desc}</p>
                            </div>
                        {/if}
                    </div>
                    <div class='fv2Tip' id="gmessTip">???????????????????????????</div>
                </div>
            </div>

            <!-- ???????????? -->        
            <div class="fv2Field typeHash clearfix hidden" id="hashProduct" style="max-width:100%;">
                <div class="fv2Left">
                    <span>????????????</span>
                </div>
                <div class="fv2Right">
                    <a id="sProduct" href="?/FancyPage/ajaxSelectProduct/" class="wd-btn primary fancybox.ajax" data-fancybox-type="ajax" style="margin:0;width:389px;" data-id="">????????????</a>
                    <div class='fv2Tip hidden' id="spdCount">?????????100?????????</div>
                    <div id="ProductItem" class="clearfix">
                        {if $products}
                            {include file='../fancy/ajaxPdBlocks.tpl'}
                        {/if}
                    </div>
                    <div class='fv2Tip' id="spdTip">?????????????????????</div>
                </div>
            </div>

            <!-- ???????????? -->
            <div class="fv2Field typeHash clearfix hidden" id="hashCat">
                <div class="fv2Left">
                    <span>????????????</span>
                </div>
                <div class="fv2Right">
                    <select id="pd-cat-select" style="color:#666">
                        {foreach from=$categorys item=cat1}
                            <option value="{$cat1.dataId}" {if $banner.relid eq $cat1.dataId}selected{/if}>{$cat1.name}</option>
                            {foreach from=$cat1.children item=cat2}
                                <option value="{$cat2.dataId}" {if $banner.relid eq $cat2.dataId}selected{/if}>-- {$cat2.name}</option>
                                {foreach from=$cat2.children item=cat3}
                                    <option value="{$cat3.dataId}" {if $banner.relid eq $cat3.dataId}selected{/if}>---- {$cat3.name}</option>
                                {/foreach}
                            {/foreach}
                        {/foreach}
                    </select>
                    <div class='fv2Tip'>????????????????????????</div>
                </div>
            </div>

            <div class="fv2Field clearfix">
                <div class="fv2Left">
                    <span>????????????</span>
                </div>
                <div class="fv2Right">
                    <select id="bn-position" style="color:#666">
                        <option value="0" {if $banner.banner_position eq 0}selected{/if}>????????????</option>
                        <option value="1" {if $banner.banner_position eq 1}selected{/if}>????????????</option>
                        <option value="2" {if $banner.banner_position eq 2}selected{/if}>????????????</option>
                        <option value="3" {if $banner.banner_position eq 3}selected{/if}>????????????</option>
                        <option value="4" {if $banner.banner_position eq 4}selected{/if}>????????????</option>
                    </select>
                    <div class='fv2Tip'>????????????????????????</div>
                </div>
            </div>

            <div class="fv2Field clearfix">
                <div class="fv2Left">
                    <span>???????????????</span>
                </div>
                <div class="fv2Right">
                    <div class="clearfix">
                        <div class="alter-cat-img">
                            <input type="hidden" value="{$banner.banner_image}" id="banner_image" />
                            <div id="loading" style="transition-duration: .2s;"></div>
                            <img id="catimage" src="{if $banner.banner_image neq ''}uploads/banner/{$banner.banner_image}{/if}" />
                            {if $banner.banner_image eq ''}
                                <div style='line-height: 100px;color:#777;' class='align-center' id="cat_none_pic">?????????</div>
                            {/if}
                            <div class="align-center top10">
                                <a class="wd-btn primary" id="alter_categroy_image" href="javascript:;">????????????</a>
                            </div>
                        </div>
                    </div>
                    <div class='fv2Tip'>????????????????????????????????? ????????????600&times;290</div>
                </div>
            </div>

            <div class="fv2Field clearfix">
                <div class="fv2Left">
                    <span>????????????</span>
                </div>
                <div class="fv2Right">
                    <input class='gs-input' type="text" id='exp' value="{$banner.exp}" />
                    <div class='fv2Tip'>?????????????????????????????????????????????</div>
                </div>
            </div>

        </div>
        <div class="fix_bottom fixed">
            <a class="wd-btn primary" id='save' data-id='{$banner.id}' href="javascript:;">??????</a>
            {if $banner.id > 0}<a class="wd-btn delete" id='delete' data-id='{$banner.id}' href="javascript:;">??????</a>{/if}
            <a class="wd-btn default" href="javascript:;" onclick="location.href = $('#http_referer').val();">??????</a>
        </div>

        <script type="text/javascript">
            $(function () {
                $('#exp').datetimepicker();
            });
        </script>

    </body>
</html>