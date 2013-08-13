YUI.add('exptabs', function(Y) {

    Y.expTabs = function(o) {
        // Y.log("expTabs");
        // set up history
        var history = new Y.HistoryHash();
        // Tab Navigation ul
        var tabs = Y.all(o.srcNode+' ul.yui-nav li a');
        //Tab Content containers
        var tabContent = Y.all(o.srcNode+' div.yui-content > div');

        // Set up the tab navigations with the right classes to grab the existing styles
        tabs.each(function(k){
            k.addClass('yui3-tab-label yui3-tab-content').get('parentNode').addClass('yui3-tab yui3-widget');
        });

        // Hide all tab content containers
        tabContent.addClass('hide');

        // tab click callback
        // Reveals the tab content, sets the current tab in history
        // Fires of a check fo CKLazyload (which may not be needed anymore)
        var openTab = function(e) {
            e.halt();
            e.container.all('li').removeClass('yui3-tab-selected');
            e.currentTarget.get('parentNode').addClass('yui3-tab-selected');
            tabContent.addClass('hide');
            Y.one(e.currentTarget.getAttribute('href')).removeClass('hide');
            lazyLoad(Y.one(e.currentTarget.getAttribute('href')));
            history.addValue('tab', tabs.indexOf(e.currentTarget) || null);
            Y.Global.fire("exptab:switch",e);
        };

        // Lazyload for CKE
        var lazyLoad = function (tab) {
            var islazycke = tab.get('innerHTML').indexOf('<!-- cke lazy -->');
            if(islazycke != 0 && islazycke >= 1) {
                Y.Global.fire("lazyload:cke");
            }
        };

        // Listener for tab clicks
        if (Y.one(o.srcNode+' ul.yui-nav') != null) {
            Y.one(o.srcNode+' ul.yui-nav').delegate('click', openTab, 'a');
        }

        // Clicks the first tab if we don't have a history for this tabset
        if (tabs.item(history.get('tab') || 0) != null) {
            tabs.item(history.get('tab') || 0).simulate('click');
        }

        // Watches the URL for hash changes
        Y.on('history:change', function(e) {
            if (e.src === Y.HistoryHash.SRC_HASH) {
                if (e.changed.tab) {
                    tabs.item(e.changed.tab.newVal).simulate('click');
                } else if (e.removed.tab) {
                    tabs.item(0).simulate('click');
                }
            }
        });

        return {
            history:history,
            tabs:tabs
        };
    };

}, '0.0.1', {
    requires: ['history','node','tabview','event-custom']
});