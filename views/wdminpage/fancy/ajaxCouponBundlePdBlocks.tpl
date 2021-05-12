{foreach from=$select_bundled item=pd}
    <div class="pdBlock" data-id="{$pd.id}">
        <a class="sel"></a>
        <p class="title Elipsis">{$pd.name}</p>
        <img height="100" width="100" src="static/Thumbnail/?w=100&h=100&p={$config.img}" />
        <p class="prices Elipsis">{$pd.sale_prices}</p>
    </div>
{/foreach}