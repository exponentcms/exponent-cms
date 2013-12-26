{*
 * Copyright (c) 2004-2014 OIC Group, Inc.
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

<h2>{'Notes'|gettext}</h2>
{control type="hidden" name="tab_loaded[notes]" value=1} 
{simplenote content_type="product" content_id=$record->id require_login="1" require_approval="0" require_notification="0" tab="notes"}
