{*
 * Copyright (c) 2004-2013 OIC Group, Inc.
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

{group label="Downloadable Files Configuration"|gettext}
    {control type=text name="title" label="File list title"|gettext value=$config.title}
    {control type="checkbox" name="usefilename" label="Always use filename instead of title"|gettext value=1 checked=$config.usefilename}
{/group}