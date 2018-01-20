# Contributing

Looking to contribute? It's great to have you here!

## Howto

Please check the [development guide](https://github.com/mishal/iless/wiki/Development).

## Coding standards

Please follow the [PSR-2 guidelines](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md), respectively [PSR-0](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md), [PSR-1](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md) and [PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md) guidelines.

### Main points

 * Files must use only **UTF-8** without BOM for PHP code.
 * Class names MUST be declared in **StudlyCaps** and
 * Class **constants** MUST be declared in **all upper case with underscore separators**.
 * Each `_` character in the **class name** is converted to a `DIRECTORY_SEPARATOR`.
 * **Method names** MUST be declared in **camelCase**.
 * **Use 4 spaces** for indenting, not tabs.
 * **Opening braces for classes** MUST go on the **next line**, and **closing braces** MUST go on the **next line after the body**.
 * **Opening braces for methods** MUST go on the **next line**, and closing braces MUST go on the next line after the body.
 * **Visibility** MUST be declared on **all properties and methods**; abstract and final MUST be declared before the visibility; static MUST be declared after the visibility.
 * **Control structure keywords** MUST have one space after them; method and function calls MUST NOT.
 * **Opening braces for control structures** MUST go on the **same line**, and closing braces MUST go on the next line after the body.
 * **Opening parentheses for control structures** MUST NOT have a space after them, and closing parentheses for control structures MUST NOT have a space before.

For more information check the guides. You can use the [PHP Coding Standards Fixer](http://cs.sensiolabs.org/) to fix the code.

## Formatting

Please use **Unix LF** line endings. If you develop on Windows, please set the `core.autocrlf` to `true`.

    $ git config --global core.autocrlf true

Read [more information on formatting](http://git-scm.com/book/ch7-1.html#Formatting-and-Whitespace).

## Issues

**Before opening any issue**, please search for [existing issues](https://github.com/mishal/iless/issues). After that if you find a bug or would like to make feature request, please [open a new issue.](https://github.com/mishal/iless/issues/new) Please *always create* a unit test. Please provide a failing LESS code, try to describe the problem in detail. [A list of issues](https://github.com/mishal/iless/issues).

## Pull requests

 * **Always make your contributions** for the latest `master` branch.
 * Create **separate branch** per patch or feature.
 * Remain focused in scope and avoid containing unrelated commits.
 * **Run the unit tests**. They should all pass.
 * If some **issue is relevant** to patch / feature, **mention the issue number with hash (e.g. `#1`)** in your commit message to get reference in GitHub web interface.

For more information how to create a pull request check the official manual on [Github's help](https://help.github.com/articles/using-pull-requests).

## Useful links

 * [PSR-0 guide](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md)
 * [PSR-1 guide](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md)
 * [PSR-2 guide](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)
 * [PHP Coding Standards Fixer](http://cs.sensiolabs.org/)
 * [Git line ending setting](http://git-scm.com/book/ch7-1.html#Formatting-and-Whitespace)
 * [Pull request Github help](https://help.github.com/articles/using-pull-requests)
 * [List of existing issues](https://github.com/mishal/iless/issues)
