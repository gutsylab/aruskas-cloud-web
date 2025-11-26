<?php

namespace App\Constants;


class TenantPermissions
{

    const APPLICATION = 'application-tenant';

    // ===========================================================//
    // ================= HR Permissions ==========================//
    // ===========================================================//
    const APPLICATION_MAIN = 'application-tenant-main';


    // Dashboard Permissions
    const GROUP_DASHBOARD = 'tenant-group-dashboard';
    const SUBGROUP_DASHBOARD = 'tenant-subgroup-dashboard';
    const MODULE_DASHBOARD = 'tenant-module-dashboard';
    const DASHBOARD_VIEW_ALL = 'tenant-dashboard-view-all';
    // =========================================================//


    // ===========================================================//
    // ================= General Settings Permissions ============//
    // ===========================================================//
    const GROUP_GENERAL_SETTINGS = 'tenant-group-general-settings';

    // Application Settings Permissions
    const SUBGROUP_APPLICATION_SETTINGS = 'tenant-subgroup-application-settings';
    const MODULE_APPLICATION_SETTINGS = 'tenant-module-application-settings';
    const APPLICATION_SETTINGS_MANAGE = 'tenant-application-settings-manage';



    const GROUP_CASH = 'tenant-group-cash';

    // Cash Flow
    const SUBGROUP_CASH_FLOW = 'tenant-subgroup-cash-flow';
    const MODULE_CASH_FLOW = 'tenant-module-cash-flow';
    const CASH_FLOW_VIEW_ALL = 'tenant-cash-flow-view-all';
    const CASH_FLOW_VIEW = 'tenant-cash-flow-view';
    const CASH_FLOW_CREATE = 'tenant-cash-flow-create';
    const CASH_FLOW_UPDATE = 'tenant-cash-flow-update';
    const CASH_FLOW_DELETE = 'tenant-cash-flow-delete';

    // Cash Category
    const SUBGROUP_CASH_CATEGORY = 'tenant-subgroup-cash-category';
    const MODULE_CASH_CATEGORY = 'tenant-module-cash-category';
    const CASH_CATEGORY_VIEW_ALL = 'tenant-cash-category-view-all';
    const CASH_CATEGORY_VIEW = 'tenant-cash-category-view';
    const CASH_CATEGORY_CREATE = 'tenant-cash-category-create';
    const CASH_CATEGORY_UPDATE = 'tenant-cash-category-update';
    const CASH_CATEGORY_DELETE = 'tenant-cash-category-delete';

    // Cash Account
    const SUBGROUP_CASH_ACCOUNT = 'tenant-subgroup-cash-account';
    const MODULE_CASH_ACCOUNT = 'tenant-module-cash-account';
    const CASH_ACCOUNT_VIEW_ALL = 'tenant-cash-account-view-all';
    const CASH_ACCOUNT_VIEW = 'tenant-cash-account-view';
    const CASH_ACCOUNT_CREATE = 'tenant-cash-account-create';
    const CASH_ACCOUNT_UPDATE = 'tenant-cash-account-update';
    const CASH_ACCOUNT_DELETE = 'tenant-cash-account-delete';




    // ===========================================================//
    // ================= User & Role Permissions =================//
    // ===========================================================//
    const GROUP_USER_AND_ROLE = 'tenant-group-user';

    // User Permissions
    const SUBGROUP_USER = 'tenant-subgroup-user';
    const MODULE_USER = 'tenant-module-user';
    const USER_VIEW_ALL = 'tenant-user-view-all';
    const USER_VIEW = 'tenant-user-view';
    const USER_CREATE = 'tenant-user-create';
    const USER_UPDATE = 'tenant-user-update';
    const USER_DELETE = 'tenant-user-delete';
    const USER_RESET_PASSWORD = 'tenant-user-reset-password';

    // Role Permissions
    const SUBGROUP_ROLE = 'tenant-subgroup-role';
    const MODULE_ROLE = 'tenant-module-role';
    const ROLE_VIEW_ALL = 'tenant-role-view-all';
    const ROLE_VIEW = 'tenant-role-view';
    const ROLE_CREATE = 'tenant-role-create';
    const ROLE_UPDATE = 'tenant-role-update';
    const ROLE_DELETE = 'tenant-role-delete';
    // =========================================================//
}
