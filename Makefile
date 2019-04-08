.PHONY: check-style check-cpd check-pmd
.PHONY: check-js
.PHONY: check-all-ci

# Both these command exit non-0 when any violations occur
# We want to ignore that so we can get all the results in jenkins
.IGNORE: check-style check-cpd check-pmd
.IGNORE: dev-version dev-db-version
.IGNORE: coverage-acceptance coverage-functional duplicate-code code-lines code-style code-quality
.IGNORE: test-db
.IGNORE: php-server-stop-ci

dev-version:
	php -r 'echo str_pad(implode(".", str_split(shell_exec("cat ./updates_pack/version.upd"))), 5, ".0");'

dev-db-version:
	php -r 'echo intval(str_pad(preg_replace("/[^\d]/", "", shell_exec("make -s dev-version")), 6, 0));'

coverage-acceptance:
	cat build/tests/acceptance.remote.coverage/index.html |\
	grep -m 1 -Eo '[0-9]+.[0-9]+%'

coverage-functional:
	cat build/tests/coverage/index.html |\
	grep -m 1 -Eo '[0-9]+.[0-9]+%'

duplicate-code:
	cat ./build/check-cpd.log |\
	grep -Eo '[0-9]+.[0-9]+%'

code-lines:
	cat build/tests/coverage/index.html |\
	grep -m 1 -Eo "[0-9]+.+/.+([0-9]+)" |\
	sed -n 's/.*\/[^0-9]*\([0-9]*\)/\1/p'

code-style:
	echo `cat ./build/checkstyle.xml | grep '<error' | wc -l`

code-quality:
	echo `cat ./build/pmd.xml | grep '<violation' | wc -l`

check-style:
	phpcs --standard=PSR2 --extensions=php -p --report=checkstyle \
		--ignore=*/libs/*,*/config/*,*/messages/*,*/tests/*,*/docs/*,*/runtime/* \
		--ignore=*/views/*,*/gii/*,*/source/*,*/web/assets/* \
		--report-file=build/checkstyle.xml ./default_site 2>&1 | tee build/check-style.log

check-cpd:
	phpcpd --min-lines 3 --min-tokens 50 \
		--exclude libs \
		--exclude config \
		--exclude messages \
		--exclude tests \
		--exclude docs \
		--exclude runtime \
		--exclude gii \
		--exclude source \
		--exclude web/assets \
		--log-pmd build/cpd.xml ./default_site 2>&1 | tee build/check-cpd.log

check-pmd:
	phpmd default_site xml phpmd.xml --suffixes php \
		--exclude libs,config,messages,tests,docs,runtime,gii,source,web/assets \
		--reportfile build/pmd.xml

fix-style:
	phpcbf --standard=PSR2 \
		--ignore=*/libs/*,*/config/*,*/messages/*,*/tests/*,*/docs/* \
		--ignore=*/runtime/*,*/views/*,*/gii/*,*/source/*,*/web/assets/* \
		./default_site 2>&1 | tee build/fix-style.log

docs:
	default_site/vendor/bin/apidoc api default_site build/docs/api \
	--interactive=0 --color=0 --pageTitle="Yii2 Community CMS API Documentation" \
	&> build/apidoc.log

php-server:
	php default_site/yii serve --port=9969

php-server-stop-ci:
	kill -9 $$(lsof -t -i:9969)

php-server-start-ci:
	php default_site/yii serve \
		--config=$$PHP_INI --port=9969 --interactive=0 \
		> build/php-server-ci.log 2>&1 &

selenium-server:
	default_site/vendor/bin/selenium-server-standalone \
		-Dwebdriver.chrome.driver=default_site/tests/drivers/mac32/chromedriver

libs:
	cd default_site && \
		composer install --no-interaction --no-progress \
		&> ../build/composer.log

migrate-down:
	php default_site/yii migrations/migrate/to 0 --interactive=0

migrate-up:
	php default_site/yii migrations/migrate/to $$(make -s dev-db-version) --interactive=0

db: migrate-down migrate-up

fixtures:
	php default_site/yii fixture/load "*" \
		--namespace=tests\\codeception\\fixtures \
		--interactive=0 && \
	if grep -q "^companyName:" /etc/group; then chown -R :companyName .; fi;

assets:
	php default_site/yii asset \
		default_site/config/default/asset_config.php \
		default_site/config/default/asset_bundles.php

test-db: db fixtures

test:
	cd default_site/tests && \
		../vendor/bin/codecept build && \
		codeception/bin/yii migrations/migrate/to 0 && \
			../vendor/bin/codecept run unit,functional \
				--html=functional-report.html --coverage --coverage-html \
				--no-interaction --no-exit \
				2>&1 | tee ../../build/tests/functional.log && \
			../vendor/bin/codecept run acceptance \
				--html=acceptance-report.html --coverage --coverage-html \
				--steps --no-interaction --no-exit \
				2>&1 | tee ../../build/tests/acceptance.log

test-all:
	cd default_site/tests && \
		../vendor/bin/codecept build && \
		codeception/bin/yii migrations/migrate/to 0 && \
		../vendor/bin/codecept run \
			--html --coverage --coverage-html --no-interaction --no-exit \
			2>&1 | tee ../../build/tests/tests.log

check-all-ci: check-style check-cpd check-pmd

php-server-ci: php-server-stop-ci php-server-start-ci