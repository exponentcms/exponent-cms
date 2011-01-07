<?PHP

##################################################
#
# Copyright (c) 2006  Maxim Mueller
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

header('Content-type: text/javascript', true);
?>

FCKConfig.ToolbarSets["Default"] = <?PHP echo stripslashes($_GET['toolbar']); ?>;

plugins = <?PHP echo stripslashes($_GET['plugins']); ?>;

for(currPlugin = 0; currPlugin < plugins.length; currPlugin++) {
	FCKConfig.Plugins.Add(plugins[currPlugin], null );
}	


