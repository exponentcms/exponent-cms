{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
 * Written and Designed by Adam Kessler
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
 *}
{if $product->isAvailable()}
<div class="module cart eventregistration addToCart">
    <h1>Register for {$product->title}</h1>
    <p>Please enter the name, email, and phone number of the person you would like to register for this event.  If you would like to add more registrants, simply click 'Add another registrant'. </p><br/>

    {form name="evregfrm" action=addItem}
        {control type="hidden" name="product_type" value=$product->product_type}
        {control type="hidden" name="product_id" value=$product->id}
        
<div class="module cart eventregistration addToCart registration_div" id="regdiv"> 
        {control type="text" id="registrations" name="registrants[]" label="Registrant Name:"}
        {control type="text" id="registrations_emails" name="registrant_emails[]" label="Registrant Email:"}
        {control type="text" id="registrations_phones" name="registrant_phones[]" label="Registrant Phone:"}
<hr>
     </div>
   
        <a class="exp-ecom-link plus" id="newregistrant" href="#"><em>Add another registrant</em> <span></span></a> &nbsp;&nbsp; OR &nbsp; <a class="exp-ecom-link addtocart" onclick="EXPONENT.validateReg()" href="#"><em>Add Registration to Cart</em><span></span></a>
    {/form}
    
</div>

{script unique="eventreg"}
{literal}
EXPONENT.validateReg = function() {
    var frm  = YAHOO.util.Dom.get('evregfrm');
    if (frm.registrations.value==undefined){
	for(i=0; i<frm.registrations.length; i++){
	   if (frm.registrations[i].value==""){
		alert("You must provide a name for each of your registrants.");
		frm.registrations[i].focus();
		return;
	   } 	
	   if (frm.registrations_emails[i].value==""){
                alert("You must provide an email for each of your registrants.");
                frm.registrations_emails[i].focus();
                return;
           }
	   if (frm.registrations_phones[i].value==""){
                alert("You must provide a phone for each of your registrants.");
                frm.registrations_phones[i].focus();
                return;
           }
	}
        YAHOO.util.Dom.get('evregfrm').submit();
    } else if (frm.registrations.value!="" && frm.registrations_emails.value!="" && frm.registrations_phones.value!=""){
        YAHOO.util.Dom.get('evregfrm').submit();
    } else {
        alert("You must provide name, email, and phone information for your registrant.");
	frm.registrations.focus();
    }
}


YAHOO.util.Event.onDOMReady(function(){
    var addNewRegs = {
        addcounter : 0,
        refcontrl : YAHOO.util.Dom.get('regdiv'),
        insertAfter : function (){
            this.refcontrl.parentNode.insertBefore(this.newcontrl, this.refcontrl.nextSibling);
        },
        init : function () {
            this.parent = this.refcontrl.parentNode;
            YAHOO.util.Event.on("newregistrant", 'click',this.process,this,true);
        },
        process : function (e) {
            YAHOO.util.Event.stopEvent(e);
            if (this.addcounter!==0){
                this.refcontrl = this.newcontrl;
                this.addcounter++;
            } else {
                this.addcounter = 1;
            }
            this.newcontrl = document.createElement('div');

            this.newcontrl.setAttribute("class",this.refcontrl.getAttribute("class"));
            this.newcontrl.innerHTML = this.refcontrl.innerHTML;
            
            
            this.insertAfter();
        }
    }
    addNewRegs.init();
});
{/literal}
{/script}
{else}
<div class="module cart eventregistration addToCart">
    <h1>{$product->title} is Closed for Registration</h1>
</div> 
{/if}
