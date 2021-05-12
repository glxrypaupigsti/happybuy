<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0"
    />
<meta name="format-detection" content="telephone=no" />
<meta name="format-detection" content="email=no" />
<link rel="stylesheet" href="{$docroot}static/layer/layer.css" />
<link rel="stylesheet" href="{$docroot}static/css/bootstrap.css" />
<link rel="stylesheet" href="{$docroot}static/font/fonticon.css" />
<link rel="stylesheet" href="{$docroot}static/css/mobile.css" />
<script src="{$docroot}static/layer/layer.js"></script>
<title>Cheerslife暖心下午茶</title>
</head>

<body style=" background-color:#101010;">
<input type="hidden" id="type" value="{$type}" />
<input type="hidden" id="share_uid" value="{$share_uid}" />
<input type="hidden" id="from_uid" value="{$uinfo.uid}" />
<input type="hidden" id="time" value="{$time}" />
<div class="wallet-body"> <img class="title-img" src="{$docroot}static/img/hb1.png" />
  <div class="wallet-infor">
    <!-- 修改手机号-->
    <div class="wallet" id="modify-phone" style="display:none">
      <div class="now-phone"> 当前手机号 <span> {$uinfo.client_phone} </span> </div>
      <div class="modify-note"> 手机号修改后将在下次抢红包时生效 </div>
      <div class="input-phone">
        <input value="{$uinfo.client_phone}"  id ="modify_phone_txt" type="text" />

      </div>
      <div class="use-now" id="update_phone"> 更改 </div>
    </div>
    <!-- 修改手机号end-->
    <!-- 输入手机号-->
    {if $uinfo.client_phone == ''}

    <div class="wallet">

      <div class="input-phone">
        <input id="bind_phone_txt" placeholder="请输入11位手机号" type="text" />
      </div>
      <div id="bind_phone" class="use-now"> 立即领取 </div>
    </div>
    {/if}
    <!-- 输入手机号end-->

    {if $uinfo.client_phone != ''}

    <!-- 领券-->
    <div class="wallet" id="has-wallet">
      <div class="wallet-money"> <img class="money-bg" src="{$docroot}static/img/money_bg.png" />
        <div class="wallet-money-num">
          <div class="share-money">{if $couponInfo}<span class="wallet-num">{$couponInfo.coupon_value}</span> <span class="money-yuan"> 元 </span> {/if}
          {if $isTake == '1' }
            <!-- 已领券-->
            <span class="has-coupon"> 已领券! </span>
            <!-- 已领券end-->
            {else if $valid == '1' && isTake != '1'}
            <!-- 已领券-->
            <span class="has-coupon"> 已领完! </span>
            <!-- 已领券end-->
            {/if} </div>
        </div>

        <div class="modify-phone"> <span class="left-text">
          {if $isTake == '1' || $isTake == '2'}
           券已放入您的账户，
          {/if}
          {$uinfo.client_phone}
           <b>  </b> </span> <span class="right-modify"> 修改 <i class="iconfont icon-right"> </i> </span>
          <div class="clear"></div>
        </div>


        <a href="?/Index/index"><div class="use-now">

         {if $isTake == '1' || $isTake == '2'}

              立即使用
         {else}
             去逛逛CheersLife
         {/if}
         </div></a>
      </div>
      <!-- 领券end-->
    </div>
    {/if}
     </div>
  <!--领券前-->
    {if !$takeList}
<div class="wallet-footer">
      <div class="footer-title"><img src="{$docroot}static/img/foot-title.png" /></div>
      <div class="rule">
      <ul>
      <li>1. 您填入的手机号将成为您下单时的默认手机号</li>
      <li>2. 若您想改变默认手机号，请点击“修改”按钮</li>
      <li>3. 优惠券仅用于结算时冲抵，不可与其他优惠券叠加使用</li>
      <li>4. CheersLife保留法律范围内允许的对此活动的解释权</li>
      </ul>
      </div>
      </div>
  {/if}

  <!--领券前end-->

  {if $takeList}
  <!--领券后-->
  <div class="wallet-footer">
    <div class="footer-title"> <img src="{$docroot}static/img/foot-title1.png" /> </div>
    <div class="friend-wallet-list">
      <ul>
        {section name=oi loop=$takeList}
        <li>
          <div class="firend-wallet">
            <div class="friend-header"> <img src="{$takeList[oi]['uinfo'].client_head}/0" /> </div>

            <div class="friend-infor">
              <div class="friend-top"> <span class="f-name">{$takeList[oi]['uinfo'].client_name} </span> <span class="f-day"> {date("m月d日",$takeList[oi].add_time)} </span> <span class="f-time"> {date("H:i",$takeList[oi].add_time)} </span> </div>
              <div class="friend-com"> {$takeList[oi].des} </div>
            </div>
            <div class="friend-money"> {$takeList[oi].coupon_value}元 </div>
          </div>
        </li>
       {/section}
      </ul>
    </div>
  </div>
  <!--领券后end-->
 {/if}

</div>
<script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js">
    </script>
<script type="text/javascript">

      wx.config({
        debug: false,
        appId: '{$signPackage.appId}',
        timestamp: '{$signPackage.timestamp}',
        nonceStr: '{$signPackage.nonceStr}',
        signature: '{$signPackage.signature}',
        jsApiList: ['onMenuShareTimeline', 'onMenuShareAppMessage']
      });
      wx.ready(function() {
        // 在这里调用 API

        wx.onMenuShareTimeline({
          title: '【福利】快来领优惠券~享用CheersLife健康下午茶',
          // 分享标题
          link: '{$base_url}?/Share/share_wallet_view/type={$type}&uid={$share_uid}&from_uid={$uinfo.uid}&time={$time}',
          // 分享链接
          imgUrl: '{$base_url}/static/img/headimg.jpeg',
          // 分享图标
          success: function() {
          
          }
        });
        wx.onMenuShareAppMessage({
          title: '【福利】快来领优惠券~享用CheersLife健康下午茶',
          // 分享标题
          desc: '吃过下午茶，吃过这么健康的下午茶么？',
          // 分享描述
          link: '{$base_url}?/Share/share_wallet_view/type={$type}&uid={$share_uid}&from_uid={$uinfo.uid}&time={$time}',
          // 分享链接
          imgUrl: '{$base_url}/static/img/headimg.jpeg',
          // 分享图标
          success: function() {
        
          }

        });
      });



    </script>
  <script data-main="{$docroot}static/script/Wshop/share.js?v={$smarty.now}" src="{$docroot}static/script/require.min.js">
    </script>
</body>
</html>
