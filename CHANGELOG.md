#Exponent Content Management System..

----------

Copyright (c) 2004-2013 OIC Group, Inc.

For a more detailed changelog visit https://github.com/exponentcms/exponent-cms/commits/master

Added / Fixed / changed for 2.2.0 beta 3
-----------------------
### The first 'pure 2.0' version of Exponent w/o any 1.0 modules, etc..., primarily implements Container 2.0 and integrates Twitter-Bootstrap/jQuery
### Known Issues
  - None

Added / Fixed / changed for 2.2.0 beta 2
-----------------------
### The first 'pure 2.0' version of Exponent w/o any 1.0 modules, etc..., primarily implements Container 2.0 and integrates Twitter-Bootstrap/jQuery
  - adds many features to online donations and event registrations making them more robust and polished
    -- quick add donation at set amount or allow user to enter amount
    -- event registrations now abide by 'options'
  - adds new forms showall portfolio view for multi-record custom view (fixes custom single record view)
  - implements broader use of ajax paging and use of new html5 input types, temporarily adds additional date/time form designer controls
  - contains all the fixes in 2.1.4, and many more fixes
### Known Issues
  - None

Added / Fixed / changed for 2.2.0 beta 1
-----------------------
### The first version of Exponent w/o any 1.0 modules, etc..., primarily implements Container 2.0 and Twitter-Bootstrap/jQuery
  - enhances attached file display features
  - adds new html5 media player module for audio, video & youtube, which deprecates both flowplayer and youtube modules
  - updates filedownload module media player to html5 (flash not required)
  - adds new import form data, or create a form from csv file feature
  - adds new import users from csv file feature
  - adds ability to optionally run selected 'upgrade scripts' outside of installation
  - moves /conf folder inside /framework to help streamline folder structure
  - contains all the fixes in 2.1.4
### Known Issues
  - None

Added / Fixed / changed for 2.2.0 alpha 3
-----------------------
### The first version of Exponent w/o any 1.0 modules, etc..., primarily implements Container 2.0 and Twitter-Bootstrap/jQuery
  - removes deprecated headline controller (converting them to text modules), suggest a custom text module 'headline' view be created if needed
  - fixes migration to work w/ container2
  - adds theme export feature
  - adds new 'blog' site sample in addition to 'sample site' during installation
  - adds category support to blog module
    -- adds new comments and categories views to blog
  - adds file upload pause, resume, & cancel feature
  - adds normalize.css and Twitter Bootstrap as system (theme) features
  - contains all the fixes in 2.1.3
### Known Issues
  - None

Added / Fixed / changed for 2.2.0 alpha 2
-----------------------
### The first version of Exponent w/o any 1.0 modules, etc..., primarily implements Container 2.0 and Twitter-Bootstrap/jQuery
  - Fixes the 'nested container not displayed' issue
  - Removes all the 'old school' files
  - Contains all the fixes from v2.1.2
### Known Issues
  - None

Added / Fixed / changed for 2.2.0 alpha 1
-----------------------
### The first version of Exponent w/o any 1.0 modules, etc..., primarily implements Container 2.0 and Twitter-Bootstrap/jQuery
  - replaces the containermodule with container2 controller
  - forces deprecation/removal of formmodule/formbuilder
    -- also fully deprecates/removes calendarmodule, simplepollmodule, & navigationmodule if they still exist
  - moves jQuery/Twitter-Bootstrap as primary libraries over YUI2/3 (which are still backwards compatible)
### Known Issues
  - This is the first release of the v2.2 code

Added / Fixed / changed for 2.1.4
-----------------------
### This release only fixes bugs found in 2.1.3, everything else is destined for the v2.2 major version update
 - updates EQL file export/import to also include table definition for future proofing backups
 - adds new 'Exponent as a Service' module for JSON service calls
 - fixes Online Event Registrations activating many features, adds email registrants
 - fixes over 3 dozen issues found in 2.1.3

Added / Fixed / changed for 2.1.3
-----------------------
### This release only fixes bugs found in 2.1.2, everything else is destined for the v2.2 major version update
 - fixes form copy feature
 - fixes a 'file manager doesn't display files' issue on some servers
 - makes blog aggregation configuration tab more intuitive
 - fixes some migration issues with forms, events, & navigation

Added / Fixed / changed for 2.1.2
-----------------------
### This release focuses on things not completed in 2.1.1, but not destined for the v2.2 major version update
 - adds category and tag assignment to multi-add photo album feature
 - adds some tooltip information in file manager (file and image sizes) and form manager (number of records and controls)
 - cleans up form assignment to a module by using manage forms instead of module configuration settings
 - fixes over a dozen bugs and anomalies from previous versions

Added / Fixed / changed for 2.1.1
-----------------------
### This release focuses on the new forms module and user interface improvements
  - adds many new event calendar features
    - adds event copying feature (create new event from existing event)
    - adds optional attached images to events and event registrations
    - adds optional event popup in a lightbox dialog (like google calendar)
    - adds 'cancelled event' feature to display events as cancelled
  - updates old school form module to a 2.0 controller
    - now allows greater flexibility in which users may enter or view data
    - forms are now site-wide objects, can view/enter form data from different modules/pages
    - adds view flexibility offered by a 2.0 module for future features
    - updates form control (input) display features and format to be more consistent and modern
  - adds ajax-based navigation in calendar, news items & blog posts to prevent reloading entire page
  - adds 'grouping by date' for uncategorized portfolio & file download items (in addition to 'grouping by alpha' for rolodex feature)
  - adds new views to several modules
    - new 'headline' view to filedownloads module
    - new 'toggle' and 'accordion' views to the text module
    - new 'toggle' view to the portfolio module
    - new 'flyout sidebar' view to navigation module for hard-coding in themes
    - new 'vertical' login view
  - now allows multiple files within a file download item; defaults to 1st attachment, but displays all attachments in show item view
  - adds new 'multi-add' feature to create multiple photo album items in one easy step
  - adds new 'inline' view to edit text module items directly on page via CKEditor v4
  - adds custom module phrase translation libraries feature
  - merges module 'Configure Actions & Views' and 'Configure Settings' into a single view for easy module configuration
  - adds new 'private module' setting to optionally restrict viewing of a specific module by permission
  - adds a mass mailer for super admins to email all users or selected site users
  - adds new site configuration setting to reverse the default logic of when module titles are displayed
  - updates file manager with 'virtual folder' grouping and date features to assist locating files
    - also adds file manager bulk selection (add multiple files to an item all at once) and bulk delete
    - adds new QuickUpload feature to bypass file manager/file uploader for file selection
  - updates file uploader to allow html5 drag/drop support and some other user-friendly features
    - images may be optionally resized to a max width, and files assigned to a virtual folder during upload
  - calendarmodule and simplepollmodule (old school) are now fully deprecated (removed)

Added / Fixed / changed for 2.1.0
-----------------------
### This release focuses on new calendar module and a 'mega' menu
  - adds optional nested comments, and optional per-item comment disabling
  - adds optional comments to filedownload module items
  - adds optional external file url for file download item
  - removes extra themes to reduce size of package, available as separate addon downloads
  - adds version change information to initial upgrade page
  - adds optional color/style to categories
  - upgrades calendar to a 2.0 event controller with categories & tags
    - optionally upgrades and converts existing calendars
    - adds color-coded categories and tags
    - adds color-coded aggregation of external Google & iCal calendars, and online event registrations
  - adds new 'mega-menu' navigation view with new free-form page/menu type for embedding modules
  - adds random sorting/sequence to photo album/slideshows
  - adds optional user selectable email destination on form submission
  - adds new built-in database manager
  - plus many, many other tweaks, fixes, and features

Added / Fixed / changed for 2.0.9
-----------------------
### This release focuses on greater HTML5 & PHP v5.4 compliance, & module upgrades
  - upgrades old school simple poll and navigation modules into 2.0 style controllers
    - page type indicators now displayed in Manage Pages view
    - new universal navigation view to emulate most old navigation views through user settings
    - initial implementation of optional menu item icons
  - adds help links to most admin displays
  - adds next/prev item in blog and news single item view
  - adds optional 'date badge' to news, blog, & filedownload views
  - calendar is more forgiving of timezone changes (events won't disappear)
    - adds selectable weekly view to calendar
    - updates ical feeds and send reminders cron task to use sef urls
  - adds new 2-column style display option in forms module
  - updates rss feeds to use sef urls, adds individual feed 'advertise' option
  - more efficient searches...several anomalies fixed and search activity reports available
  - importing EQL database backup files now more descriptive if errors are encountered (detailed solution provided)
  - adds some new upgrade scripts to attempt to clean up the system from previous updates
    - deletes moved files where old file in old location may still linger
    - attempts to fix some database issues such as mixed case naming or controller name quirks
  - new system fallback theme so pages will display even if the entire themes folder is missing
  - adds instructions to get e-commerce up and running
    - closer to an 'out of the box' solution
    - can now activate e-commerce w/o activating an ecom module
    - PayPal Express checkout updated to use new api to give more into on the PayPal screen
    - FedEx & UPS shipping calculators working
    - e-commerce use a little more intuitive/more features now functional
  - adds a working 'upload' control to forms which will email submitted file as an attachment
  - (finally) upgrades the YUI framework to v3.7.2 (after being stuck at v3.4.0 due to issue)
  - plus many, many other tweaks, fixes, and features

Added / Fixed / changed for 2.0.8
-----------------------
### This release focuses on fixing bugs and enhancing list management (tags, categories, comments)
  - adds manual sorting to news module
  - adds random sorting to links module
  - enhances category management by removing global categories and grouping categories by module type
  - adds site-wide comment management to exponent menu
  - enhances comment, tag, & category management with bulk module item processing
  - adds additional views to photoalbum
    - new tabbed and accordion views just like filedownloads & portfolio
    - view category/gallery/group album to pull up individual group/gallery
  - adds group (category) viewing to filedownloads
  - adds quick upload feature to wysiwyg editor (upload in editor without going to file manager/uploader)
  - finally implements email alerts (ealerts) in a usable form for blog, news, & filedownloads (subscribe to updates)
  - adds support for custom smarty plugins & form controls (in current theme folder)
  - adds alpha-level implementation of less style sheets (both system and theme)
  - plus many other tweaks, fixes, and features

Added / Fixed / changed for 2.0.7
-----------------------
### This release focuses on fixing bugs and enhanced speed/size
  - incorporates all fixes from v2.0.6 patches
  - Exponent CMS should work on servers running php v5.4.x (v5.2.1 or later required)
  - polishes older modules to better resemble new modules for greater interface consistency
    - all modules now use same rank reordering dialog instead of up/down arrow buttons
       - module reordering now found under container chrome menu
    - tabbed item editing, configuration
  - removes unnecessary (new) unpublish feature from blog
  - adds wysiwyg comments
  - adds publish date feature to filedownloads
  - adds 'go to date' feature to calendar module (chrome browser not working yet)
    - adds experimental iCalendar/Google Calendar XML event aggregation feature (can be slow)
  - adds new photo album slideshow view using vertical thumbnails with text
  - better handling of video (and audio) uploads and previews
  - adds tag list/cloud sorting by # hits and setting limits
  - adds option to rename 'Uncategorized' group label per module
  - fixes youtube module to finally handle multiple items (paging, reordering, etc...)
  - fixes some pagination anomalies associated with multiple pages and sorting
  - sorting/ranking lists should be more user accessible (esp for LONG lists)
    - reordering dialog now pops up under 'Order ...' link
    - items now can be dragged to above/below the visible list and it will scroll browser window
  - manage users is now is sortable with more accurate filtering
  - adds optional links to display printer friendly views or export-to-pdf for blog, news, filedownload, portfolio & calendar
    - export to pdf requires optional 'dompdf' package installation
  - adds optional formmodule control descriptions
  - adds new delete command to recycle bin items, plus new display of all recycled modules (from Exponent menu)
  - translations more widely implemented; machine translations updated
  - many 3rd party libraries updated (ckeditor, flowplayer, minify, & swiftmailer)
  - plus many other tweaks, fixes, and features

Added / Fixed / changed for 2.0.6
-----------------------
### This release focuses on usability features and security
  - incorporates all fixes from v2.0.5 patches
  - adds publish/unpublish dates and 'draft' feature to blog
  - implements WYSIWYG comments
  - adds aggregated content indicator with command to move item into current module
  - fixes a plural/singular MVC naming issue to provide more fluid integration (developer feature)
    - photoalbum, faq, & filedownloads; all fixed during the upgrade
  - upgrade scripts now run when versions are equal to allow better upgrading from develop code
    - also adds 'optional' upgrade scripts; includes optional deprecated headline controller removal
  - prevent installation/upgrade over 0.9x database (must be a clean install then migrate old db)
    - adds cURL library support requirement to installation
  - enhanced podcast/rss feeds; deprecates 1.0/calendar rss, in favor of ical feed
  - adds true Finnish (machine) translation
  - adds additional url security checking
  - plus other tweaks, fixes, and features

Added / Fixed / changed for 2.0.5
-----------------------
### This release focuses on implementing categories
  - categories are (fully) implemented within the faq, filedownloads, links, photoalbum & portfolio modules
    - adds tabbed view either by category or alphabetized like rolodex
    - adds an accordion like view w/ collapse/expand by category/alphabetized
    - categories now migrated; imagegallerymodule galleries converted to categories

### Many fixes and updates
  - Minification has been fixed for many server scenarios
  - adds optional thumbnail display and list size setting to file manager
  - adds tag cloud view to search module
  - adds tags to faq, news, & photoalubm modules
  - adds deletion of multiple standalone pages at once
  - adds notification of version patch releases
  - adds additional sorting options to news, filedownloads, & portfolio modules
  - adds 'Recent' views to blog, filedownloads, & news modules...good teaser view
  - many migration updates and fixes
    - fixes slideshowmodule migration (which wasn't occuring)
    - better migration of 0.9x-type 'reused' type modules
    - newsmodule tags now migrated (why weren't we already doing this)
    - now migrates many additional module and view configuration settings
  - adds module scope type indicator to top-level/hard-coded chrome menus
  - implemented extension (theme, patch, & mods) repository access
    - extensions loaded into current theme unless told to patch system
  - adds a module description feature and making the display of the module title optional
    - module titles are now mandatory; to better differentiate on aggregation selection, etc...
  - coolwater theme enhanced with header configuration, new 'wide' style variation, etc...
  - changes calling parameters for send_reminders.php

Added / Fixed / changed for 2.0.4
-----------------------
### This release focuses on revising the install/update/upgrade process
  - installation and upgrade must now be specifically invoked by url
  - notice given to admin users on upgrade need or new version availability
### WYSIWYG editor (CKEditor) now more faithfully displays content styles
  - can now also have customized format, font, & styles drop-downs
  - can now access File Manager to link to files in addition to pages/modules
### Many fixes and updates
  - corrects path problem w/ user extensions, esp. the avatar extension
  - improved tag viewing, usage and management
  - improved aggregation selection list
  - can now remove missing files or add files found on server to File Manager
  - greatly enhanced Coolwater theme that allows easy header customization
  - plus many other bug fixes and updates

Added / Fixed / changed for 2.0.3
-----------------------
### This release focuses on refining the language system
  - for general use, installation, and to simplify translation creation
  - Spanish, German, Danish, Norwegian, Czech, and Finnish translations added
### Tweaked permission system to more accurately reflect designed purpose
  - Permissions now cascade down thru pages and containers
  - Management screens accurately reflect inherited and implied permissions
  - The super-admin may now create other super-admin users
  - Global sidebar modules don't inherit current page permissions & now require permission for non-admin users
### File Manager, Uploader, & Pixidou editor - tweaked and fixed
### Flowplayer module now offers style selection
### Filedownloads optionally shows media player for audio(mp3)/video(flv/f4v) downloads
### If database is down, the system is put in a temp maintenance mode instead of running installation
### Adds search result highlighting
### Plus many other bug fixes and updates

Added / Fixed / changed for 2.0.2
-----------------------
### This release focuses on integrating Smarty v3
  - Custom themes/views must be updated to follow Smarty syntax (v2 allowed sloppy syntax)
### 3rd party libraries updated
### E-commerce updates
### Several new or enhanced features
  - New auto-complete feature with 'tag' selection
### Fixes numerous bugs and other issues; incorporates 3 patches to v2.0.1
### All 1.0 subsystems & FCKeditor now fully deprecated

Added / Fixed / changed for 2.0.1
-----------------------
### This release focuses on adding new features to and fixing problems with the first stable release
### (major change) YUI Loader changed to 'yui2in3', YUI3 updated to v3.4.0
  - Custom themes/views must be updated to new YUI2 loading method
### Several other 3rd party libraries updated
  - SwiftMail v4.1.2 (adds secure connection protocol selection: SSL, TLS, or none)
  - CKeditor v3.6.2 (adds iOS5 support)
  - MagPieRSS feedreader class replaced by SimplePie class
### Several critical issues and other bugs fixed
  - Includes several file manager fixes and tweaks
### Theme engine updated to include theme previewing, configuration, style variations, and mobile theme variations
  - Adds new user configurable Multi-Options theme
    - Demonstrates the new FlyoutYUI Login view (note the green icon near the upper left corner)
  - Adds selectable style variations to SimpleTheme
    - includes an example barebones Mobile theme variation (view the site on an iPhone or Android)
### Twitter module updated to better mimic twitter.com view, includes retweet features
### Many ecommerce updates
### Deprecates several 1.0 style subsystems & modules including administration & login modules
  - Custom themes/views must be updated to reference login controller instead of loginmodule

Added / Fixed / changed for 2.0.0 (stable)
-----------------------
### The first stable release
