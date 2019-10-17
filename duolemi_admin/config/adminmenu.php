<?php
/**
 * 后台菜单配置
 */
return [
    [
        'id' => 1,
        'name' => '加盟课管理',
        'icon' => '',
        'sub_menu' => [
            [
                'sid'   => 1,
                'sname' => '加盟课列表',
                'sicon' => '',
                'srule' => '/admin/franchise_course/list'
            ]
        ]
    ],
    [
        'id' => 2,
        'name' => '体验课程管理',
        'icon' => '',
        'sub_menu' => [
            [
                'sid'   => 2,
                'sname' => '体验课程列表',
                'sicon' => '',
                'srule' => '/admin/experience_course/list'
            ]
        ]
    ],
    [
        'id' => 3,
        'name' => '购买记录管理',
        'icon' => '',
        'sub_menu' => [
           [
               'sid'  => 1,
               'sname' => '购买记录',
               'sicon' => '',
               'srule' => '/admin/purchase_history/list'
           ]
        ]
    ],
    [
        'id' => 4,
        'name' => '推广员管理',
        'icon' => '',
        'sub_menu' => [
           [
               'sid'  => 1,
               'sname' => '推广员列表',
               'sicon' => '',
               'srule' => '/admin/guider/list'
           ]
        ]
    ],
    [
        'id' => 5,
        'name' => '会员管理',
        'icon' => '',
        'sub_menu' => [
           [
               'sid'  => 1,
               'sname' => '会员列表',
               'sicon' => '',
               'srule' => '/admin/member/list'
           ]
        ]
    ],
    [
        'id' => 6,
        'name' => '加盟申请记录管理',
        'icon' => '',
        'sub_menu' => [
            [
                'sid'  => 1,
                'sname' => '加盟申请记录列表',
                'sicon' => '',
                'srule' => '/admin/franchise_apply/list'
            ]
        ]
    ],
    [
        'id' => 7,
        'name' => '提现管理',
        'icon' => '',
        'sub_menu' => [
           [
               'sid'  => 1,
               'sname' => '提现列表',
               'sicon' => '',
               'srule' => '/admin/withdraw/list'
           ]
        ]
    ],
    [
        'id' => 8,
        'name' => '轮播图管理',
        'icon' => '',
        'sub_menu' => [
            [
                'sid'  => 1,
                'sname' => '轮播图列表',
                'sicon' => '',
                'srule' => '/admin/banner/list'
            ]
        ]
    ],
    [
        'id' => 9,
        'name' => '系统管理',
        'icon' => '',
        'sub_menu' => [
            [
                'sid'  => 1,
                'sname' => '系统设置',
                'sicon' => '',
                'srule' => '/admin/settings/list'
            ]
        ]
    ],
    [
        'id' => 10,
        'name' => '导航设置管理',
        'icon' => '',
        'sub_menu' => [
            [
                'sid'  => 1,
                'sname' => '导航设置列表',
                'sicon' => '',
                'srule' => '/admin/navigation_settings/list'
            ]
        ]
    ],
    [
        'id' => 11,
        'name' => '文章管理',
        'icon' => '',
        'sub_menu' => [
            [
                'sid'  => 1,
                'sname' => '文章列表',
                'sicon' => '',
                'srule' => '/admin/article/list'
            ]
        ]
    ],
    [
        'id' => 12,
        'name' => '账号管理',
        'icon' => '',
        'sub_menu' => [
            [
                'sid'  => 1,
                'sname' => '账号列表',
                'sicon' => '',
                'srule' => '/admin/admin/list'
            ]
        ]
    ]
];