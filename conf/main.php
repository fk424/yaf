<?php

return array(
    'yaf' => array(
        'directory' => APPLICATION_PATH,
    ),
    'enableCache' => true,
    'runtimePath' => __DIR__.'/../../runtime',

    'api'=>array(
         'emq' => array(
             'host' => '127.0.0.1',
             'port' => 5672,
             'user' => 'dianjing',
             'pass' => 'dianjing',
             'vhost' => '/dianjing',
        ),
        'edc' => array(
            'appkey' => 'dj_test',
            'token' => 'fbb13e6a4516ebb1c193ea934a29c4f6',
            'ver' => '1.0',
            'url' => 'http://edc.l.cn/',
            'sys_name' => 'dianjing',
        ),
        'auditApi' => array(
            'url' => 'http://audit.l.cn/api/',
        ),
        'blackAuditApi' => array(
//            'url' => 'http://black.audit.e.360.cn/api/',
            'url'=>array('tcp://127.0.0.1:2000'),
        ),
    )
);
