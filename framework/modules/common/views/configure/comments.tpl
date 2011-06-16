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

{control type=checkbox name=usescomments label="Disable Adding Comments" value=1 checked=$config.usescomments}
{control type=checkbox name=hidecomments label="Hide Posted Comments" value=1 checked=$config.hidecomments}
{control type=editor name=commentinfo label="Comment Information" value=$config.commentinfo}
