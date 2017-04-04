#!/usr/bin/env bash
## *************************************************************************
#   Magento 2 deployment script.
## *************************************************************************

## =========================================================================
#   Working variables and hardcoded configuration.
## =========================================================================

# pin current folder and deployment root folder
CUR_DIR="$PWD"
DIR_ROOT="$( cd "$( dirname "$0" )" && pwd )"

# Available deployment modes ('work' only mode is used in the sample)
MODE_WORK=work
MODE_PILOT=pilot
MODE_LIVE=live

# parse runtime args and validate current deployment mode (work|pilot|live)
MODE=${MODE_WORK}
case "$1" in
    ${MODE_PILOT}|${MODE_LIVE})
        MODE=$1;;
esac

# Folders shortcuts
DIR_SRC=${DIR_ROOT}/src             # folder with sources
DIR_DEPLOY=${DIR_ROOT}/deploy       # folder with deployment templates
DIR_MAGE=${DIR_ROOT}/${MODE}        # root folder for Magento application
DIR_BIN=${DIR_ROOT}/bin             # root folder for shell scripts

# check configuration file exists and load deployment config (db connection, Magento installation opts, etc.).
FILE_CFG=${DIR_ROOT}/config.${MODE}.sh
if [ -f "${FILE_CFG}" ]
then
    echo "There is deployment configuration in ${FILE_CFG}."
    . ${FILE_CFG}
else
    echo "There is no expected configuration in ${FILE_CFG}. Aborting..."
    cd ${DIR_CUR}
    exit
fi
echo "Deployment is started in the '${MODE}' mode."



## =========================================================================
#   Magento application deployment.
## =========================================================================

# (re)create root folder for application deployment
if [ -d "${DIR_MAGE}" ]
then
    if [ ${MODE} != ${MODE_LIVE} ]
    then
        echo "Re-create '${DIR_MAGE}' folder."
        rm -fr ${DIR_MAGE}    # remove Magento root folder
        mkdir -p ${DIR_MAGE}  # ... then create it
    fi
else
    mkdir -p ${DIR_MAGE}      # just create folder if not exist (live mode)
fi
echo "Magento will be installed into the '${DIR_MAGE}' folder."

#   Create shortcuts for deployment files.
COMPOSER_MAIN=${DIR_MAGE}/composer.json                         # original Magento 2 descriptor
COMPOSER_UNSET=${DIR_DEPLOY}/composer/unset.${MODE_WORK}.json   # options to unset from original descriptor
COMPOSER_OPTS=${DIR_DEPLOY}/composer/opts.${MODE_WORK}.json     # options to set to original descriptor
case "${MODE}" in
    ${MODE_PILOT}|${MODE_LIVE})
        COMPOSER_UNSET=${DIR_DEPLOY}/composer/unset.${MODE_LIVE}.json
        COMPOSER_OPTS=${DIR_DEPLOY}/composer/opts.${MODE_LIVE}.json ;;
esac

echo ""
echo "Create M2 CE project in '${DIR_MAGE}' using composer"
composer create-project --repository-url=https://repo.magento.com/ magento/project-community-edition=^2 ${DIR_MAGE}

echo ""
echo "Merge original"
echo "    '${COMPOSER_MAIN}' with"
echo "    '${COMPOSER_UNSET}' and"
echo "    '${COMPOSER_OPTS}'..."
php ${DIR_DEPLOY}/merge_json.php ${COMPOSER_MAIN} ${COMPOSER_UNSET} ${COMPOSER_OPTS}

echo ""
echo "Update M2 CE project with additional options..."
cd ${DIR_MAGE}
composer update

## =========================================================================
#   Database installation.
## =========================================================================

# Prepare database password for using with Magento and MySQL utils
if [ -z ${DB_PASS} ]; then
    MYSQL_PASS=""
    MAGE_DBPASS=""
else
    MYSQL_PASS="--password=${DB_PASS}"
    MAGE_DBPASS="--db-password=""${DB_PASS}"""
fi

if [ ${MODE} != ${MODE_LIVE} ]
then
    echo "Drop-create db '${DB_NAME}'"
    mysqladmin -f -u"${DB_USER}" ${MYSQL_PASS} -h"${DB_HOST}" drop "${DB_NAME}"
    mysqladmin -f -u"${DB_USER}" ${MYSQL_PASS} -h"${DB_HOST}" create "${DB_NAME}"

    # Full list of the available options:
    # http://devdocs.magento.com/guides/v2.0/install-gde/install/cli/install-cli-install.html#instgde-install-cli-magento
    php ${DIR_MAGE}/bin/magento setup:install  \
    --admin-firstname="${ADMIN_FIRSTNAME}" \
    --admin-lastname="${ADMIN_LASTNAME}" \
    --admin-email="${ADMIN_EMAIL}" \
    --admin-user="${ADMIN_USER}" \
    --admin-password="${ADMIN_PASSWORD}" \
    --base-url="${BASE_URL}" \
    --backend-frontname="${BACKEND_FRONTNAME}" \
    --key="${SECURE_KEY}" \
    --language="${LANGUAGE}" \
    --currency="${CURRENCY}" \
    --timezone="${TIMEZONE}" \
    --use-rewrites="${USE_REWRITES}" \
    --use-secure="${USE_SECURE}" \
    --use-secure-admin="${USE_SECURE_ADMIN}" \
    --admin-use-security-key="${ADMI_USE_SECURITY_KEY}" \
    --session-save="${SESSION_SAVE}" \
    --cleanup-database \
    --db-host="${DB_HOST}" \
    --db-name="${DB_NAME}" \
    --db-user="${DB_USER}" \
    ${MAGE_DBPASS} \
    # 'MAGE_DBPASS' should be placed on the last position to prevent failures if this var is empty.
fi

## =========================================================================
#   Post installation setup.
## =========================================================================

# Create folders and copy service files to Magento dir.
echo "Create working folders before permissions will be set."
mkdir -p ${DIR_MAGE}/var/cache
mkdir -p ${DIR_MAGE}/var/generation
mkdir -p ${DIR_MAGE}/var/log

if [ ${MODE} != ${MODE_LIVE} ] && [ ${MODE} != ${MODE_PILOT} ]; then
    echo "Switch Magento 2 instance into 'developer' mode, reindex data, run cron jobs and disable cache."
    php ${DIR_MAGE}/bin/magento deploy:mode:set developer
    php ${DIR_MAGE}/bin/magento indexer:reindex
    php ${DIR_MAGE}/bin/magento cron:run
    php ${DIR_MAGE}/bin/magento cache:disable
fi

if [ -z "${LOCAL_OWNER}" ] || [ -z "${LOCAL_GROUP}" ] || [ -z "${DIR_MAGE}" ]; then
    echo "Skip file system ownership and permissions setup."
else
    ## http://devdocs.magento.com/guides/v2.0/install-gde/prereq/integrator_install.html#instgde-prereq-compose-access
    echo ""
    echo "Set file system ownership (${LOCAL_OWNER}:${LOCAL_GROUP}) and permissions..."
    chown -R ${LOCAL_OWNER}:${LOCAL_GROUP} ${DIR_MAGE}
    find ${DIR_MAGE} -type d -exec chmod 770 {} \;
    find ${DIR_MAGE} -type f -exec chmod 660 {} \;
    chmod -R g+w ${DIR_MAGE}/var
    chmod -R g+w ${DIR_MAGE}/pub
    chmod u+x ${DIR_MAGE}/bin/magento
    chmod -R go-w ${DIR_MAGE}/app/etc
fi


# finalize deployment process
cd ${DIR_CUR}
echo "Deployment is complete."
