# i18next-php
A port of [i18next](https://www.i18next.com/) in PHP. With a rest API. (coming soon \[tm\])

Code in this repository is largely based on the original code base in JavaScript, rewritten in PHP.
This project focuses only on the 3rd version of i18next, disregarding compatibility with previous ones.

### Warning!

This version is still in early development stages, I'm advising against using it. As of now.
This project is something I'm working on between other work, as such the updates may appear spontanously.

It's extremely likely that things are broken as right now I'm simply porting over the 
JS code without testing it for compatibility with PHP.

### Features

* PSR3 logging support (psr/log is a hard requirement, but you're not required to actually use a logger)
* Extremely similar API to the JS version
* PHP-specific features like interfaces for modules
* 7.1+ syntax / support

### Composer

Simply enter your project directory and run

`composer require pkly/i18next-php`

### Todo

* Finish all components
* Port over tests
* Actually test it
* Add PHP-specific loading code for files, searching etc.
* Add PHPDocs with examples and links to i18next

#### Packagist

Visit the page [here](https://packagist.org/packages/pkly/i18next-php)

#### Donate

If you want feel free to buy me a coffee [by clicking here](https://paypal.me/pklytastic?locale.x=en_US)