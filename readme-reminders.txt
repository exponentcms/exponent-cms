ExponentCMS Calendar Event Reminders implementation

There are NO guarantees this may not open up security vulnerabilites on your site!  Please use with caution!

After installation (into the root ExponentCMS directory), you'll need to update/change the settings on the calendars you plan to use it with.  Primarily this allows you to add the info needed about the mailouts.

To use, pull up the send_reminders.php file in a browser (or curl with cron, etc...) passing a calendar src number.  Currently, you'll have to know the src number by pulling up an event, etc....  You can also pass other parameters, e.g.
http://www.mysite.org/send_reminders.php?src=@random4b2903417d3bb
http://www.mysite.org/send_reminders.php?src=@random4b2903417d3bb&view=_reminder_week
(or with curl)
curl -G -d "src=@random4b2903417d3bb&days=14" -s http://www.mysite.org/send_reminders.php

The script responds with either an error or displays a copy of the sent e-mail contents.

Variables
src - id of calendar to use (mandatory)
view - name of template to use (defaults to using _reminder)
time - time/date to start from (defaults to now)
days - number of days of events to pull (defaults to 7)

Currently this script:
- adhere's to aggregated/merged calendars
- uses the default (weekly) template of "_reminder" which pulls the next 7 days of events.  I may add a daily template
- sends an html/text formatted message, but the text message isn't formatted very well, it's just the hmtl less the tags
- only sends to the addresses selected in module settings which are selected from web site user profiles (think of it as the calendar owner).  I may add the ability to address groups and freestyle address just like in private messages and forms.  Not planning on adding a subscription option, but anything's possible.
