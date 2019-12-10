# Multisites

The Multisites module.

**Please check the [master branch](https://github.com/zikula-modules/Multisites/tree/master) for the new version!**

## Documentation

  1. [Introduction](#introduction)
  2. [Requirements](#requirements)
  3. [Installation](#installation)
  4. [Upgrading](#upgrading)
  5. [Configuration](#configuration)
  6. [FileUtil change](#fileutilchange)
  7. [Structure and management](#structure)
  8. [Creating and adapting sites](#siteoperations)
  9. [Questions, bugs and contributing](#contributing)


<a name="introduction" />

## Introduction

The Multisites module allows to create and use a huge number of websites for arbitrary purposes and clients.
Beside normal "main" sites you can also run other relevant pages, like for example for multiple retail stores,
sales partners or landing pages for specific topics.

It is possible to manage any number of projects for multiple clients. Each project can thereby contain any number
of sites. By using site templates you can easily reuse structure, content as well as layouts and functionality across
multiple sites.


<a name="requirements" />

## Requirements

This module is intended for being used with Zikula 1.4.5+.


<a name="installation" />

## Installation

The Multisites module is installed like this:

1. Copy ZikulaMultisitesModule into your `modules` directory. Afterwards you should have a folder named `modules/Zikula/MultisitesModule/Resources`.
2. Copy the content of _Resources/config-folder/_ into the _/config_ folder of your Zikula site.
3. Copy the content of _Resources/app-resources/_ into the _/app/Resources_ folder of your Zikula site.
4. Initialize and activate ZikulaMultisitesModule in the extensions administration.
5. Move or copy the directory `Resources/userdata/ZikulaMultisitesModule/` to `/userdata/ZikulaMultisitesModule/`.
   Note this step is optional as the install process can create these folders, too.
6. Make the directory `/userdata/ZikulaMultisitesModule/` writable including all sub folders.


<a name="upgrading" />

## Upgrading

The upgrade process from 2.0.0 to 2.1.0 has *NOT* been implemented yet.

The upgrade process from 1.0.x to 2.0.0 has been implemented.
It has not been tested throughly yet though.

Please report your experience.


<a name="configuration" />

## Configuration

When entering the Multisites administration area you will be redirected to a configuration wizard
automatically.


<a name="fileutilchange" />

## FileUtil change

At the moment you are required to edit the _/lib/legacy/util/FileUtil.php_ file and change the _getDataDirectory()_ method as follows:

```
    /**
     * Get system data directory path.
     *
     * @return string The path to the data directory.
     */
    public static function getDataDirectory()
    {
        global $ZConfig;

        $path = DataUtil::formatForOS(System::getVar('datadir'));

        // return if this is a normal Zikula installation
        if (!isset($ZConfig['Multisites']) || !isset($ZConfig['Multisites']['multisites.enabled']) || $ZConfig['Multisites']['multisites.enabled'] != 1) {
            // strip prepending slash
            $path = substr($path, 1, (strlen($path)-1));

            return $path;
        }

        // this is a Multisites installation
        $msConfig = $ZConfig['Multisites'];

        $scriptRealPath = substr($_SERVER['SCRIPT_FILENAME'], 0, strrpos($_SERVER['SCRIPT_FILENAME'], '/'));
        $dataFolderSegment = $msConfig['multisites.site_files_folder'];

        $absoluteDataPath = $msConfig['multisites.files_real_path'] . $dataFolderSegment;
        $relativeDataPath = str_replace($scriptRealPath, '', $absoluteDataPath);

        // check whether this is the main site
        $sitedns = $_SERVER['HTTP_HOST'];
        if ($sitedns == $msConfig['multisites.mainsiteurl']) {
            $path = $relativeDataPath;
        } else {
            // amend path to point to the site folder
            $path = $relativeDataPath;
            $pos = strrpos($path, $dataFolderSegment);
            if ($pos !== false) {
                $path = substr_replace($path, '', $pos, strlen($dataFolderSegment));
            }
            $path .= '/' . $msConfig['multisites.sitealias'] . $dataFolderSegment;
        }

        // strip prepending slash
        $path = substr($path, 1, (strlen($path)-1));

        return $path;
    }
```


<a name="structure" />

## Structure and management

1. Projects

   1. Example: _client xy_
   2. A project serves for grouping websites for a certain client or topic.
   3. A project can contain multiple site templates.
   4. A project can contain multiple sites.


2. Site templates ("website master")

   1. Examples: _central website_, _community portal_, _sales partner page_
   2. Each site template represents the blueprint for multiple sites.
   3. Each site template includes a sql file containing it's structure and data. This involves all created content pages, user permissions and much more.
   4. After a site has been created it's database can be exported in order to reuse it as site template for other sites afterwards.
   5. A site template can be available either for all or just for specific projects.


3. Sites

   1. Example: _community portal xy_
   2. Each site is assigned to a project and based on a certain site template.
   3. Each site gets a unique name as well as a domain or subdomain.
   4. A site carries more data, like credentials for the database and for the primary admin user.
   5. For each site you can upload a logo as well as a favicon image.
   6. Each site is equipped with a dedicated databases (for reasons of architecture and scalability). It is also possible to let multiple sites work with the same database. This requires corresponding customisations in the used theme though.
   7. The Multisites backend offers a quick search function to find a certain site quickly.


4. Site parameters

   1. A site template can define parameters. These correspond to a variable piece of information which must be entered for each site based on this template. For example a template describing sites for sales partners could require parameters for the address, contact data and other individual data.
   2. The detail page of a template documents the existing parameters to make this information transparent.
   3. The parameter values for a certain site can be uploaded using a csv file. The first table column contains the parameter name (e. g. _LASTNAME_), the second column contains the value for the edited site. Alternatively you can also enter these values by hand using a form.
   4. When creating or editing a site which is going to be used as a template you can use placeholders which are replaced by the parameter values of sites based on the template in the final output of a page. Parameter values are also provided as module variables (see below for more details).


5. Themes / Layouts

   1. New layouts are placed in a central place in the system (themes/ folder).
   2. Layouts can centrally inspected and activated for each site.
   3. Which layouts are available and which ones are initially active for a site is controlled as part of the underlying site template.


6. Modules

   1. Examples: _News_, _Forums_, _Guestbook_, etc.
   2. New modules are placed in a central place in the system (modules/ folder).
   3. Modules can centrally inspected and activated for each site.
   4. Which modules are available and their states for a site is controlled as part of the underlying site template.


<a name="siteoperations" />

## Creating and adapting sites

1. Initialise sites

  When a site is initially created the database is filled with the assigned template's data. After that
  the CMS delivers this site as soon as it gets called by the configured domain.

  During the site creation all parameters are replaced using their deposited values. This approach
  makes the initialisation process repeatable.


2. Copy sites

  To duplicate an already created site you can use the corresponding database as a basis for a new site template.
  This new template can then be used for creating further sites, like described above.


3. Change templates

  To amend a site template change the site which was used to create the template initially. Edit the site's structure
  and content until it meets your requirements. Afterwards the database is exported again and the resulting sql file
  is uploaded again into the site template.


4. Copy templates

  Site templates can be copied to change them and store them with a new name. Site parameter definitions are
  considered and included during the copy process.


5. Reapply templates

  It is possible to reapply a site template which has been updated before to all sites based on this template.
  After approving a confirmation question the initialisation process is performed again for all affected sites ("reset").
  This includes overriding of existing content, unless specific database tables have been added to the list of table names
  to be excluded in the template data. You can even use wildcards here, for example you can exclude all Content tables
  by using "content_*".

  It is also possible to decouple selected sites from their template. As soon as the assignment is removed this
  site is not affected anymore when the template is reapplied, but is independently configured and maintained.
  If a decoupled site is reassigned to a site template, this template is applied to it's database like described above.


6. Customise layouts

  By using template plugins it is possible to represent variants within one layout theme. Therewith you can
  use a theme for multiple sites. All data, variables and settings can be part of individual logic thereby.
  This particularly includes the site parameters mentioned above. So if you for example have three sites for
  different cities can check the content of the _city_ parameter for design-related decisions.

  For this reason all parameter values are provided as module variables during the site initialisation process.

  Use in code files:
```php
    $city = ModUtil::getVar('Multisites', 'parameterValueCity', 'default value');
    if ($city == 'Berlin') {
        echo '<h2>Hello Berlin</h2>';
    }
```

  Use in templates:
```
    {modgetvar module='Multisites' name='parameterValueCity' default='default value' assign='city'}
    {if $city eq 'Munich'}
        <h2>Hello Munich</h2>
    {/if}
```


<a name="contributing" />

## Questions, bugs and contributing

If you want to report something or help out with further development of the Multisites module please refer
to the corresponding GitHub project at https://github.com/zikula-modules/Multisites
