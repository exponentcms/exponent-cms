{*
 * Copyright (c) 2004-2020 OIC Group, Inc.
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

{form action=update_model_alias}
	{control type="hidden" name="tab_loaded[model_alias]" value=1}
	{control type="hidden" name="product_id" value=$product_id}
    {control type="hidden" name="id" value=$model_alias->id}
    {control type="text" name="model" label="Model Alias:"|gettext value=$model_alias->model}

    {control type="buttongroup" submit="Submit"|gettext cancel="Cancel"|gettext}
{/form}
