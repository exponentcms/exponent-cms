{*
 * Copyright (c) 2007-2008 OIC Group, Inc.
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
<h1>Vendor Information</h1>
{form action=update_vendor}
    {control type="hidden" name="vendor[id]" value=$vendor->id}
    {control type="text" name="vendor[title]" label="Title" value=$vendor->title}
	{control type="textarea" name="vendor[body]" label="Body" rows=5 cols=85 value=$vendor->body}
	{control type="text" name="vendor[address1]" label="Address 1" value=$vendor->address1}
	{control type="text" name="vendor[address2]" label="Address 2" value=$vendor->address2}
	{control type=text name="vendor[city]" label="City" value=$vendor->address1}
	{control type=state name="vendor[state]" label="State" value=$vendor->state}
	{control type=text name="vendor[zip]" label="Zip Code" value=$vendor->zip}
	{control type="text" name="vendor[phone]" label="Phone Number (xxx-xxx-xxxx)" value=$vendor->phone}
	{control type="text" name="vendor[fax]" label="Fax" value=$vendor->email}
	{control type="text" name="vendor[website]" label="Website" value=$vendor->website}
	{control type="text" name="vendor[email]" label="Email Address" value=$vendor->email}
	
    {control type="buttongroup" submit="Submit" cancel="Cancel"}
{/form}