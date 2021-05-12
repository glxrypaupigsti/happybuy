<div class="search-left-box">
    <select id="month-select">
        {foreach from=$months item=m}
            <option value="{$m.index}" {if $month eq $m.index}selected{/if}>{$m.name}</option>
        {/foreach}
    </select>
</div>