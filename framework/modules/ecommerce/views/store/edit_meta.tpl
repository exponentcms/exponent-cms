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

<h2>{'Meta Info'|gettext}</h2>
{control type="hidden" name="tab_loaded[meta]" value=1} 
{control type="text" name="meta[sef_url]" label="SEF URL"|gettext value=$record->sef_url}
{control type="text" name="canonical" label="Canonical URL"|gettext value=$record->canonical}
{control type="text" name="meta[meta_title]" label="Meta Title"|gettext value=$record->meta_title}
{control type="textarea" name="meta[meta_description]" label="Meta Description"|gettext value=$record->meta_description}
{control type="textarea" name="meta[meta_keywords]" label="Meta Keywords"|gettext value=$record->meta_keywords}
