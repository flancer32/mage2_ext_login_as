# "Login As Customer" extension for Magento 2.



## Description

This extension adds ability for backend users (adminhtml) to log in as customers (frontend).

### Features
* Login button on customer form;
* Login links on "Customers" & "Sale Orders" grids;
* Login events log;
* ACL configuration;



## Install

### From console

```bash
$ cd ${DIR_MAGE_ROOT}   // go to Magento 2 root folder ('composer.json' file should be placed there)
$ composer require flancer32/mage2_ext_login_as
$ bin/magento module:enable Flancer32_LoginAs
$ bin/magento setup:upgrade
$ bin/magento setup:di:compile

```

### Using 'composer.json'

```json
  "require": {
    "flancer32/mage2_ext_login_as": "dev-master"
  }
```

### Development version

See [here](./docs/develop.md).


