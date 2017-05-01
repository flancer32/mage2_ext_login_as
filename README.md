# "Login As Customer" extension for Magento 2.



## Description

This extension adds ability for backend users (adminhtml) to log in as customers (frontend).

### Features
Main features [overview](./docs/overview/README.md):
* Login button on customer form;
* Login links on "Customers" & "Sale Orders" grids;
* Login events log;
* ACL configuration;


### Demo
Login [here](http://loginas.m2.flancer64.com/admin/admin) as user "**fl32_loginas_full**" with password "**Ss4N1i1Poq8bOjzbcOWi**".



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
    "flancer32/mage2_ext_login_as": "^0.1"
  }
```

### Development version

See [here](./docs/develop.md).


