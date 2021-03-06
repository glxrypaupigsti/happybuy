{include file='../__header.tpl'}
<i id="scriptTag">{$docroot}static/script/Wdmin/settings/base.js</i>

<form style="padding:15px 20px;padding-bottom: 70px;" id="settingFrom">

    <div class="fv2Field clearfix">
        <div class="fv2Left">
            <span>签到积分</span>
        </div>
        <div class="fv2Right">
            <input type="text" class="gs-input" name="sign_credit" value="{if $settings.sign_credit > 0}{$settings.sign_credit}{else}0{/if}" />
            <div class='fv2Tip'>签到之后获取的积分数额</div>
        </div>
    </div>

    <div class="fv2Field clearfix">
        <div class="fv2Left">
            <span>签到间隔</span>
        </div>
        <div class="fv2Right">
            <input type="text" class="gs-input" name="sign_daylim" value="{$settings.sign_daylim}" />
            <div class='fv2Tip'>用户签到时间间隔 单位：天</div>
        </div>
    </div>

</form>


<div class="fix_bottom" style="position: fixed">
    <a class="wd-btn primary" id='saveBtn' style="width:150px" href="javascript:;">保存设置</a>
</div>

{include file='../__footer.tpl'} 