# RAN Plugin Starter

So far we have extracted out many of the core functionality of the plugin into a single namespace: Base

Activation
Deactivation
BaseController (plugin paths etc)

Todo:
BaseController move managers declaration out of the BaseController.
It may be we move this entire functionality into a ServiceManagers class instead?

ServiceManagers has activated_manager, set_manager, get_managers methods, as well as a private managers array.

-   Common core services installable with compposer
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
-   Example gutenburg block template
-   Move js and scss processing from gulp to NPM scripts.

Consolidate plugin specific configuration out of different base files into central services config that can be triggered without augmenting base files.

-   BaseController:
    Holds a 'managers' array which is the list of contollers (managers) identified by their slug. This is used by ManagerCallbacks and Dashboard.php to set individual options on the dashboard.

    -- Some of this could be centeralised on a "ServicesController", which could extend BaseController, and which the rest of the plugin would use rather then BaseController.

-   Activate.php:
    Individually enables named options using `update_option('bla', array())`

    -- perhaps this too could be moved to "ServicesController" as an `$options' array()`.

-   Deactivate does not do anything but flush rewrite rules.

-   uninstall.php only uninstalls a specific 'books' cpt. However, again the ServicesController might be able to dynamically handle this, however there may be some 'quirks' about using OOP with uninsall or autoloading? Vague memory here...

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
