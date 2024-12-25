Version 3.0.2 - Specific changes from previous version
------------------------------------------------------

## v3.0.2 - Jan 1, 2025

### v3.0.2 adds these features to v300rc1/v301 and previous patches:
- optional titles for containers (for sorting)
- add sort items feature to dropdown control form edit
- add small-device-friendly form design feature

### v3.0.2 fixes these issues in v300rc1/v301 and previous patches:
- fix group block label to account for multi-column form changes
- remove updating tables on eql restore to prevent timeouts
- fix preselected tables for Backup DB if db not set for mixed case table names
- regression fix (301p3) jquery validate crashes, esp. in BS 5
- regression fix form wizard/pages formatting broken in BS 4/5

### v3.0.2 updates these 3rd party libraries in v300rc1/v301 and previous patches:
- easypost sdk to v7.2.0
- less.php to v 5.1.2
- bootstrapicons to v1.11.3
- elFinder to v2.1.65
- ace editor cdn to v1.37.0
- codemirror cdn to v5.65.18
- mediaelement to v7.0.7
- mediaelement plugins to v5.0.0
- sortable to v1.15.4
- moment.js to v2.30.1
- tempus-dominus to v6.9.5
- bootstrap/bootswatch to v5.3.3
- smarty to v4.4.1
- datatables to v2.1.8
- datatables-checkboxes to v1.3.0
- font-awesome to v6.7.2
- tinymce 5 to v5.10.9
- jquery-migrate to v3.5.2
- jquery-ui to v1.14.1
- phpxmlrpc to v4.11.0
- yadcf.js to v0.9.6
- jquery validate to v1.21.0
- jstree to v3.3.17
- sortable.js to v1.15.4
- EmailValidator to v4.2.6
- Lexer to v3.0.1
- simplepie to v1.8.1
- CKEditor to v4.21.0 (reversion)

## v3.0.1 Patch #3 - Jan 1, 2024

### v301patch3 adds these features to v300rc1/v301 and previous patches:
- add multi-column form controls for BS 4/5
- add meta_fb to events so they offer the attached event image when pasted to Facebook
- better date sorting forms showall view
- add mutlti-select option for dropdown controls in form design

v301patch3 fixes these issues in v300rc1/v301 and previous patches:
- regression fix (v271p2) site crash on older php versions due to forms coding error
- regression fix (v271p2) upgrade could sometimes automatically be triggered
- fix fatal error forms view with order dropdown all
- regression fix old file manager image editor

v301patch3 updates these 3rd party libraries in v300rc1/v301 and previous patches:
- easypost sdk to v7.0.0
- less.php to v 4.1.1
- ace editor cdn to v1.32.3
- codemirror cdn to v5.65.16
- deprecations to v1.1.2
- EmailValidator to v3.2.6
- elFinder to v2.1.64
- Bootstrap to v5.3.2
- Bootswatch to v5.3.1
- Bootstrap Icons to v1.11.2
- Tempus Dominus v6.9.4
- Datatables to v1.13.8
- datables checkboxes to 1.2.14
- CKEditor to v4.22.1
- smarty to v4.3.2
- yadcf to v0.9.4b46
- phpThumb to v1.7.22
- fontawesome to v6.5.1
- mediaelement to v7.0.2
- mediaelement plugins to v2.6.7
- jquery to v3.7.1
- class.upload to v2.1.6
- Smarty to v4.3.4
- scssphp to v1.11.1
- jquery.validation to v1.20.0
- jstree to v 3.3.16
- getid3 to v1.9.23
- sortable.js to v1.15.1

## v3.0.1 Patch #2 - May 31, 2023

### v301patch2 adds these features to v300rc1/v301 and previous patches:
- add forms report title to editor insert-field dropdown in custom report template
- form submit now goes back with message instead displaying simple page
- add Invisible reCaptcha anti-spam option
- implement full-width static wysiwyg text form control option
- make outbound emails more friendly by adding organization name to email address when using default smtp address
- add better child help doc support and help doc search using datatables
- (re)add auto-launch installer for new installation
- add time only option to yuicalendarcontrol
- make pop button color match type

### v301patch2 fixes these issues in v300rc1/v301 and previous patches:
- fix the broken bs2 form designer which was never intended to be included, but was
- fix yui/bootstrap3 theme framework file upload widget broken, unable to load cross-domain script (https/http)
- attempt to fix some cross-domain loading issues behind proxies
- fix UPS shipping calculator issues (now allows either a HTTP 1.1 or 2 response)
- regression fix some bs/bs3 ajax actions might crash if they attempt to compile an scss instead of less file
- fix crash bs3 forms showall view
- regression fix bs5 rss/ical pull module configuration tab broken
- regression fix blog item date display and tweet ref
- regression fix bs4 calendarcontrol wouldn't update input
- regression fix form design change control type isn't saved
- regression fix form design control ranks mangled during design session
- regression fix facebook/twitter buttons not rebuilt on ajax page load for blog/news
- fix page reordering anomaly

### v301patch2 updates these 3rd party libraries in v300rc1/v301 and previous patches:
- easypost sdk to v6.6.0
- less.php to v4.1.0
- mediaelement to v6.0.3
- mediaelement plugins to v2.6.6
- jquery to v3.7.0
- jquery-migrate to v3.4.1
- jquery-confirm v3.3.4
- reCaptcha lib v1.3.0 (php 8+)
- tempus-dominus to v6.7.7
- ckeditor to v4.21.0
- datatables to v1.13.4
- codemirror cdn to v5.65.13
- ace editor cdn to v1.22.0
- font-awesome to v6.4.0
- bootstrap-icons to v1.10.5
- smarty to v4.3.1

## v3.0.1 Patch #1 - Mar 7, 2023

### v301patch1 adds these features to v300rc1:
- better metainfo for forms and their records
- add blog dates calendar view
- greatly speed up authors and categories views (~3x)
- don't offer to import/export ecommerce data if ECOM is not active
- allow/advertise xmlrpc editing page break to OpenLiveWriter(WP more)
- moved EAAS data processing to individual modules, allows custom module integration
- add form/global level unrestrict view to existing module level setting
- add feature to add dates to an existing event while editing an existing event

### v301patch1 fixes these issues in v300rc1:
- fix xmlrpc issues w/ php v8 (errors/warnings crash output parsing)
- regression fix page redirect code logic
- regression fix (v220) unable to save activate module changes
- fix event/show/id/x to pull the 'next' event date instead of the 1st event date
- several php v8 fixes to eaas module
- fix scssphp ALWAYS setting timezone to UTC which created upcoming events issue
- regression fix (v300rc1) mpdf7/mpdf8/mpdf81 crash on PHP < 7.0 due to wrong version of included random_compat
- fix possible error in forms portfolio view, add 'item' div to forms/show view
- regression fix default events search results limit not enforced

### v301patch1 updates these 3rd party libraries in v300rc1:
- mediaelement.js to v6.0.1
- codemirror cdn to v5.65.12
- ace editor cdn to v1.15.3
- jstree to v3.3.15
- phpxmlrpc to v4.10.1
- ckeditor to v4.20.2
- datatables to v1.13.3
- easypost to v6.3.0
- simplepie to v1.8.0