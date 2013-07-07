<?php

return array(
    'db' => array(
        'driver'         => 'Pdo',
        'dsn'            => 'mysql:dbname=' . getenv('DB_NAME') . ';host=' . getenv('DB_HOST'),
        'driver_options' => array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
        ),
        'username' => getenv('DB_USER'),
        'password' => getenv('DB_PASS'),
    ),
    'doctrine' => array(
        'connection' => array(
            'orm_default' => array(
                'driverClass' => 'Doctrine\DBAL\Driver\PDOMySql\Driver',
                'params' => array(
                    /*'host'     => getenv('DB_HOST'),
                    'port'     => getenv('DB_PORT'),
                    'dbname'   => getenv('DB_NAME'),
                    'user'     => getenv('DB_USER'),
                    'password' => getenv('DB_PASS'),*/
                    'host'     => 'localhost',
                    'port'     => '3306',
                    'dbname'   => 'audiodox',
                    'user'     => 'root',
                    'password' => 'root',
                )
            )
        )
    ),
);
