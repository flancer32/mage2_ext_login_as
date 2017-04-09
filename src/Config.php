<?php
/**
 * Container for module's constants (hardcoded configuration).
 *
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Flancer32\LoginAs;

class Config
{
    const ACL_CUSTOMER_LOGGED_AS = 'admin_customer_loggedAs';

    const ENTITY_ADMIN_USER = 'admin_user';
    const ENTITY_CUSTOMER = 'customer_entity';

    const E_ADMIN_USER_A_EMAIL = 'email';
    const E_ADMIN_USER_A_FIRSTNAME = 'firstname';
    const E_ADMIN_USER_A_LASTNAME = 'lastname';
    const E_ADMIN_USER_A_USERNAME = 'username';
    const E_ADMIN_USER_A_USER_ID = 'user_id';
    const E_COMMON_A_ENTITY_ID = 'entity_id';
    const E_CUSTOMER_A_CREATED_AT = 'created_at';
    const E_CUSTOMER_A_DEF_BILLING = 'default_billing';
    const E_CUSTOMER_A_DEF_SHIPPING = 'default_shipping';
    const E_CUSTOMER_A_DOB = 'dob';
    const E_CUSTOMER_A_EMAIL = 'email';
    const E_CUSTOMER_A_ENTITY_ID = self::E_COMMON_A_ENTITY_ID;
    const E_CUSTOMER_A_FIRSTNAME = 'firstname';
    const E_CUSTOMER_A_GENDER = 'gender';
    const E_CUSTOMER_A_GROUP_ID = 'group_id';
    const E_CUSTOMER_A_LASTNAME = 'lastname';
    const E_CUSTOMER_A_PASS_HASH = 'password_hash';
    const E_CUSTOMER_A_WEBSITE_ID = 'website_id';

    const MENU_CUSTOMER_LOGGED_AS = self::ACL_CUSTOMER_LOGGED_AS;
    const MODULE = 'Flancer32_LoginAs';
    const ROUTE_NAME_ADMIN_LOGINAS = 'loginas';
    const ROUTE_NAME_FRONT_LOGINAS = 'loginas';

}