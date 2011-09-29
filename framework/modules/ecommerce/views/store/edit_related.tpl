<h2>Related Products</h2>                    
{control type="hidden" name="related_tab_loaded" value=1} 
{capture assign="callbacks"}
{literal}


// the text box for the title
var tagInput = Y.one('#related_items');

// the UL to append to
var tagUL = Y.one('#relatedItemsList');

// the Add Link
var tagAddToList = Y.one('#addToRelProdList');


var onRequestData = function( oSelf , sQuery , oRequest) {
    tagInput.setStyles({'border':'1px solid green','background':'#fff url('+EXPONENT.PATH_RELATIVE+'subsystems/forms/controls/assets/autocomplete/loader.gif) no-repeat 100% 50%'});
}

var onRGetDataBack = function( oSelf , sQuery , oRequest) {
    tagInput.setStyles({'border':'1px solid #000','backgroundImage':'none'});
}

var appendToList = function(e,args) {
    tagUL.appendChild(createHTML(args[2]));
    return true;
}

var removeLI = function(e) {
    var t = e.target;
    if (t.test('a')) tagUL.removeChild(t.get('parentNode'));
}

var createHTML = function(val) {
    var li = '<li>'+val.title+' - <a href="javascript:{}">X</a><br />';
        li += 'Model #: '+val.model+'';
        li += '<br /><input type="checkbox" name="relateBothWays['+val.id+']" value="'+val.id+'"> Relate both ways';
        li += '<input type=hidden name="relatedProducts['+val.id+']" value="'+val.id+'" /></li>';
    var newLI = Y.Node.create(li);
    return newLI;
}

//tagAddToList.on('click',appendToList);
tagUL.on('click',removeLI);

// makes formatResult work mo betta
oAC.resultTypeList = false;

// when we start typing...?
oAC.dataRequestEvent.subscribe(onRequestData);
oAC.dataReturnEvent.subscribe(onRGetDataBack);

// format the results coming back in from the query
oAC.formatResult = function(oResultData, sQuery, sResultMatch) {
    return oResultData.title;
}

// what should happen when the user selects an item?
oAC.itemSelectEvent.subscribe(appendToList);


{/literal}
{/capture}

{control type="autocomplete" controller="store" action="search" name="related_items" label="Related Products" value="Search Title or SKU" schema="title,id,sef_url,expFile,model" searchmodel="product" searchoncol="title,model" jsinject=$callbacks}

<ul id="relatedItemsList">
    {foreach from=$record->crosssellItem item=prod name=prods}
        <li>
            {$prod->title|strip_tags} - <a href="javascript:{ldelim}{rdelim}">X</a><br />
            Model #: {$prod->model|strip_tags}
            <input type=hidden name="relatedProducts[{$prod->id}]" value="{$prod->id}" />
        </li>                   
    {/foreach}
</ul>
