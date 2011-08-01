##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
# Written and Designed by James Hunt
#
# This file is part of Exponent
#
# Exponent is free software; you can redistribute
# it and/or modify it under the terms of the GNU
# General Public License as published by the Free
# Software Foundation; either version 2 of the
# License, or (at your option) any later version.
#
# GPL: http://www.gnu.org/licenses/gpl.txt
#
##################################################

<div class="module users extension avatar">
    {img src=$edit_user->image}     
    {control type="hidden" name="current_avatar" value=$edit_user->image}
	{control type=checkbox name=use_gravatar value=1 label="Use Gravatar.com Avatar Image (using above e-mail)?" checked=$edit_user->use_gravatar}
    {control type="file" name="avatar" label="or Upload a Custom Avatar Image"}
</div>


