{strip}{section name=i loop=$product_list}
        <section class="cartListWrap clearfix" id="cartsec{$product_list[i].product_id}">
            <input type="hidden" value="{$product_list[i].envs}" id="pd-envs-{$product_list[i].product_id}" 
                   data-pid="{$product_list[i].product_id}" class="pd-envstr" />
            <img alt="{$product_list[i].product_name}" width="100" height="100" src="{if $config.usecdn}{$config.imagesPrefix}product_hpic/{$product_list[i].catimg}_x120{else}static/Thumbnail/?w=120&h=120&p={$config.productPicLink}{$product_list[i].catimg}{/if}" />
            <div class="cartListDesc">
                <p class="title">
                    {$product_list[i].product_name}
                </p>
                <span class="btn del" style="float:right;color:red">删除</span>
                <p class="count">
                    <span class="spec Elipsis">
                        {if $product_list[i].det_name1} 
                            [{$product_list[i].det_name1} {$product_list[i].det_name2}]
                        {else}
                            默认规格
                        {/if}
                    </span>
                    <span class="dprice prices" 
                          data-expfee="{$product_list[i].product_expfee}"
                          data-price="{$product_list[i].sale_prices}"
                          data-weight="{if $product_list[i].product_weight neq ''}{$product_list[i].product_weight}{else}0{/if}" 
                          data-count="{$product_list[i].count}">&yen; {$product_list[i].sale_prices}
                    </span>
                </p>
                <dl class="pd-dsc clearfix">
                    <dt class="productCount clearfix">
                    <a class="btn productCountMinus" data-pdid="{$product_list[i].product_id}" data-spid="{$product_list[i].spid}" href='javascript:;'></a>
                    <span class="productCountNum"><input type='tel' data-mhash="p{$product_list[i].product_id}m{$product_list[i].spid}" data-prom-limit="{$product_list[i].product_prom_limit}" value='{$product_list[i].count}' class="dcount productCountNumi" /></span>
                    <a class="btn productCountPlus" href='javascript:;'></a>
                    
             
                    </dt>
                </dl>
                </p>
            </div>
        </section>
{/section}{/strip}