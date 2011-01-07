YUI(EXPONENT.YUI3_CONFIG).use('node','event', function(Y) {

    var showMenu = function(e) {
        e.halt();
        hideMenus();
        e.target.addClass('current-trigger').next().setStyles({'display':'block','zIndex':50});
    }
    
    var hideMenus = function(e) {
        Y.all(".container-chrome .container-menu").setStyle('display','none').each(function(n){
            n.previous().removeClass('current-trigger');  
        });
    }

    Y.on('domready',function(){
        var chromes = Y.all(".container-chrome");
        chromes.each(function(n,k){
            n.setStyle('zIndex',(chromes.size()-k)+10);
            //n.one(".exp-dropmenu").setStyle('zIndex',chromes.size()+1);
        });
                
        // if (Y.UA.ie > 0) {
        //     Y.delegate('click', showMenu, document.body, '.container-chrome .trigger');
        //     Y.on('click', hideMenus, document.body);            
        // } else {
            Y.on('click', hideMenus, document.body);
            Y.delegate('click', showMenu, document.body, '.container-chrome .trigger');
            
        // };

    });
});
