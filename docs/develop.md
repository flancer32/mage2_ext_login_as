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

    $ sh deploy.sh work
