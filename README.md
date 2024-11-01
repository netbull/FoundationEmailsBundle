FoundationEmailsBundle
==========

Installation
============

Applications that use Symfony Flex
----------------------------------

Open a command console, enter your project directory and execute:

```console
$ composer require netbull/foundation-emails-bundle
```

Applications that don't use Symfony Flex
----------------------------------------

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require netbull/core-bundle
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new NetBull\FoundationEmailsBundle\NetBullFoundationEmailsBundle(),
        );

        // ...
    }

    // ...
}
```

### Step 3: Configuration
After installing the bundle, configure it by adding the following settings to your Symfony configuration file:
```yaml
# config/packages/netbull_foundation_emails.yaml
netbull_foundation_emails:
  templates_path: '%kernel.project_dir%/templates/Emails'    # Path to the email templates
  custom_inky_path: null                                     # Optional custom path to the Inky library
  rendered_templates_path: '%kernel.project_dir%/var/email_previews'  # Output directory for rendered email templates
```
Commands
============
This bundle provides two Symfony console commands for handling email templates:

### 1. Test Email Templates
**Command:** netbull:emails:test

**Description:** This command allows you to send test emails using the templates to verify their appearance and delivery.

**Usage:**

```bash
php bin/console netbull:emails:test
```
**Options:**

--template, -t: Specify a particular template for sending. If not provided, all available templates will be sent.

**Example:**

```bash
php php bin/console netbull:emails:test --template email/example.inky.twig
```

### 2. Render Email Templates
**Command:** netbull:emails:render

**Description:** This command renders all email templates located in the specified templates_path and saves the rendered versions to the rendered_templates_path. It is useful for testing email templates or preparing preview files for debugging in the browser.

**Usage:**

```bash
php bin/console netbull:emails:render
```

**Options:**

--template, -t: Specify a particular template to render. If not provided, all available templates will be rendered.

**Example:**

```bash
php php bin/console netbull:emails:render --template email/example.inky.twig
```

