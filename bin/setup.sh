#!/usr/bin/env bash

wp core download --path=$LANDO_WEBROOT
wp config create \
	--path=$LANDO_WEBROOT \
	--dbname=wordpress \
	--dbuser=wordpress \
	--dbpass=wordpress \
	--dbhost=database
wp config set \
	--path=$LANDO_WEBROOT \
	--type=constant \
	--raw \
	WP_DEBUG true
wp core install \
	--path=$LANDO_WEBROOT \
	--url="http://vetting-app.lndo.site" \
	'--title="Event Vetting"' \
	--admin_user="admin" \
	--admin_password="admin" \
	--admin_email="admin@test.com" \
	--skip-email

WP_VERSION=$(wp core version)

svn co --quiet https://develop.svn.wordpress.org/tags/$WP_VERSION/tests/phpunit/includes/ $LANDO_WEBROOT/includes
svn co --quiet https://develop.svn.wordpress.org/tags/$WP_VERSION/tests/phpunit/data/ $LANDO_WEBROOT/data
ln -sf $LANDO_MOUNT/.wp-tests-config.php $LANDO_WEBROOT/wp-tests-config.php
