{css unique="files-gallery" link="`$smarty.const.PATH_RELATIVE`framework/modules/common/assets/css/filedisplayer.css"}

{/css}

{css unique="files-gallery" link="`$smarty.const.PATH_RELATIVE`framework/modules/common/assets/css/gallery-lightbox.css"}

{/css}

{foreach from=$files item=img key=key}
<a href="{$img->url}" rel="lightbox['{$config.uniqueid}']" title="{$img->title}" class="image-link" style="margin:{$config.spacing}px;" />
    {if $key==0}
        {img file_id=$img->id w=$config.piwidth|default:$config.thumb style="margin:0;" alt="`$img->alt`" class="mainimg `$config.tclass`"}
    {else}
        {img file_id=$img->id w=$config.thumb h=$config.thumb zc=1 alt="`$img->alt`" class="`$config.tclass`"}
    {/if}
</a>
{/foreach}

{script unique="shadowbox" yui3mods=1}
{literal}
EXPONENT.YUI3_CONFIG.modules = {
           'gallery-lightbox' : {
               fullpath: EXPONENT.PATH_RELATIVE+'framework/modules/common/assets/js/gallery-lightbox.js',
               requires : ['base','node','anim','selector-css3']
           }
     }

YUI(EXPONENT.YUI3_CONFIG).use('gallery-lightbox', function(Y) {
    Y.Lightbox.init();    
});
{/literal}
{/script}
