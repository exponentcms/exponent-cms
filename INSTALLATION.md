#Exponent Content Management System

===============================================

Copyright (c) 2004-2013 OIC Group, Inc.

Installation of Exponent CMS 2.0
--------------------------------

Requirements
- Linux-based web host (recommended)
- Apache 2.0 or greater
- PHP 5.2 or greater
- MySQL database

---------------------

Upgrading / Migrating
If you are upgrading, it is HIGHLY RECOMMENDED that you export your database and back up your files.
Upgrading from 1.0 (0.98 currently) to 2.0 isn't an upgrade, but a migration. Refer to to documentation on migrating
content from 1.0 to 2.0.

---------------------

1. Download the latest package
    - Check [http://www.exponentcms.org](http://www.exponentcms.org) for the latest available releases. 

2. Unpack the archive in place on your web server
    - This can be locally if you have a web server installed on your computer, or on any number of linux-based hosts.

3. Database Setup
    - For security reasons, the Exponent Install Wizard is not able to create a database for you.  This must be done
      outside of the system, before the wizard is run.  However you do this is up to you (through command-line database
      client, phpMyAdmin, etc.).  The installer will ask you for name of the database to use, as well as a username and
      password for accessing the database.  The user account you specify must have enough rights to perform normal
      database operations (like running SELECT, INSERT, UPDATE, DELETE queries, etc.).  The installation wizard will
      ensure that the provided account has these privileges.

4. Run the Install/Upgrade Wizard
    - Once the directory is on the server in the website's directory, the web-based installer takes over the process
      of configuring and setting up the CMS. To access the installer and finish setting up, visit your website, and go to
      index.php file in the install folder. For instance, if your website (www.example.com) stores its files in
      ~/public_html/,     and you unpacked the archive there, go to http://www.example.com/install/index.php.
    - From there, just follow the instructions and the Exponent Install/Upgrade Wizard will configure and set up your
      new Exponent site.

5. Enjoy using Exponent!

6. For additional help and documentation, visit [docs.exponentcms.org](docs.exponentcms.org).
