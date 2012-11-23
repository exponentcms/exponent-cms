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

{'RSVP for event:'|gettext} '{$event->event->title}' {'on'|gettext} {$event->date|date_format}

{'Name'|gettext}:      {$params.name}
{'Phone:'|gettext}     {$params.phone}
{'Attendees:'|gettext} {$params.attendees}
{'Comments'|gettext}:  {$params.comments}