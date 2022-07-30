Version 2.7.0 - Specific changes from previous version
------------------------------------------------------

## v2.7.0

### v270 adds these features to v260 and previous patches:
- add Twitter Bootstrap v5 integration including bootswatches and sample theme
- add FontAwesome v6 support to Bootstrap v5
- add optional Twitter Bootstrap Icons to Bootstrap v5
- add author selection to blog post edit, fix show blogs by author order
- add ability to create a default module configuration for one-off views without a src
- add theme, framework, and db info to 'About ExponentCMS' menu, phpinfo window when PHP Version clicked
- add error trapping to installation for unable to connect to database
- add examples to router_maps for using portfolio or forms as a directory
- add optional add form records to search results
- add non-US currency support to ecommerce settings

### v270 fixes these issues in v260 and previous patches:
- fix XSS User Agent vulnerability reported by Oscar Uribe, CVE-2022-23049
- fix some php 8.1 fatal ecommerce and configure module anomalies
- fix php 8.1 mysql fatal errors during installation and reindexing and when workflow not turned on
- fix mysql server v8.0.19+ prevents saving some data
- regression fix less.php optional autoprefixer was always run
- regression fix module styles wrongly set to Box/Dark by default upon first save
- regression fix leaving events which ended earlier in the day which won't be displayed, cancels out tomorrow's event
- fix yuidatetimecontrol checkbox format in bs4/5
- fix save from inline editing text module item now updates search index
- fix removing some modules from recyclebin will delete ALL module items in the system
- regression fix inability to run Exponent scripts from cli
- regression fix (v251p2) username erased when editing using
- fix past events view is empty
- regression fix generic FB/TW images for blog/news/filedownload post w/o specific FB/TW image attached
- fix form db import fails in some scenarios
- regression fix (v260p1) ckeditor field insert plugin broken
- fix form report definition not populated on edit form
- regression fix (v260p2) xmlrpc broken
- fix db table primary key not always created on restore eb
- fix several EasyPost processing regressions
- fix unable to save complex wysiwyg entries in general store settings
- fix region/state dropdown control population after changing country selection
- quick fix PayPalExpress checkout error with discount and multiple items

### v270 updates/adds these 3rd party libraries in v260 and previous patches:
- Twitter Bootstrap v5.1.3
- Bootswatches v5.1.3
- FontAwesome v6.1.2
- Bootstrap Icons v1.9.1
- smarty v4.1.1
- easypost sdk v4.0.3
- datatables v1.12.1
- yadcf v0.9.4beta45
- scssphp v1.10.5
- lexer v1.2.3
- phpxmlrpc v4.8.0
- elFinder v2.1.61
- CKEditor to v4.19.1
- Ace Editor CDN v1.8.1
- CodeMirror CDN v5.65.7
- sortable v1.15.0
- simplepie v1.6.0
- moment.js v2.29.4
- tinymce v5.10.5
- emailvalidator to v3.2.1
- twitter bootstrap/bootswatch to v4.6.2
- jquery validation 1.19.5

## v2.6.0patch3

### v260patch3 adds these features to v260 and previous patches:
- add Font Awesome v6 support for Twitter Bootstrap v5 (requires BS5 addon)
- add bs4 specific responsive maintenance view
- add new store category option to display description instead of subcategories

### v260patch3 fixes these issues in v260 and previous patches:
- fix some bs5 support for non-CDN
- regression fix (v260) created_at date not set for new blog, filedownload, or news items
- regression fix (v251patch2) scssphp server doesn't include called .scss file into cached info
- regression fix (v260p2) mysqli rank keyword fix obscures order by rank configuration
- fix to update order addresses when they change
- regression fix (v243) font-awesome v5 file names were changed
- regression fix (v260p2) unable to save new/updated objects
- regression fix configure store category doesn't display saved 'show products' setting

### v260patch3 updates these 3rd party libraries in v260 and previous patches:
- scssphp to v1.10.0
- smarty to v4.0.4
- datatables to v1.11.4
- lexer to v1.2.2
- ckeditor to v4.17.2
- tinymce to v5.10.3
- mediaelement.js to v5.0.5

## v2.6.0patch2

### v260patch2 adds these features to v260 and previous patches:
- update support for Twitter Bootstrap v5.0 final (requires BS5 addon)
- add Bootstrap Icons option for Bootstrap5 framework (requires BS5 addon)
- add responsive bootstrap framework maintenance view
- allow custom (theme) administration views (_maintenance, _msg_queue, etc...)
- allow custom (theme) billing and shipping calculators (ecommerce)
- change db definition processing to prioritize custom (theme) definitions
- allow for optional loading of jquery migrate v3 (default is to load)
- add json data preview to adminer db manager
- add bootstrap styles to ckeditor in sample bootstrap themes
- add location based upcharges to flatrate, peritem, perweight, & tablebased shipping calculators
- activate parser caching in less & scss stylesheet compilers
- initial support for php v8.1
- update ecommerce dashboard to add 'last month' as new default period
- add optional support for Kint v4.0 enhance debugger output

### v260patch2 fixes these issues in v260 and previous patches:
- regression fix shipping option price list mislisted prices over $1,000
- regression fix bootstrap message queue views (allows responsive maintenance views)
- regression fix some passwords too long to store in db table
- regression fix several Bootstrap 5 issues (requires BS5 addon)
- regression fix forms not saving/viewing selected report columns
- fix possible sql injection vulnerability reported by pang0lin
- regression fix edit discounts date zeroization
- autoprefixer support was not fully implemented/coded
- regression fix radiogroups to select the first radio (0 value) when set
- regression fix attempting to edit/configure category within product edit fails
- fix possible Host Header Injection vulnerability reported by dumpling-soup CVE-2021-38751
- fix mysqli driver to work with MySQL v8 'rank' keyword
- regression fix file upload control and edit form image display issues

### v260patch2 updates these 3rd party libraries in v260 and previous patches:
- jQuery to v3.6.0
- jquery-migrate to v3.3.2
- fontawesome to v5.15.4
- Swiftmailer to v6.3.0
- Emailvalidator to v3.1.2
- elfinder to v2.1.60
- tinymce5 to v5.10.2
- ckeditor to v4.17.1
- plupload to v2.3.9
- EasyPost v4.0.2
- adminer db manager to v4.8.1
- code snippet codemirror editor cdn to v5.65.0
- code snippet ace editor cdn to v1.4.13
- scssphp to v1.9.0
- sortable.js to v1.14.0
- jquery datatables to v1.11.3
- jquery datatables.checkboxes.js to v 1.2.13
- jstree to v3.3.12
- getid3 to v1.9.21
- jqueryui to v1.13.0
- mediaelement to v5.0.4 & mediaelement-plugins to v2.6.2
- smarty to v4.0.0
- class.upload.php to v2.1.0
- bootstrap/bootswatch to v4.6.1
- simplepie to v1.5.8
- phpxmlrpc to v4.6.0

## v2.6.0patch1

### v260patch1 adds these features to v260:
- add help doc grandchildren display
- add Access Point Economy and Sure Post methods to available UPS shipping, also allow Negotiated Shipping

### v260patch1 fixes these issues in v260:
- regression fix sqlsvr/odbc warning
- fix php 8 regression mass mailing group listing
- regression fix (v251p2) adding/editing help doc deletes all search index items except new one
- regression fix paginator not formatting output of dates (order report, etc...)
- regression fix display shipping options if only one option
- fix fedex shipping calculator fails to return option if only one available
- regression fix manager users large db edit user link broken
- regression fix print buttons didn't display
- regression fix (v251p3) - mysqli db insertObject broke when first 'true' value hit - e.g. unable to login to store

### v260patch1 updates these 3rd party libraries in v260:
- adminer to v4.8.0 for php 8 fix
- swiftmailer to v6.2.5
- EmailValidator to v3.0.0
- phpxmlrpc to v4.5.2
- scssphp to v1.4.1
- jstree to v3.3.11
- FedEx WSDL to v28.0.0
- bootstrap/bootswatch to v4.6.0
- font-awesome to v5.15.2
- smarty to v3.1.39
- codemirror cdn to v5.59.3
- jquery validate to v1.19.3
- sortable.js to v1.13.0
- ckeditor to v4.16.0
- phpThumb t v1.7.16