# elFinder "Moono" Theme
A theme for elFinder that mimics [CKEditor's](http://ckeditor.com/)
[Moono](http://ckeditor.com/addon/moono) skin.

![screenshot from 2015-08-06 16 27 17](https://cloud.githubusercontent.com/assets/4363863/9115748/95e2dcf2-3c58-11e5-8bbb-6c17074a5b35.png)

# Usage
* Copy the `/moono` folder from this repository to the `/themes` folder of your
elFinder installation (create the folder if it doesn't exist)
* On the page where elFinder will be displayed (normally `elfinder.html`),
load the `theme.css` file from the `/moono/` folder:

    ```html
    <link rel="stylesheet" type="text/css" media="screen" href="themes/moono/css/theme.css">
    ```

# Features
* Works with [elFinder 2.0-rc1](https://github.com/Studio-42/elFinder/releases/tag/2.0-rc1),
[2.x (Nightly)](http://nao-pon.github.io/elFinder-nightly/latests/elfinder-2.x.zip)
and [2.1 (Nightly)](http://nao-pon.github.io/elFinder-nightly/latests/elfinder-2.1.zip)
* Styling done with pure CSS - no image spritesheets
* Uses [Font Awesome (v4.4.0+)](http://fortawesome.github.io/Font-Awesome/) for icons

# Modifying the source
This theme uses `.less` files that are compressed into `moono/css/theme.css`.
Compressing these files requires [Node.js](https://nodejs.org/).

1. Clone this repository:

    ```
    $ git clone https://github.com/lokothodida/elfinder-theme-moono.git
    ```

2. Edit the corresponding `.less` files in `moono/css/`

3. In the terminal, if this is your first time doing the build, run this to
install all of the dependencies:

    ```
    $ npm install
    ```

4. Then to build, run:

    ```
    $ npm run build
    ```
