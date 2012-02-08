<div class="item">
    {$filetype=$file->expFile.downloadable[0]->filename|regex_replace:"/^.*\.([^.]+)$/D":"$1"}
    {if $file->expFile.preview[0] != "" && $config.show_icon}
        {img class="preview-img" file_id=$file->expFile.preview[0]->id square=150}
    {/if}
    {if $config.quick_download}
        <h3><a class="download" href="{link action=downloadfile fileid=$file->id}">{$file->title}</a></h3>
    {else}
        {if $file->title}<h3><a {if !$config.usebody}class="readmore"{/if} href="{link action=show title=$file->sef_url}">{$file->title}</a></h3>{/if}
    {/if}
    {if $config.show_info}
        <span class="label size">{'File Size'}:</span>
        <span class="value">{$file->expFile.downloadable[0]->filesize|kilobytes}{'kb'|gettext}</span>
        &nbsp;|&nbsp;
        <span class="label downloads"># {'Downloads'|gettext}:</span>
        <span class="value">{$file->downloads}</span>
        {if $file->expTag|@count>0}
            &nbsp;|&nbsp;
            <span class="tag">
                {'Tags'|gettext}:
                {foreach from=$file->expTag item=tag name=tags}
                    <a href="{link action=showall_by_tags tag=$tag->sef_url}">{$tag->title}</a>{if $smarty.foreach.tags.last != 1},{/if}
                {/foreach}
            </span>
        {/if}
    {/if}
    {permissions}
        <div class="item-actions">
            {if $permissions.edit == 1}
                {icon action=edit record=$file title="Edit this file"|gettext}
            {/if}
            {if $permissions.delete == 1}
                {icon action=delete record=$file title="Delete this file"|gettext onclick="return confirm('"|cat:("Are you sure you want to delete this file?"|gettext)|cat:"');"}
            {/if}
        </div>
    {/permissions}
    {if $config.usebody!=2}
        <div class="bodycopy">
            {if $config.usebody==1}
                <p>{$file->body|summarize:"html":"paralinks"}</p>
            {else}
                {$file->body}
            {/if}
        </div>
    {/if}
    {if $config.usebody==1 || $config.usebody==2}
        <a class="readmore" href="{link action=show title=$file->sef_url}">{'Read more'|gettext}</a>
        &nbsp;&nbsp;
    {/if}
    {if !$config.quick_download}
        <a class="download" href="{link action=downloadfile fileid=$file->id}">{'Download'|gettext}</a>
    {/if}
    {if $config.show_player && ($filetype == "mp3" || $filetype == "flv" || $filetype == "f4v")}
        <a href="{$file->expFile.downloadable[0]->url}" style="display:block;width:360px;height:30px;" class="filedownloads-media"></a>
    {/if}
    {clear}
    {permissions}
        <div class="module-actions">
            {if $permissions.create == 1}
                {icon class=add action=edit title="Add a File Here" text="Add a File"|gettext}
            {/if}
        </div>
    {/permissions}
    {clear}
</div>