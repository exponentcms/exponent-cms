{*
 * Copyright (c) 2007-2008 OIC Group, Inc.
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

{form action=update}
    {control type="hidden" name="id" value=$record->id}
    {control type="text" name="name" label="Class Name" value=$record->classname}
    {control type="text" name="rate" label="Rate" value=$record->rate}
    {control type="dropdown" name="zone" label="Zone" values=$record->zones default=$record->zonename}
    {control type=state name=state label="State/Province" value=$record->state}
    {control type=country name=country label="Country" value=$record->country} 

    {control type="buttongroup" submit="Submit" cancel="Cancel"}
{/form}
