# RAN Plugin Starter

## Requirements

Minimum Requirements are PHP `8.2`, and this scaffold plugin has been built under WordPress `6.1.1`

This project relies on the Node runtime to process resources like Sass and JavaScript, and the dependency manager Composer for PHP resources like PSR-4 autoloading, and WordPress Code Standards and linting using PHP Code Sniffer.

[Installing Node Package Manager](https://docs.npmjs.com/downloading-and-installing-node-js-and-npm)
[Installing Composer](https://getcomposer.org/)

## Getting started

Using your terminal, `CD` into your this plugin's directory, and run the following command:

`$ composer install`

Followed by:

`$ npm install`

### Manually updating

Change the name of the plugin root directory as well as the root PHP file from `my-plugin.php` to `your-plugin.php` using kabab-case.

In `your-plugin.php` change the details of the documentation block as desired.

> Note: the name of your plugin file name, should be the same as your text domain.

Also check for relevant details within the following resources:

-   `package.json`
-   `composer.json`
-   `phpcs.xml`

## Avaliable tools

### Linting

#### Linting Styles and JavaScript

The project uses [StyleLint](https://stylelint.io/) and [EsLint](https://eslint.org/) with [Prettier](https://prettier.io/) using the configs to help ensure your code follows [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/):

-   [@wordpress/stylelint-config](https://developer.wordpress.org/block-editor/reference-guides/packages/packages-stylelint-config/)
-   [@wordpress/eslint-plugin](https://developer.wordpress.org/block-editor/reference-guides/packages/packages-eslint-plugin/)
-   [@wordpress/prettier-config](https://developer.wordpress.org/block-editor/reference-guides/packages/packages-prettier-config/)

In edition to enabling linting in your IDE, these rules can also be tested using the following commands:

`npm run sylelint` uses stylelint to lint scss
`npm run eslint` uses eslint to lint JavaScript
`npm run lint` lints both styles and JavaScript

#### Linting PHP

The project has included [WordPress Coding Standards](https://github.com/WordPress/WordPress-Coding-Standards) with [PHP Code Sniffer]() as `dev` dependencies using Composer.

As this configuration

// Stuff that needs to be done by user for their own environments.

After running the , if you already have Code Sniffer installed globally, and run: `$ phpcs -i` you will likely not see WordPress rules installed unless you have already installed them globally as well, however if you check in the local configuration version of `phpcs` like so: `./vendor/bin/phpcs -i` the output should look like the following:

    The installed coding standards are MySource, PEAR, PSR1, PSR2, PSR12, Squiz, Zend, WordPress, WordPress-Core, WordPress-Docs and WordPress-Extra

If you have a UNIX based operating system symbolic links to these local binaries have already been created:

```bash
ln -s /vendor/bin/phpcs phpcs
ln -s /vendor/bin/phpcbf phpcbf
```

So now you should be able to just run `./phpcs -i` to see the full set of installed rules. If you would rather use rules installed globally, then

### Helpful `phpcs` Commands

`./phpcs --version` PHP Code Sniffer version:

    PHP_CodeSniffer version 3.7.1 (stable) by Squiz (http://www.squiz.net)

`./phpcs -i` Shows installed rules:

The installed coding standards are MySource, PEAR, PSR1, PSR2, PSR12, Squiz, Zend, WordPress, WordPress-Core, WordPress-Docs and WordPress-Extra

`./phpcs --config-show` Shows the location and contents of configuration giving output like this:

    Using config file: /path/to/your/project/vendor/squizlabs/php_codesniffer/CodeSniffer.conf

    installed_paths: ../../wp-coding-standards/wpcs

## Developing front end resources

The project uses [Parcel](https://parceljs.org/) to compile Sass and JavaScript which allows for Hot Module Reloading to speed up your development, and a host of other features.

The project separates styles and JavaScript into two areas:

-   `assets/src/public` (front end of the website)
-   `assets/src/admin` (back end admin screens)

The primary commands are:

```bash
npm run watch
npm run build
npm run clean
```

Each of these commands can be run with the additional flags `:admin` or `:public` to target these resources specifically, i.e.

```bash
npm run build:admin
```

> NOTE: HMR fundamentally changes the contents of scripts and styles. Be sure to run your `build` commands prior to committing resources to production!

### Browser Targets

One of the many benefits of using Parcel is that it has many common toolchains built in, such as Babble and PostCSS's AutoPrefixer and more.Target builds are set done automagically using [BrowserList](https://github.com/browserslist/browserslist) rules found in `.browserlistrc`.

### Sass

To make it easier to import shared libraries we have configured `.sassrc` `includePaths` to include the `node_modules` and `./assets/src/shared/` directories, so you can `@import` sass libs installed from NPM, or shared with both public and admin styles.

```bash
  🚨 Build failed.
  @parcel/transformer-css: Unexpected token Delim('*')
```

If your build fails due to older IE hacks, you can enable CSS error recovery by adding the following to your `package.json`:

```json
 "@parcel/transformer-css": {
  "errorRecovery": true
 },
```

---

# DEV NOTES

So far we have extracted out many of the core functionality of the plugin into a single namespace: Base

Activation
Deactivation
BaseController (plugin paths etc)

Todo:
BaseController move managers declaration out of the BaseController.
It may be we move this entire functionality into a ServiceManagers class instead?

ServiceManagers has activated_manager, set_manager, get_managers methods, as well as a private managers array.

-   Common core services installable with Composer
    -   Plugin base directory
    -   Autoloading
-   Modular Administration Area
-   CPT generator
-   Custom Taxonomy Generator
-   Widget Generator
-   Metabox generation
-   Custom template section
-   Custom Login/Register screen
-   Custom fields
-   Shortcodes generation
-   Noces
-   Example Gutenburg block template
-   Move js and scss processing from gulp to NPM scripts.

Consolidate plugin specific configuration out of different base files into central services config that can be triggered without augmenting base files.

-   BaseController:
    Holds a 'managers' array which is the list of controllers (managers) identified by their slug. This is used by ManagerCallbacks and Dashboard.php to set individual options on the dashboard.

    -- Some of this could be centeralised on a "ServicesController", which could extend BaseController, and which the rest of the plugin would use rather then BaseController.

-   Activate.php:
    Individually enables named options using `update_option('bla', array())`

    -- perhaps this too could be moved to "ServicesController" as an `$options' array()`.

-   Deactivate does not do anything but flush rewrite rules.

-   uninstall.php only uninstalls a specific 'books' cpt. However, again the ServicesController might be able to dynamically handle this, however there may be some 'quirks' about using OOP with uninstall or autoloading? Vague memory here...

-   Enqueue.php also has a static list of files and services that it loads. This could be part of Services Enqueue could be a helper for enquing media, styles and scripts.

-   SettingsLinks.php

    -- Like many of the above, it enques a set list of links instead of being dynamic.
    Here SettingsLinks is triggered by our list of classes in init::getServices().
    Because of this, its more difficult to create a dynamic list of links that can be changed.

    Again a ServicesController class could create a master list of services... and Init, would register only classes that extend Base classes instead of the core funcionality.

-   Init.php:
    As mentioned, currently the instantiate() method works with a static array of 'services'. All of Init's methods are currently static, so can be extended.

    Its a bit weird to see Enqueue and SettingsLinks enqued here, as I would think that they would be a part of some base configuration.

    Basically I'd expect that each of these would be called from a Services namespace.

    Move the registerService, and instantiate methods into a base class. getServices() should be in the init class, within the Services namespace.
