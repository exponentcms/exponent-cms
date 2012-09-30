YUI.add('exptabs', function(Y) {

    Y.expTabs = function(o) {

        var history = new Y.HistoryHash(),
            tabview = new Y.TabView({
                srcNode: o.srcNode
            });
        tabview.render();

        var lazyLoad = function () {
            var islazycke = tabview.get('selection').get('content').indexOf('<!-- cke lazy -->');
            if(islazycke != 0 && islazycke >= 1) {
                Y.Global.fire("lazyload:cke");
            }
        };

        // Set the selected tab to the bookmarked history state, or to
        // the first tab if there's no bookmarked state.
        tabview.selectChild(history.get('tab') || 0);

        // Store a new history state when the user selects a tab.
        tabview.after({
            'selectionChange': function(e) {
                lazyLoad();
                history.addValue('tab', e.newVal.get('index') || null);
            },
            'render': function (){  //FIXME fwiw, this will never occur since we've already rendered the tab above!
                lazyLoad();
            }
        });

        // Listen for history changes from back/forward navigation or
        // URL changes, and update the tab selection when necessary.
        Y.on('history:change', function(e) {
            // Ignore changes we make ourselves, since we don't need
            // to update the selection state for those. We're only
            // interested in outside changes, such as the ones generated
            // when the user clicks the browser's back or forward buttons.
            if (e.src === Y.HistoryHash.SRC_HASH) {

                if (e.changed.tab) {
                    // The new state contains a different tab selection, so
                    // change the selected tab.
                    tabview.selectChild(e.changed.tab.newVal);
                } else if (e.removed.tab) {
                    // The tab selection was removed in the new state, so
                    // select the first tab by default.
                    tabview.selectChild(0);
                }
            }
        });
        return (tabview);  // let's return the tabview object so we can manipulate it in the calling function
    };

}, '0.0.1', {
    requires: ['history', 'tabview','event-custom']
});