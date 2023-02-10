Version 2.7.1 - Specific changes from previous version
------------------------------------------------------

## v2.7.1

### v271 adds these features to v270 and previous patches:
- add global xmlrpc debugging switch
- display 'no files' for filedownloads module if applicable
- update forms views to display module title/desc if no report title/desc, better handle actions display
- add dropdown product type selection to manage products
- expHtmlToPDF support for Dompdf v2+
- add do-not-index indicator to manage pages by sitemap status
- set email reply-to to actual sender when smtp send requires smtp username

### v271 fixes these issues in v270 and previous patches:
- change default pdf output from A4 to Letter
- remove exlicit obgzhandler from ob_start() calls, since it conflicts with many server setups
- regression fix (v270) product store categories unable to be selected
- fix size of tinymce editor so it isn't so small
- regression fix (v270p2) only use smtp username to send mail logic broken
- fix podcast categories parsing crash on php v7.4+
- fix export db or files input filename disregarded
- regression fix pagination ribbon when sorting by category to trim total records
- regression fix aggregation anomaly prevented blog post next/prev links
- regression fix (v270p1) new bs5 datepicker not passing good date
- 
### v271 updates these 3rd party libraries in v270 and previous patches:
- bootstrap-icons to v1.10.3
- EmailValidator to v3.2.4
- Lexer to v2.1.0
- Deprecations to v1.0.0
- phpxmlrpc to v4.9.5
- phpThumb to v1.7.20
- less.php to v3.2.0 with some custom php v5.6 regregssion code changes
- jstree to v3.3.14
- ace editor cdn to v1.15.0
- datatables to v1.13.2
- fontawesome to v6.3.0

## v2.7.0 Patch #2 - Dec 23, 2022

### v270patch2 adds these features to v270 and previous patches:
- adds better support for php 8.2 deprecations
- activate file manager trash feature
- allow file manager theme selection from file manager preferences
- add tinymce v5 editor (functional with new tinymce v4/v5 file manager implementation)
- add optional selection of store home page, or page after adding item to cart
- update non-bs datatables views to use standard styling instead of jqueryui
- add object type name to db manager unserialized info
- update control types for better styling, includes upgrade script
- add/fix maintenance view countdown clock (never worked), use proper theme framework view
- add asc/desc to sort forms records after grouping
- add forms portfolio view user dropdown filter option
- add option to export all form columns in CSV instead of only those selected for display
- add smtp option to only use from smtp username for very strict smtp servers

### v270patch2 fixes these issues in v270 and previous patches:
- regression fix (v270p1) calendar month selector popup
- fix tinymce5 support in bs4/5 broken & had no file manager support
- fix multiple issues with tinymce/tinymce5 custom configurations
- fix editing existing blog, filedownload, or news item (view) may crash on php v8 
- clean form control styling and processing, esp. bs5
- update expUnserialize function to include 'message' in countdownController
- fix multiple errors in countdownController default view and with newer smarty/php versions
- fix file manger root files and folders were locked for super-admins and coundn't be removed
- remove links from Workflow Revisions list to prevent issues
- (v270) regression fix Forms search cumulating records issue
- fix bootstrap.js v5 cdn wrong version
- fix extraneous datatables styles were being loaded
- fix turning off antispam causes views with antipam to crash (reset password, etc...)
- fix stepy form wizard to not require 'title' attribute for form pages
- fix bs5 accordion view in photo & filedownload has group title missing
- fix bs5 news configure module view broken
- fix bs5 pagination ribbon broken
- fix bs5 manage orders datetimepicker icons

### v270patch2 updates these 3rd party libraries in v270 and previous patches:
- tinymce5 to v5.10.9
- ckeditor to v4.20.1
- bootstrap-icons to v1.10.2
- font-awesome to v6.2.1
- tempus-dominus to v6.2.10
- phpxmlrpc to v4.9.3
- datatables.js to v1.13.1
- jquery.datetimepicker to v2.5.21
- jquery.countdown to v1.1
- less.php minor updates, no version change
- bootstrap/bootswatch to v5.2.3
- ace editor cdn to v1.14.0
- codemirror cdn to v5.65.11
- phpthumb to v1.7.19
- smarty to v4.3.0
- bootbox to v6.0.0
- jquery to v3.6.3

## v2.7.0 Patch #1 - Nov 2, 2022

### v270patch1 adds these features to v270:
- updated datetimepicker widget to newer tempus-dominus for bootstrap 3/4/5

### v270patch1 fixes these issues in v270:
- removed several php v8.1 warnings
- fix recyclebin to only display items within orphan module instead of all aggregated content
- fix issues where hard-coded modules would be marked as being in the recycle bin after running a repair
- regression fix some bs5 hidden elements were being shown (sr-only deprecation)
- regression fix (270) failed to include smarty lexer files
- update/fix select2 bootstrap themes for bs 4 & 5 (edit pages & manage orders)
- regression fix current file manager theme setting not displayed in site configuration
- regression fix unable to turn off several site configuration display options (ajax paging, old browser support, bootstrap icons)

### v270patch1 updates these 3rd party libraries in v270:
- bootstrap-duallistbox to v3.0.9/4.0.2
- jquery to v3.6.1
- jquery-migrate to v3.4.0
- jquery-ui to 1.13.2
- font-awesome to v6.2.0
- class.upload.php to v2.1.3
- scssphp to v1.11.0
- mediaelement.js to v5.1.0
- ace editor cdn to v1.12.5
- codemirror cdn to v5.65.9
- ckeditor to v4.20.0
- bootstrap-datetimepicker to tempusdominus for bs3/bs4/bs5 - v4.17.49, v5.39.0, v6.2.6
- getID3 to v1.9.22
- phpThumb to v1.7.18
- simplepie to v1.7.0
- smarty to v4.2.1
- bootstrap/bootswatch to v5.2.2