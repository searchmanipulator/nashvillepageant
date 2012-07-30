=== EZPZ One Click Backup ===

Contributors: EZPZSolutions, Joe "UncaJoe" Cook

Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=JSQGRHN58DXPE

Tags: backup, plugins, wordpress, security, dropbox, easy, maintenance

Requires at least: 3.0

Tested up to: 3.3.1

Stable tag: 12.03.10



EZPZ One Click Backup(OCB) is a very easy way to do a complete backup of your entire WordPress site. EZPZ OCB is not compatible with Windows servers.



== Description ==



**EZPZ One Click Backup**, or **EZPZ OCB** as we call it, is a very easy way to do a complete backup of your entire Wordpress site. In fact it's so easy to use there are no required user settings, everything is automatic. Just one click and presto, you'll have a complete backup stored on your server. One more click and you can download the entire backup to your own computer.



**EZPZ OCB** now features a "One-Stop" control panel with status indicators and better cross-browser compatibility. (Tested on Firefox 3.5+, IE 9, Chrome, Safari 5.1 [windows version], Opera 11, and Flock 2.6. It is compatible with IE 9 but earlier versions may have some display quirks during Manual backups however the backups are NOT affected.)



**EZPZ OCB** also stores up to 10 backups on the server.



With **EZPZ Easy Restore** restoring your site is a simple two step process.



EZPZ OCB can automatically upload your backups to your **Dropbox** account or any FTP server.



Now just because no settings are required doesn't mean there are no options. There are several choices that can make your backup the way you want.



1. You can schedule backups ranging from 4 times a day to once per week.

1. With the optional Dropbox Extension you can save backups to your Dropbox account.

1. You can transfer backups via FTP.

1. EZPZ Restoration now features multiple redundant backups for safety.

1. You can receive email alerts for scheduled backups.

1. The option to choose the timezone your backup's datestamp is based on.

1. Choose one of ten pre-defined datestamp formats for your backup or customize your own.

1. If you're using a shared database you can choose to backup only the tables needed for your WordPress installation.

1. You can choose to exclude selected folders you don't want to include in the backup.



Like most applications **EZPZ OCB** has certain limitations and requirements. First and foremost, **EZPZ OCB only works on Linux servers running PHP 5.2 and above** and only those servers which allow certain required php functions with exec and mysqldump seeming to be the most frequently unavailable ones.



Most WordPress users will have no problems but there are some servers with which **EZPZ OCB** is simply incompatible. Sorry...



On the drawing board...



* Internationalization.

* Amazon S3 (Simple Storage Service) integration.



== Installation ==



1. Upload 'ezpz-one-click-backup' to the '/wp-content/plugins/' directory

1. Activate **EZPZ One Click Backup** through the 'Plugins' menu in WordPress





== Frequently Asked Questions ==



= Is it really one click? =



Yes and no. The backup can be completed by a single click with absolutely no user settings required.



You will need to make a second click if you choose to download your backup but let's face it, what's the purpose of a backup if you're not keeping it somewhere other than your server.



= How do I restore my site from a backup using EZPZ Easy Restore? =



Full instructions are included in the FAQ's section of the control panel.



== Screenshots ==



1. Log of a successful backup, `/tags/12.03.10/images/screenshot-1.png`



2. The new Control Panel, `/tags/12.03.10/images/screenshot-2.png`



3. Improved scheduling with email notification, `/tags/12.03.10/images/screenshot-3.png`



4. Extension Settings, `/tags/12.03.10/images/screenshot-4.png`



5. Rescheduled backup warning, `tags/12.03.10/images/screenshot-5.png`



== Upgrade Notice ==



= 12.03.10 =

Recommended for immediate upgrade. Fixed missing htaccess file in backups. Updated Dropbox extension to new API requirements. Added database repair and optimization before backup option. 


== Changelog ==

= 12.03.10 =

* Added database repair and optimization option.* Updated Dropbox extension to new API requirements

= 12.03.01 =

* Fixed missing .htaccess in backup

* Added wp database repair and optimization prior to database dump

* Added timeout script to control panel to reduce server load

= 12.02.08 = 

* Fixed cron_log error.

= 12.02.07 =

* Several bug fixes including scheduling problem.


= 12.01.26 =

* bcadd() function is no longer used. Workaround created.


= 12.01.24 =

* Improved compatibility checks

* Fixed FTP password special character error

* Replaced shell_exec() function call with exec()

* Improved c-panel performance


= 12.01.16 =

* Added new control panel

* Added status indicators

* Major recoding for better performance and browser compatibility

* Added multi-backup capability


= 0.8.0 =

* Added improved menu functions

* Improved restore function

* Made Dropbox extension optional per Wordpress plugin repository T.O.S


= 0.7.0.3 =

* Added fix for backups freezing up plugin.


= 0.7.0.1 =

* Fixed database bug

* Fixed typo in restoration script

= 0.7.0 =

* Added ability to split large archives for better Dropbox transfers

* Improved restoration process

* Added background backup feature


= 0.6.5 =

* Improved backup methods

* Improved scheduling

* Added email alerts for backups option

* Added ability to change backup folder name(not location)


= 0.6.3.1 =

* Fixed intermittent cron execution problem

* Streamlined zip process

* Exclude server generated error_log files from backups


= 0.6.3 =

* Added FTP support

* Added email alerts for FTP backups

* Fixed cron operation

* Reformatted zipping to avoid using ZipArchive altogether


= 0.6.0.1 =

* Corrected ZipArchive test and two typos (My thanks to Simon!)


= 0.6.0 =

* Added Dropbox extension


= 0.5.1.2 =

* Added missing ZipArchive workaround


= 0.5.1.1 =

* Fixed folder size calculation bug

* Deactivated Faq and News auto updates until bug fix is found


= 0.5.0 =

* Added EZPZ Easy Restore capability

* Added auto updated FAQ section

* Added auto updated News section

* Streamlined coding and corrected typos

* Improved error handling


= 0.4.6 =

* Added ability to customize datestamp format

* Added option to block browser downloads of backup files

* Added option to log php errors for troubleshooting purposes

* Removed troublesome asterisk in cron backups

* Added pre-installation compatibility checks for most troublesome issues


= 0.4.5 =

* Added ability to schedule backups using wp-cron

* Timezone bug RESOLVED

* Relocated sql file in backup file for easier locating

* Streamlined coding for smoother operation


= 0.4.2 =

* Added optional datestamp formats

* Added improved timezone support

* Added styling option

* Streamlined coding


= 0.4.0 =

* Added option to exclude folders

* Added option to adjust execution speed

* Added option to backup wp-content folder only

* Added option for more control over database backup


= 0.3.0.2 =

* mysqldump problem RESOLVED


= 0.3.0.1 =

* Storing excess backups bug RESOLVED


= 0.3.0 =

* Improved cross-browser performance

* Now BASH free, All scripting is in PHP/JAVASCRIPT

* Improved timer

* Streamlined code for faster operation

* Now using tar archive format to improve performance


= 0.2.9 =

* Download saved file bug RESOLVED

* Individualized zip and sql files based on blog name

* Added support for shared databases

* Added elapsed time counter


= 0.2.8 =

* Now compatible with WordPress 2.6 +


= 0.2.5 =

* Public Release


= 0.2.2 =

* Beautified display

* Added visual confirmation of data collection and archive completion


= 0.2.0 =

* IE download bug RESOLVED


= 0.1.2 =

* Download saved backup bug RESOLVED


= 0.1.0 =

* Initial limited release for testing