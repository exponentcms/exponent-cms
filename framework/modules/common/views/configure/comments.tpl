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

<h2>{'Comments Settings'|gettext}</h2>
{control type=checkbox name=usescomments label="Disable Adding Comments"|gettext value=1 checked=$config.usescomments}
{control type=checkbox name=hidecomments label="Hide Posted Comments"|gettext value=1 checked=$config.hidecomments}
{control type=editor name=commentinfo label="Comment Information"|gettext value=$config.commentinfo}
