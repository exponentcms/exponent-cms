{*
 * Copyright (c) 2004-2012 OIC Group, Inc.
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

<h2>{'Configure this Module'|gettext}</h2>
{control type=dropdown name=order label="Sort By"|gettext items="Title, Order Manually"|gettextlist values="title,rank" value=$config.order|default:rank}
{*control type="radiogroup" name="usebody" label="Body Text"|gettext value=$config.usebody|default:0 items="Full,Summary,None" values="No Float,Left,Right"  values="0,1,2"*}

