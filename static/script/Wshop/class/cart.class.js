/* global shoproot */

/**
 * Desc
 * @description Holp You Do Good But Not Evil
 * @copyright   Copyright 2014-2015 <ycchen@iwshop.cn>
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Chenyong Cai <ycchen@iwshop.cn>
 * @package     Wshop
 * @link        http://www.iwshop.cn
 */

define([], function () {
    var storage = window.localStorage;
    var _o = {
        // cartJson对象
        cart: {},
        // 初始化
        init: function () {
            var _d = storage.getItem('cart');
            if (_d) {
                this.cart = eval('(' + storage.getItem('cart') + ')');
            } else {
                this.cart = {};
            }
            this.check();
        },
        doResultData:function(data){

        
            this.cart = {};
            storage.setItem('cart', '{}');
            storage.removeItem('tmporder');
            storage.removeItem('carthash');
            //alert(data.cartData);
            console.log("data=="+data.cartData);
            if(data.cartData != ""){

                storage.setItem('cart', JSON.stringify(data.cartData));
                
            }
            //console.log("data=    "+ JSON.stringify(data.cartData));

        },
        add: function (productId, count, priceHashId) {
            console.log("count="+count);

            eval("var ext = this.cart.p" + productId + "m" + priceHashId);
            var cmd = ext ? ' +=' : ' =';

            console.log("cmd=    "+ cmd);
            eval("this.cart.p" + productId + "m" + priceHashId + cmd + parseInt(count));
            this.save();
            console.log(storage.getItem('cart'));
             $.post(shoproot + '?/Cart/add_product_to_cart/', {
                data: storage.getItem('cart')
            }, this.doResultData);
        },
        add: function (productId, count, priceHashId,callback) {
            console.log("count="+count);

            eval("var ext = this.cart.p" + productId + "m" + priceHashId);
            var cmd = ext ? ' +=' : ' =';

            console.log("cmd=    "+ cmd);
            eval("this.cart.p" + productId + "m" + priceHashId + cmd + parseInt(count));
            this.save();
            console.log(storage.getItem('cart'));
             $.post(shoproot + '?/Cart/add_product_to_cart/', {
                data: storage.getItem('cart')
            }, callback);
        },
        del: function (productId, priceHashId) {
            eval("delete this.cart.p" + productId + "m" + priceHashId);
            $.post(shoproot + '?/Cart/removeProduct/product_id='+productId+"&spec_id="+priceHashId);
            this.save();

        },
        count: function () {
            var c = 0;
            for (var k in this.cart) {
                c += parseInt(this.cart[k]);
                console.log("this.cart[k]="+this.cart[k]);
            }
             console.log("c="+c);

            return c;
        },
        clear: function () {
            this.cart = {};
            storage.setItem('cart', '{}');
            storage.removeItem('tmporder');
            storage.removeItem('carthash');
        },
        save: function () {
            storage.setItem('cart', JSON.stringify(this.cart));
        },
        set: function (mhash, count) {
            eval("this.cart." + mhash + "=" + count);
            this.save();
            console.log(storage.getItem('cart'));
            $.post(shoproot + '?/Cart/add_product_to_cart/', {
                data: storage.getItem('cart')
            });
        },
        set: function (mhash, count,callback) {
            eval("this.cart." + mhash + "=" + count);
            this.save();
            console.log(storage.getItem('cart'));
            $.post(shoproot + '?/Cart/add_product_to_cart/', {
                data: storage.getItem('cart')
            },callback);
        },
        sync:function(callback){
             $.post(shoproot + '?/Cart/add_product_to_cart/', {
                data: storage.getItem('cart')
            },callback);
        },
        
        check: function () {

            $.post(shoproot + '?/Cart/checkCart/',this.doResultData);
            console.log("check");

        }
    };
    _o.init();
    return _o;
});