####v240 adds these features to v239 previous releases:
- adds accessibility hints
- updates the optional clean file db upgrade script to remove missing attachments and update file info (filesize, mimetype, & image dimensions)
- adds optional 'output as a link' feature to the url, telephone, and email form controls
- adds optional user selectable 'empty records'message for the forms showall_portfolio view

####v240 fixes these issues in v239 previous releases:
- security fix many sql injection, rce, file exploit, and xss vulnerabilities
- security fix to prevent possible sql injections and other vulnerabilities in pixidou editor, thanks to Manuel Garcia Cardenas, CVE-2016-7452 & CVE-2016-7453
- security fix to prevent uploading dot files, thanks to DM_ PKAV Team & fyth
- security fix popup.php, thanks to DM_ PKAV Team
- security fix xss vulnerability in worldpay, thanks to felixk3y PKAV Team
- security fix xss issue with uploader, thanks to fyth 
- security fix to prevent possible hacking by moving security checks earlier in the install code, thanks to felixk3y PKAV Team
- security fix for rce issue, thanks to xiojunjie, CVE-ID 2016-7565
- regression fix (v2.3.9) event send reminders to strip out styles from email text message variation
- fix new comment notification email link; display comment author's name using site attribution setting
- regression fix jquery/bootstrap2 date/time widget
- regression fix display of event feedback form on event showall announcement view
- regression fix non-admin users unable to view form enter_data when set up for restrict data entry by permissions

####v240 updates these 3rd party libraries in v239 previous releases:
- revert bootstrap datetimepicker to v4.17.37
- jQueryUI to v1.12.1
- easypost library to v3.1.3
- moment.js to v2.15.2
- normalize.css to v5.0.0
- mediaelement to v2.23.4
- jquery.validate to v1.15.1
- elFinder to v2.1.16
- phpxmlrpc to v4.1.1
- font-awesome to v4.7.0


####v239patch1 adds these features to v239:
- update rss/podcast feeds to include language and remove 'generator' comment since we include that element tag

####v239patch1 fixes these issues in v239:
- security fix (v2.3.0+) to prevent uploading files to wrong location, thanks to Balisong, CVE-ID 2016-7095, CVE-ID 2016-7443
- security fix to prevent possible sql injections, thanks to Manuel Garcia Cardenas and PKAV TEAM, CVE-ID 2016-7400
- fix filedownload facebook meta tags to include link to audio/video reference if it is 1st attached file
- fix events reminder email embedded links and update styles including using bootstrap2/3 if using that theme framework
- fix possible facebook meta issues; sending wrong 'type'
- fix bootstrap3 calendar views to not display date selector in printer friendly view
- regression fix (v2.3.9) .htaccess is too restrictive for uploaded media files

####v239patch1 updates these 3rd party libraries in v239:
- TinyMce to v4.4.3
- CKEditor to v4.5.11
- bootstrap-dialog to v1.35.3
- elfinder to v2.1.15
- mediaelement.js to v2.23.0
- bootstrap duallistbox to v3.0.6
- bootstrap datetimepicker to v4.17.42
- moment.js to v2.15.0 (needed by bootstrap datetimepicker)
- yadcf to v0.9.0

