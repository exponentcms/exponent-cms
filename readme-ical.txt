ExponentCMS Calendar iCalendar feed implementation

To use, click on the ical feed icon next to the calendar module or event title.  This will download the .ics file.
Alternatively, you can copy that shortcut/link and use it as a feed for calendar programs such as MS Outlook (2007/2010) or Google Calendar (others not tested)

Currently this action
- requires either an sef_url ('title'), 'event_id', or event/calendar module 'src' as a parameter
- adheres to aggregated/merged calendars
- passes a single event when an 'event_id' is passed
- passes all future events from a date 1 month ago, if a calendar module sef_url/src is passed; limited by the module iCal setting
- passes a "month's" events if passed with a 'time'
- three styles available using the 'style' parameter to better format line-endings
  -- no style = standard ics
  -- style='g' will provide better formatting for display in Google calendar
  -- style='n' (or any letter besides g) provides better formatting for display in calendar applications which don't like quoted-printable

e.g.,
http://www.mysite.com/event/ical/title/mycalendar/style/g
http://www.mysite.com/event/ical/src/@random4f298b89/time/146534278