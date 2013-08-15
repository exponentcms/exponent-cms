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
{control type="text" name="meta[sef_url]" label="SEF URL"|gettext value=$record->sef_url description='If you don\'t put in an SEF URL one will be generated based on the title provided. SEF URLs can only contain alpha-numeric characters, hyphens, forward slashes, and underscores.'|gettext}
{control type="text" name="meta[canonical]" label="Canonical URL"|gettext value=$record->canonical description='Helps get rid of duplicate search engine entries'|gettext}
{control type="text" name="meta[meta_title]" label="Meta Title"|gettext value=$record->meta_title description='Override the item title for search engine entries'|gettext}
{control type="textarea" name="meta[meta_description]" label="Meta Description"|gettext value=$record->meta_description description='Override the item summary for search engine entries'|gettext}
{control type="textarea" name="meta[meta_keywords]" label="Meta Keywords"|gettext value=$record->meta_keywords description='Comma separated phrases - overrides site keywords and item tags'|gettext}
