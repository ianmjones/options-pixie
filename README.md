# [Options Pixie](https://wordpress.org/plugins/options-pixie/)
[![Build Status](https://img.shields.io/travis/bytepixie/options-pixie/develop.svg)](https://travis-ci.org/bytepixie/options-pixie) [![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/bytepixie/options-pixie.svg)](https://scrutinizer-ci.com/g/bytepixie/options-pixie/) [![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/bytepixie/options-pixie.svg)](https://scrutinizer-ci.com/g/bytepixie/options-pixie/) [![License](https://img.shields.io/badge/license-GPL--2.0%2B-green.svg)](https://github.com/bytepixie/options-pixie/blob/master/src/LICENSE.txt)

A WordPress plugin to list, filter, sort and view options records, even serialized and base64 encoded.

## Installing
### From your WordPress dashboard
1. Visit 'Plugins > Add New'
1. Search for 'Options Pixie'
1. Activate Options Pixie from your Plugins page.

### From WordPress.org
1. Download [Options Pixie](https://wordpress.org/plugins/options-pixie/).
1. Upload the 'options-pixie' directory to your '/wp-content/plugins/' directory, using your favorite method (ftp, sftp, scp, etc...)
1. Activate Options Pixie from your Plugins page.

### From GitHub
1. Clone https://github.com/bytepixie/options-pixie.git or download the [zip](https://github.com/bytepixie/options-pixie/archive/master.zip)
2. Symlink `src` into your WordPress plugins folder as `options-pixie`.

## Running Unit Tests
Run `tests/bin/run-unittests.sh -d testdb_name`

Usage: tests/bin/run-unittests.sh -d testdb_name [ -u dbuser ] [ -p dbpassword ] [ -h dbhost ] [ -P dbport ] [ -x dbprefix ] [ -D (drop-db) ] [ -s plugin_slug ] [ -c coverage_file ]

## Bugs & Feature Requests
Please report any issues via our [GitHub Issues list](https://github.com/bytepixie/options-pixie/issues).