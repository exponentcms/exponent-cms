Version 2.5.0 - Specific changes from previous version
------------------------------------------------------

## v250

#### v250 adds these features to v243 and previous patches:
- update mediaplayer module to support more video url types, add better bootstrap 4 support
- add dynamic updates to the manage orders view
- add experimental ms sql server db support and odbc variant
- add option to select CSSMIN as the css minifier over JSMIN, revert to behavior prior to v2.4.3

#### v250 fixes these issues in v243 and previous patches:
- regression fix some bootstrap 4 controls (tag picker, listbuilder & datetimecontrol)
- regression fix missing bs4 views for filedownload & news modules
- regression fix bs4 announcement views
- regression fix bs4 datetimepicker popup not working
- fix logic of event showall_announcement view without feedback
- regression fix socialfeed bs4 configuration/views
- regression fix some ldap user issues
- regression fix forms wysiwyg text area control escaped output which defeated its purpose
- regression fix IIS not recognized as SEF compatible during installation
- regression fix small db manage products views (bs3/bs4) to display price for all product types; also fix bs4 image and product link in that view
- regression fix (v2.3.7) paypal express checkout in-context option
- regression fix shipping/billing calculators didn't select bs4 configuration template if available
- regression fix (v2.3.8p1) easypost shipping calculator doesn't work
- regression fix (v2.4.2p2) search index missing many modules
- regression fix single click form submission option not working

#### v250 updates these 3rd party libraries in v243 and previous patches:
- TinyMCE to v4.9.3
- CKEditor to v4.11.3
- Swiftmailer to v6.1.3
- jquery Validation to v1.19.0
- Font Awesome to v5.7.2
- jstree v3.3.7
- Jasny Bootstrap (fileinput) v3.2.0
- Normalize.css to v8.0.1
- iCalCreator to v2.26.8
- popper.js to v1.14.7
- elFinder to v2.1.48
- Twitter Bootstrap to v3.4.1, BootSwatch to v3.4.1
- Twitter Bootstrap to v4.3.1, BootSwatch to v4.3.1
- moment.js to v2.24.0
- fedex shipping wsdl to v24
- sortable.js to v1.8.3
- adminer to v4.7.1 (adds sqlserver support)
- smarty v3.1.33 (for php > v7.1.x, we include v3.1.27)

## v243patch1

#### v243patch1 adds these features to v243:
- removes support for PHP v5.5 (prepping way to eventually remove 5.6 & 7.0 also)
- adds optional support for elFinder ZohoOffice editor if api key is defined
- adds optional support for elFinder using MS Office online preview over Google Docs
- add generic facebook and twitter meta info to all pages, use theme logo if it exists
- add Kint v3 eDebug support (must be in folder /kint3)

#### v243patch1 fixes these issues in v243:
- language phrase libraries not updated prior to v243 release
- fix form wizard subheaders shown on small devices
- regression fix bs3/bs4 view orders unable to load select2-bootstrap.css file
- regression fix jstree fails to load if minify linked js turned on
- regression fix elFinder not setting file owner on file upload
- fix elFinder sets file owner to current (non-admin?) user if new file was found on server
- fix elFinder only file owner or admin may change file's shared, title or alt entries
- regression fix bootstrap 4 toggles widget
- regression fix bs4 slingbar picking up theme styles
- regression fix bs4 listbuilder control broke dialogs (form module configuration)
- regression fix bs4 hardcoded chrome style sometimes missing

#### v243patch1 updates these 3rd party libraries in v243:
- TinyMCE to v4.8.2
- ReCaptcha lib to v1.2.1
- popper.js to v 1.14.4
- elFinder to v2.1.42
- SimplePie to v1.5.2
- Font-Awesome to v5.3.1
