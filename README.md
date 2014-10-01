#Bower integration module for Zend Framework 2

This module provides a convenient way to manage JavaScript dependencies loaded with [Bower](http:/bower.io)
within your Zend Framework 2 application.

If your Zend Framework 2 application has a complex front-end with lots of external JavaScript dependencies
[Bower package manager](http:/bower.io) might be your salvation. But there is one major inconvenience
when you use ZF2 and Bower together: for debugging you usually want your JS-dependencies to be in separate
non-obfuscated files while for deploying you want them in single obfuscated file. You can create two partial views
and switch them via template map but that feels a bit awkward. This module solves this issue in
(hopefully :)) more elegant way.

## Get Bower packages

First of all you need to find and get all Bower packages that your application requires using either
[Bower](http:/bower.io) itself or its substitute [Bowerphp](http://bowerphp.org/) or any other way you prefer.
Then you need to set `bower.bower_folder.os` setting in your configuration - it should point to `bower_components` folder
 where you have just installed all packages. By default it is supposed to be located inside ZF2 application root.

## Declare packs of Bower packages

Next you need to declare packs of Bower packages you want to use. Each pack is a collection of Bower packages
which you want to have in both combined (for deploying) and separated (for debugging) states. To do that you need to set:

* `bower.pack_folder.os` - filesystem folder where all pack files should be located
* `bower.pack_folder.web` - web path to access all pack files
* `bower.debug_folder.os` - filesystem folder where all debug package files should be located
* `bower.debug_folder.web` - web path to access all debug package files
* `bower.packs.<pack name>.modules` - Bower packages names to form a pack
* `bower.packs.<pack name>.token` - string that will be appended to file path of the pack (to force browser to reload)
* `bower.debug_mode` - flag that determines if you are currently debugging your app and want each pack content in separate files  

Most configuration options have default values (check `config/module.config.php` for details), so the only thing 
you have to declare in your ZF2 app config is something like this (sample is in YAML just to make it easier to read):

    bower:
      debug_mode: false
      packs:
        main:
          token: 'f22f0d9703c2ef8dc9cc14f6286ee302'
          modules:
            - jquery-form
            - jquery.mousewheel
            - jquery.jscrollpane
            - angular
            - angular-animate
            - angular-messages
            - angular-ui-router
        admin:
          modules:
            - jquery
            - angular
            - bootstrap

Beware of hidden dependencies between packages: module does its best to detect package dependencies
from `bower.json` files but some of them are not declared there (like between `jquery.mousewheel` and `jquery.scrollpane`).

## Prepare pack content

Next you need to generate content for pack folder and debug folder. That can be done with console command
`bower prepare-packs` provided by this module (so you need to execute something like
`php ./public/index.php bower prepare-packs` which obviously depends on structure of your ZF2 app) or you can do that
manually if you prefer more sophisticated instrument like [grunt](http://gruntjs.com/) or [gulp](http://gulpjs.com/).
In later case you just need to prepare file `<pack name>.min.js` for each declared pack in pack folder and
file `<bower package name>.js` for each Bower package you used to declare packs in debug folder.

## Use pack in template

Last but not the least you need to use 'bower' view helper provided by this module to add all required JS files
to either inlineScript or headScript container in your template. For example if you declared pack 'main'
you can write something  `$this->bower('main');` to append all JS required for main pack to inlineScript view helper.
Or if you want normal IDE autocomplete:

    /** @var BowerModule\View\Helper\Bower $bower */
    $bower = $this->plugin('bower');
    $bower('main');

## Enjoy!

Hopefully this module will be helpful for someone except me :) Fill free to report issues and/or suggest new features.

