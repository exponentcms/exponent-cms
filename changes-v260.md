Version 2.6.0 - Specific changes from previous version
------------------------------------------------------

## v260

###v260 adds these features to v251 and previous patches:
- add initial support for PHP v8 (removes ob_gzhandler processing and deprecated code)
- add ability to select web pages for more easy internal link item creation
- add optional .less/.scss auto-prefixer support (requires php-autoprefixer installation)
- add initial support for bootstrap5 framework (requires optional package)
- update exportPDF support to include latest PDF libraries (optional addons)
- changed sample db snippet module to use javascript instead of flash example

###v260 fixes these issues in v251 and previous patches:
- regression fix (v2.5.1p2) EAAS won't properly decode apikey and fails on all calls
- regression fix (v2.5.1p1) mysql strict mode (sessionticket/billingmethods records)
- regression fix (v2.5.1p1) mysql strict mode can't update blog, news, or filedownload items
- regression fix (v2.5.1p1) mysql strict mode ecommerce unable to add items to the cart
- fix display of bs4 radio groups
- fix list of pages available for internal alias pages
- regression fix attachItem() function to actually replace existing attachments
- regression fix (v251p2) bs4 cdn loading bs3 script when minified was on
- fix 'state' dropdown not re-populated correctly
- fix parsing issue affecting search term pagination
- fix import ics events adds 'n' on new lines
- fix display of bs3/bs4 file uploader widget
- regression fix ups/fedex shipping configuration of shipping methods not properly redisplayed
- prevent accidently breaking form during design by now requiring control id (input validation)
- regression fix ajax javascript load didn't load bs4/bs5 version nor cdn which caused errors/anomalies
- renamed login.php to logmein.php to prevent autoloading as a loginController module
- regressioin fix ajax call loaded bootstrap v3 modules instead of bootstrap v4 scripts

###v260 updates these 3rd party libraries in v251 and previous patches:
- datatables to v1.10.22
- datatables checkboxes to v1.2.12
- tinymce v5 to v5.6.1
- tinymce v4 to v4.9.11
- ckeditor to v4.15.1
- moment.js to v2.29.1
- validate to v1.19.2
- scssphp to v1.4.0
- less.php to v3.1.0 for up to php 8 compatibility
- elFinder to v2.1.57
- fontawesome v5 to v5.15.1
- popper.js to v1.16.1
- jstree to v3.3.10
- jquery migrate to v3.3.1
- simplepie to v1.5.6
- ReCaptcha to v1.2.4  
- EmailValidator to v2.1.24
- lexer to v1.2.1
- getid3 to v1.9.20
- codemirror cdn to v5.58.3
- aceeditor cdn to v1.4.12
- (fix) bootstrap4 cdn for v4.5.3
- bootstrap/bootswatch v4 to v4.5.3


###v251patch3 adds these features to v251 and previous patches:
- adds persistence (within session) to dismissed news alert items
- add facebook og:site_name & og:locale meta tags to pages
- add module configuration border styles for left/right/left-right border (however styles must style be defined like in sample themes)

###v251patch3 fixes these issues in v251 and previous patches:
- regression fix (v2.5.1p2) file download rss feed fails
- fix bootstrap 4 sample theme to properly move side column to left
- fix sorting by column for some store reports
- fix bs4 module configuration visibility styles options not displayed
- regression fix (v2.5.1p1) changes in jstree page hierarchy change page alias type to null
- regression fix broken less/scss source maps

###v251patch3 updates these 3rd party libraries in v251 and previous patches:
- jquery to v3.5.1
- jquery-migrate to v3.3.0
- easypost library to v3.4.5
- bootstrap/bootswatch to v4.5.0
- scssphp to v1.1.0 (not actually updated in last patch)


###v251patch2 adds these features to v251 and previous patches:
- switches to CDN for jquery, bootstrap, font-awesome & yui3, which can optionally be turned off
- add optional faster simplified permissions system (site config, ecom tab), users are either a basic user or an admin
- add (limited initial) support for TinyMCE v5 WYSIWYG Editor
- add manage site rss feeds to admin menu
- add optional dismissable for featured news announcements in bs3/4
- add content item link in manage comments list
- add improved display of unapproved comments within post/item display and allow approval
- add universal gravatar support to comments instead of only for Exponent users
- add a limit # years option to blog dates view
- add media info support for video (audio & image) files using getID3 library
- add image dimensions to file picker list widget
- add product show a thumbnail as main image on hover without lightbox

###v251patch2 fixes these issues in v251 and previous patches:
- fix (regression v2.5.1p1) many MySQL 'strict' mode anomalies such as...
  - new or copied forms don't work correctly
  - unable to create new news items
  - unable to attach/add categories and non-image file items
- TinyMCE missing essential plugins when only a few custom plugins added
- fix (regression v2.4.2) import files broken on non-MS servers
- fix bs3/4 color control size obscured by validation marker
- fix missing icons in some bs4 management views
- fix (regression v2.2.0) categorized blog list displays sorted by category instead of sorted by entire list
- removes Google+ Signature since Google+ no longer exists
- fix some possible security issues
- fix social feed facebook posts not displaying image
- fix manage comments sort by column
- update code to better utilize system attribution style for names (RSS Feed Item author, etc...)
- fixes broken sort feature on bs4 item order and file manager dialogs
- fix elFinder resize to update image width/height, etc in the database
- re-assess all files filesize, width & height. etc on update

###v251patch2 updates these 3rd party libraries in v251 and previous patches:
- tinymce to v4.9.10
- tinymce v5.2.2
- scssphp to v1.1.0
- elFinder to v2.1.56 (plus jQuery v3.5.0 patch)
- jquery to v3.5.0 (reverted use to v3.4.1)
- jquery migrate to v3.2.0 (reverted use to v3.1.0)
- smarty to v3.1.36
- ace editor cdn link to v1.4.11
- codemirror cdn link to v5.53.2
- getID3.php v1.9.19
- moment.js to v2.25.0
- simplepie to v1.5.5
- phpThumb to 1.7.15


###v251patch1 adds these features to v251:
- support for MySQL v5.7+ 'strict' mode
- PHP v7.4 compatibility
- phone number validation when editing addresses

###v251patch1 fixes these issues in v251:
- fix router maps for specific word match instead of stopping at partial match
- fix update theme configuration code

###v251patch1 updates these 3rd party libraries in v251:
- ckeditor to v4.14.0
- tinymce to v4.9.9
- mediaelement.js to v4.2.16, mediaelement-plugins to v2.5.1
- less.php to v3.0.0 for up to php 7.3(7.4?) compatibility
- select2 to v4.0.13
- elFinder to v2.1.55
- easypost sdk to v3.4.4
- xmlrpc-php to v4.4.2
- EmailValidator to v2.1.17
- fontawesome v5 to v5.13.0
- codemirror cdn to v5.52.2
