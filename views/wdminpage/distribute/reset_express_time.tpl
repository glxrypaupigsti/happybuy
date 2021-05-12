<div style="width:550px;">
    <div class="distribute-title clearfix">
        <p>请重新输入配送时间</p>
    </div>
    <input type="hidden" id='distribute_id' value="{$distribute_id}"/>
    <input type="hidden" id='status' value="{$status}"/>
    <div class="fv2Field-query clearfix">
        <div class="fv2Left">配送日期:</div>
        <div class="fv2Right">
        	<input type="text" class="gs-input-query layer-date" id="exp_time1" name="exp_time1" data-column="11" placeholder="有效期结束时间" autofocus/> (格式为2015-09-02)
        </div>
    </div>
    
    <div class="fv2Field-query clearfix">
        <div class="fv2Left">配送时间段:</div>
        <div class="fv2Right">
        	<select id="exp_time2" name="exptime2" style="width:200px;">
		    	<option value="14:00-15:00">14:00-15:00</option>
		    	<option value="15:00-16:00">15:00-16:00</option>
		    	<option value="16:00-17:00">16:00-17:00</option>
		    	<option value="17:00-18:00">17:00-18:00</option>
		    </select>
        </div>
    </div>
    
    <div style="text-align: center;margin-top: 10px;">
        <a class="wd-btn primary" href='javascript:;' id="ok">确认</a>
        <a class="wd-btn" href='javascript:;' id="close">取消</a>
    </div>
</div>

<script type="text/javascript">
	laydate.skin('molv')
	laydate({
	   elem: '#exp_time1'
	})
</script>