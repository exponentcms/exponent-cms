# Exponent Content Management System

---

Copyright (c) 2004-2023 OIC Group, Inc.

## Optional Features

This file contains details about optional features which are activated
by the installation of 3rd party libraries which are not shipped with Exponent CMS.
These optional features include:

- PDF Export
- Enhanced Debugging Output
- Alternate .less Stylesheet Compiler
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

**mPDF is the preferred library.** We currently support five (5) versions:

v8.1.4 is the newest version supported

- [mpdf81v271.zip](https://sourceforge.net/projects/exponentcms/files/Add-ons/mpdf81v271.zip/download)
  This uses the mPDF v8.1.4 library which has been customized for Exponent.
  This requires Exponent CMS v2.7.1 or later. Works with PHP v5.6 to v8.2

v8.0.17 is supported

- [mpdf8v270.zip](https://sourceforge.net/projects/exponentcms/files/Add-ons/mpdf8v270.zip/download)
  This uses the mPDF v8.0.17 library which has been customized for Exponent.
  This requires Exponent CMS v2.7.0 or later. Works with PHP v5.6 to v8.1

v7.1.9 is supported

- [mpdf7v260.zip](https://sourceforge.net/projects/exponentcms/files/Add-ons/mpdf7v260.zip/download)
  This uses the mPDF v7.1.9 library which has been customized for Exponent.
  This requires Exponent CMS v2.6.0 or later. Works with PHP v5.6 to v7.3

v6.1.4 was an older version

- [mpdf61.zip](https://sourceforge.net/projects/exponentcms/files/Add-ons/mpdf61.zip/download)
  This requires Exponent CMS v2.4.1 or later.
- The generic package if used should be extracted to the /external folder and MUST be renamed to 'mpdf61'

v5.7.4 is the oldest version we support

- [mpdf57a.zip](https://sourceforge.net/projects/exponentcms/files/Add-ons/mpdf57a.zip/download)
  This uses the mPDF v5.7.4 library which has been customized for PHP v7 compatibility. This
  package requires Exponent CMS v2.2.3 or later.

#### domPDF

domPDF was the first supported PHP based library. We currently support four (4) versions:

**v2.0.3 is the newest version supported**

- [dompdf2v271.zip](https://sourceforge.net/projects/exponentcms/files/Add-ons/dompdf2v271.zip/download)
  This uses the domPDF v2.0.3 library which has been customized for Exponent with a fix for thumbnails.
  This package requires Exponent CMS v2.7.1 and later and PHP v7.1 or later.

v1.2.2 is the previous version which began at v0.8.0

- [dompdf08v270.zip](https://sourceforge.net/projects/exponentcms/files/Add-ons/dompdf08v260.zip/download)
  This uses the domPDF v1.2.2 library which has been customized for Exponent with a fix for thumbnails.
  This package requires Exponent CMS v2.7.0 and later and PHP v7.1 or later.

v0.7.0 is a previous stable version

- [dompdf070.zip](https://sourceforge.net/projects/exponentcms/files/Add-ons/dompdf070.zip/download)
  This uses the domPDF v0.7.0 library which has been customized for Exponent with a fix for thumbnails.
  This package requires Exponent CMS v2.4.1 and later and PHP v5.3 or later

v0.6.2 is an older version, it is the first PDF library we supported

- [dompdf62a.zip](https://sourceforge.net/projects/exponentcms/files/Add-ons/dompdf62a.zip/download)
  This uses the domPDF v0.6.2 library which has been customized for Exponent with a fix for pdf
  invoices and thumbnails. This package requires Exponent CMS v2.2.3 or later.

#### HTML2PDF

HTML2PDF differs from the previous two libraries in that is uses a second 3rd party
library (TCPDF) to perform the actual PDF creation. This appears to the be the slowest engine.

**v5.2.7 is the newest version supported**

- [html2pdf5v271.zip](https://sourceforge.net/projects/exponentcms/files/Add-ons/html2pdf5v271.zip/download)
  This uses the HTML2PDF v5.2.7 library which has the configuration customized for Exponent. It requires
  the TCPDF v6.4.4 PDF engine which is included in this package. This package requires Exponent CMS v2.7.1
  or later. Requires PHP v5.6 to v8.2.

v4.6.1 is the previous stable version, though it is possible that earlier versions back to v4.5.0
may also work if installed correctly.

- [html2pdf-1.zip](https://sourceforge.net/projects/exponentcms/files/Add-ons/html2pdf-1.zip/download)
  This uses the HTML2PDF v4.6.1 library which has the configuration customized for Exponent. It requires
  the TCPDF v6.2.13 PDF engine which is included in this package. This package requires Exponent CMS v2.3.8
  or later.

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
into the /external folder (for v1 & v2 extract into the /external folder to create a subfolder).
The feature is auto-activated by this installation.

- v3.3.0 to v5.0.3 (/external/kint.phar), (requires Exponent CMS v2.6.0patch2 or later)
- v2.2 (/external/kint-2.2/) is the last 2.x version,
- but v1.1 (/external/kint/) is also supported

### Alternate .less Compiler

#### iLess

Exponent CMS includes built-in .less stylesheet compiling support. We include the older lessphp compiler
which worked well for Bootstrap v2, but also added less.php for Bootstrap v3 support which still works well.
There is another PHP based .less compiler called iLess which can be used. This option can be extended by
installing the [iLess](https://github.com/mishal/iless) PHP library. In most instances, this compiler is much
slower than less.php, though it will compile Bootstrap v3. Simply extract a release into
the /external folder which creates an subfolder named 'iless'. You must also manually edit the
/framework/conf/config.php file and change the LESS_COMPILER entry to 'iless'.

- v2.2 is the newest version

### .less/.scss Auto-prefixer

#### php-autoprefixer

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
