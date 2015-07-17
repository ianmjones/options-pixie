#!/bin/bash

RUN_CMD="tests/bin/run-unittests.sh -d wordpress_test -u travis -h 127.0.0.1"

#
# Only run coverage check for one build as it's quite slow.
#
if [ "3.9" == "${WP_VERSION}" -a "0" == "${WP_MULTISITE}" -a "5.3" == "${TRAVIS_PHP_VERSION}" ]
then
	bash ${RUN_CMD} -c /tmp/clover.xml

	# Send coverage to Scrutinizer CI.
	curl -sSL https://scrutinizer-ci.com/ocular.phar -o ocular.phar
	php ocular.phar code-coverage:upload --format=php-clover /tmp/clover.xml

	# Quick check that coverage hasn't dropped.
	tests/bin/coverage-checker.php /tmp/clover.xml 5
else
	bash ${RUN_CMD}
fi
