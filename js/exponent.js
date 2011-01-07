YAHOO.namespace("exp");
YAHOO.exp = function () {

	//private shorthand references to YUI utilities:
	var Event = YAHOO.util.Event,
		Connection = YAHOO.util.Connect,
		Dom = YAHOO.util.Dom,
		Element = YAHOO.util.Element;

	//private method:
	var getListItems = function () {
		return aListItems;
	};
		
	var buildmenus = function () {
	var sideMenu = new YAHOO.widget.Menu("verticalbreadcrumbflyoutmenu",{
																		position: "static", 
																		zindex:50,
																		showdelay: 0,
																		monitorresize:false,
																		hidedelay: 750, 
																		lazyload: true 
																	//	effect: { 
																	 //		effect: YAHOO.widget.ContainerEffect.FADE,
																	//		duration: 0.35
																	//	}
																		}
										);
		sideMenu.render();

	}
	
	var expandablenav = function () {
		var expanders = Dom.getElementsByClassName('twisty','img');
		for (i=0; i<=expanders.length; i++){
		//	alert(Dom.get(expanders[i].getAttribute("id")+"gc"))
			if (Dom.get(expanders[i].getAttribute("id")+"gc")!=null){
				Event.on(expanders[i],"click",expand,expanders[i]);
			}else{
				Dom.setStyle(expanders[i],"display","none");
			}
		}
		function expand (e,el){
			Dom.setStyle(Dom.getAncestorByTagName(el,'li'),"background-image","url(themes/peoriatheme/images/childbgexp.jpg)");
			Dom.setStyle(Dom.getNextSibling(el,'a'),"color","#000000");
			el.src = "themes/peoriatheme/images/contract.gif";
			Event.removeListener(el,"click",null);
			var gc = Dom.get(el.getAttribute("id")+"gc");
			Dom.addClass(gc,"show");
			eh = (Dom.getChildren(gc).length)*20;
			var attributes = {
			   height: { from: 0, to: eh }
			};
			var myAnim = new YAHOO.util.Anim(gc, attributes,.5,YAHOO.util.Easing.easeBoth);
			myAnim.animate();
			Event.on(el,"click",contract,el);		
		}

		function contract (e,el){	
			Dom.setStyle(Dom.getAncestorByTagName(el,'li'),"background-image","url(themes/peoriatheme/images/childbg.jpg)");
			Dom.setStyle(Dom.getNextSibling(el,'a'),"color","#ffffff");
			el.src = "themes/peoriatheme/images/expand.gif";
			Event.removeListener(el,"click",null);
			var gc = Dom.get(el.getAttribute("id")+"gc");

			eh = YAHOO.util.Region.getRegion(gc).bottom - YAHOO.util.Region.getRegion(gc).top;
			var attributes = {
			   height: { from: eh, to: 0 }
			};

			var myAnimIn = new YAHOO.util.Anim(gc, attributes,.5,YAHOO.util.Easing.easeBoth);
			myAnimIn.animate();//Dom.setStyle(gc,"margin-top","0px")
			myAnimIn.onComplete.subscribe(function(){
				Dom.removeClass(gc,"show");
			});
			Event.on(el,"click",expand,el);		
		}

	}
	
	var ajaxloded = new YAHOO.util.CustomEvent("Ajax Loaded");
	return  {
		flowredirect: function(){
			window.location = eXp.URL_FULL+"index.php?ajax_action=1&action=ajax_flow_redirect&module=common";	
		},
		sessionget: function(variable){
			var sUrl = "index.php?ajax_action=1&action=ajax_session_get&module=common&var="+variable;
			var connect = function(){
				YAHOO.util.Connect.asyncRequest('GET', sUrl, {
		    		success : function(o){
					YAHOO.exp.session = YAHOO.lang.JSON.parse(o.responseText);
				},
				failure : function(o){
			        	//console.debug('Error handling request: '+o.responseText);
				},
					timeout : 5000
			    });
			}()
			
		},
		sessionset: function(variable,value){
			var sUrl = "index.php?ajax_action=1&action=ajax_session_set&module=common&var="+variable+"&value="+value;
			var connect = function(){
				YAHOO.util.Connect.asyncRequest('GET', sUrl, {
				success : function(o){
					//YAHOO.exp.ajaxcontent.session = o.responseText;
				},
				failure : function(o){
			        //console.debug('Error handling request: '+o.responseText);
				},
					timeout : 5000
			    });
			}()
			
		},
		ajaxcontent: function(module, action, id, source){
			var sUrl = "index.php?ajax_action=1&action="+action+"&module="+module+"&id="+id+"&src="+source;
			var response;
			var connect = function(){
				YAHOO.util.Connect.asyncRequest('GET', sUrl, {
		    		success : function(o){
					YAHOO.exp.ajaxcontent.response = o.responseText;
					ajaxloded.fire();
				},
				failure : function(o){
			        	//console.debug('Error handling request: '+o.responseText);
				},
					timeout : 5000
			    });
			}()
			
		},
		registerpanel: function(width,height){
			if (height==undefined) {height=""}
			if (width==undefined) {width="300px"}
			YAHOO.exp.panel = new YAHOO.widget.Panel(Dom.generateId(), {zIndex:5000,fixedcenter:true,height:height,width:width,constraintoviewport: true, modal:false,underlay:"shadow",close:true,visible:false,draggable:true} );
			YAHOO.exp.panel.setHeader('Header');
			YAHOO.exp.panel.setBody("Body");
			function setajaxbody(){
				//alert(YAHOO.exp.ajaxcontent.response);
				////console.debug(YAHOO.exp.ajaxcontent.response);
				YAHOO.exp.panel.setBody(YAHOO.exp.ajaxcontent.response);
			}
			ajaxloded.subscribe(setajaxbody);
			YAHOO.exp.panel.setFooter('Footer');
			YAHOO.exp.panel.render(document.body);
		},
		gallerypopups : function (){
			YAHOO.exp.myPanel = new YAHOO.widget.Panel("imagepanel", {fixedcenter:true,constraintoviewport:true, modal:true,underlay:"none",close:true,visible:false,draggable:true} );
			YAHOO.exp.myPanel.setHeader('');
		    YAHOO.exp.myPanel.setBody('');
			YAHOO.exp.myPanel.render(document.body);
		},
		popImage : function (imgname,desc,imgfile,width,height) {
			//alert(width+" - "+height);
			YAHOO.exp.myPanel.setHeader("&nbsp;"+imgname);
			YAHOO.exp.myPanel.cfg.setProperty("width",width+20+"px");
			YAHOO.exp.myPanel.cfg.setProperty("height",height+20+"px");
			YAHOO.exp.myPanel.setBody('<img class="popupimage" src="'+imgfile+'" />');
			YAHOO.exp.myPanel.setFooter('<div id="gallerypopfooter">'+desc+'</div>');
			YAHOO.exp.myPanel.render(document.body);
			YAHOO.exp.myPanel.show();
		},
		init: function () {
			
		}
	};
}(); 

YAHOO.util.Event.onDOMReady(YAHOO.exp.init);