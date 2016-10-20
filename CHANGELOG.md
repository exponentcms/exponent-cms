# Exponent Content Management System..

----------

Copyright (c) 2004-2016 OIC Group, Inc.

For a more detailed changelog visit [https://github.com/exponentcms/exponent-cms/commits/master](https://github.com/exponentcms/exponent-cms/commits/master)

----------

Version 2.4.0
-------------
### Address issues in v2.3.9, fix security vulnerabilities, and add accessibility hints
  - adds accessibility hints for screen readers
  - fixes numerous SQL Injection, XSS, file exploits/redirection, and RCE security vulnerabilities
    - security fix (v2.3.0+) to prevent uploading files to wrong location, thanks to Balisong, CVE-ID 2016-7095, CVE-ID 2016-7443
    - security fix to prevent possible sql injections, thanks to Manuel Garcia Cardenas and PKAV TEAM, CVE-ID 2016-7400
    - security fix for rce issue, thanks to xiojunjie, CVE-ID 2016-7565
    - security fix to prevent possible sql injections and other vulnerabilities in pixidou editor, thanks to Manuel Garcia Cardenas, CVE-2016-7452 & CVE-2016-7453
    - security fix to prevent uploading dot files, thanks to DM_ PKAV Team & fyth
    - security fix popup.php, thanks to DM_ PKAV Team
    - security fix xss vulnerability in worldpay, thanks to felixk3y PKAV Team
    - security fix xss issue with uploader, thanks to fyth 
    - security fix to prevent possible hacking by moving security checks earlier in the install code, thanks to felixk3y PKAV Team
    - security fix for rce issue, thanks to xiojunjie, CVE-ID 2016-7565
  - other minor fixes and tweaks
### Known Issues
  - eCommerce purchase-order functionality has not been tested/is not be complete
  - Item re-ranking (ddrerank) doesn't account for aggregation
  - LDAP support may not work in all LDAP server scenarios due to limited testing

Version 2.3.9
-------------
### Address issues in v2.3.8
  - adds dynamic drag/drop form designer for bootstrap 3 based themes
  - adds much better small device support in elFinder and Bootstrap 3 based themes
  - adds new 'tax exempt' user group setting for ecommerce
  - adds import/export tags
  - speeds up many operations within Exponent
  - adds filter/search to event calendar administration and past views
  - adds support for Twitter twitter:xxx meta data
  - add (upload) image display in form module show, showall_portfolio (with no custom configuration), and email default_report views
  - we now display the graphic for an 'image' field in the confirm_data view
  - adds showall records button/link to form module (individual) show view
  - adds form module showall view setting to prevent individual record viewing
  - adds optional lightbox (jquery colorbox) for event on event module showall Upcoming Events Headlines view
  - updates bootstrap3 show product and photoalbum showall accordion views to use colorbox lightbox instead of yui3
  - adds new ecommerce search setting to only return ecommerce items or products in searches if desired
  - adds external calendar event caching and/or importing
  - now returns a 403 error if attempting to 'show' a missing item
  - now allows a CKEditor custom config.js file within theme /editors/ckeditor/config.js
  - !!! update/include .htaccess file in ALL /tmp folders to prevent security issues, CVE-ID 2016-7095 reported by Balisong
  - !!! fix security issue with database manager and pixidou editor: Security Advisory XS3C-2016-05-20 & XS3C-2016-05-19 reported by Julian Held
  - !!! regression fix several minify issues
  - !!! regression fix support for single param in url to search for product
  - !!! fix to actually skip over 'cgi-bin' for 'check permissions' upgrade scripts
  - !!! regression fix possible issue with extraneous directory separator in a phpThumb generated thumbnail
  - !!! regression fix ecommerce error when easypost shipping calculator isn't set up
  - !!! fix form next/prev issue
  - !!! regression fix remove redundant 'read more' link for news module showall views;
  - !!! regression fix file download module views redundant 'read more' links; add 'read more' link if configured for quick download without item body
  - !!! allow form control names 'email' & 'image' to be any case for processing in the views
  - !!! form module did not properly process a 'reloaded' uploadcontrol
  - !!! we now use field names in the form module showall and confirm_data views to properly process 'email' and 'image' fields
  - !!! regression fix form submission/display within multi-page datatables (user/group permissions & view event registrants)
  - plus many, many more minor tweaks and fixes, detailed list found in [changes-v239.md](changes-v239.md)
### Known Issues
  - eCommerce purchase-order functionality has not been tested/is not be complete
  - Item re-ranking (ddrerank) doesn't account for aggregation
  - LDAP support may not work in all LDAP server scenarios due to limited testing

Version 2.3.8
-------------
### Address issues in v2.3.7
  - !!! Security fix for malformed associative array injection
  - !!! no workflow (revisions/approval) feedback in text module inline edit view
	- !!! THIS FIX BREAKS EXISTING CUSTOM TEXT SHOWALL INLINE (EDIT) VIEWS
	- User now sees immediate workflow feedback when editing inline
  - !!! workflow styling missing in bootstrap 3
  - !!! regression fix unable to edit/copy existing calendar events
  - !!! regression fix problem selecting insert links with ckeditor within inline edit text module views
  - !!! fix import items (blog?) with attachments didn't import attachments
  - !!! regression fix manage categories would only work with 1st 50 system categories
  - !!! regression fix some blog views displayed default date/author/category instead of item's info
  - !!! regression fix re-order dialog in portfolio module odd styling
  - !!! regression fix unable to add new text item in text module inline edit view if no items already exist
  - !!! regression fix text item body in text module inline edit view passed as garbage on save
  - !!! fix file uploader control did not recognize 'accept' parameter in bootstrap2/3 themes
  - !!! fix getting mp3 file ID3 tag comments for more precise rss/podcast details
  - !!! regression fix bootstrap 2/3 navbar on small screens may not always drop down/appear
  - !!! fix upcoming events were displayed until end of day instead of until end of event
  - !!! regression fix page crash when returning to elFinder from Pixlr editor
  - !!! regression fix elFinder search broken under php v5.3
  - !!! regression fix database manager filtered table phrase not highlighted in table results
  - !!! regression fix possible errors with Simple Poll voting when time block in effect
  - !!! regression fix printer and pdf missing styling (invoice address lines run together, etc...)
  - !!! regression fix slingbar location not being set/recognized, esp. in NewUI/Bootstrap3
  - !!! regression fix unable to actually delete multiple photo album items
  - !!! fix event showall announcement view to properly advertise event rich data to search engines
  - !!! regression fix unable to edit blog/event (view broken) in non-bootstrap3 themes
  - !!! regression fix e-store quicklinks view module title display broken
  - !!! fix jquery broken in very old Firefox browsers
  - !!! fix bootstrap 3 carousel touch support wouldn't allow scrolling vertically
  - !!! regression fix remove recycle bin content from restore content popup (not view recycle bin view) used wrong 'action'
  - !!! regression fix text module inline edit view tinymce menu display on non-bs3
  - !!! fix Facebook Meta configuration tab content was invisible on bootstrap2 themes
  - !!! fix rearranging attached files could scroll the list out of site (bs3?)
  - !!! fix adding new form module will crash page if 'show' action initially selected (before form is assigned)
  - !!! regression fix text module inline edit views to correctly populate ddrerank/order dialog list
  - !!! regression fix unable to display images from avatars or uploads folders
  - !!! fix bootstrap2 container 'column' views
  - !!! regression fix for non-bs2/bs3 theme user permissions 'manage' checkbox broken on user pages greater than page 1
  - !!! regression fix event display/popup doesn't display entire event (graphic)
  - fix issue with form module show view if no records present
  - fix jquery datetimepicker didn't initially display/scroll to selected time
  - fix display of events calendar 'all day' marker if event is categorized (colored)
  - fix issue with form module show view if no records present
  - adds new SocialFeed module to aggregate facebook, twitter, instagram and/or pinterest posts
  - adds drag/drop support to attach files (file manager control)
  - new 'manage by sitemap' feature to better analyze and manage entire existing page structure
  - now allows for styling of 'tags', example in bootstrap3theme sample theme
  - we now attempt to email to user name instead of only using email address (more professional)
  - optional support for Kint debugging eDebug output (http://raveren.github.io/kint/)
  - replaced old custom browser fallback/polyfill support with Webshims library
  - adds module styling settings to help emphasize individual modules (borders, background & visibility)
  - attempts to suppress easypost error messages from USPS if shipping package over 70lbs
  - now forces a .less re-compile (if necessary) after an upgrade, required on sites with error-reporting off
  - fix issue with attempting to swipe up/down past bootstrap 3 carousel
  - adds new wysiwyg autosave feature to help recover from page crashes, etc...
  - now allows optional image and author selection per podcast/rss feed
  - better 'read more' implementation
  - elFinder cache now moved to its own /tmp folder
  - new global setting to Save Inline Editing Changes w/o Prompt
  - better implementation of setting (new) file/folder permissions (2 new optional upgrade scripts to assist)
  - styling tweaks to the sample bootstrap3theme
    - removes display of urls when printing from Chrome browser with a bootstrap 3 theme
    - updates bootstrap 3 rss/ical link icons to be orange regardless of font color
    - updates form input placeholder styles to look less like an entry
    - implements a twitter bootstrap 3 based date/time picker widget
    - implements a new slideshow/carousel for bootstrap 3 photoalbum (Owl Carousel 2)
  - CKEditor now used as WYSIWYG file editor within elFinder if set as system editor
  - adds an autosave feature to wysiwyg editors to help recover from page crashes
  - better implementation of theme styles within WYSIWYG editor format menus, esp in text module inline edit
  - adds WYSIWYG editor 'additional configs' setting to help with some custom plugin requirements
  - (finally) implements fonts, styles, & blocks toolbar configuration settings implementation for TinyMCE
  - adds image tools, image caption, and drag/drop image support for TinyMCE
  - adds quicktables and showborder (for tables) plugins to CKEditor
  - adds optional font icon selection for menu items in bootstrap 2/3 based themes; also allows suppressing the menu item text
  - adds optional server error document handling so we got to not-found instead of logging it as a search
  - adds readable date/time tooltip in database manager table view (like we do for serialized data)
  - adds a 'total bytes saved' to optimize database results view
  - adds passing an array of scripts through expJavascript::pushToFoot
  - adds warning messages is less compiler is unable to create output folder or file
  - adds support for optional HTML2PDF pdf engine
  - adds url single parameter router mapping for one module, e.g., products, product categories, blog posts, etc...
  - adds optional reduced and or grouped search results
  - adds prev, next, and add record actions to form module show record view
  - adds password strength meter to non-bs3 new password inputs; including installation
  - adds more options to form module if allowing User Selected Email Destination (radio or dropdown), etc...
  - adds optionally sending form entries back to user with response email
  - adds new 'filter' param for form module showall action
  - adds display of uploaded image in form module showall view (if upload control is named 'image')
  - adds 'copy' item action to news module; comes in handy if module is used for announcements
  - adds attached image display to events module Upcoming Events view
### Known Issues
  - eCommerce purchase-order functionality has not been tested/may not be complete
  - Item re-ranking (ddrerank) doesn't account for aggregation
  - LDAP support may not work in all LDAP server scenarios due to limited testing

Version 2.3.7
-------------
### Address issues in v2.3.6 esp. because it was pulled
  - !!! regression fix all styles were stripped from rich text upon save due to recent security fix
  - !!! regression fix an admin was able to possibly edit a super-admin user profile
  - !!! security fix elFinder would allow an authenticated user to upload an xss script then execute it CVE-ID 2015-8684
  - regression fix enhanced password hash strength would break all future logins due to stored hash field not being long enough (since v2.3.5)
    -- only occurred when upgrading from a version prior to v2.3.5 and only when increasing password crypto depth above 0
  - regression fix ajax paging would add 'time' parameter twice to calendar urls
  - regression fix ajax paging would add google analytics params to the urls
### Known Issues
  - eCommerce purchase-order functionality has not been tested/may not be complete
  - Item re-ranking (ddrerank) doesn't account for aggregation
  - LDAP support may not work in all LDAP server scenarios due to limited testing

Version 2.3.6 - release pulled
-------------
### Refine 'shipment' interface, plus address issues in v2.3.5
  * !!! adds additional security checking for XSS vulnerabilities - CVE-ID 2015-8667
  * !!! adds support for PHP v7.x
    - compatible with PHP v5.3.x, 5.4.x, 5.5.x, 5.6.x, and 7.0.x
  * !!! regression fix ALL reCaptcha responses always fail since v2.3.3
  - adds new 'loading' animation (font icon) for boostrap/bootstrap3
  - cleans up some bootstrap3 views, returns option of displaying extra-small buttons in sample theme
  - adds new setting to bootstrap/bootstrap3 themes to limit menu item depth in navbars
  - adds new setting to bootstrap3 theme to center main navbar (in addition to left & right alignment)
  - adds new optional paypalExpress 'in-context' checkout experience
  - adds two optional elFinder themes, also cleans up default theme
  - better EAAS error and event record support (events now sent by date instead of by entry sequence)
  - much better (optional) ajax paging support
  - much better job of returning to previous pages
  - adds new optional upgrade script to quickly clean up files database (adds new files, removes missing files)
  - includes all fixes from v2.3.5 patches (#1 & #2)
### Known Issues
  - eCommerce purchase-order functionality has not been tested/may not be complete
  - Item re-ranking (ddrerank) doesn't account for aggregation
  - LDAP support may not work in all LDAP server scenarios due to limited testing

Version 2.3.5
-------------
### Finish removing YUI3 code/widgets from Bootstrap3 views, plus address issues in v2.3.4
  - adds bootstrap 3 variation to event & news announcement view using 'panels'
  - enhances elFinder/TinyMCE/CKEditor integration; CKEditor now allows paste/drop images
  - adds Facebook og: meta tag support
  - adds remote blog post editing (xmlrpc); this feature is turned off by default
  - adds new easypost ecommerce shipping calculator includes order fulfillment functions
  - improves security by allowing admin controlled password strength settings and more secure password hashing
  - includes all fixes from v2.3.4 patches (#1)
### Known Issues
  - eCommerce purchase-order functionality has not been tested/may not be complete
  - Item re-ranking (ddrerank) doesn't account for aggregation
  - LDAP support may not work in all LDAP server scenarios due to limited testing

Version 2.3.4
-------------
### Finish removing YUI2 code/widgets, plus address issues in v2.3.3
  * !!! fixes an XSS vulnerability in source_selector.php/selector.php
  * regression fix for issues caused by previous security fix (json data could be corrupted) unable to delete files, etc..
  - adds 'announcement' view to events module
  - adds alternate color to 'featured' items in announcement views
  - updates twitter view to more closely resemble twitter.com
  - updates optional ajax paging to be seo friendly; site-wide setting now on display tab of site configuration
  - adds 'empty recycle bin' feature to remove all items in recycle bin
  - add quick image upload button to TinyMCE editor
  - adds most recent event date to search hit for events and event registrations
  - now only indexes content from active modules instead of every module
  - updates several bootstrap3 ecommerce views to more closely follow bootstrap3 styling
    -- newly styled invoices and packing slips
  - new product option: must be purchased in multiple quantities
  - ecommerce invoice payment info is now more customer friendly when not managing order(s)
  - ecommerce store database sample now includes product options, discounts, and sample orders
  - reCaptcha anti-spam support updated to latest library
  - reactivates split credit card ecommerce payment option
  - adds optional 'time till site returns' countdown clock to maintenance view
    -- login removed from maintenance view unless attempting to logon or if db is down
  - after adding a new module which requires configuration, the system now displays the module configuration settings page
  - includes all fixes from v2.3.3 patches (#1)
### Known Issues
  - eCommerce purchase order functionality has not been tested/may not be complete
  - Item re-ranking (ddrerank) doesn't account for aggregation
  - LDAP support may not work in all LDAP server scenarios due to limited testing

Version 2.3.3
-------------
### Address issues in v2.3.2
  * !!! Removes support for PHP v5.2.x (primarily due to included 3rd party libraries)
    - Compatible with PHP v5.3.x, 5.4.x, 5.5.x, and 5.6.x
  - adds feature to delete multiple photo album items at one time
  - adds a 'hide links' option to events module views
  - updates elFinder to be the default file manager
    - fixes 'create' and 'extract' archive files feature
    - better 'touch' support
    - better implementation of 'Places', the folder bookmarking feature
    - changes logging to now only occur if both error reporting & logging are turned on
    - fixes interaction with Pixlr image editor
  - E-Commerce updates
    - adds a bootstrap3 product show view suitable for small device viewing
    - adds 'configure category' link to ecommerce views where we can edit a store category
    - adds user selectable product option display features (segregate required/non-required options, show on product page)
    - adds additional fields to product import/export; weight, width, height, length, manufacturer, and importing images by url
    - updates import product to be more intelligent based on column header names; allows model/sku matching; accepts Mac format files
    - add store category export/import
    - adds store category thumbnail display to manage store categories tree
    - adds a cron script to import products useful for updating inventory levels
    - fixes several issues with simple notes for products & orders; adds simple wysiwyg formatting
    - adds new optional upgrade script to trim orphan ecommerce database records (tables can grow huge)
  - adds support for optional mPDF v6.0 pdf creation library
  - includes all fixes from v2.3.2 patches (#1 & #2)
### Known Issues
  - eCommerce gift card & purchase order functionality have not been tested/may not be complete
  - Item re-ranking (ddrerank) doesn't account for aggregation
  - LDAP support may not work in all LDAP server scenarios due to limited testing

Version 2.3.2
-------------
### Address issues in v2.3.1
  * !!! Fixes possible cross-site security (XSS) issue, CVE-ID 2014-6635 & CVE-ID 2014-8690
  - adds 'freeform' (modules) menu item and icon support to bootstrap 3 menu
  - adds bootstrap 2 multi-column container module views
  - adds lightbox option to mediaplayer showall view, allows grid of icons
  - adds optional 'date badge' to motd show view
  - adds (fixes) bootstrap 3 form 'horizontal' controls (label beside control instead of above)
  - fixes 'column' styling of several 'login' views
  - updates many ecommerce features, especially for bootstrap 3
    - adds ecommerce navigation 'breadcrumb' on store show and showall views
    - adds bootstrap3 showall customer selectable 'list' view in addition to standard 'grid' view
    - adds product display 'sort by' control
    - adds customer checkout breadcrumb to indicate checkout progress
    - new filter-able and color coded manage orders view
    - completely revised bootstrap 'show order' view for easier order management
    - adds better sorting and filtering for manage products
    - adds new shipping calculators - by item & by weight
    - changes ecommerce 'meta' tags to display store name instead of site name
    - now allows for either origin or destination sales tax; taxable shipping costs; entered tax rates may now be disabled
      - US states sales tax tables now installed with store sample database
  - adds optional 0.9x theme compatibility by setting OLD_THEME_COMPATIBLE constant
  - updates database manager display to be 'responsive' for smaller screens
  - includes all fixes from v2.3.1 patches (#1, #2, #3, & #4)
### Known Issues
  - eCommerce gift card & purchase order functionality have not been tested/may not be complete
  - Item re-ranking (ddrerank) doesn't account for aggregation
  - LDAP support may not work in all LDAP server scenarios due to limited testing

Version 2.3.1
-------------
### Refine implementation of TinyMCE, elFinder, & NewUI/Bootstrap3, remove YUI2 code/widgets, provide jQuery/Bootstrap code/widgets, plus address issues in v2.3.0
  * !!!Fixes regression bug where non-admin user login with workflow turned on always broke page
  * !!!Fixes regression bug where all checkboxes were either checked or unchecked
  * !!!Fixes regression bug where adding/editing a module would display a blank page or disable the save button
  * !!!Fixes loss of admin/super-admin status when password is changed
  * !!!Updates install/upgrade logic for greater security
  * !!!Fixes bug which could allow display of orders to non-admin users
  - php v5.6 compatible
  - updates ecommerce to be more robust with non-US areas (countries w/o regions/states)
    -- Much improved interface for managing sales tax and also countries/regions
  - adds many Twitter Bootstrap v3 widgets/components to that theme framework, moving away from YUI2/YUI3/jQueryUI
    -- many tweaks and fixes applied to the Twitter Bootstrap v3 theme framework
  - adds NEWUI & BS3 Slingbar 'bottom' location (can't be dragged/dropped, but only changed in site configuration)
  - allows upgrading a site where the config.php file is read-only to prevent hacking (w/ assoc. warnings)
  - adds 4-column container view
  - adds .less file compilation minification setting, and better error trapping on compilation error
  - fixes loading of ckeditor & tinymce if linked js is minified
  - adds new Portuguese translation
  - includes all fixes from v2.3.0 patches (#1, #2, #3, & #4)
### Known Issues
  - eCommerce gift card & purchase order functionality have not been tested/may not be complete
  - Item re-ranking (ddrerank) doesn't account for aggregation
  - LDAP support may not work in all LDAP server scenarios due to limited testing

  Version 2.3.0
-------------
### Implement revisions/workflow/approval, integrate TinyMCE WYSIWYG Editor, implement form data filtering both actively in the view, plus address issues in v2.2.3
  - updated default bootstrap theme (and the files to support it) to be more mobile friendly (responsive), esp. in tables
  - adds new touch enabled, responsive photo album slideshow view (default slideshow for bootstrap)
  - adds new optional search/filter to portfolio & faq showall views
  - adds new optional workflow features (revisions and approval) to blog, news, & text modules
  - module and item heading levels are now selectable within module configuration settings
  - adds optional 'websnapr' link thumbnail support to links module
  - adds module specific quick upload folder selection
  - updates several 'widgets' from YUI to jQuery variants (tag picker, calendar, list builder color picker, lightbox)
  - initial support for alternate/optional WYSIWYG editor - TinyMCE (works on Android devices)
  - initial support for alternate/optional file manager - elFinder (uses an OS file manager paradigm)
  - adds optional 'hidden' controls (checkbox, text, textarea) to forms to allow (prefilled) data fields unavailable on initial entry, but can be updated later (e.g. paid, notes)
  - adds new 'church site' site sample database in addition to 'sample site', 'blog' and 'eCommerce store' during installation
  - vastly improved SEO for events, eventregistrations, & products
    -- event & product data (dates, cost, reviews, etc...) now available to search engine as rich snippets
    -- more accurate meta data made available to search engines
  - enhances the import/export abilities for transferring site content/data from other sites or in other formats
  - adds Google+ link to blog author signature option for linking to search hits on Google (profile/picture)
  - adds new search/filter & sorting to manage permissions, users, groups and forms showall views
  - adds new 'reset.php' file to allow easily clearing all caches if the site crashes when designing/changing themes
  - includes all fixes from v2.2.3 patches (#1 to #10)
### Known Issues
  - eCommerce gift card & purchase order functionality have not been tested/may not be complete
  - Item re-ranking (ddrerank) doesn't account for aggregation
  - LDAP support may not work in all LDAP server scenarios due to limited testing

----------

Version 2.2.3
-------------
### Address issues in v2.2.2 and finally remove features deprecated in v2.2.0
  * !!! changes use/function of the 'create' permission
    -- 'create' permission is NO longer automatically tied to an 'edit' permission
    -- a user with the 'create' permission may also edit and delete items which they have created
    -- an 'edit' permission is required to edit other users' items and likewise for delete
    -- an 'edit' permission by itself will NOT allow creation of new items
  - adds user group 'global' permissions/restrictions to: prevent file uploading, prevent user profile changes, hide individual exponent/slingbar menus, or the entire slingbar
    -- restrictions apply to all non-admin users assigned to that user group
  - now enforces non-public page and hidden module restrictions to content on/in those pages/modules to prevent access by search engines, etc...
  - adds 'noindex' and 'nofollow' SEO meta tag options to pages/items to prevent addition to search engines
  * revises navigation flyout sidebar view to display module title vertically and allow more than one navigation flyout sidebar on a page
    ** NOTE, any previous use of the navigation flyout sidebar contents will be invisible since we move away from a single hard-coded source reference!
  - adds forms showall view data filtering to module configuration settings
  - adds 'Page' summary type to showall views to allow an editor inserted 'page break' to determine content displayed in list view
  - adds force image auto-resize and folder on quick-upload/add
  - adds new 'dim controls' to slideshow views to only display slide controls when the cursor is over the slide
  - adds copy portfolio item command
  - adds new login 'show Login only' view
  - adds slideshow transition options (some combinations do NOT work together)
  - adds new optional universal PDF generation via mPDF, PDF generator engine now selectible in site configuration
  - changes calendar ajax pagination to become optional (default is off)
  - better theme support for mobile devices with theme configurable 'meta viewport' and optional apple-touch-icon implementation
  - 0.9x theme support is removed
  - Flowplayer & YouTube modules are removed
  - includes all fixes from v2.2.2 patches
### Known Issues
  - eCommerce gift card & purchase order functionality have not been tested/may not be complete
  - Item re-ranking (ddrerank) doesn't account for aggregation
  - LDAP support may not work in all LDAP server scenarios due to limited testing

----------

Version 2.2.2
-------------
### Address issues in v2.2.1, enhance SEO and Social Media features
  - greatly enhances default page meta data (SEO out of the box)
    -- changes 'show item' page meta description to fallback to item summary for better display by Facebook when sharing links
    -- uses item tags in 'show' view for keywords when no meta keywords available instead of defaulting to site keywords
  - adds more social media features
    -- new optional facebook like & tweet button to news posts
    -- new optional auto facebook post/tweet to blog posts, news items, file downloads, & events
  - adds showall news by date method
  - adds a new 'toggle' view to faq display
  - adds 'word match only' setting for search results
  - enhances eCommerce with many fixes and new features to products and event registrations
  - better user feedback for max file upload size and resulting errors
  - adds nested help documents
### Known Issues
  - 0.9x theme support is deprecated but still present in this distribution, it will be removed in the near future
  - Flowplayer & YouTube modules are deprecated but still present in this distribution, they will be removed in the near future
    -- There is no media player migration script, but you can migrate to flowplayer/youtube modules, then run the media player upgrade script
  - eCommerce gift card & purchase order functionality have not been tested/may not be complete
  - Item re-ranking (ddrerank) doesn't account for aggregation
  - LDAP support may not work in all LDAP server scenarios due to limited testing

----------

Version 2.2.1
-------------
### Address issues in v2.2.0 and streamline integration of Twitter-Bootstrap/jQuery
  - php v5.5 compatible
  - new facebook module to display like buttons, like boxes, and optional like button to blog articles & file downloads
  - adds optional tweet button to blog articles & file downloads, and optional twitter follow button to twitter view
  - adds paged form (wizard) feature
  - adds form design export/import
  - adds form report designer insert field command to editor
  - Message of the Day module now accepts WYSIWYG text and offers an 'every month' on this date option
  - adds a rudimentary site configuration profile (backup/restore) feature
  - adds optional author signature to blog posts, handled by user profile extension
  - now supports multiple simultaneous file uploads for 'quick add' uploads w/ new progress indicator (if browser supports)
  - recycle bin is now more consistent...all removed modules sent to recycle bin, all modules removed from recycle bin have all items deleted
  - adds 'hide module title' setting to the add/create module view
  - adds ldap user sync to update all ldap users against ldap server data (email, first/last name)
  - no longer automatically loads bootstrap.min.js file, explicit loading of individual scripts as required
  - includes all fixes from v2.2.0 patches
### Known Issues
  - 0.9x theme support is deprecated but still present in this distribution, it will be removed in the near future
  - Flowplayer & YouTube modules are deprecated but still present in this distribution, they will be removed in the near future
    -- There is no media player migration script, but you can migrate to flowplayer/youtube modules, then run the media player upgrade script
  - eCommerce gift card & purchase order functionality have not been tested/may not be complete
  - Item re-ranking (ddrerank) doesn't account for aggregation
  - LDAP support may not work in all LDAP server scenarios due to limited testing

----------

Version 2.2.0
-------------
### The first 'pure 2.0' version of Exponent w/o any 1.0 modules, etc..., primarily implements Container 2.0 and integrates Twitter-Bootstrap/jQuery
  - Fixes CVE-ID 2013-3294 SQL Injection vulnerabilities
  - Fixes CVE-ID 2013-3295 Directory traversal vulnerability
  - Removes all the 'old school' 1.x files
    -- removes deprecated headline controller (converting them to text modules), suggest a custom text module 'headline' view be created if needed
    -- forces deprecation/removal of formmodule/formbuilder
    -- replaces the containermodule with container 2.0 controller
    -- also fully deprecates/removes calendarmodule, simplepollmodule, & navigationmodule if they still exist
  - moves to jQuery/Twitter-Bootstrap as primary libraries over YUI2/3 (which are still backwards compatible)
    -- adds normalize.css and Twitter Bootstrap as system (theme) features
  - implements an html5 input fallback system to display html5 controls/features in older browsers
    -- adds more simple controls to forms designer (email, url, telephone, range, number, several date/time controls)
    -- allows switching to similar type control after creation
  - updates style of maintenance/site-down page
  - tweaks and more features to ecommerce (esp. online donations and event registrations) making it more robust and polished
    -- adds single or multi-person event registration using site forms to collect registration data
    -- quick add donation at set amount or allow user to enter amount
    -- event registrations now abide by 'options'
  - adds new forms showall portfolio view for multi-record custom view (fixes custom single record view)
    -- adds new import form data, or create a form from csv file feature
  - implements broader use of ajax paging and use of new html5 input types, temporarily adds additional date/time form designer controls
  - enhances attached file display features
  - adds new html5 media player module for audio, video & youtube, which deprecates both flowplayer and youtube modules
    -- updates filedownload module media player to html5 (flash not required)
  - adds new import users from csv file feature
  - adds ability to optionally run selected 'upgrade scripts' outside of installation
  - moves /conf folder inside /framework to help streamline folder structure
  - adds theme export feature
  - adds new 'blog' and 'eCommerce store'site sample database in addition to 'sample site' during installation
  - adds category support to blog module
    -- adds new comments and categories views to blog
  - adds file upload pause, resume, & cancel feature
  - enables user authentication via an LDAP server (requires php ldap module)
  - updates look of countdown module to be more professional looking with new display options
  - removes addressbook module from list of available modules since it's not designed to be placed on a page
### Known Issues
  - Flowplayer & YouTube modules are deprecated, but still present in this distribution, they will be removed in the future
    -- There is no media player migration script, but you can migrate to flowplayer/youtube modules, then run the media player upgrade script
  - eCommerce gift card & purchase order functionality have not been tested/may not be complete
  - Item re-ranking (ddrerank) doesn't account for aggregation
  - LDAP support may not work in all LDAP server scenarios due to limited testing

----------

Version 2.1.4
-------------
### This release only fixes bugs found in 2.1.3, everything else is destined for the v2.2 major version update
 - updates EQL file export/import to also include table definition for future proofing backups
 - adds new 'Exponent as a Service' module for JSON service calls
 - fixes Online Event Registrations activating many features, adds email registrants
 - fixes over 3 dozen issues found in 2.1.3

----------

Version 2.1.3
-------------
### This release only fixes bugs found in 2.1.2, everything else is destined for the v2.2 major version update
 - fixes form copy feature
 - fixes a 'file manager doesn't display files' issue on some servers
 - makes blog aggregation configuration tab more intuitive
 - fixes some migration issues with forms, events, & navigation

----------

Version 2.1.2
-------------
### This release focuses on things not completed in 2.1.1, but not destined for the v2.2 major version update
 - adds category and tag assignment to multi-add photo album feature
 - adds some tooltip information in file manager (file and image sizes) and form manager (number of records and controls)
 - cleans up form assignment to a module by using manage forms instead of module configuration settings
 - fixes over a dozen bugs and anomalies from previous versions

----------

Version 2.1.1
-------------
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

----------

Version 2.1.0
-------------
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

----------

Version 2.0.9
-------------
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

----------

Version 2.0.8
-------------
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

----------

Version 2.0.7
-------------
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

----------

Version 2.0.6
-------------
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

----------

Version 2.0.5
-------------
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

----------

Version 2.0.4
-------------
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

----------

Version 2.0.3
-------------
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

----------

Version 2.0.2
-------------
### This release focuses on integrating Smarty v3
  - Custom themes/views must be updated to follow Smarty syntax (v2 allowed sloppy syntax)
### 3rd party libraries updated
### E-commerce updates
### Several new or enhanced features
  - New auto-complete feature with 'tag' selection
### Fixes numerous bugs and other issues; incorporates 3 patches to v2.0.1
### All 1.0 subsystems & FCKeditor now fully deprecated

----------

Version 2.0.1
-------------
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

----------

Version 2.0.0 (stable)
-------------
### The first stable release
