<?php
define('BASE_PATH', dirname(__DIR__) . '/');
define('MATADATA_PATH', "Src/Entities/");

require_once BASE_PATH . 'vendor/autoload.php';

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

$dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
$dotenv->load();

$isDevMode  = $_ENV['DEV_MODE'];
$proxyDir   = null;
$cache      = null;
$useSimpleAnnotationReader = false;

$config = Setup::createAnnotationMetadataConfiguration(
    [BASE_PATH . MATADATA_PATH],
    $isDevMode,
    $proxyDir,
    $cache,
    $useSimpleAnnotationReader
);

$config->setAutoGenerateProxyClasses(true);

$dbParams = [
    'driver'    => $_ENV['DB_DRIVER'],
    'host'      => $_ENV['DB_HOST'],
    'user'      => $_ENV['DB_USERNAME'],
    'password'  => $_ENV['DB_PASSWORD'],
    'dbname'    => $_ENV['DB_DATABASE'],
    'port'    => $_ENV['DB_PORT'],
];

$entityManager = EntityManager::create($dbParams, $config);
