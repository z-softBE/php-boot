<?php

namespace PhpBoot\Starter\DoctrineORM\Composer;

use PhpBoot\Utils\FileSystemUtils;

class DoctrineORMPostInstaller
{
    private const string PHP_FILE_CONTENT = <<<'PHP'
<?php

require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

$reader = new \PhpBoot\Di\Property\PropertiesReader();
$properties = $reader->readProperties(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'properties.yaml');

if (!isset($properties['doctrine']) || !isset($properties['doctrine']['orm'])) {
    echo "Can not execute command. Not all properties are set.";
    die; 
}

$ormProps = $properties['doctrine']['orm'];

$connectionOptions = array_filter([
            'driver' => $ormProps['driver'] ?? null,
            'driverClass' => $ormProps['driverClass'] ?? null,
            'user' => $ormProps['user'] ?? null,
            'password' => $ormProps['password'] ?? null,
            'driverOptions' => $ormProps['driverOptions'] ?? null,
            'wrapperClass' => $ormProps['wrapperClass'] ?? null,
            'path' => isset($ormProps['sqlite']['path']) ? dirname(__DIR__) . DIRECTORY_SEPARATOR . $ormProps['sqlite']['path'] : null
        ]);

$metaDataConfig = \Doctrine\ORM\ORMSetup::createAttributeMetadataConfiguration(
    array_map(fn($path) => dirname(__DIR__) . DIRECTORY_SEPARATOR . $path, $ormProps['scanPaths'] ?? []),
    true
);
$connection = \Doctrine\DBAL\DriverManager::getConnection($connectionOptions, $metaDataConfig);
$entityManager = new \Doctrine\ORM\EntityManager($connection, $metaDataConfig);

\Doctrine\ORM\Tools\Console\ConsoleRunner::run(
    new \Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider($entityManager)
);
PHP;

    public static function execute(): void
    {
        $binDir = getcwd() . DIRECTORY_SEPARATOR . 'bin';

        FileSystemUtils::createDirectory($binDir);

        $filePath = $binDir . DIRECTORY_SEPARATOR . 'doctrine';

        file_put_contents($filePath, self::PHP_FILE_CONTENT) or die("Unable to write file: {$filePath}\n");

        echo "Created the executable file: {$filePath}\n";
    }
}