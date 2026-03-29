<?php

return [
    'groups' => [
        'Overview' => [
            'dashboard.view' => 'Dashboard access',
        ],
        'Catalog' => [
            'products.manage' => 'Products',
            'categories.manage' => 'Categories',
            'coupons.manage' => 'Coupons',
        ],
        'Operations' => [
            'orders.manage' => 'Orders',
            'customers.manage' => 'Customers',
            'contact.manage' => 'Contact messages',
            'admins.manage' => 'Admin users',
        ],
        'Content' => [
            'pages.manage' => 'Pages',
        ],
        'Growth' => [
            'marketing.manage' => 'Marketing',
        ],
        'System' => [
            'settings.manage' => 'Settings',
        ],
    ],

    'route_map' => [
        'dashboard.view' => ['admin.dashboard'],
        'products.manage' => ['admin.products*'],
        'categories.manage' => ['admin.categories*'],
        'coupons.manage' => ['admin.discounts*'],
        'orders.manage' => ['admin.orders*'],
        'customers.manage' => ['admin.customers*'],
        'contact.manage' => ['admin.contact.messages*'],
        'admins.manage' => ['admin.users*'],
        'pages.manage' => ['admin.pages*'],
        'marketing.manage' => ['admin.marketing.*'],
        'settings.manage' => ['admin.settings*', 'admin.homepage.settings*', 'admin.shipping.methods*'],
    ],

    'role_defaults' => [
        'super_admin' => [
            'dashboard.view',
            'products.manage',
            'categories.manage',
            'coupons.manage',
            'orders.manage',
            'customers.manage',
            'contact.manage',
            'admins.manage',
            'pages.manage',
            'marketing.manage',
            'settings.manage',
        ],
        'editor' => [
            'dashboard.view',
            'products.manage',
            'categories.manage',
            'coupons.manage',
            'orders.manage',
            'customers.manage',
            'contact.manage',
            'pages.manage',
            'marketing.manage',
        ],
        'viewer' => [
            'dashboard.view',
            'orders.manage',
            'customers.manage',
            'contact.manage',
        ],
    ],
];
