Version 2.4.3 - Specific changes from previous version
------------------------------------------------------

#### v243 adds these features to v242 and previous patches:
- added private module indicator to module chrome
- added sample http2 config settings to .htaccess & nginx.conf
- added sample Bootstrap 4 theme & associated libraries to package
- removed sample Bootstrap 2 theme from package

#### v243 fixes these issues in v242 and previous patches:
- regression fix (242p4) unable to change user group membership
- regression fix (241p3) order report doesn't adhere to order status or order status changed input
- regression fix (241p3) order and product reports don't adhere to date request
- regression fix (242p5) fix order payment summary charts
- fix bs4 form design horizontal style
- regression fix tags views by title were in reverse alphabetical order
- fix event module SEO issues (spider too deep, bad meta info, no day view link icon)
- fix ecommerce report creation rejecting product/order amount due to currency symbol

#### v243 updates these 3rd party libraries in v242 and previous patches:
- adminer v4.6.3
- ckeditor v4.10.0
- tinymce v4.8.0
- swiftmailer v6.1.2
- elFinder v2.1.40
- datatables to v1.10.19
- scssphp to v0.7.7
- adds Bootstrap v4.1.3 with Boot Swatches v4.1.3
- adds Font Awesome 5.2.0

## v242patch7

#### v242patch7 adds these features to v242 and previous patches:
- adds dropdown configuration settings to simpletheme
- adds a sample NGINX server configuration file for sef urls
- add display of socialfeed facebook link type post thumbnails
- add optional mapquest maps option now that google maps will cost to use

#### v242patch7 fixes these issues in v242 and previous patches:
- regression fix 'show past events' should be most recent first
- regression fix (v242) export files/themes broken
- regression fix (v242) bootstrap4 (or theme custom) shipping/billing calculator views not displayed
- fix minified css removed necessary spaces
- regression fix (v242p4) unable to update group membership
- regression fix (v242p6) ckeditor file upload send to server feature broken
- fix issues with ckeditor custom toolbar if empty lines are at end of configuration
- regression fix (242p6) ckeditor undo feature missing

#### v242patch7 updates these 3rd party libraries in v242 and previous patches:
- elFinder to 2.1.39
- TinyMCE to 4.7.13
- YADCF to v0.9.3
- scssphp to v0.7.6
- moment.js to v2.22.2

## v242patch6

#### v242patch6 adds these features to v242 and previous patches:
- update robots.txt to remove thumb.php thumbnailer since Google indexing has begun to complain...also disallowing newer documents
- adds customers command (manage users) to ecommerce menu
- adds forms quick-submit option to skip form entry confirmation for a 1-step submission

#### v242patch6 fixes these issues in v242 and previous patches:
- fix slideshow scroll page up/down on touch devices
- regression fix (v242p5) form submission warning
- give proper ids to event feedback forms to prevent errors when multiple RSVP events are shown on page
- fix expString and billing calculators to be php v7.2 compliant (remove each() statement)
- regression fix recaptcha (form submission) was broken when minimize & combine js scripts was turned on
- regression fix (v242p5) remove orphan child product records upgrade script
- regression fix (v242) crash with ical pull events time zones
- fix a twitter profile image issue; add 'noq' param to {img} to preven tacking on &q=75 to twitter call for profile image

#### v242patch6 updates these 3rd party libraries in v242 and previous patches:
- CKEditor to v4.9.2
- TinyMCE to v4.7.11
- elFinder to 2.1.37
- jquery datetimepicker to v2.5.20
- mediaelement to v4.2.9
- owlCarousel2 to v2.3.4
- moment.js to v2.22.1
- easypost sdk to v3.4.1
- EmailValidator to v2.1.4
- Bootstrap DualListBox to v3.0.7

## v242patch5

#### v242patch5 adds these features to v242 and previous patches:
- adds small device responsive view to store show product w/ child products in bs3 framework
- uses speedier casting instead of intval/floatval/stringval functions
- update delete products/categories to better clean up associated tables
- adds new optional upgrade script to remove orphan eCommerce records
- adds customer stats to ecommerce dashboard, splits out dashboard data
- add delete confirmation to bootstrap store category tree deletions
- add autoplay setting to media player

#### v242patch5 fixes these issues in v242 and previous patches:
- regression fix (v242) unable to update existing form records
- regression fix (v241p6) new users unable to create new account (non-ecommerce)
- regression fix (v242p1) IIS support always switches to https urls
- regression fix orders report, show payment summary tax data not displayed
- regression fix (v242p4) images in manage products not displayed
- regression fix media player youtube videos fail to play when multiple videos are on a page
- fix unused tables shouldn't list forms data tables for removal
- fix product model complains when doing update() from other than edit view
- fix fix_sef_urls upgrade script to not add an sef_url to child products
- fix photoalbum configuration to not send duplicate config settings from both 'gallery' and 'module view' tabs
- fix some cross-domain loading issues for https: sites (fonts, maps)
- fix bootstrap 4 support in orders and printer-friendly views

#### v242patch5 updates these 3rd party libraries in v242 and previous patches:
- TinyMCE to v4.7.9
- Adminer db manager to v4.6.2
- moment.js to v2.21.0
- owlCarousel2 to v2.3.2
- elFinder to v2.1.33

## v242patch4

#### v242patch4 adds these features to v242 and previous patches:
- allow optional custom import/export methods
- adds # child products to the manage products view
- adds view customer to the manage users view if ecom is on

#### v242patch4 fixes these issues in v242 and previous patches:
- fix to get the bs3 view order invoice map to display correctly (instead of gray)
- regression fix users unable to create their own account
- regression fix (v242p2) some search issues
- better large ecom database optional support (orders, products, & users)
- still lacks group membership & permissions support
- removes tabletools & responsive support (at least temporarily)

#### v242patch4 updates these 3rd party libraries in v242 and previous patches:
- TinyMCE to v4.7.6
- Adminer database manager to v4.6.1
- jQuery to v3.3.1
- SwiftMailer to v5.4.9 (though we still also ship v6.0.2 for php 7.x)
- scssphp to v0.7.5
- elFinder to v2.1.32
- DataTables to v1.10.16
- jquery datetimepicker to v2.5.18
- phpxmlrpc to v 4.3.1
- normalize.css to v8.0.0

## v242patch3

#### v242patch3 adds no features to v242 and previous patches:

#### v242patch3 fixes these issues in v242 and previous patches:
- regression fix (242p2) unable to install exponent due to no db engine installed message

#### v242patch3 updates no 3rd party libraries in v242 and previous patches:

## v242patch2

#### v242patch2 adds these features to v242 and previous patches:
- allow customer order comments/instructions during checkout
- enhance search indexing code

#### v242patch2 fixes these issues in v242 and previous patches:
- regression fix (v241p6) admins unable to add new users
- regression fix (v242p1) code snippet saves emtpy body when using Ace editor
- regression fix (v242p1) db manager issues; fix tinymce implementation; fix tables filter; calendar widget extra input field; disable ckeditor for now
- fix some methods to better handle large database tables

#### v242patch2 updates these 3rd party libraries in v242 and previous patches:
- tinymce to v4.7.5
- adminer to v4.5.0

## v242patch1

#### v242patch1 adds these features to v242:
- add version constants for some HTMLTOPDF engines to facilitate easier future updates
- update store category tree to display number of products in category and flag improper category selection
- Microsoft IIS server compatibility (requires url_rewrite module), thanks to Todd Giardina, Todd's IT
- Additional db indexes added which should improve performance on sites with much data (blogs, ecommerce)
- enhance adding new items by only adding that single item to the search index instead of re-indexing the entire module

#### v242patch1 fixes these issues in v242:
- !!regression fix (v242) sending mail crashes under php v7+ on non-windows server or when using simple mail option on php v7+ on any system
- resolve some cross-domain (https to http) security issues (this was NOT a vulnerability)
- resolve some store/showall and time limit issues with large databases
- fix db manager calendar widget saving wrong dates

#### v242patch1 updates these 3rd party libraries in v242:
- jstree to v3.3.5
- mediaelement.js to v4.2.8
- adminer (db manager) to v4.4.0
