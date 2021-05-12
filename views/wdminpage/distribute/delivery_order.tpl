<div style="width:550px;">
    
    <input type="hidden" id='distribute_id' value="{$distribute_id}" />
    <input type="hidden" id='status' value="{$status}" />
   
    <div class="distribute-title clearfix">
        <p>选择配送信息</p>
    </div>
    
    <div class="fv2Field-query clearfix">
        <div class="fv2Left">配送方式:</div>
        <div class="fv2Right">
        	<select id="expressCompany" style="width:200px;">
		        {foreach from=$exps item=i}
		            <option value="{$i.key}">{$i.value}</option>
		        {/foreach}
		    </select>
        </div>
    </div>
    
    <div class="fv2Field-query clearfix">
        <div class="fv2Left">配送人员:</div>
        <div class="fv2Right">
        	<select id="couriers" style="width:200px;">
		        {foreach from=$couries  item=i}
		             <option value="{$i.key}">{$i.value}</option>
		        {/foreach}
		    </select>
        </div>
    </div>
    

    <div style="text-align: center;margin-top: 10px;">
        <a class="wd-btn primary" href='javascript:;' id="ok">确认</a>
    </div>
</div>