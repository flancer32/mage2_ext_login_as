# Development deployment


## Create local configuration

    $ cp config.init.sh config.work.sh
    $ nano config.work.sh                   //edit configuration for deployment

## Get credentials to authenticate on 'repo.magento.com'

Go to your [Magento Connect](https://www.magentocommerce.com/magento-connect/customer/account/) account, 
section (_My Account / Connect / Developer / Secure Keys_) and generate pair of keys to connect to Magento 2 repository.

## Setup environment

[System requirements](http://devdocs.magento.com/guides/v2.0/install-gde/system-requirements.html)

## Run deployment script

    $ sh deploy.sh
    There is deployment configuration in /home/alex/work/prj/mage2_ext_login_as/config.work.sh.
    Deployment is started in the 'work' mode.
    Re-create '/home/alex/work/prj/mage2_ext_login_as/work' folder.
    Magento will be installed into the '/home/alex/work/prj/mage2_ext_login_as/work' folder.
    ...
    Set file system ownership (alex:www-data) and permissions...
    Deployment is complete.
