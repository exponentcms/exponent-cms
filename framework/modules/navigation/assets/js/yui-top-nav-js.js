var buildmenu = function (elem,cfg) {

    var oMenuBar = new YAHOO.widget.MenuBar(elem, { 
                                            
                                                constraintoviewport:false,
                                                postion:"dynamic",
                                                visible:true,
                                                zIndex:250,
                                                autosubmenudisplay: true, 
                                                hidedelay: 750, 
                                                lazyload: true });

    var aSubmenuData = cfg.menu;
    oMenuBar.subscribe("beforeRender", function () {

        if (this.getRoot() == this) {
            for (var i=0; i<=this.getItems().length; i++){
                var j=i;
                if (aSubmenuData[j].itemdata.length>0){
                    this.getItem(i).cfg.setProperty("submenu", aSubmenuData[j]);
                    var test = this.getItemGroups();
                    //console.debug(test);
                    //this.setItemGroupTitle("Yahoo! PIM", aSubmenuData[j].itemdata);
                }
            }
        }
    });
    ////console.debug(oMenuBar.getItems());
    
    oMenuBar.render();         

};
