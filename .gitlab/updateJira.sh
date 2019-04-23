#!/bin/sh

# variables
VERSION="PS17_4_0_02"
IS_PLUGIN="YES"
IS_CONNECTOR="NO"
FULLNAME="PrestaShop17"
ABBREVIATION="PS17"

# check if the version tag is complete
php .gitlab/scripts/jiraReleaseVersion.php ${VERSION} ${IS_PLUGIN} ${IS_CONNECTOR} ${FULLNAME} ${ABBREVIATION}


