ExponentCMS Calendar iCalendar feed implementation

To use, click on the ical feed icon next to the calendar module or event title.  This will download the .ics file.
Alternatively, you can copy that shortcut/link and use it as a feed for calendar programs such as MS Outlook (2007/2010) or Google Calendar (others not tested)

The script responds with either an error or displays a copy of the sent e-mail contents.

Currently this action
- requires either an sef_url (title), event id, or calendar src as a parameter
- adheres to aggregated/merged calendars
- passes a single event when an event id is passed
- passes all future events if a calendar module sef_url or src is passed
- passes "month's" events if a calendar src is passed with a time, defaults to now
- three styles available using the 'style' parameter to better format line-endings
  -- no style = standard ics
  -- style='g' will provide better formatting for display in Google calendar
  -- style='n' (or any letter besides g) provides better formatting for display in calendar applications which don't like quoted-printable
