# "Login As Customer" extension for Magento 2.

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