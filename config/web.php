<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'Project eSport',
    'language' => 'en-US',
    'basePath' => dirname(__DIR__),
    'defaultRoute' => '/site/index',
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            'defaultTimeZone' => 'Europe/Berlin',
            'timeZone' => 'Europe/Berlin',
            'nullDisplay' => '',
            'dateFormat' => 'dd.MM.yyyy'
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'at5I4u-ZfXzo85LZHzZXbzopgfnhh1ae',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\modules\user\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'birnchen-interactive.de',
                'username' => "noreply@project-esport.gg",
                'password' => 'Birnchen2016',
                'port' => '25',
                'encryption' => 'tls',
            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'class' => 'yii\web\UrlManager',
            // Disable index.php
            'showScriptName' => false,
            // Disable r= routes
            'enablePrettyUrl' => true,
            'rules' =>[
                'company/<action>'=>'company/company/<action>',
                'user/<action>' => 'user/user/<action>',
                'teams/<action>'=>'teams/teams/<action>',
                'tournaments/<action>'=>'tournaments/tournaments/<action>',
                'platformgames/<action>' => 'platformgames/platformgames/<action>', 
                'rocketleague/<action>' => 'rocketleague/rocketleague/<action>',
                'rules/<action>' => 'rules/rules/<action>',
                'events/<action>' => 'events/events/<action>',
                'tournamenttrees/<action>' => 'tournamenttrees/tournamenttrees/<action>',
                'mariokartdeluxe/<action>' => 'mariokartdeluxe/mariokartdeluxe/<action>',
            ],
        ],
        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'fileMap' => [
                        'app' => 'app.php'
                    ],
                ]
            ]
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager'
        ],
        'MetaClass' => [
            'class' => 'app\modules\miscellaneous\metaClass',
        ],
        'HelperClass' => [
            'class' => 'app\modules\miscellaneous\helperClass'
        ]
    ],
    'modules' => [
        'company'   => 'app\modules\company\Module',
        'user'      => 'app\modules\user\Module',
        'platformgames' => 'app\modules\platformgames\Module',
        'teams'         => 'app\modules\teams\Module',
        'tournaments' => 'app\modules\tournaments\Module',
        'rocketleague' => 'app\modules\rocketleague\Module',
        'rules' => 'app\modules\rules\Module',
        'events' => 'app\modules\events\Module',
        'tournamenttrees' => 'app\modules\tournamenttrees\Module',
        'mariokartdeluxe' => 'app\modules\mariokartdeluxe\Module',
        //'rbac' => 'app\modules\rbac\Module',
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;