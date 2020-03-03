Version 2.5.1 - Specific changes from previous version
------------------------------------------------------

## v251

####v251 adds these features to v250 and previous patches:
- Add 'include a blank' option for user form dropdown controls

####v251 fixes these issues in v250 and previous patches:
- Fix db manager warnings when displaying some tables
- Fix not checking antispam on event feedback responses nor new user account request
- Fix possible iCal import anomaly
- Fix some anomalies in usersController (courtesy Fred Dirske)
- Fix bs4 photo slideshow view display of body text and also linkify text content
- Regression fix (v238p1) not creating a real sef url for new pages

####v251 updates these 3rd party libraries in v250 and previous patches:
- Bootstrap 4 to v4.4.1
- Bootswatch 4 to v4.4.1
- elFinder to v2.1.53
- ckeditor to v4.13.1
- fontawesome 5 to v5.12.1
- scssphp to v1.0.8
- ace editor link to v1.4.7
- tinymce to v4.9.8
- simplepie v1.5.4
- smarty v3.1.34
- sortable to v1.10.2
- recaptcha to v1.2.3
- jQuery Migrate to v3.1.0
- DataTables to v1.10.20
- jstree.js to v3.3.9
- EmailValidator to v2.1.15
- class.upload to v1.0.8

###v250patch2

####v250patch2 adds these features to v250 and previous patches:
- new events/ical parameter (date) to allow sending all events after passed date
- revised the reset password process to occur online after email received instead of first assigning random password
- eCommerce stats reported as Net (subtotal) vs Gross (w/ shipping & tax)
- bs3/bs4 sample themes updated - setting viewport maximum_scale no longer recommended

####v250patch2 fixes these issues in v250 and previous patches:
- regression fix iCal calendar pull/import broken under PHP v5.6 and other anomalies
- regression fix (v250) missing store quick links icons
- regression fix (v243) error in bootstrap 4 theme stylesheet, esp. centered menu
- regression fix (v243) bs4 navigation breadcrumb view
- regression fix (v223) adds missing noindex/nofollow fields to section table
- regression fix some ecommerce dashboard display (totals) logic anomalies
- regression fix ajax paging doesn't update expHistory

####v250patch2 updates these 3rd party libraries in v250 and previous patches:
- font-awesome5 to v5.11.2
- ckeditor to v4.13.0
- tinymce to 4.9.6
- bootswatch3 to v3.4.1+1
- popper.js to v1.16.0
- elFinder to 2.1.50
- jquery to v3.4.1
- sortable.js to v1.10.1
- swiftmailer to v6.2.3
- lexer to v1.2.0
- mediaelement.js to v4.2.14
- jstree to v3.3.8
- iCalcreator to v2.28.2
- jquery validate to v1.19.1
- codemirror link to v5.48.4
- ace editor link to v1.4.6
- scssphp to v1.0.5
- select2 to v4.0.12
- phpxmlrpc to v4.4.1
- EmailValidator to v2.1.11
- Adminer db manager to v4.7.5
- SimplePie to v1.5.3
- Jasny Bootstrap to v4.0.0
- Simple Ajax Uploader to v2.6.7
- class.upload to v1.0.2

###v250patch1

####v250patch1 adds no features to v250:

####v250patch1 fixes these issues in v250:
- regression fix (v250) bs3/bs4 update quantities button didn't work
- regression fix (v250) some scripts (mediaplayer) failed to load under php 7.1/7.2/7.3
- regression fix (v250) multiple tags/other-attachments not displayed/saved correctly
- fix manage tags/cats not connecting assoc items (blog/news/etc)
- regression fix (v250) mail not working under php 7.x, missing swiftmailer v6.x
- remove (redundant) bs4 form validation icons (bs) since they're now included in bs4
- regression fix (v237) we've never saved wysiwyg editor additionalconfig setting

####v250patch1 updates these 3rd party libraries in v250:
- sortable.js to v1.8.4
- tinymce to v4.9.4
- font-awesome to v5.8.0
- swiftmailer to v6.2.0
- codemirror link to v5.44.0
- ace editor link to v1.4.3
