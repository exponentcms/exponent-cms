{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
 * Written and Designed by James Hunt
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


{pop modal="true" onnogo=$onnogo onyesgo=$redirect close="false" id=alert dialog="Send to Recycle Bin:Permanently Delete" width=500px type=alert hide=true fade="0.15" header="Send to Recycle Bin?"}

{$_TR.confirm}

{/pop}	



