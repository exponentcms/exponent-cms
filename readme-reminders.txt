ExponentCMS Calendar Event Reminders implementation

There are NO guarantees this may not open up security vulnerabilities on your site!  Please use with caution!

After installation, you'll need to update/change the settings on the calendars you plan to use it with.  Primarily this allows you to add the info needed about the mailouts.

To use, pull up the send_reminders.php file in a browser (or curl with cron, etc...) passing a calendar sef_url which can be found/set on the module configuration screen.
You can also pass other parameters, e.g.
http://www.mysite.org/cron/send_reminders.php?id=1
http://www.mysite.org/cron/send_reminders.php?id=1&view=_reminder_week
(or with curl)
curl -G -d "id=1&days=14" -s http://www.mysite.org/cron/send_reminders.php

The script responds with either an error or displays a copy of the sent e-mail contents.

Variables (title, id, or src is mandatory)
title - sef_url of calendar to use
id - id of calendar to use
src - src of the calendar to use
view - name of template to use (defaults to using _reminder)
time - time/date to start from (defaults to now)
days - number of days of events to pull (defaults to 7)

Currently this script:
- adheres to aggregated/merged calendars
- uses the default (weekly) template of "_reminder" which pulls the next 7 days of events.
- sends an html/text formatted message, but the text message isn't formatted very well, it's just the html less the tags
- sends to the addresses selected in module settings.
