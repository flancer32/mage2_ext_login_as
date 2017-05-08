# "Login As Customer" extension for Magento 2.



## Description

This extension adds ability for backend users (adminhtml) to log in as customers (frontend).

### Features
Main features [overview](./etc/dev/docs/overview/README.md):
* Login button on customer form;
* Login links on "Customers" & "Sale Orders" grids;
* Login events log;
* ACL configuration;


### Demo
Login [here](http://loginas.m2.flancer64.com/admin/admin) as:
 * user: **fl32_loginas_full** 
 * password: **Ss4N1i1Poq8bOjzbcOWi**



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

See [here](./etc/dev/docs/develop.md).



## Uninstall

You need an authentication keys for `https://repo.magento.com/` to uninstall any Magento 2 module. Go to your [Magento Connect](https://www.magentocommerce.com/magento-connect/customer/account/) account, section (My Account / Connect / Developer / Secure Keys) and generate pair of keys to connect to Magento 2 repository. Then place composer authentication file `auth.json` besides your `composer.json` as described [here](https://getcomposer.org/doc/articles/http-basic-authentication.md) and put your authentication keys for `https://repo.magento.com/` into the authentication file:
```json
{
  "http-basic": {
    "repo.magento.com": {
      "username": "...",
      "password": "..."
    }
  }
}
```

Then run these commands to completely uninstall `Flancer32_LoginAs` module: 
```bash
$ cd ${DIR_MAGE_ROOT}   
$ bin/magento module:uninstall Flancer32_Repo Flancer32_LoginAs
$ composer remove flancer32/php_data_object
$ bin/magento setup:upgrade
$ bin/magento setup:di:compile
```

Remove `auth.json` file and it's copy at the end:

 ```bash
$ rm ./auth.json ./var/composer_home/auth.json
```