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
               'srule' => '/admin/promoter/list'
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
               'srule' => ''
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
                'srule' => ''
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
               'srule' => ''
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
                'srule' => ''
            ]
        ]
    ],
];