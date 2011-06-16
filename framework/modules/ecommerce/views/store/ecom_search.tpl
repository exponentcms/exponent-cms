{css unique="autocomplete-extras" link="`$asset_path`css/ecom_search.css"}

{/css}

<div class="module ecommerce ecom-search yui3-skin-sam  yui-skin-sam">
    <div id="search-autocomplete" class="control">
      {if $moduletitle}<label class="label" for="ac-input">{$moduletitle}</label>{/if}
      <input id="ac-input" type="text" class="text">
    </div>
</div>

{script unique="ecom-autocomplete" yui3mods=1}
{literal}
YUI(EXPONENT.YUI3_CONFIG).use("datasource-io","datasource-jsonschema","autocomplete", "autocomplete-highlighters", "datasource-get", function (Y) {
    
    var formatResults = function (query, results) {


        return Y.Array.map(results, function (result) {
            var result = result.raw;

            var template = (result.fileid != '') ? '<img width="30" height="30" class="srch-img" src="'+EXPONENT.PATH_RELATIVE+'thumb.php?id='+result.fileid+'&w=30&h=30&zc=1" />' : '';
            
            // title
            template += '<strong class="title">'+result.title+'</strong>';
            // model/SKU
            template += '<em class="title">SKU: '+result.model+'</em>';
            template += '<div style="clear:both;"></div>';

            return template;
        });

     }
    
    var autocomplete = Y.one('#ac-input');
    
    autocomplete.plug(Y.Plugin.AutoComplete, {
        width:"250px",
        maxResults: 10,
        resultListLocator: 'data',
        resultTextLocator: 'title', // the field to place in the input after selection
        resultFormatter: formatResults,
        source: EXPONENT.URL_FULL+'index.php?controller=store&action=search&json=1&ajax_action=1',
        requestTemplate: '&query={query}'
    });
    
    autocomplete.ac.on('select', function (e) {
        window.location = EXPONENT.URL_FULL+"store/showByTitle/title/"+e.result.raw.sef_url;
        return e.result.raw.title;
    });
    
});

{/literal}
{/script}

