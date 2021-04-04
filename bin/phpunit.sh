#!/usr/bin/env bash
WORKING_DIR="$PWD"
cd "$WP_CORE_DIR/wp-content/plugins/wp-ever-accounting/"
if [[ {$COMPOSER_DEV} == 1 ]]; then
	./vendor/bin/phpunit --version
	if [[ {$RUN_RANDOM} == 1 ]]; then
		./vendor/bin/phpunit -c phpunit.xml --order-by=random
	else
		./vendor/bin/phpunit -c phpunit.xml
	fi
else
	phpunit --version
	phpunit -c phpunit.xml
fi
TEST_RESULT=$?
cd "$WORKING_DIR"
exit $TEST_RESULT
