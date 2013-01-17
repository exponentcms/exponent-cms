/**
 * Creates a light box with the help of some of the YUI2 library.
 *
 * YUI2 does provide Panel, but I find some of the default behavior of
 * Panel to be undesirable for a simple lightbox.
 *
 * YUI Requirements:
 *
 *   Dom
 *   Event
 *   Connection Core
 *   JSON
 *   Selector
 *   Animation (if animate set to tru)
 *
 * Copyright (c) 2011, Brian Moon
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or
 * without modification, are permitted provided that the following
 * conditions are met:
 *
 *  * Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 *  * Redistributions in binary form must reproduce the above
 *    copyright notice, this list of conditions and the following
 *    disclaimer in the documentation and/or other materials
 *    provided with the distribution.
 *  * Neither the name of Brian Moon nor the names of its
 *    contributors may be used to endorse or promote products
 *    derived from this software without specific prior written
 *    permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND
 * CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES,
 * INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS
 * BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 * EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED
 * TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
 * ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
 * OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY
 * OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 */

//FIXME convert to yui3
YUI(EXPONENT.YUI3_CONFIG).use('node', 'yui2-container', 'yui2-yahoo-dom-event', 'yui2-dom', 'yui2-event', 'yui2-connectioncore', 'yui2-json', 'yui2-selector', 'yui2-animation', function (Y) {

    var YAHOO = Y.YUI2;
    var Y = YAHOO.util;
    var YJSON = YAHOO.lang.JSON;

    EXPONENT.Lightbox = function (cfg) {

        /**
         * Holds the configuration
         */
        this.cfg = cfg;

        /**
         * Has the lightbox been shown?
         */
        this.init = false;

        /**
         * The dn-lightbox element
         */
        this.element = null;

        /**
         * The bd element that has the dynamic contents
         */
        this.lbBody = null;

        /**
         * Key listener for handling escape
         */
        this.kl = null;

        /**
         * Custom event to be fired on close
         */
        this.onClose = new YAHOO.util.CustomEvent("onClose");

        /**
         * Shows the lightbox
         */
        this.show = function (e) {

            Y.Event.preventDefault(e);

            if (!this.init) {

                /**
                 * On first load, we initialize some things
                 */

                if (!this.element) {

                    /**
                     * You can pass in an element name or one will be created
                     */

                    if (this.cfg.contentElement) {
                        this.element = Y.Dom.get(this.cfg.contentElement);
                    } else {
                        this.element = document.createElement("DIV");
                        this.element.className = "dn-lightbox";
                        this.element.innerHTML = '<div class="lb-bg"></div><div class="lb-bx"><a class="lb-close" href="#"><span class="txt">Close</span></a><div class="lb-bd"></div></div>';
                        document.body.appendChild(this.element);
                    }
                }

                /**
                 * You can set a max width
                 */
                if (this.cfg.maxWidth) {
                    innerBox = YAHOO.util.Selector.query(".lb-bx", this.element, true);
                    innerBox.style.maxWidth = this.cfg.maxWidth + "px";
                }

                /**
                 * We find the bd element of the lightbox. this holds the content
                 */
                this.lbBody = YAHOO.util.Selector.query(".lb-bd", this.element, true);

                /**
                 * See if there is a close box defined.
                 */
                closeEl = YAHOO.util.Selector.query(".lb-close", this.element, true);
                if (closeEl) {
                    Y.Event.addListener(
                        closeEl,
                        "click",
                        this.hide,
                        this,
                        true
                    );
                }

                /**
                 * If a content string was passed in, set up the body with the value
                 */
                if (this.cfg.contentString) {
                    this.lbBody.innerHTML = this.cfg.contentString;
                }

                /**
                 * Add the key listener for handling ESC
                 */
                this.kl = new Y.KeyListener(
                    document,
                    {
                        keys: 27
                    },
                    {
                        fn: this.hide,
                        scope: this,
                        correctScope: true
                    },
                    "keyup" // keyup for Safari
                );

            }

            /**
             * Now we check if we have some other method for filling in
             * the body.
             */

            /**
             * A callback or url can be used to fill in the content
             */
            if (this.cfg.contentCallback) {
                this.lbBody.innerHTML = this.cfg.contentCallback();
                this.makeVisible();
            } else if (this.cfg.contentURL) {
                /**
                 * If we have a contentURL,make an async call to the URL for content
                 */
                Y.Connect.asyncRequest('GET',
                    this.cfg.contentURL,
                    {
                        success: this.handleJSONResponse,
                        scope: this
                    }
                );
            } else {
                /**
                 * Otherwise, just show the lightbox
                 */
                this.makeVisible();
            }

        }

        /**
         * Handles AJAX responses for dyanmic light boxes
         */
        this.handleJSONResponse = function (o) {
            if (o.responseText !== undefined) {

                try {
                    var data = YJSON.parse(o.responseText);

                    /**
                     * The return data should be json string with a success value
                     * and a content value
                     */

                    if (data["success"]) {
                        this.lbBody.innerHTML = data["content"];
                        this.makeVisible();
                    }
                } catch (x) {  // not json data
                    if (o.status == 200) {
                        this.lbBody.innerHTML = o.responseText;
                        this.makeVisible();
                    }
                }
            }
        }

        /**
         * Makes the lightbox visible
         */
        this.makeVisible = function () {

            /**
             * In order to do math to position things, we have to have
             * display set to block for the lightbox. But, we hide it first
             */
            this.element.style.visibility = "hidden";
            this.element.style.display = "block";

            /**
             * We need to position the inner box, so get it from the dom
             */
            innerBox = YAHOO.util.Selector.query(".lb-bx", this.element, true);

            var bodyRegion = Y.Dom.getRegion(innerBox);

            newLeftMargin = (bodyRegion.width / 2) * -1;
            newTopMargin = (bodyRegion.height / 2) * -1;

            innerBox.style.marginLeft = newLeftMargin + "px";
            innerBox.style.marginTop = newTopMargin + "px";

            /**
             * If we are animating, go ahead and set opacity to 0
             */
            if (this.cfg.animate) {
                Y.Dom.setStyle(this.lbBody, "opacity", 0);
            }

            /**
             * Make the lightbox visible
             */
            this.element.style.visibility = "visible";

            /**
             * if we are animating, run the animation
             */
            if (this.cfg.animate) {
                anim = new YAHOO.util.Anim(this.lbBody,
                    { opacity: { to: 1 } }, .25);
                anim.animate();
            }

            /**
             * Enable our key listener
             */
            this.kl.enable();

        }

        /**
         * Hides the lightbox
         */
        this.hide = function (e) {
            Y.Event.preventDefault(e);

            /**
             * disable the key listener immediately
             */
            this.kl.disable();

            /**
             * If we are animating, we do things a bit harder
             */
            if (this.cfg.animate) {
                /**
                 * find the box
                 */
                innerBox = YAHOO.util.Selector.query(".lb-bx", this.element, true);
                anim = new YAHOO.util.Anim(innerBox,
                    { opacity: { to: 0 } }, .1);
                anim.onComplete.subscribe(function () {
                        /**
                         * after the animation is done, hide the light box first
                         * Then reset the opacity on the inner box before
                         * completely removing the light box from display
                         */
                        this.element.style.visibility = "hidden";
                        Y.Dom.setStyle(
                            YAHOO.util.Selector.query(".lb-bx", this.element, true),
                            "opacity", 1);
                        this.element.style.display = "none";
                        this.onClose.fire();
                    },
                    this,
                    true
                );

                anim.animate();

            } else {
                /**
                 * If not animating, just remove it from the display
                 */
                this.element.style.display = "none";
            }
        }

    }

});
