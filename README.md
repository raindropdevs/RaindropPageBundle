# WARNING: This bundle is under development and not ready for production use.

# Raindrop Page Bundle

This bundle offers a simple CMS mechanism based on SonataAdminBundle + stuffs and FOSUserBundle.
We also use some proprietary bundle.
This bundle exists as an alternate approach to SonataPageBundle, in particular it is more
database oriented for long term and continuous maintenance and the administration is all
bound to a backend application (no frontend inline editing. As alternative option, preview is into roadmap).


### **INSTALLATION**:

First add the dependency to your `composer.json` file:

    "require": {
        ...
        "raindrop/page-bundle": "dev-master"
    },

Then install the bundle with the command:

    php composer.phar update

Enable the bundle in your application kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Raindrop\PageBundle\RaindropPageBundle(),
    );
}
```

Now the bundle is enabled.

### **CONFIGURATION**:

First of all update your schema:
    php app/console doctrine:schema:update --force (for development environment)
now it is up to your strategy... the production should migrate similarly or execute a query as result to following
    php app/console doctrine:schema:update --dump-sql

There is no need for yml configuration except for the fact that you maybe
want PageAdmin to show up into your dashboard:

    sonata_block:
        default_contexts: [cms]
        blocks:
            raindrop_page.block.service.template: // <---- add this line
            sonata.admin.block.admin_list:
                contexts:   [admin]

### **USAGE**:
