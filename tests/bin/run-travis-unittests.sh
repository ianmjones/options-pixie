#!/bin/bash

RUN_CMD="tests/bin/run-unittests.sh -d wordpress_test -u travis -h 127.0.0.1"

#
# Only run coverage check for one build as it's quite slow.
#
if [ "3.9" == "${WP_VERSION}" -a "0" == "${WP_MULTISITE}" -a "5.3" == "${TRAVIS_PHP_VERSION}" ]
then
	bash ${RUN_CMD} -c /tmp/clover.xml

	# Send coverage to Scrutinizer CI.
	php vendor/bin/coveralls -v -x /tmp/clover.xml
else
	bash ${RUN_CMD}
fi
