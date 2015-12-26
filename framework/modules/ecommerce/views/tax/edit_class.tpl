{*
 * Copyright (c) 2004-2016 OIC Group, Inc.
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

<h1>{"Edit Tax Class"|gettext}</h1>
{form action=update_class}
    {control type="hidden" name="id" value=$class->id}
    {control type="text" name="name" label="Class Name"|gettext value=$class->name focus=1}
    {control type="buttongroup" submit="Submit"|gettext cancel="Cancel"|gettext}
{/form}
