Version 2.4.2 - Specific changes from previous version
------------------------------------------------------

#### v242 adds these features to v241 previous releases:
- implement php v7.2 compatibility, removes create_function() calls
- remove support for php v5.3 and v5.4 
- remove support for obsolete browsers, optionally can be turned back on
- implement support for SwiftMailer v6.x if php version 7.0+; v5.4.8 used if older php version
- unserialize feature to the SQL Command output view of the database manager
- filedownload item attached file as podcast item image, if an image
- export to csv in portfolio module, so pieces could be imported as a form 
- wysiwyg editor option to text area in form design to allow wysiwyg form entry
- tweak forms portfolio view to optionally display 'grouping'; also allow paged support for that view
- update expCSS subsystem to allow passing scss files for compilation using scssprimer and scsscss params
- update elFinder windows 10 theme icons and add 3 new elFinder themes (bootstrap/libreicons, Material, & Material Gray)
- update bootstrap 3 listbuilder and datetimepicker to use font awesome icons instead of bootstrap glyphicons
- change bs3 chrome menus to use newui skin (helps when using custom styles)
- events showall_by_date action and showall_year view
- form record sef url support; change edit record save to update record instead of delete and recreate
- inline css compiler mapping output to iLess, less.php and scssphp
- add form validation feedback icons
- add motd csv file import, random message display, yearly calendar display of all messages available
- update social feed to better display facebook videos by an image rather than a simple link

#### v242 fixes these issues in v241 previous releases:
- fix elFinder upload fails without having php fileinfo module loaded
- fix old yui mega menu so it is usable within bootstrap themes (though not recommended)
- regression fix (240p1) bad regex param cleaning removes valid dash in src names
- regression fix (v237p3) unable to import files because of too strict regex param array cleaning
- fix feedcreator library to properly set itunes item image
- fix broken page on search output in some scenarios
- fix/standardize csv export/import issues in forms & event registration modules
- fix mangled output of portfolio view custom definition
- fix issues with form csv import and dropdownlist population
- fix ecommerce order management filtering by date
- fix input to bootstrap tags picker allowing 'enter' to select from list
- regression fix (v241p3) ckeditor control custom settings can cause crash in non-bs3 themes
- regression fix less compiler sourcemap issues with less.php (241p3 bad path) & iLess (crash)
- regression fix (v239) bootstrap 3 wysiwyg form designer drag/drop form control reranking logic was flawed
- regression fix (v2.0) MOTD module flawed logic
- fix mediaplayer displays to be responsive
- fix toggle objects might crash with languages using accents
- fix text module inline edit view would crash when reverting and no item title existed

#### v242 updates these 3rd party libraries in v241 previous releases:
- CKEditor to v4.8.0
- tinymce to v4.7.4
- elFinder to v2.1.30, adds dockable previewer, icon filetype tags
- plupload to v2.3.6 (v3.1.1 is latest)
- mediaelement to v4.2.7 and mediaelement plugins to v2.5.0
- jQuery to v3.2.1 (if old browser support turned off), also loads jQuery migrate v3.0.0
- swiftmailer to v6.0.2 (v5.4.8 remains for older php version 5.x support)
- minify to v2.3.3 (v3.0.2 is latest)
- twitter-api-php to v1.0.6
- xmlrpc.php to v4.3.0 (removed old v3 files)
- select2 to v4.0.5
- moment.js to v2.19.4
- strength-meter to v1.1.4
- datetimepicker to v2.5.14
- yadcf to v0.9.2
- scssphp to v0.7.1 (with hack to compile newui)
- EasyPost library to v3.4.0
- iCalCreator library to v2.24.0


#### v241patch6 adds these features to v241:
- update optional eDebug support for Kint v2.0
- font-awesome icon on page not found view in bootstrap2/3; adds search box if no similar results found
- more secure system password generator
- adds error checking to fileupload control to account for a file not being uploaded due to a server configuration issue

#### v241patch6 fixes these issues in v241:
- fix ie10 viewport bug workaround
- fix ealerts send, since expBot doesn't appear to be working correctly
- security fix for rouge admins attempting to elevate their permissions (thanks to chengable)
- regression fix (v241p5) valid custom wysiwyg configuration may break page
- fix the verify return shopper view (probably always broken?)
- regression fix (v240) unable to edit existing orders
- regression fix (v240) unable to add/activate user addresses (ecom)
- fix photoalbum bs3 slideshow view to optionally display text
- fix workflow issue which prevented updating item ranks/order
- fix fatal issue when attempting to view form data using default columns
- fixes several issues with workflow, esp. search index

#### v241patch6 updates these 3rd party libraries in v241:
- tinymce to v4.6.6, adds new help/about plugin
- ckeditor to v4.7.2, also updates autosave plugin
- elFinder to v2.1.28, updates edit file editors
- mediaelement to v4.2.5 and mediaelement plugins to v2.4.0
- normalize.css to v7.0.0
- swiftmailer to v5.4.8 (v6.x requires php v7+)
- easypost library to v3.3.5
- sortable jquery plugin to v1.6.0
- validate jquery plugin to v1.17.0
- xmlrpc to v4.2.0
- plupload to v2.3.3 (used by tinymce quickupload plugin)


#### v241patch5 adds these features to v241:
- change default password security to blowfish vs md5
- allow specifying events send_reminders view in url
- add jquery/bootstrap-3 based toggle widget

#### v241patch5 fixes these issues in v241:
- regression fix (v240) invalidating valid source names made some modules disappear
- fix styling issue with bs3 form designer 'Toggle Designer Grid'
- regression fix with links showall view links if the open new window option was selected
- fix possible xss security issue with elFinder (thanks to chengable)
- fix new socialfeed notes view photos on firefox and opera

#### v241patch5 updates these 3rd party libraries in v241:
- mediaelement.js to v4.0.6


#### v241patch4 adds NO features to v241:

#### v241patch4 fixes these issues in v241:
- fix issue with chrome (module dropdown menus) in bootstrap 3 nested containers (mainly tabbed)
- regression fix (v241p3) links have wrong id's

#### v241patch4 updates these 3rd party libraries in v241:
- database manager (adminer) to v4.3.1
- mediaelement.js to v4.0.5, mediaelement plugins to v2.1.1
- iCalcreator to v2.22.5


#### v241patch3 adds these features to v241:
- adds better serialized data preview feature to database manager
- adds optional support for dompdf v0.8.0
- adds default pdf paper size support
- update optional update script ecom3 to trim orphaned orders to last 7 days since system now auto-trims to last 30 days
- adds 'post it' style view variation to socialfeed module under bootstrap3
- now attempts to help prevent complete page crashes for improper custom wysiwyg editor configuration entries
- google maps now mandates an api key; added setting to general store settings

#### v241patch3 fixes these issues in v241:
- regression fix (v241) upload (zipped) extension/patch broken
- (regression?) fix some issues with eaas module image setting storage/retrieval
- fixes issue with dompdf v0.7.0 library detection
- regression fix google_maps plugin crashes on addresses with apostrophe (bootstrap 3 show order)
- regression fix (v240p1) unable to edit addresses for basic user
- regression fix (v240) saving (Facebook, etc...) 'app_id' corrupted entry
- fix manage orders/products sort by price/date links were bad
- fix many issues with sorting order/product reports, however still doesn't retain all settings between refreshes
- regression fix (v240) saving site configuration values with quotes added multiple slashes

#### v241patch3 updates these 3rd party libraries in v241:
- TinyMCE editor to v4.5.6
- ReCaptcha to v1.1.3
- Owl Carousel to v2.2.1
- SimpleAjaxUploader to v2.6.2
- mediaelement.js to v4.0.3, includes plugins v2.1.0
- adminer database manager to v4.3.0
- normalize.css to v6.0.0
- less.php to v1.7.0.14
- bootstrap-dialog to v1.35.4
- moment.js to v2.18.1
- elFinder to v2.1.23
- jstree to v3.3.4


#### v241patch2 adds these features to v241:
- update dynamic SEO page titles to reduce length

#### v241patch2 fixes these issues in v241:
- regression fix (v240) unable to update cart item quantities
- regression fix (v241) several elFinder upload/paste issues
- regression fix wildcard module name for action_maps.php (probably never worked correctly)
- security fix exploits using source_selector.php, reported by Belladona-c0re and croxy CVE-2017-6364
- regression fix some 500 errors when permissions or logged in checks fail

#### v241patch2 updates these 3rd party libraries in v241:
- bootstrap datetimepicker to v4.17.47
- easypost library to v3.3.3
- plupload to v2.3.1
- TinyMCE to v4.5.4
- elFinder to v2.1.22 to fix upload/mimetype (security) issues
- Sortable jquery plugin to v1.5.1
- less.php less compiler to v1.17.0.13 to bring less.js support from 1.7.0 to 2.5.3
- mediaelement.js to v3.2.3, includes plugins v1.2.2


#### v241patch1 adds NO features to v241:

#### v241patch1 fixes these issues in v241:
- fix fatal crash when sending emails
- Unrestricted File Deletion / Upload Vulnerability in elFinder, reported by mm

#### v241patch1 updates these 3rd party libraries in v241:
- TinyMCE to v4.5.2
- CKEditor to v4.6.2
- elFinder to v2.1.20
- mediaelement.js to v2.23.5