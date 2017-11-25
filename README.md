ManagePanel
===========

System administration tool.  Manage user accounts, permissions, and other data 
shared between the apps.

https://netsyms.biz/apps/managepanel

Installing
----------

0. Follow the installation directions for [AccountHub](https://source.netsyms.com/Business/AccountHub), then download this app somewhere.
1. Copy `settings.template.php` to `settings.php`
2. Import `database.sql` into your database server
3. Edit `settings.php` and fill in your DB info ("DB_*" for the AccountHub database, "DB2_*" for the ManagePanel one you just installed)
4. Set the location of the AccountHub API in `settings.php` (see "PORTAL_API") and enter an API key ("PORTAL_KEY")
5. Set the location of the AccountHub home page ("PORTAL_URL")
6. Set the URL of this app ("URL")
7. Run `composer install` (or `composer.phar install`) to install dependency libraries.