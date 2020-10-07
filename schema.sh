#!/bin/bash

if [[ "$TRAVIS_BRANCH" != "$DOCS_BUILD_BRANCH" ]]; then
  echo "We're NOT on the $DOCS_BUILD_BRANCH branch."
  # analyze current branch and react accordingly
  exit 0
fi

if [[ "$TRAVIS_PHP_VERSION" = "7.4" && "$DEPENDENCIES" = "" ]]; then
  echo "Documentation will be generated."
else
  echo "We only build and deploy the docs for specific PHP version."
  exit 0
fi

# Generate the documentation from catalogue module,
# as we want to generate schema for it as well.
cd ../
mkdir graphql-catalogue-module
cd graphql-catalogue-module
echo "{}" >> composer.json
composer require oxid-esales/graphql-catalogue --no-update
composer require oxid-esales/oxideshop-db-views-generator --no-update
composer require oxid-esales/oxideshop-composer-plugin:^v5.0.1 --no-update
composer require oxid-esales/oxideshop-ce:6.5 --no-update
composer require oxid-esales/oxideshop-unified-namespace-generator:^2.0 --no-update
composer update $DEFAULT_COMPOSER_FLAGS
npm install -g @2fd/graphdoc
cp $TRAVIS_BUILD_DIR/schema_database.php ./
cp $TRAVIS_BUILD_DIR/graphdocs.php ./

MODULE_DIR=$(pwd);

cat composer.json
ls -a
ls -a source/

sudo sed -e 's|utf8_unicode_ci|latin1_general_ci|g; s|utf8|latin1|g' --in-place /etc/mysql/my.cnf
sudo service mysql restart
cat $MODULE_DIR/var/configuration/shops/1.yaml
cat $MODULE_DIR/var/configuration/configurable_services.yaml
#mkdir $MODULE_DIR/source && mkdir $MODULE_DIR/source/tmp
#mkdir $MODULE_DIR/var && mkdir $MODULE_DIR/var/configuration
#mkdir $MODULE_DIR/var/configuration/shops && touch $MODULE_DIR/var/configuration/shops/1.yaml
#echo -e "imports:" > $MODULE_DIR/var/configuration/configurable_services.yaml
#echo -n "    - { resource:" >> $MODULE_DIR/var/configuration/configurable_services.yaml
#echo  " SET_PATH_HERE_A }" >> $MODULE_DIR/var/configuration/configurable_services.yaml
#echo -n "    - { resource:" >> $MODULE_DIR/var/configuration/configurable_services.yaml
#echo  " SET_PATH_HERE_B }" >> $MODULE_DIR/var/configuration/configurable_services.yaml
#sed -i "s|\SET_PATH_HERE_A|\'$MODULE_DIR/services.yaml'|" $MODULE_DIR/var/configuration/configurable_services.yaml
#sed -i "s|\SET_PATH_HERE_B|\'$MODULE_DIR/vendor/oxid-esales/graphql-base/services.yaml'|" $MODULE_DIR/var/configuration/configurable_services.yaml
cd $MODULE_DIR/vendor/oxid-esales/oxideshop-ce
cp source/config.inc.php.dist source/config.inc.php
sed -i 's|<dbHost>|localhost|; s|<dbName>|oxideshop|; s|<dbUser>|root|; s|<dbPwd>||; s|<sShopURL>|http://localhost|; s|<sShopDir>|'$MODULE_DIR'/source|; s|<sCompileDir>|'$MODULE_DIR'/source/tmp|' source/config.inc.php
sed -i "s|\$this->edition = ''|\$this->edition = 'CE'|" source/config.inc.php
sed -i "s|\$this->sCompileDir = ''|\$this->sCompileDir = '$MODULE_DIR/source/tmp'|" source/config.inc.php
sed -i "s|\INSTALLATION_ROOT_PATH . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR|\'$MODULE_DIR/vendor/'|" source/bootstrap.php
cp source/config.inc.php $MODULE_DIR/source
cd $MODULE_DIR

ls -a

php -S localhost:8080 &
php schema_database.php

TOKEN=$(curl --silent http://localhost:8080/graphdocs.php?skipSession=1 -H 'Content-Type: application/json' --data-binary '{"query":"query {token(username: \"admin\", password:\"admin\")}"}' | sed -n 's|.*"token":"\(.*\)\"}}|\1|p')

#generate graphql schema documentation using graphdoc
graphdoc -e http://localhost:8080/graphdocs.php?skipSession=1 -o ./docs/_static/schema -f -x "Authorization: Bearer $TOKEN"

cp -r docs/_static/schema/ $TRAVIS_BUILD_DIR/docs/_static/
