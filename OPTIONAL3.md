# Exponent Content Management System

---

Copyright (c) 2004-2025 OIC Group, Inc.

## Optional Features

This file contains details about optional features which are activated
by the installation of 3rd party libraries which are not shipped with Exponent CMS.
These optional features include:

- PDF Export
- Enhanced Debugging Output
- .less/.scss Auto-prefixer

### PDF Export

Exponent CMS doesn't include a built-in PDF Exporter, but this feature can be activated by
installing one or more of several PDF Output libraries. The package(s) can be downloaded
and must be extracted to the root folder, or installed from within Exponent
(install extension) as a 'Patch' . Your choice of library will depend on the desired
speed or accuracy of the output. You may choose to not activate this feature and
simply require the user to locally create a PDF file on their end from printable output.
If you are running an older version of PHP, you may need to use an older PDF Library.

#### mPDF

**mPDF is the preferred library.** We currently support two (2) versions:

v8.1.4 is the newest version supported

- [mpdf81v271.zip](https://sourceforge.net/projects/exponentcms/files/Add-ons/mpdf81v271.zip/download)
  This uses the mPDF v8.1.4 library which has been customized for Exponent.
  This requires Exponent CMS v2.7.1 or later. Works with PHP v5.6 to v8.2

v8.0.17 is supported

- [mpdf8v270.zip](https://sourceforge.net/projects/exponentcms/files/Add-ons/mpdf8v270.zip/download)
  This uses the mPDF v8.0.17 library which has been customized for Exponent.
  This requires Exponent CMS v2.7.0 or later. Works with PHP v5.6 to v8.1

#### domPDF

domPDF was the first supported PHP based library. We currently supports two (2) versions:

**v2.0.3 is the newest version supported**

- [dompdf2v271.zip](https://sourceforge.net/projects/exponentcms/files/Add-ons/dompdf2v271.zip/download)
  This uses the domPDF v2.0.3 library which has been customized for Exponent with a fix for thumbnails.
  This package requires Exponent CMS v2.7.1 and later and PHP v7.1 or later.

v1.2.2 is the previous version which began at v0.8.0

- [dompdf08v270.zip](https://sourceforge.net/projects/exponentcms/files/Add-ons/dompdf08v260.zip/download)
  This uses the domPDF v1.2.2 library which has been customized for Exponent with a fix for thumbnails.
  This package requires Exponent CMS v2.7.0 and later and PHP v7.1 or later.

#### HTML2PDF

HTML2PDF differs from the previous two libraries in that is uses a second 3rd party
library (TCPDF) to perform the actual PDF creation. This appears to the be the slowest engine.

**v5.2.7 is the newest version supported**

- [html2pdf5v271.zip](https://sourceforge.net/projects/exponentcms/files/Add-ons/html2pdf5v271.zip/download)
  This uses the HTML2PDF v5.2.7 library which has the configuration customized for Exponent. It requires
  the TCPDF v6.4.4 PDF engine which is included in this package. This package requires Exponent CMS v2.7.1
  or later. Requires PHP v5.6 to v8.2.

#### WKHTMLtoPDF

WKHTMLtoPDF differs from all the other PDF Export libraries. While the other libraries
are PHP scripts which are installed/extracted into the Exponent file structure, WKHTMLtoPDF
requires installation of server specific binary files onto the server. In many cases
it can be both the fastest and most accurate, yet the most difficult to install and configure.

v0.12.6 is the newest version which can be downloaded from [wkhtmltopdf.org](https://wkhtmltopdf.org/downloads.html)

### Enhanced Debugging Output

#### Kint

Exponent CMS includes built-in Developer Debugging support, but this feature can be extended by
installing the [Kint](https://github.com/kint-php/kint) PHP library. Simply place the kint.phar file
into the /external folder. The feature is auto-activated by this installation.

- v3.3.0 to v6.0.0 (/external/kint.phar), (requires Exponent CMS v2.6.0patch2 or later)

### .less/.scss Auto-prefixer

#### php-autoprefixer

Though this option is still available, it is NOT recommended since it slows down page loading when a new 
stylesheet must be built! 

Exponent CMS includes built-in .less and .scss stylesheet compiling support. Stylesheet library developers
often expect their stylesheets to be pre-compiled on the server then run through a binary post-css processor.
Our PHP based solution allows this to take place within Exponent. Adding this option will cause the less/scss 
compilers to run much slower than without it, but will add prefixes needed by older browsers.This option can be
implemented by installing the [php-autoprefixer](https://github.com/padaliyajay/php-autoprefixer) package.
This library also requires the [PHP-CSS-Parser](https://github.com/sabberworm/PHP-CSS-Parser) PHP
library.  Simply extract our complete package into the / (root) folder and it will be automatically recognized.

- [php-autoprefixerv260.zip](https://sourceforge.net/projects/exponentcms/files/Add-ons/php-autoprefixerv260.zip/download)
  This uses the php-autoprefixer v1.4 library with PHP-CSS-Parser v8.4.0 library which has been customized
  for Exponent due to the unique file structure required. This requires Exponent CMS v2.6.0 or later.
