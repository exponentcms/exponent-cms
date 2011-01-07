/**
 * expressinstall.as v1.0 - http://blog.deconcept.com/swfobject/
 * 
 * SWFObject is (c) 2006 Geoff Stearns and is released under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 *
 *  Updated: 12-20-2005
 *
 * Use this file to invoke the Macromedia Flash Player Express Install functionality
 * This file is intended for use with the SWFObject embed script. You can download SWFObject 
 * and this file at the following URL: http://blog.deconcept.com/swfobject/
 *
 * * SWFObject is the embed script formerly known as FlashObject. The name changed
 *    due to legal reasons.
 *
 * Usage: 
 *          var ExpressInstall = new ExpressInstall();
 *          
 *          // test to see if install is needed:
 *          if (ExpressInstall.needsUpdate) { // returns true if update is needed
 *              ExpressInstall.init(); // starts the update
 *          }
 *          // these actions can be placed on a button for extra 
 *          // functionality (see fo_tester.fla), or can be invoked at the start of the movie
 *
 *	NOTE: Your Flash movie must be at least 214px by 137px in order to use ExpressInstall.
 *        Please see http://blog.deconcept.com/swfobject/ for other notes.
 *
 */

var ExpressInstall = function():Void {
    // does the user need to update?
    this.needsUpdate = (_root.MMplayerType == undefined) ? false : true;
}

ExpressInstall.prototype = {
    init: function():Boolean {
        if (this.needsUpdate) {
            this.loadUpdater();
            return true;
        } else {
            return false;
        }
    },

    loadUpdater: function():Void {
        System.security.allowDomain("fpdownload.macromedia.com");

        // hope that nothing is at a depth of 10000000, you can change this depth if needed, but you want
        // it to be on top of your content if you have any stuff on the first frame
        this.updater = _root.createEmptyMovieClip("expressInstallHolder", 10000000);
        
        // register the callback so we know if they cancel or there is an error
        var _self = this;
        this.updater.installStatus = _self.onInstallStatus;
        this.hold = this.updater.createEmptyMovieClip("hold", 1);

        // can't use movieClipLoader because it has to work in 6.0.65
        this.updater.onEnterFrame = function():Void {
        if (typeof this.hold.startUpdate == 'function') {
                _self.initUpdater();
                this.onEnterFrame = null;
            }
        }

        var cacheBuster:Number = Math.random();
        this.hold.loadMovie("http://fpdownload.macromedia.com/pub/flashplayer/update/current/swf/autoUpdater.swf?"+ cacheBuster);
    },

    initUpdater: function():Void {
        this.hold.redirectURL = _root.MMredirectURL;
        this.hold.MMplayerType = _root.MMplayerType;
        this.hold.MMdoctitle = _root.MMdoctitle;
        this.hold.startUpdate();
    },

    onInstallStatus: function(msg):Void {
        if (msg == "Download.Complete") {
            // Installation is complete. In most cases the browser window that this SWF 
            // is hosted in will be closed by the installer or manually by the end user
        } else if (msg == "Download.Cancelled") {
            // The end user chose "NO" when prompted to install the new player
            // by default no User Interface is presented in this case. It is left up to 
            // the developer to provide an alternate experience in this case

            // feel free to change this to whatever you want, js errors are sufficient for this example
            getURL("javascript:alert('This content requires a more recent version of the Macromedia Flash Player.')");
        } else if (msg == "Download.Failed") {
            // The end user failed to download the installer due to a network failure
            // by default no User Interface is presented in this case. It is left up to 
            // the developer to provide an alternate experience in this case

            // feel free to change this to whatever you want, js errors are sufficient for this example
            getURL("javascript:alert('There was an error downloading the Flash Player update. Please try again later, or visit macromedia.com to download the latest version of the Flash plugin.')");
        }
    }
}
