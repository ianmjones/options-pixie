#!/bin/bash

for PROG in mysqladmin composer curl
do
	which ${PROG}
	if [ 0 -ne $? ]
	then
		echo "${PROG} not found in path."
		exit 1
	fi
done

# Get reference to and enter root of plugin source.
REPO_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )/../.." && pwd )"
SRC_BASE_DIR="${REPO_DIR}/src"
TESTS_BASE_DIR="${REPO_DIR}/tests"
BUILD_CFG_BASE_DIR="${REPO_DIR}/build-cfg"

cd "${REPO_DIR}"

function usage() {
	echo "Usage: $0 -d testdb_name [ -u dbuser ] [ -p dbpassword ] [ -h dbhost ] [ -P dbport ] [ -x dbprefix ] [ -D (drop-db) ] [ -s plugin_slug ] [ -c coverage_file ]"
	exit 2
}

while getopts "c:d:r:u:p:h:P:x:DU:F:s:" ARG
do
	case ${ARG} in
		c)	COVERAGE_FILE=$OPTARG;;
		d)	DB_NAME=$OPTARG;;
		u)	DB_USER=$OPTARG;;
		p)	DB_PASSWORD=$OPTARG;;
		h)	DB_HOST=$OPTARG;;
		P)	DB_PORT=$OPTARG;;
		x)	DB_PREFIX=$OPTARG;;
		D)	DROP_DB=true;;
		s)	PLUGINS=$OPTARG;;
		\?)	usage;;
	esac
done
shift `expr $OPTIND - 1`

if [ -z ${DB_NAME} ]
then
	echo "Test Database Name required."
	usage
fi

DB_USER=${DB_USER-root}
DB_HOST=${DB_HOST-localhost}
DB_PORT=${DB_PORT-3306}
DB_PREFIX=${DB_PREFIX-wptests_}
DROP_DB=${DROP_DB-false}

export WP_VERSION=${WP_VERSION-latest}
export WP_MULTISITE=${WP_MULTISITE-0}
export WP_CORE_DIR="${WP_CORE_DIR-/tmp/wordpress-${WP_VERSION}/}"
export WP_TESTS_DIR="${WP_TESTS_DIR-/tmp/wordpress-${WP_VERSION}-tests-lib}"

ALL_PLUGINS="options-pixie"
PLUGINS=${PLUGINS-${ALL_PLUGINS}}

set -ex

function check_plugin_slug() {
	PLUGIN_FILE="${SRC_BASE_DIR}/${PLUGIN_SLUG}.php"

	if [ ! -f "${PLUGIN_FILE}" ]
	then
		echo "${PLUGIN_FILE} does not exist."
		usage
	fi

	BUILD_CFG_DIR="${BUILD_CFG_BASE_DIR}/${PLUGIN_SLUG}"

	if [ ! -d "${BUILD_CFG_DIR}" ]
	then
		echo "${BUILD_CFG_DIR} does not exist."
		usage
	fi

	TESTS_DIR="${TESTS_BASE_DIR}/${PLUGIN_SLUG}"

	if [ ! -d "${TESTS_DIR}" ]
	then
		echo "${TESTS_DIR} does not exist."
		usage
	fi
}

function init_env() {
	#
	# Set up local unit test environment.
	#
	cd "${REPO_DIR}"
	composer install
	export PATH="${REPO_DIR}/vendor/bin:${PATH}"

	# Create local database for unit tests.
	bash "${TESTS_BASE_DIR}/bin/install-wp-tests.sh" ${DB_NAME} ${DB_USER} "${DB_PASSWORD}" ${DB_HOST}:${DB_PORT} ${WP_VERSION} ${DROP_DB}
}

function build_plugin() {
	PLUGIN_VERSION=`grep "Version: " "${SRC_BASE_DIR}/${PLUGIN_SLUG}.php" | awk '{print $3}'`

	#
	# Build plugin zip file.

	cd "$REPO_DIR"

	if [ ! -d builds ]
	then
		mkdir builds
	fi

	if [ ! -f builds/plugin-build -o ! -x builds/plugin-build ]
	then
		curl -sSL https://raw.githubusercontent.com/deliciousbrains/wp-plugin-build/master/plugin-build -o builds/plugin-build
		chmod +x builds/plugin-build
	fi

	cd "${BUILD_CFG_BASE_DIR}/${PLUGIN_SLUG}"
	"$REPO_DIR/builds/plugin-build" ${PLUGIN_VERSION}
}

#
# First set up env and check that building plugin(s) works.
#
init_env

for PLUGIN_SLUG in ${PLUGINS}
do
	check_plugin_slug

	if [ 0 -ne $? ]
	then
		"Trouble validating plugin ${PLUGIN_SLUG}."
		usage
	fi

	build_plugin
done

PHPUNIT_OPTS="--debug"

if [ "${ALL_PLUGINS}" != "${PLUGINS}" ]
then
	PHPUNIT_OPTS="${PHPUNIT_OPTS} --testsuite ${PLUGINS}"
fi

if [ -n "${COVERAGE_FILE}" ]
then
	PHPUNIT_OPTS="${PHPUNIT_OPTS} --coverage-clover=${COVERAGE_FILE}"
fi

# Enter the tests folder.
cd "${TESTS_BASE_DIR}"

# Run the tests.
phpunit ${PHPUNIT_OPTS}