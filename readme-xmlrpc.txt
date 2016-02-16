ExponentCMS xmlrpc Weblog access implementation

As shipped this feature is turned OFF!  There are NO guarantees this may not open up security vulnerabilities on your site!  Please use with caution!
To activate, go to the Site Configuration, Security tab and turn on 'Activate Remote Blog Editing'

After activation, you should be able to edit your blog module using a desktop blog editor like MS Word, etc...be advised many are quite finicky.

To use, run your blog editor using several parameters, e.g.
Site/APi Type - Custom Metaweblog
Account Username - admin account on the site
Account Password - site password
Blog Post API URL - http://www.mysite.org/xmlrpc.php
Picture Options - Use Blog Provider

If all goes well (and sometimes it doesn't), you'll be given a list of all the blogs on the site.  Most blog editors allow you to choose multiple blogs from the same address.

Currently this action:
- Allows you to create new and edit existing blog posts including those with graphics.  You can also set draft or publish status
- Allows access to use or create categories if turned on within that blog module
- Allows access to use or create tags within that blog module

If you edit the included '/rsd.xml.sample' file by entering your web site url in place of the 2 instances of 'yoursite.url',
and then rename that file to '/rsd.xml', it may help guide your offline editor during account creation