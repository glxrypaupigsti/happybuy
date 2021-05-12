<form id="form_alter_company" style="width:350px;padding:5px 10px;">

    <div class="gs-label">账号</div>
    <div class="gs-text">
        <input type="text" id="acc" value="{$auth.admin_account}" autofocus/>
    </div>    

    <div class="gs-label">密码</div>
    <div class="gs-text">
        <input type="password" id="pwd" value="" />
    </div>

    <div class="gs-label">权限</div>
    <div id="authList" class="expprovince" style="display: block;width: 349px;">
        <a href="javascript:;"><label>报表中心</label><input type="checkbox" value="stat" {if $auth.arr.stat}checked{/if}/></a>
        <a href="javascript:;"><label>订单管理</label><input type="checkbox" value="orde" {if $auth.arr.orde}checked{/if}/></a>
        <a href="javascript:;"><label>商品管理</label><input type="checkbox" value="prod" {if $auth.arr.prod}checked{/if}/></a>
        <a href="javascript:;"><label>消息群发</label><input type="checkbox" value="gmes" {if $auth.arr.gmes}checked{/if}/></a>
        <a href="javascript:;"><label>会员管理</label><input type="checkbox" value="user" {if $auth.arr.user}checked{/if}/></a>
        <a href="javascript:;"><label>代理合作</label><input type="checkbox" value="comp" {if $auth.arr.comp}checked{/if}/></a>
        <a href="javascript:;"><label>微店设置</label><input type="checkbox" value="sett" {if $auth.arr.sett}checked{/if}/></a>
        <a href="javascript:;"><label>优惠券管理</label><input type="checkbox" value="coupon" {if $auth.arr.coupon}checked{/if}/></a>
        <a href="javascript:;"><label>充值管理</label><input type="checkbox" value="charge" {if $auth.arr.charge}checked{/if}/></a>
        <a href="javascript:;"><label>配送管理</label><input type="checkbox" value="distribute" {if $auth.arr.distribute}checked{/if}/></a>
        <a href="javascript:;"><label>库存管理</label><input type="checkbox" value="stock" {if $auth.arr.stock}checked{/if}/></a>
    </div>
</form>
<div class="center" style="margin:0 -15px;">
    <a class="wd-btn primary" style="width:150px" id="al-com-save" data-id="{$auth.id}" href="javascript:;">
        {if $com.id > 0}提交{else}保存{/if}
    </a>
</div>