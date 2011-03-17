{uniqueid assign="id"}

{css unique="`id`" link="`$smarty.const.YUI3_PATH`tabview/assets/skins/sam/tabview.css"}

{/css}

<div class="module text showall yui3-skin-sam">
    {if $moduletitle}<h1>{$moduletitle}</h1>{/if}

    {permissions}
        <div class="item-actions">
            {if $permissions.create == 1}
                {icon class="add" action=edit rank=1 title="Add Tab" text="Add Tab"}
            {/if}
            {if $permissions.edit == 1}
                {ddrerank items=$items model="text" label="Text Items"|gettext}
            {/if}
        </div>
    {/permissions}
    <div id="{$id}">
        <ul>
            {foreach from=$items item=tab name=tabs}
                <li><a href="#tab{$smarty.foreach.items.iteration}">{$tab->title}</a></li>
            {/foreach}
        </ul>
        <div>
            {foreach from=$items item=text name=items}
                <div id="tab{$smarty.foreach.items.iteration}">
                    {permissions}
                        {if $permissions.edit == 1}
                            {icon action=edit class="edit" id=$text->id title="Edit this `$modelname`"}
                        {/if}
                        {if $permissions.delete == 1}
                            {icon action=delete id=$text->id title="Delete this Text Item" onclick="return confirm('Are you sure you want to delete this `$modelname`?');"}
                        {/if}
                    {/permissions}
                    <div class="bodycopy">
                        {chain controller="snippet" action="showall" source="snippet`$text->id`" scope="sectional"}
                        {$text->body}
                        {permissions}
                            {if $permissions.create == 1}
                                {icon class=add action=edit rank=`$text->rank+1` title="Add tab" text="Add another tab after this one"}
                            {/if}
                        {/permissions}
                    </div>
                </div>
            {/foreach}
        </div>
    </div>
</div>

{script unique="`$id`" yui3mods="1"}
{literal}
YUI(EXPONENT.YUI3_CONFIG).use('tabview', function(Y) {
    var tabview = new Y.TabView({srcNode:'#{/literal}{$id}{literal}'});
    tabview.render();
});
{/literal}
{/script}

