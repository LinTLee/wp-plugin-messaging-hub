# Message Hub (MHub) WP plugin

## Overview

Messaging Hub (MHub) plugin demonstrates integration with external APIs offered by instant chatting platform by pull data and import as WP posts. The following highlights show this plugin's features and capabilities.

* Support integration with Hipchat and Slack messaging tools
* Periodically (hourly) pull messages from messaging apps thru the APIs
* Create a new post object for pulled message
* Attach "online chat message" tag, and assign "channel" taxonomy term to the post
* Offer admin options screen to configure connectivity parameters to messaging tool's APIs.

## Getting Started

1. Copy source files to WP plugins directory, e.g., wp-content/plugins/
2. Install and activate the plugin thru WP-Admin
3. Navigate to this plugin's options (MHub) page under "Settings" menu to enter the connectivity configurations for each channel system

## Pre-requisites

* To access MHub options screen, WP "manage options" user privilege is required. 
* For low traffic site, a cron scheduled job is recommended to ping wp-cron.php periodically to trigger import events on this plugin

## Docker Environment (Optional)

The following example docker-compose.yml gives a quick start to spin up WP environment in docker and try out this plugin.

Default wp-admin username: wordpress | password: wordpress

```yaml
version: '3'

services:

 db:
   image: mysql:5.7
   volumes:
     - "./mysqldb-data:/var/lib/mysql"
   restart: always
   environment:
     MYSQL_ROOT_PASSWORD: wordpress
     MYSQL_DATABASE: wordpress
     MYSQL_USER: wordpress
     MYSQL_PASSWORD: wordpress

 wordpress:
   depends_on:
     - db
   image: wordpress:latest
   volumes:
     - "./wp_messaging_hub:/var/www/html/wp-content/plugins/wp_messaging_hub"
   ports:
     - "8000:80"
   restart: always
   environment:
     WORDPRESS_DB_HOST: db:3306
     WORDPRESS_DB_USER: wordpress
     WORDPRESS_DB_PASSWORD: wordpress

```
