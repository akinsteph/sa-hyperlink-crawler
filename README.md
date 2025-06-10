# Sa Hyperlink Crawler

## Usage
This WordPress plugin records which hyperlinks are visible above the fold when visitors load site homepage.

## Get started
- Have a mysql DB ready and a user.
- Have `svn` installed.
- Run `composer install`
- Run `bash bin/install-wp-tests.sh wordpress_test mysql_user mysql_password localhost latest`
- Run `composer run-tests` to run the unit and integration tests
- Run `composer phpcs`
- Activate the plugin on your site to begin tracking homepage visits

## Content
* `bin/install-wp-tests.sh`: installer for WordPress tests suite
* `.editorconfig`: config file for your IDE to follow our coding standards
* `.gitattributes`: list of directories & files excluded from export
* `.gitignore`: list of directories & files excluded from versioning
* `.travis.yml`: Travis-CI configuration file
* `composer.json`: Base composer file to customize for the project
* `LICENSE`: License file using GPLv3
* `phpcs.xml`: Base PHP Code Sniffer configuration file to customize for the project
* `README.md`: The readme displayed on Github, to customize for the project

## Development Notes
This repository contains a WordPress plugin for tracking hyperlinks on the homepage. Each feature branch holds a part of the implementation. See `Explanation.md` for a high-level design overview.