# Exponent Content Management System

----------

Copyright (c) 2004-2017 OIC Group, Inc.

This file contains details about optional features which are activated
by the installation of 3rd party libraries which are not shipped with Exponent CMS.
These optional features include:

- PDF Export
- Enhanced Debugging Output

## PDF Export

Exponent CMS includes no built-in PDF Exporter, but this feature can be activated by
installing one or more of several PDF Output libraries. The package can be downloaded
and must be extracted to the root folder, or installed from within Exponent
(install extension) as a 'Patch' . Your choice of library will depend on the desired
speed or accuracy of the output. You may choose to not activate this feature and
simply require the user to locally create a PDF file on their end from printable output.

#### mPDF

**mPDF is the preferred library.** We currently support three (3) versions:

v6.1.3 is the newest version, but a v7 is in development
- [mpdf61.zip](https://sourceforge.net/projects/exponentcms/files/Add-ons/mpdf61.zip/download)
This requires Exponent CMS v2.4.1 or later.
- The generic package if used should be extracted to the /external folder and MUST be renamed to 'mpdf61'

v6.0 is also available
- [mpdf60a.zip](https://sourceforge.net/projects/exponentcms/files/Add-ons/mpdf60a.zip/download)
This uses the mPDF v6.0.0 library which has been customized for PHP v7 compatibility. This
package requires Exponent CMS v2.3.3 or later.
- The generic package if used should be extracted to the /external folder and MUST be renamed to 'mpdf60'

v5.7.4 is the oldest version we support
- [mpdf57a.zip](https://sourceforge.net/projects/exponentcms/files/Add-ons/mpdf57a.zip/download)
This uses the mPDF v5.7.4 library which has been customized for PHP v7 compatibility. This
package requires Exponent CMS v2.2.3 or later.

#### domPDF

domPDF was the first supported PHP based library. We currently support two (2) versions:

v0.8.0 is the newest version
- [dompdf080.zip](https://sourceforge.net/projects/exponentcms/files/Add-ons/dompdf080.zip/download)
This uses the domPDF v0.8.0 library which has been customized for Exponent with a fix for thumbnails.
This package requires Exponent CMS v2.4.2 and later.

v0.7.0 is the previous version
- [dompdf070.zip](https://sourceforge.net/projects/exponentcms/files/Add-ons/dompdf070.zip/download)
This uses the domPDF v0.7.0 library which has been customized for Exponent with a fix for thumbnails. 
This package requires Exponent CMS v2.4.1 and later.

v0.6.2 is the older version, but the first library we supported
- [dompdf62a.zip](https://sourceforge.net/projects/exponentcms/files/Add-ons/dompdf62a.zip/download)
This uses the domPDF v0.6.2 library which has been customized for Exponent with a fix for pdf
invoices and thumbnails. This package requires Exponent CMS v2.2.3 or later.

#### HTML2PDF

HTML2PDF differs from the previous two libraries in that is uses a second 3rd party
library (TCPDF) to perform the actual PDF creation.

v4.6.1 is the newest version, though it is possible that earlier versions back to v4.5.0
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

v0.12.4 is the newest version which can be downloaded from [wkhtmltopdf.org](http://wkhtmltopdf.org/downloads.html)

## Enhanced Debugging Output

Exponent CMS includes built-in Developer Debugging support, but this feature can be extended by
installing the [Kint](https://github.com/raveren/kint) PHP library. Simply extract a release into
the /external folder which creates an subfolder named 'kint'. The feature is auto-activated
by this installation.
- v1.1 is the newest version, but a v2 is in development
