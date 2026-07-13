<?php

return [
    'default_role' => 'customer',

    'roles' => [
        'super_admin' => [
            'label' => 'Super Admin',
            'permissions' => ['*'],
        ],

        'admin' => [
            'label' => 'Admin',
            'permissions' => [
                'users.view',
                'users.create',
                'users.update',
                'users.delete',
                'products.manage',
                'regions.manage',
                'stories.manage',
                'creators.manage',
            ],
        ],

        'moderator' => [
            'label' => 'Moderator',
            'permissions' => [
                'users.view',
                'products.view',
                'stories.view',
                'reports.view',
            ],
        ],

        'creator' => [
            'label' => 'Creator',
            'permissions' => [
                'products.create',
                'stories.create',
                'profile.manage',
            ],
        ],

        'seller' => [
            'label' => 'Seller',
            'permissions' => [
                'products.create',
                'products.update.own',
                'orders.view.own',
                'profile.manage',
            ],
        ],

        'tour_guide' => [
            'label' => 'Tour Guide',
            'permissions' => [],
        ],

        'customer' => [
            'label' => 'Customer',
            'permissions' => [
                'profile.manage',
                'orders.create',
                'stories.view',
            ],
        ],
    ],

    'permissions' => [
        'users.view',
        'users.create',
        'users.update',
        'users.delete',
        'products.manage',
        'products.create',
        'products.update.own',
        'products.view',
        'regions.manage',
        'stories.manage',
        'stories.create',
        'stories.view',
        'creators.manage',
        'orders.view.own',
        'orders.create',
        'profile.manage',
        'reports.view',
    ],
];
