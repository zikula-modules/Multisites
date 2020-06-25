# CHANGELOG

Changes in Multisites 2.1.0

- Regenerated using ModuleStudio 1.3.2-dev.
- Dropped unneeded site extension table (#29).
- Migrated to Zikula 2.0.15+ (#29).

Changes in Multisites 2.0.0

- Reimplemented module using ModuleStudio (early development 0.7.0 version) (#16).
- Added an index for the siteDNS field to the site table.
- Added project table for grouping sites by client or topic.
- Put basic settings into two different sections (general settings and security-related options).
- Added custom action for exporting an existing site database into a new template.
- Added support for template parameters (variable information to be defined for each site).
- Templates can export a csv file for their parameters which can be used for new sites.
- Added two parameter fields to the site table (csv upload, array for manual form-based input).
- Added custom action for reapplying templates to all assigned sites.
- Added two new upload fields to the site entity for storing logo and favicon images.
- Added new optional field to the site entity for defining a whitelist of allowed languages (#17).
- Changed folders field type in the template entity from string to array.
- Added array field named exludedTables to the template entity to allow skipping certain tables when reassigning a template to all sites based on it.
- A new query multiplier and shell interface allows to perform sql queries on all databases (#14).
- Removed unrequired access table.
- Many other new features.
