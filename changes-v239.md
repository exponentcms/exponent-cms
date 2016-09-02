v239 adds these features to all previous releases of v238:
- adds new option for accordion views in filedownload, photoalbum, portfolio, and text modules allowing all panels to initially be open in addition to all closed or only first one open
- adds new 'tax exempt' user group setting for ecommerce
- update store sample database to include two user groups including tax exempt customers
- better accentuate odd/even row highlighting for bootstrap3 themes
- add feed caching for socialfeed module
- remove vertical brace separator between buttons on bootstrap themes; adds new 'not_bs' smarty modifier
- change attached images on events monthly view to be responsive for small displays
- update 'show' actions to properly act on a missing record by returning a 404 page not found error, esp for search engine with deleted content
- elFinder adds list view column size/sequence adjustment
- scrub some google analytics params from our expConfig records when saving module configuration
- traps exceptions in socialfeed module for distant end issues so we don't crash the page
- adds scrolling to all 'flyout' views and 'free-form' menu items; previously content was hidden off bottom of page on small devices
- adds appropriate 'alt' tag to photoalbum slideshow images
- update calendar/event header to be more small device friendly
- adds import/export tags
- adds new (search) tag cloud 'list' view which limits/displays tags from a settings list
- search tag cloud view processing is much, much faster
- adds Blog Sidebar subtheme to shipped themes 
- speeds up rss/podcast feeds if module contains many items
- adds (missing) title to lightbox popups in calendars
- implement rss/podcast feed server side caching
- update easypost billing controller configuration to default to 3 cents as the new handling charge
- now allows for theme custom language file and a slightly more efficient search/update for missing phrases
- add optional 'look back' limit for events returned within a search (we likely want more recent events, not everything in the system)
- tweaks pagination links output for small devices
- adds optional upgrade script to remove abandoned event records
- updates filter/search in events calendar administration and past-events views to help locate events to now look at dates
- initial support for twitter meta data for blog, filedownload, & news posts

v239 fixes these issues in all previous releases of v238:
* - fixes security issue with possible execution of uploaded scripts, CVE-2016-7095 by Balisong
- regression (v2.3.8patch1) fix ecom autocomplete may break invoice/packing views and create new order
- regression fix (v2.3.7patch4) upcoming events won't display current date 'all day' events
- regression fix bootstrap3theme sample theme some labels are invisible
- regression fix socialfeed module facebook photo statuses pulled too small image
- add 'force less compile' after clearing css cache, so style sheets will be updated when error-reporting is turned off
- fix possible issue with form module 'submit' button overlapping content below module
- regression fix ical feed gives recurring events same UID so they are not all displayed on some sources like Google Calendar
- fix ckeditor 'image' styles not appearing in 'styles' dropdown
- regression fix socialfeed styling to limit custom 'img-reponsive' style to only socialfeed module
- regression fix blog comments link on 'showall' views didn't link to article
- regression fix text module accordion view which added list styling though we only have a single text body under each text title
- fixes chrome (module settings menu) on blog cloud view
- regression fix (v237p3) db manager table filter added 'table prefix' after filtering tables
- regression fix 'ecommerce minimum order amount not being saved' error
- regression fix improper loading of ajax page stylesheets due to doubled quotes
- regression fix to require permission to empty recycle bin
- fix rss/podcast feed syndication link to validate
- fix some display anomalies with freeform menu items and containers
- fix issue with images attached to module configurations

v239 updates these 3rd party libraries in all previous releases of v238:
- normalize.css to v4.2.0
- moment.js to v2.14.1
- jqueryui to v1.12.0
- ckeditor to v4.5.10
- tinymce to v4.4.2
- elfinder to v2.1.14
- swiftmailer to v5.4.3
- mediaelement to v2.22.1
- bootstrap to v3.3.7
- bootswatch to v3.3.7
- easypost shipping sdk to v3.1.1
- jstree to v3.3.2
- phpThumb to v1.7.14-201608101311


v238patch6 adds these features to all previous releases of v238:
- additional ie compatibility with the ie10/win8 viewport workaround; and canceling ie compatibility mode since we want newest browsing engine
- change eLog() object/array output from print_r() to json for easier development handling

v238patch6 fixes these issues in all previous releases of v238:
- fix forms 'export to csv' to (also) follow module configuration for columns or simply default to all columns (instead of only first 5)
- regression fix traditional file manager uploader broken on bootstrap based themes where buttons wouldn't work
- update (bs3) datetimecontrol to not separate components on smaller displays
- remove warnings if no tweets returned in twitter module

v238patch6 updates these 3rd party libraries in all previous releases of v238:
- owl carousel to v2.1.6
- update bootstrap3-dialog to v1.35.2


v238patch5 adds these features to all previous releases of v238:
- allows for custom ckeditor config.js within theme (/themes/customtheme/editors/ckeditor/config.js)

v238patch5 fixes these issues in all previous releases of v238:
- regression fix (v238p4) unable to add any controls to a new or empty form using new bs3 drag/drop form designer
- regression fix (v238p2+) elFinder uploaded files and new folders/text files have wrong/unusable permissions (NO read)
- fix issue with bootstrap (3?) and navigation/showall Collapsing views since 'collapsing' is a reserved class in bs3 which hides contents

v238patch5 updates these 3rd party libraries in all previous releases of v238:
- none


v238patch4 adds these features to all previous releases of v238:
- update db manager to now recognize all datetimestamp fields and allow tooltip human date, and datetime widget in record edit
- update db manager to link 'poster' and 'editor' fields to the user record
- update db manager wysiwyg editor support to fix ckeditor conflict with 'edit sql command', and add ckeditor file manager integration
- updates 'day of week' labels to use 'locale' instead of expLang strings; also done for site configuration select 1st day of week
- adds filter/search to events calendar administration and past-events views to help locate events
- adds dynamic form designer for bootstrap 3 themes; adds toggle form designer grid and toggle form style switch
- adds form designer (blue) highlight for wizard/page-break controls (controls initially hidden were already grayed out)

v238patch4 fixes these issues in all previous releases of v238:
- regression fix several 'minify' problems; 
  - (v238) fix webshim to not complain/fail-to-load when it is loaded via minify (required temporarily moving to non-minifed webshim)
  - (v238) minfy has been broken with the update to minify v2.3.0, we failed to customize its config.php file
  - the yui3 'combine' function has probably been broken for a long time
- fix URL_BASE (http/https) to now account for ssl load balancer
- fix bootstrap-tagsinput to recognize android input
- change datetimecontrol to not print out (redundant) 'date' or 'time' label if not displaying both date AND time input

v238patch4 updates these 3rd party libraries in all previous releases of v238:
- bootstrap3 swatches to v3.3.6+2
- adminer (db manager) to v4.2.5
- tinymce to v4.3.13
- easypost sdk to v3.1.0


v238patch3 adds these features to all previous releases of v238:
- add remove empty tags upgrade script
- elFinder adds small device/display support
- adds error catching to text module inline edit view if user is logged out at server (and live edit view still visible)
- update database manager to use a much enhanced table search filter

v238patch3 fixes these issues in all previous releases of v238:
- fix security issue with database manager: Security Advisory XS3C-2016-05-20 reported by Julian Held
- fix security issue with pixidou editor: Security Advisory XS3C-2016-05-19 reported by Julian Held
- regression fix (238p2) visual cue (border) when hovering over editable text in ONLY text module inline edit view
- regression fix (238p2) module configuration settings view broken on non-bs3 themes due to attached files view selection code problem
- regression fix navigation manage by sitemap standalone pages link was invalid
- fix some issues with eaas; regression fix 'aboutus' request would always fail, adds quick module item count to 'aboutus' request, removes some unnecessary clauses from aggregate modules sql statement
- fix expTheme::runAction() to not spit out a 403 error for the 'current page' if issued an ajax request/action (kills some ajax requestors)

v238patch3 updates these 3rd party libraries in all previous releases of v238:
- elFinder to v2.1.12
- owl carousel to v2.1.5


v238patch2 adds these features to all previous releases of v238:
- update instagram icon for socialmedia module
- add special elFinder icons for avatar and uploads folders
- update bootstrap3 'alternate' controls from yui3 to jquery
- update bootstrap3/owl-carousel view configurations to allow selecting available animations
- add news module showall 'toggle' view
- add 'autoplay' setting support to attached files slideshow view
- add bs3/jquery variations for many ajax-paged blog, news, mediaplayer, photo, portfolio, search, events and eventregistration views
- add 'in-line' ajax paging indicator support, esp. for bs3
- fully deprecate store/upcomingEvents() & store/eventsCalendar() so if called they redirect to same action in eventregistration module
- add bootstrap3 based photoalbum accordion view
- added support to store/ecomSearch so that 'enter' pulls up search results
- adds new ecommerce (general settings) search feature to 
  - 1) limit search results to only ecommerce, or to only products; 
  - 2) when active, the search hits will be displayed more like a store listing instead of search list; 
  - 3) this setting also determines which items are displayed in the store/ecomSearch autocomplete list
- adds external event calendar caching feature (requires cron type job)
- add ics file/feed import to event module
- adds visual cue (border) when hovering over editable text in text module inline edit view

v238patch2 fixes these issues in all previous releases of v238:
- regression fix (v2.3.7) event calendar pull external ical breaks page, also many warnings on page
- fix loss of external events when more than one external calendar feed was pulled in
- deprecates google calendar xml feed support (google deprecated this feature 11/2015)
- now makes allowances for /install folder not existing
- now allows for non-existent (removed?) shipping/billing calculators
- some links with PDF engine selection were incorrect
- fix some bootstrap3 view configuration settings display loading
- regression fix (v2.3.8) non-bs 'loading' indicator animated image missing
- regression fix non-bs icon links with no action weren't in-line, but created a new line
- fix several ajax paging issues with blog, news, events and eventregistration modules
- regression fix locate store category or product by single parm in url (without using custom router_maps.php)
- regression fix in migration and the update_rss_feed upgrade script, we were not truly looking for an existing sef url in order to create a unique one
- regression fix expPaginator now converts 'product' search hits into full product objects for processing
- fix ecomSearch to also return items without model numbers (donation, event registration, etc...)
- fix expPaginator crashing when custom module no longer available (search, page not found, etc...)
- fix bootstrap3 dialog widget wasn't picking up theme colors
- fix some password strength meter anomalies by moving all framework widgets to 'strength-meter'
- fix some form field error flagging when 'thrown' back to a form
- regression fix (v2.3.8) emails sent to system default user for addresses where 'user attribution' returned a blank result (users without first/last name) 
- fix forms stylesheet not loaded for 'design form' view
- regression fix restore database/eql would sometimes leave random 'n' characters in place of newlines
- tweak styles to photoalbum/slideshow_slider view

v238patch2 updates these 3rd party libraries in all previous releases of v238:
- tinymce to v4.3.12
- colorbox to v1.6.4
- mediaelement to v2.21.2
- ckeditor to v4.5.9
- jquery.datetimepicker to v2.5.4
- iCalcreator to v2.22.0
- Font-Awesome to v4.6.3
- jQuery to v1.12.4/2.2.4
- jQuery migrate to v1.4.1
- plupload to v2.1.9
- owl.carousel to v2.1.4


v238patch1 adds these features to v238:
- add (upload) image display in form module show, showall_portfolio (with no custom configuration), and email default_report views
- we now display the graphic for an 'image' field in the confirm_data view
- adds showall records button/link to form module (individual) show view
- adds form module showall view setting to prevent individual record viewing
- adds optional lightbox (jquery colorbox) for event on event module showall Upcoming Events Headlines view
- updates bootstrap3 show product and photoalbum showall accordion views to use colorbox lightbox instead of yui3
- adds jquery based bootstrap3 view for attached files in Showcase & Slideshow views

v238patch1 fixes these issues in v238:
- fix to actually skip over 'cgi-bin' for 'check permissions' upgrade scripts
- regression fix possible issue with extraneous directory separator in a phpThumb generated thumbnail
- regression fix ecommerce error when easypost shipping calculator isn't set up
- fix form next/prev issue
- regression fix remove redundant 'read more' link for news module showall views; 
- regression fix file download module views redundant 'read more' links; add 'read more' link if configured for quick download without item body
- allow form control names 'email' & 'image' to be any case for processing in the views
- form module did not properly process a 'reloaded' uploadcontrol
- we now use field names in the form module showall and confirm_data views to properly process 'email' and 'image' fields
- regression fix form submission/display within multi-page datatables (user/group permissions & view event registrants)

v238patch1 updates these 3rd party libraries in v238:
- updates jstree to v3.3.1 with bug fixes
- updates font-awesome to v4.6.2

