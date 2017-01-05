Version 2.4.1 - Specific changes from previous version
------------------------------------------------------

#### v241 adds these features to v240 previous releases:
- add optional syntax highlighting editor support for code snippet module (CodeMirror or Ace editors)
- activates new ckeditor drag/drop file upload which allows instantly uploading a non-image file to insert the link

#### v241 fixes these issues in v240 previous releases:
- now prohibits .php5/.php7 uploaded files from being run, even if server allows
- integrate class.upload to help process all file uploads and filter executable scripts; also filter uploads in elFinder to same mime types
- regression fix traditional file manager changing folders action not working as expected
- fix/handle possible fatal error/exception with event module external calendars
- fix jquery/bs2 popupcalendar initial date issue
- fixes a ckeditor drag/drop file/image upload issue when error reporting was turned on

#### v241 updates these 3rd party libraries in v240 previous releases:
- elFinder to v2.1.19
- easypost library to v3.2.1
- swiftmailer library to v5.4.5
- bootstrap-datetimepicker to v4.17.44


#### v240patch5 adds these features to v240 previous releases:
- updates/adds/exposes support for mPDF v6.1 and DOMPDF v0.7.0, fixes html2pdf support
- adds separate forms showall_portfolio custom view configuration in addition to show item custom view

#### v240patch5 fixes these issues in v240 previous releases:
- regression fix (v240) `<meta charset...>` tag wasn't properly closed with quote
- regression fix event announcement view; edit/delete wasn't passing date_id; added copy command
- regression fix (v240p2) 404 errors NOT being dispatched unless new optional page redirection support is turned on

#### v240patch5 updates these 3rd party libraries in v240 previous releases:
- (optional) mPDF to v6.1.2
- (optional) DOMPDF to v0.7.0
- (optional) html2pdf to v4.6.1 and tcpdf to v6.2.13
- TinyMCE to v4.5.1
- CKEditor to v4.6.1
- moment.js to v2.17.1
- jquery.validate to v1.16.0


#### v240patch4 adds NO features to v240 previous releases:

#### v240patch4 fixes these issues in v240 previous releases:
- fix page redirection log styling issue with minimized styles/scripts
- fix ckeditor add custom plugin from {control} to custom toolbar
- regression fix (v240p2) listbuilder widgets are broken, won't save contents
- regression fix (v240p2) YUI calendarcontrol and popupdatetimecontrol widgets are broken give warning
- forms showall records command only displayed filtered records

#### v240patch4 updates these 3rd party libraries in v240 previous releases:
- elFinder to v2.1.18
- CKEditor to v4.6.0
- TinyMCE to v4.5.0
- PLUpload to v2.2.1
- Moment.js to 2.17.0
- SwiftMailer to v5.4.4


#### v240patch3 adds these features to v240 previous releases:
- adds filtered records count to form showall views if filtered (already displayed total form records)
- adds 'clear page redirection log' command
- change page redirect log to display entire redirection record on hover and add redirect' button instead of using linked name
- allow 301,302,307, & 308 as page redirect code options
- page redirection now also records the requested url to help determine what the user was trying to do

#### v240patch3 fixes these issues in v240 previous releases:
- regression fix (v237p1) new directories created with wrong/bad permissions in some cases (less to css, etc...)
- regression fix (v240) new customers unable to create new account

#### v240patch3 updates these 3rd party libraries in v240 previous releases:
- yadcf to v0.9.1
- moment.js to v2.16.0
- webshims to v1.16.0


#### v240patch2 adds these features to v240 previous releases:
- initial implementation of optional page redirection support; must be turned on in site configuration Error Messages, then managed by manage all pages

#### v240patch2 fixes these issues in v240 previous releases:
- prevent logged in users from viewing other user records and admins from super-admin records; thanks to pang0lin
- fix sql injection issue in notfound controller; reported by pang0lin
- fix db indexes removed during 'remove db unneeded columns' command
- (regression) fix text accordion view (non-bs/bs3), may have never worked correctly

#### v240patch2 updates NO 3rd party libraries in v240 previous releases:


#### v240patch1 adds these features to v240:
- adds form control description option to calendarcontrol, popupdatetimecontrol, and yuicalendarcontrol

#### v240patch1 fixes these issues in v240:
- fix unable to display multiple recaptcha widgets per page (multiple forms per page)
- fix anomalies with event feedback email from announcement view
- fix some issues with the new 'output as link' form control option and some form showall portfolio view issues
- regression fix (v2.4.0) file upload logic error...would rename '_' to '..'
- regression fix expPaginator would only return a single page if called with sql statement (total records was set to page limit)
- fix security vulnerability to bypass permissions using method name in wrong case, reported by fyth
- fix security vulnerability attempt to modify config.php (logic was incorrect), reported by xiaojunjie
- fix security vulnerability to get user list, reported by pang0lin
- fix security vulnerability in search method, reported by pang0lin
- fix security vulnerability to editing addresses, countries, and regions; reported by pang0lin
- fix security vulnerability to reranking pages; reported by kyohpc
- fix security vulnerability update group; reported by DM_
- fix security vulnerability in order search and editor preview; reported by fyth
- fix security vulnerability in ratings; reported by fyth
- prevent swf/flash uploads in elFinder to prevent malicious code upload; reported by DM_
- fix many sql injection security vulnerabilities which failed to account for sef urls; reported by many people; CVE-2016-9272 
- fix failure to output jquery addon stylesheets within ajax call
- fix bs3 popupdatetimecontrol initial display if system date/time format is not default...now consistent with other bs3 date time widgets

#### v240patch1 updates these 3rd party libraries in v240:
- update jstree to v3.3.3
- update owl carousel to v2.2.0