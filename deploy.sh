#!/bin/bash

# install graphql catalogue module
mkdir ../graphql-doc
cd ../graphql-doc
git clone https://github.com/OXID-eSales/graphql-catalogue-module.git .
git checkout $(git tag | tail -1)
composer update $DEFAULT_COMPOSER_FLAGS

# install graphdoc
npm install -g @2fd/graphdoc

# start mysql and import
sudo sed -e 's|utf8_unicode_ci|latin1_general_ci|g; s|utf8|latin1|g' --in-place /etc/mysql/my.cnf
sudo service mysql restart

echo "create database oxideshop;" | mysql -u root --password=""
mysql -u root --password="" oxideshop < vendor/oxid-esales/oxideshop-ce/source/Setup/Sql/database_schema.sql
mysql -u root --password="" oxideshop < vendor/oxid-esales/oxideshop-ce/source/Setup/Sql/initial_data.sql

# prepare shop
mkdir -p source/tmp/
mkdir -p var/configuration
echo "imports:
  -
    resource: ../../vendor/oxid-esales/graphql-base/services.yaml
  -
    resource: ../../services.yaml

" > var/configuration/configurable_services.yaml

# prepare configuration
cp vendor/oxid-esales/oxideshop-ce/source/config.inc.php.dist vendor/oxid-esales/oxideshop-ce/source/config.inc.php
sed -i 's|<dbHost>|localhost|' vendor/oxid-esales/oxideshop-ce/source/config.inc.php
sed -i 's|<dbName>|oxideshop|' vendor/oxid-esales/oxideshop-ce/source/config.inc.php
sed -i 's|<dbUser>|root|' vendor/oxid-esales/oxideshop-ce/source/config.inc.php
sed -i 's|<dbPwd>||' vendor/oxid-esales/oxideshop-ce/source/config.inc.php
sed -i 's|<sShopURL>|http://localhost:8080|' vendor/oxid-esales/oxideshop-ce/source/config.inc.php
sed -i "s|'<sShopDir>'|__DIR__|" vendor/oxid-esales/oxideshop-ce/source/config.inc.php
sed -i "s|'<sCompileDir>'|__DIR__ . '/tmp'|" vendor/oxid-esales/oxideshop-ce/source/config.inc.php
sed -i "s|blSkipViewUsage = false|blSkipViewUsage = true|" vendor/oxid-esales/oxideshop-ce/source/config.inc.php

cp vendor/oxid-esales/oxideshop-ce/source/config.inc.php source/
cp $TRAVIS_BUILD_DIR/graphdocs.php .

# start php built-in webserver
php -S localhost:8080 &

# wait for it ;-)
sleep 2;

TOKEN=$(curl --silent http://localhost:8080/graphdocs.php?skipSession=1 -H 'Content-Type: application/json' --data-binary '{"query":"query {token(username: \"admin\", password:\"admin\")}"}' | sed -n 's|.*"token":"\(.*\)\"}}|\1|p')

#generate graphql schema documentation using graphdoc
graphdoc -e http://localhost:8080/graphdocs.php?skipSession=1 -o $TRAVIS_BUILD_DIR/docs/_static/schema -f -x "Authorization: Bearer $TOKEN"
