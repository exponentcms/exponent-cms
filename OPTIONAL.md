# Exponent Content Management System

----------

Copyright (c) 2004-2018 OIC Group, Inc.

## Optional Features

This file contains details about optional features which are activated
by the installation of 3rd party libraries which are not shipped with Exponent CMS.
These optional features include:

- PDF Export
- Enhanced Debugging Output
- Alternate .less Compiler

### PDF Export

Exponent CMS doesn't include a built-in PDF Exporter, but this feature can be activated by
installing one or more of several PDF Output libraries. The package(s) can be downloaded
and must be extracted to the root folder, or installed from within Exponent
(install extension) as a 'Patch' . Your choice of library will depend on the desired
speed or accuracy of the output. You may choose to not activate this feature and
simply require the user to locally create a PDF file on their end from printable output.

#### mPDF

**mPDF is the preferred library.** We currently support three (3) versions:

v7.0.2 is the newest version
- [mpdf7.zip](https://sourceforge.net/projects/exponentcms/files/Add-ons/mpdf7.zip/download)
This uses the mPDF v7.0.2 library which has been customized for Exponent.
This requires Exponent CMS v2.4.2 or later.

v6.1.4 is the previous stable version
- [mpdf61.zip](https://sourceforge.net/projects/exponentcms/files/Add-ons/mpdf61.zip/download)
This requires Exponent CMS v2.4.1 or later.
- The generic package if used should be extracted to the /external folder and MUST be renamed to 'mpdf61'

v5.7.4 is the oldest version we support
- [mpdf57a.zip](https://sourceforge.net/projects/exponentcms/files/Add-ons/mpdf57a.zip/download)
This uses the mPDF v5.7.4 library which has been customized for PHP v7 compatibility. This
package requires Exponent CMS v2.2.3 or later.

#### domPDF

domPDF was the first supported PHP based library. We currently support three (3) versions:

v0.8.2 is the newest version
- [dompdf082.zip](https://sourceforge.net/projects/exponentcms/files/Add-ons/dompdf082.zip/download)
This uses the domPDF v0.8.2 library which has been customized for Exponent with a fix for thumbnails.
This package requires Exponent CMS v2.4.2 and later.

v0.7.0 is the previous stable version
- [dompdf070.zip](https://sourceforge.net/projects/exponentcms/files/Add-ons/dompdf070.zip/download)
This uses the domPDF v0.7.0 library which has been customized for Exponent with a fix for thumbnails. 
This package requires Exponent CMS v2.4.1 and later.

v0.6.2 is the older version, but the first library we supported
- [dompdf62a.zip](https://sourceforge.net/projects/exponentcms/files/Add-ons/dompdf62a.zip/download)
This uses the domPDF v0.6.2 library which has been customized for Exponent with a fix for pdf
invoices and thumbnails. This package requires Exponent CMS v2.2.3 or later.

#### HTML2PDF

HTML2PDF differs from the previous two libraries in that is uses a second 3rd party
library (TCPDF) to perform the actual PDF creation. This appears to the be the slowest engine.

v5.0.1 is the newest version.
- [html2pdf5.zip](https://sourceforge.net/projects/exponentcms/files/Add-ons/html2pdf5.zip/download)
This uses the HTML2PDF v5.0.1 library which has the configuration customized for Exponent. It requires
the TCPDF v6.2.13 PDF engine which is included in this package. This package requires
Exponent CMS v2.4.2 or later.

v4.6.1 is the previous stable version, though it is possible that earlier versions back to v4.5.0
may also work if installed correctly.
- [html2pdf-1.zip](https://sourceforge.net/projects/exponentcms/files/Add-ons/html2pdf-1.zip/download)
This uses the HTML2PDF v4.6.1 library which has the configuration customized for Exponent. It requires
the TCPDF v6.2.13 PDF engine which is included in this package. This package requires
Exponent CMS v2.3.8 or later.
- The generic TCPDF package if used should be extracted to the /external folder and MUST be renamed to 'TCPDF'

#### WKHTMLtoPDF

WKHTMLtoPDF differs from all the other PDF Export libraries. While the other libraries
are PHP scripts which are installed/extracted into the Exponent file structure, WKHTMLtoPDF
requires installation of server specific binary files onto the server. In many cases
it can be both the fastest and most accurate, yet the most difficult to install and configure.

v0.12.4 is the newest version which can be downloaded from [wkhtmltopdf.org](https://wkhtmltopdf.org/downloads.html)

### Enhanced Debugging Output

#### Kint

Exponent CMS includes built-in Developer Debugging support, but this feature can be extended by
installing the [Kint](https://github.com/raveren/kint) PHP library. Simply extract a release into
the /external folder which creates an subfolder named 'kint'. The feature is auto-activated
by this installation.
- v2.2 is the newest version, but v1.1 is also supported

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
