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

<h1>{'Showing'|gettext} {$model_name}, id: {$object->id}</h1>

<div id="scaffold-object">
	{list_object object=$record}
    <a href="{link controller=$model_name action=showall}">{'Go back to Show All'|gettext} {$model_name}</a> or
    <a href="{link controller=$model_name action=edit id=$record->id}"> {'Edit this'|gettext} {$model_name}</a>
</div>
