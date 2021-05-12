<!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv=Content-Type content="text/html;charset=utf-8" />
        <title>{$page.title}</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" />
        <meta name="format-detection" content="telephone=no">
        <link href="{$docroot}static/css/wshop_gmess.css?{$cssversion}" type="text/css" rel="Stylesheet" />
        <link href="/favicon.ico" rel="Shortcut Icon" />
    </head>
    <body>
        <input type="hidden" id="msgid" value="{$page.id}" />
        <input type="hidden" id="share-img" value="{$page.catimg}" />
        <input type="hidden" id="share-desc" value="{$page.desc}" />
        <input type="hidden" id="share-title" value="{$page.title}" />
        <div id="wrapper">
            <div class="art-title">{$page.title}</div>
            <img src="uploads/gmess/{$page.catimg}" class="topImage" />
            <div id="img-content">
                {$page.content}
            </div>
        </div>
        <script type="text/javascript" src="{$docroot}static/script/jquery-2.1.1.min.js"></script>
        <script type="text/javascript" src="{$docroot}static/script/Wshop/shop_gmess.js"></script>
    </body>
</html>