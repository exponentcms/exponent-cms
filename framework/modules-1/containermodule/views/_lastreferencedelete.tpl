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

{capture assign='onnogo'}
{link m=$iloc->mod s=$iloc->src i=$iloc->int action=delete_content}
{/capture}

{pop modal="true" onnogo=$onnogo onyesgo=$redirect close="false" id=alert dialog="Send to Recycle Bin:Permanently Delete"|gettext width=500px type=alert hide=true fade="0.15" header="Send to Recycle Bin"|gettext|cat:"?"}
    {"This module's content is not being used anywhere else.  Would you like to send this module to the Recycle Bin?"|gettext}<br><br>{"If you do not send it to the Recycle Bin the content will be permanently deleted."|gettext}
{/pop}
