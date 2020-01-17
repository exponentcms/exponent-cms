/*
 * Copyright (c) 2004-2020 OIC Group, Inc.
 *
 * This file is part of Exponent
 *
 * Exponent is free software; you can redistribute
 * it and/or modify it under the terms of the GNU
 * General Public License as published by the Free
 * Software Foundation; either version 2 of the
 * License, or (at your option) any later version.
 *
 * GPL: http://www.gnu.org/licenses/gpl.txt
 *
 */

//FIXME convert to yui3/jquery
YUI(EXPONENT.YUI3_CONFIG).use('node','yui2-animation','yui2-container','yui2-json','yui2-yahoo-dom-event', function(Y) {
    var YAHOO = Y.YUI2;

    var toggleCart = {
            cartWrap: YAHOO.util.Dom.get('shoppingcartwrapper'),
            cart: YAHOO.util.Dom.get('cart'),
            ogCartWrapH: 0,
            clickTarget:YAHOO.util.Dom.get('expandcart'),
            expanded:false,
            init: function(){
                YAHOO.util.Event.on('expandcart', 'click', this.toggle,this,true);
//                YAHOO.util.Dom.setStyle(this.cartWrap, 'height', '0');
                YAHOO.util.Dom.setStyle(this.cartWrap, 'overflow', 'hidden');
                // var attributes = {
                //     height: { to: 0 }
                // };
                // var anim = new YAHOO.util.Anim(this.cartWrap, attributes, 0.5, YAHOO.util.Easing.easeOut);
                // anim.animate();
            },
            toggle: function(e){
                var thr = YAHOO.util.Dom.getRegion(this.cart);
                this.ogCartWrapH = Math.ceil(thr.bottom - thr.top);
                YAHOO.util.Event.stopEvent(e);
                if(!this.expanded){
                    this.expanded=true;
                    this.expand();
                }else{
                    this.expanded=false;
                    this.collapse();
                }
            },
            expand: function(){
                this.clickTarget.innerHTML = "Hide them? <span></span>";
                var attributes = {
                    height: { to: this.ogCartWrapH }
                };
                var anim = new YAHOO.util.Anim(this.cartWrap, attributes, 1, YAHOO.util.Easing.easeOut);
                anim.animate();
                anim.onComplete.subscribe(function(){
                    YAHOO.util.Dom.setStyle(this.cartWrap, 'overflow', 'visible');
                },this,true);
            },
            collapse: function(){
                this.clickTarget.innerHTML = "Show them? <span></span>";
                var attributes = {
                    height: { to: 0 }
                };
                var anim = new YAHOO.util.Anim(this.cartWrap, attributes, 1, YAHOO.util.Easing.easeOut);
                YAHOO.util.Dom.setStyle(this.cartWrap, 'overflow', 'hidden');
                anim.animate();
            }
        };

    var shippingMethod = {
        switchShippingMethodLink : YAHOO.util.Dom.get('shippingmethodoptionslink'),
        servicepicker : YAHOO.util.Dom.get('servicepicker'),
        shpmthdopswtch : YAHOO.util.Dom.getElementsByClassName('shpmthdopswtch'),
        serviceOptions : YAHOO.util.Dom.getElementsByClassName('servopt'),
        switchForm : {},
        serviceForm : YAHOO.util.Dom.get('SelShpCal'),
        udID : YAHOO.util.Dom.get('shprceup'),
        ssDisplay : YAHOO.util.Dom.get('cur-calc'),
//        ajx : new EXPONENT.AjaxEvent(),
        init : function(){

            //listen carefully...
            YAHOO.util.Event.on(this.switchShippingMethodLink, 'click', this.showswitcher, this, true);
            YAHOO.util.Event.on(this.servicepicker, 'click', this.serviceswitcher, this, true);
            YAHOO.util.Event.on(this.shpmthdopswtch, 'click', this.selectNewOption, this, true);
            YAHOO.util.Event.on(this.serviceOptions, 'click', this.selectNewService, this, true);
            YAHOO.util.Event.on(document.body, 'click', function(e){
                var targ = YAHOO.util.Event.getTarget(e);
                while (targ !== document.body) {
                    if(YAHOO.util.Dom.hasClass(targ, "shippingmethodswitch")) {
                        break;
                    } else if(targ.parentNode===document.body) {
                        this.switchPanelsmo.hide();
                        this.switchCalc.hide();
                        break;
                    } else {
                        targ = targ.parentNode;
                    }
                }

            },this,true);

            if (typeof(EXPONENT.onQuantityAdjusted)==="undefined") {
                EXPONENT.onQuantityAdjusted = new YAHOO.util.CustomEvent('Quantity Adjusted',this,false,false, YAHOO.util.CustomEvent.FLAT);
            }
            //EXPONENT.onQuantityAdjusted.subscribe(this.refreshPrices);
            EXPONENT.onQuantityAdjusted.subscribe(updateService);

            this.switchCalc = new YAHOO.widget.Panel("calculators", { width:"250px", visible:false, zindex:55, constraintoviewport:false, close:false,draggable:false } );
            this.switchCalc.render(document.body);

            this.switchCalc.beforeShowEvent.subscribe(function(e){
                this.cfg.setProperty("context",['servicepicker','tl','bl']);
            });

            this.optionPanel();

        },
        optionPanel : function(){
            if (this.switchPanelsmo) {this.switchPanelsmo.destroy()}
            this.switchPanelsmo = new YAHOO.widget.Panel("shippingmethodoptions", { width:"250px", visible:false, zindex:56, constraintoviewport:false, close:false,draggable:false } );
            this.switchPanelsmo.render(document.body);

            this.switchPanelsmo.beforeShowEvent.subscribe(function(e){
                this.cfg.setProperty("context",['shippingmethodoptionslink','tl','bl']);
            });
            this.serviceOptions = YAHOO.util.Dom.get('shpmthdopts');

            this.shpmthdopswtch = YAHOO.util.Dom.getElementsByClassName('shpmthdopswtch');
            YAHOO.util.Event.on(this.shpmthdopswtch, 'click', this.selectNewOption, this, true);
            YAHOO.util.Event.on('shippingmethodoptionslink', 'click', this.showswitcher, this, true);

        },
        showswitcher : function(e){
            YAHOO.util.Event.stopEvent(e);
            this.switchPanelsmo.show();
        },
        serviceswitcher : function(e){
            YAHOO.util.Event.stopEvent(e);
            this.switchCalc.show();
        },
        refreshPrices : function(e){

            var ej = new EXPONENT.AjaxEvent();
            ej.subscribe(function (o) {
                var opts = YAHOO.util.Dom.getElementsByClassName('shpmthdopswtch', 'a');
                var curop = YAHOO.util.Dom.get('shippingmethodoptionslink');
                var i=0;
                for (var val in o.data.methods){

                    if (YAHOO.util.Dom.hasClass(curop, o.data.methods[val].id)) {
                        curop.innerHTML = o.data.methods[val].title+" ($"+o.data.methods[val].cost+")<span></span>";
                    };

                    opts[i].innerHTML = o.data.methods[val].title+" ($"+o.data.methods[val].cost+")";
                    i++;
                }

            },this);
            ej.fetch({action:"listPrices",controller:"shippingController",json:1});
          },
          selectNewOption : function(e){
              YAHOO.util.Dom.setStyle("shipping-service", 'background', 'url('+EXPONENT.THEME_RELATIVE+'../common/skin/ecom/checkout/loader.gif) 99% 50% no-repeat');
              //checkoutDisable();
              YAHOO.util.Event.stopEvent(e);
              var targ = YAHOO.util.Event.getTarget(e);
              //EXPONENT.forms.setSelectValue("smoptions",targ.rel);
              YAHOO.util.Dom.get('option').value = targ.rel;
              this.switchPanelsmo.hide();
              //this.udID.innerHTML = "<img style='float:left;margin-right:3px;' src=\""+EXPONENT.THEME_RELATIVE+"images/loader2.gif\">Updating<span></span>";
              this.switchForm = YAHOO.util.Dom.get('shpmthdopts');

              var aj = new EXPONENT.AjaxEvent();

              aj.subscribe(function (o) {

                  YAHOO.util.Dom.get('shprceup').innerHTML = o.data.title + "<br /> ($" + o.data.cost + ")" ;
                  YAHOO.util.Dom.removeClass(YAHOO.util.Dom.getElementsByClassName('current', 'a', "shippingmethodoptions"), 'current');
                  YAHOO.util.Dom.addClass(targ, 'current');
                  YAHOO.util.Dom.setStyle("shipping-service", 'background', 'none');
                  checkoutEnable();
              },this);

              aj.fetch({form:this.switchForm,json:1});
          },
          selectNewService : function(e){
              checkoutDisable();
              YAHOO.util.Dom.setStyle("shipping-service", 'background', 'url('+EXPONENT.THEME_RELATIVE+'../common/skin/ecom/checkout/loader.gif) 99% 50% no-repeat');
              YAHOO.util.Event.stopEvent(e);
              var targ = YAHOO.util.Event.getTarget(e);

              var scid = YAHOO.util.Dom.get('shipcalc_id').value = targ.rel;

              this.switchCalc.hide();
              //this.udID.innerHTML = "<img style='float:left;margin-right:3px;' src=\""+EXPONENT.THEME_RELATIVE+"images/loader2.gif\">Updating<span></span>";
              var ajx = new EXPONENT.AjaxEvent();
              ajx.subscribe(function (o) {

                YAHOO.util.Dom.get('cur-calc').innerHTML = o.data.calculator.title;
                updateService();
              },this);
              ajx.fetch({form:this.serviceForm,json:1});
          }
    }

    var giftmessage = {
        init : function () {
            var msgpops = YAHOO.util.Dom.getElementsByClassName('ordermessage', 'a');
            YAHOO.util.Event.on(msgpops, 'click', this.popmsgs, this, true);
            this.msgpanel = new YAHOO.widget.Panel("ordermessageform", { width:"475px", height:"420px", modal:true, zIndex:"54",fixedcenter:true, visible:false, constraintoviewport:true, close:true, draggable:true } );
            this.msgpanel.render(document.body);
            this.oForm = YAHOO.util.Dom.get("omform");
            YAHOO.util.Event.on(this.oForm, 'submit', this.handleSubmit, this, true);

            //this.nosave = YAHOO.util.Dom.get('nosave');
            this.msgto = YAHOO.util.Dom.get('shpmessageto');
            this.msgfrom = YAHOO.util.Dom.get('shpmessagefrom');
            this.msgmsg = YAHOO.util.Dom.get('shpmessage');

            //this.nosave.value = 0;
            this.msgpanel.hideEvent.subscribe(function(e){
                //this.nosave.value = 0;
            },this,true);

        },
        popmsgs : function (e) {
            this.msgto.value = "Loading...";
            this.msgfrom.value = "Loading...";
            this.msgmsg.value = "Loading...";
            this.msgto.disabled = true;
            this.msgfrom.disabled = true;
            this.msgmsg.disabled = true;
            this.nosave.value = 1;
            var aj = new EXPONENT.AjaxEvent();
            var targ = YAHOO.util.Event.getTarget(e);
            YAHOO.util.Dom.get('shippingmessageid').value = targ.rel;

            aj.subscribe(function(o){
                this.msgto.disabled = false;
                this.msgfrom.disabled = false;
                this.msgmsg.disabled = false;
                this.msgto.value = o.data.to;
                this.msgfrom.value = o.data.from;
                this.msgmsg.value = o.data.message;
            },this);
            aj.fetch({form:this.oForm,json:1});

            YAHOO.util.Event.stopEvent(e);
            this.msgpanel.show();
        },
        handleSubmit : function (e) {
            this.nosave.value = 0;

            YAHOO.util.Event.stopEvent(e);
            var ajx = new EXPONENT.AjaxEvent();

            ajx.subscribe(function(o){

            },this);
            ajx.fetch({form:this.oForm,json:1});
            this.msgpanel.hide();
        }
    }

    var updateService = function (elem){
        //Grab shipping method options
        YAHOO.util.Dom.setStyle("shipping-service", 'background', 'url('+EXPONENT.THEME_RELATIVE+'../common/skin/ecom/checkout/loader.gif) 99% 50% no-repeat');
        var ajx = new EXPONENT.AjaxEvent();

        ajx.subscribe(function (o) {
            YAHOO.util.Dom.get('shipping-services').innerHTML = o;
            shippingMethod.optionPanel();
            checkoutEnable();
        });

        ajx.fetch({action:"renderOptions",controller:"shipping"});
    }

    function unhide() {
        var hiding = YAHOO.util.Dom.getElementsByClassName('hide');
        YAHOO.util.Dom.removeClass(hiding,'hide');
    }

    function hide() {
        var tohide = YAHOO.util.Dom.getElementsByClassName('tohide');
        YAHOO.util.Dom.setStyle(tohide, 'display', 'none');
    }

    //initialize things
    function inititCheckout(){
        hide();
        shippingMethod.init();
//        addressManager.init();
//        creditCard.init();
        giftmessage.init();
        toggleCart.init();
    }
    Y.on('domready',inititCheckout);


    // YUI 2 ajax helper method. This is much easier in YUI 3. Should also migrate.
	EXPONENT.AjaxEvent = function() {
	    var obj;
	    var data = "";

	    var gatherURLInfo = function (obj){
	        json = obj.json ? "&json=1" : "";
	        if (obj.form){
	            data = YAHOO.util.Connect.setForm(obj.form);
	            //slap a date in there so IE doesn't cache
	            var dt = new Date().valueOf();
	            var sUri = EXPONENT.PATH_RELATIVE + "index.php?ajax_action=1" + json + "&yaetime=" + dt;
	            return sUri;
	        } else if (!obj.action || (!obj.controller && !obj.module)) {
	            alert("If you don't pass the ID of a form, you need to specify both a module/controller AND a corresponding action.");
	        } else {
	            //slap a date in there so IE doesn't cache
	            var dt = new Date().valueOf();
	            var modcontrol = (obj.controller) ? "&controller="+obj.controller : "&module="+obj.module;
	            var sUri = EXPONENT.PATH_RELATIVE + "index.php?ajax_action=1" + modcontrol + "&action=" + obj.action + json + "&yaetime=" + dt + obj.params;
	            return sUri;
	        }
	    }

	    return {
	        json:0,
	        subscribe: function(fn, oScope) {
	            if (!this.oEvent) {
	                this.oEvent = new YAHOO.util.CustomEvent("ajaxevent", this, false, YAHOO.util.CustomEvent.FLAT);
	            }
	            if (oScope) {
	                this.oEvent.subscribe(fn, oScope, true);
	            } else {
	                this.oEvent.subscribe(fn);
	            }
	        },
	        fetch: function(obj) {
	            if (typeof obj == "undefined" || !obj){
	                alert('{/literal}{"EXPONENT.ajax requires a single object parameter."|gettext}{literal}');
	                return false;
	            } else {
	                if (typeof(obj.json)!=="undefined"){
	                    this.json = obj.json;
	                } else {
	                    this.json = false;
	                }
	                var sUri = gatherURLInfo(obj);
	                //Y.log(sUri);
	                YAHOO.util.Connect.asyncRequest("POST", sUri, {
	                success: function (o) {
	                    //if we're just sending a request and not needing to do
	                    //anything on the completion, we can skip firing the custom event
	                    if (typeof(this.oEvent)!=="undefined") {

	                        //otherwise, we check if we've got JSON coming back to parse
	                        if (this.json!==false) {
	                            //if so, parse it
	                            var oParse = YAHOO.lang.JSON.parse(o.responseText);
	                        } else {
	                            //if not, it's probably HTML we're going to update a view with
	                            var oParse = o.responseText;
	                        }
	                        //fire off the custom event to do some more stuff with
	                        this.oEvent.fire(oParse);
	                    }
	                },
	                    scope: this
	                },obj.data);
	            }
	        }
	    }
	}

});
