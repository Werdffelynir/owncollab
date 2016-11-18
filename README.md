# OwnCollab Dashboard

Place this app in **owncloud/apps/**

## Application Destination

The application is dependent for part of OwnCollab

- __owncollab__
- owncollab_chart
- owncollab_talks
- owncollab_calendar

## Publish to App Store

First get an account for the [App Store](http://apps.owncloud.com/) then run:

    make appstore_package

The archive is located in build/artifacts/appstore and can then be uploaded to the App Store.

## Running tests
After [Installing PHPUnit](http://phpunit.de/getting-started.html) run:

    phpunit -c phpunit.xml
