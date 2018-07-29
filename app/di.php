<?php

use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Mvc\Model\Manager as ModelsManager;
use Phalcon\Events\Manager;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;

//注册自动加载
$loader = new \Phalcon\Loader();
$loader->registerNamespaces(
    [
        'app' => ROOT_DIR . '/app/',
    ]
);
$loader->register();


/**
 * The FactoryDefault Dependency Injector automatically registers the right
 * services to provide a full stack framework.
 */
$di = new Phalcon\DI\FactoryDefault();

$di->setShared('dConfig', function () {
    #Read configuration
    $config = new Phalcon\Config(require ROOT_DIR . '/config/config.php');
    return $config;
});

$di->setShared('config', function () {
    #Read configuration
    $config = new Phalcon\Config([]);
    return $config;
});

/**
 * 本地缓存
 */
$di->setShared('cache', function () {
    // Create an Output frontend. Cache the files for 2 days
    $frontCache = new \Phalcon\Cache\Frontend\Data(
        [
            "lifetime" => 172800,
        ]
    );

    $cache = new \Phalcon\Cache\Backend\File(
        $frontCache, [
            "cacheDir" => CACHE_DIR,
        ]
    );
    return $cache;
});

/**
 * 全局缓存
 */
$di->setShared('gCache', function () use ($di) {
    // Create an Output frontend. Cache the files for 2 days
    $frontCache = new \Phalcon\Cache\Frontend\Data(
        [
            "lifetime" => 172800,
        ]
    );
    $op = [
        "host" => getenv('GCACHE_HOST'),
        "port" => getenv('GCACHE_PORT'),
        "auth" => getenv('GCACHE_AUTH'),
        "persistent" => getenv('GCACHE_PERSISTENT'),
        'prefix' => getenv('GCACHE_PREFIX'),
    ];
    if (empty($op['auth'])) {
        unset($op['auth']);
    }
    $cache = new \Phalcon\Cache\Backend\Libmemcached($frontCache, [
        "servers" => [
            [
                "host" => $op['host'],
                "port" => $op['port'],
                "weight" => 1,
            ],
        ],
        "client" => [
            \Memcached::OPT_HASH => \Memcached::HASH_MD5,
            \Memcached::OPT_PREFIX_KEY => $op['prefix'],
        ],
    ]);
    return $cache;
});


/**
 * session缓存
 */
$di->setShared('sessionCache', function () use ($di) {
    // Create an Output frontend. Cache the files for 2 days
    $frontCache = new \Phalcon\Cache\Frontend\Data(
        [
            "lifetime" => 172800,
        ]
    );
    output($di['config']->cache, 'gCache');
    $op = [
        "host" => getenv('SESSION_CACHE_HOST'),
        "port" => get_env('SESSION_CACHE_PORT', 6379),
        "auth" => get_env('SESSION_CACHE_AUTH', ''),
        "persistent" => get_env('SESSION_CACHE_PERSISTENT', 1),
        'prefix' => get_env('SESSION_CACHE_PREFIX', 'session_'),
        "index" => getenv('SESSION_CACHE_INDEX')
    ];
    if (empty($op['auth'])) {
        unset($op['auth']);
    }
    $cache = new \Phalcon\Cache\Backend\Redis(
        $frontCache, $op);
    return $cache;
});


$di["router"] = function () {
    $router = new \Phalcon\Mvc\Router();
    $router->setDefaultNamespace('app\\controller');
    $router->setDefaultController('index');
    $router->setDefaultAction('index');
    $router->add(
        "/:controller/:action/:params", [
            "controller" => 1,
            "action" => 2,
            'params' => 3
        ]
    );

    return $router;
};

//事件管理器
$di->setShared('eventsManager', function () {
    $eventsManager = new \Phalcon\Events\Manager();
    return $eventsManager;
});

//注册过滤器,添加了几个自定义过滤方法
$di->setShared('filter', function () {
    $filter = new \Phalcon\Filter();
//    $filter->add('json', new \core\Filter\JsonFilter());
    return $filter;
});


$di->set(
    "modelsManager", function () {
    return new \Phalcon\Mvc\Model\Manager();
});

$di->set(
    "proxyCS", function () {
    $client = new \pms\bear\ClientSync(get_env('PROXY_HOST'), get_env('PROXY_PROT'), 10);
    return $client;

});

$di->setShared('logger', function () {
    $logger = new \Phalcon\Logger\Adapter\File(RUNTIME_DIR . 'log/' . date('YmdHi') . '.log');
    return $logger;
});

/**
 * Database connection is created based in the parameters defined in the
 * configuration file
 */
$di["db"] = function () use ($di) {
    $connection = new DbAdapter(
        [
            "host" => getenv('MYSQL_HOST'),
            "port" => getenv('MYSQL_PORT'),
            "username" => getenv('MYSQL_USERNAME'),
            "password" => getenv('MYSQL_PASSWORD'),
            "dbname" => getenv('MYSQL_DBNAME'),
            "options" => [
                \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'",
                \PDO::ATTR_CASE => \PDO::CASE_LOWER,
            ],
        ]
    );
    // Listen all the database events
    $eventsManager = new \Phalcon\Events\Manager();
    $logger = $di->get('logger');
    // Listen all the database events
    $eventsManager->attach(
        "db:beforeQuery",
        function ($event, $connection) use ($logger) {
            $logger->log(
                $connection->getSQLStatement(),
                \Phalcon\Logger::INFO
            );
        }
    );
    $connection->setEventsManager($eventsManager);
    return $connection;
};





