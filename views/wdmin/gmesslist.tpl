<div style="width:700px">
    {strip}
        {section name=ls loop=$list}
            <div class="gmessItem" id="gmessItem-{$list[ls].id}">
                <div id="js_appmsg_preview" class="appmsg_content" style="{if $smarty.section.ls.index % 3 == 0}margin-left:15px;{/if}">
                    <div id="appmsgItem1" class="js_appmsg_item">
                        <h4 class="appmsg_title"><a href="{$list[ls].href}" target="_blank">{$list[ls].title}</a></h4>
                        <div class="appmsg_info">
                            <em class="appmsg_date"></em>
                        </div>
                        <div class="appmsg_thumb_wrp" id="fileDragArea" style="height: auto;">
                            <img style="height:116px;" class="js_appmsg_thumb appmsg_thumb" src="{$list[ls].catimg}" id="appmsimg-preview">
                        </div>
                        <p class="appmsg_desc" style="height: 40px;overflow: hidden;">{$list[ls].desc}</p>
                        <div class="appmsg-bar">
                            <a class="bbtn gsend-chosbtn" data-msgid="{$list[ls].id}" href="javascript:;" onclick="bindMsg({$list[ls].id})" style="width:100%;">选择</a>
                        </div>
                    </div>
                </div>
            </div>
        {/section}
    {/strip}
</div>