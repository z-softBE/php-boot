<?php

namespace PhpBoot\Di\Cache;

use Nette\PhpGenerator\PhpNamespace;
use PhpBoot\Di\Scan\Model\ConstructorInjectionArg;
use PhpBoot\Di\Scan\Model\ServiceInjectionInfo;
use PhpBoot\Utils\FileSystemUtils;

class ServiceInjectionCacheGenerator
{
    /**
     * @param string $cacheDirectory
     * @param ServiceInjectionInfo[] $serviceInjectionInfoArray
     * @return void
     */
    public function cacheServiceInjectionInfo(string $cacheDirectory, array $serviceInjectionInfoArray): void
    {
        $serviceInjectionInfo = array_map(fn($info) => $this->createInjectionInfo($info), $serviceInjectionInfoArray);

        $namespace = new PhpNamespace('Cache\\PhpBoot\\Di');
        $class = $namespace->addClass('CachedServiceInjectionInfo');
        $class->setFinal()
            ->setReadOnly();

        $class->addConstant('INJECTION_INFO', $serviceInjectionInfo)
            ->setPrivate()
            ->setType('array');

        $class->addMethod('getServiceInjectionInfoArray')
            ->setPublic()
            ->setFinal()
            ->setStatic()
            ->setReturnType('array')
            ->addBody('$infoArray = [];')
            ->addBody('foreach (self::INJECTION_INFO as $info) {')
            ->addBody('$infoArray[] = \\PhpBoot\\Di\\Scan\\Model\\ServiceInjectionInfo::fromArray($info);')
            ->addBody('}')
            ->addBody('return $infoArray;');

        $saveDirectory = $cacheDirectory . DIRECTORY_SEPARATOR . 'Di';
        FileSystemUtils::createDirectory($saveDirectory);

        file_put_contents(
            $saveDirectory . DIRECTORY_SEPARATOR . 'CachedServiceInjectionInfo.php',
            "<?php\n\n" . $namespace
        );
    }

    private function createInjectionInfo(ServiceInjectionInfo $info): array
    {
        return [
            "class" => $info->getClass()->name,
            "injectionName" => $info->getInjectionName(),
            "serviceAttributeClassName" => $info->getServiceAttributeClassName(),
            "primary" => $info->isPrimary(),
            "constructorArgs" => array_map(fn($arg) => $this->createConstructorArg($arg), $info->getConstructorArgs()),
            "beanMethod" => $info->getBeanMethod()?->name,
            "configurationContainerKey" => $info->getConfigurationContainerKey()
        ];
    }

    private function createConstructorArg(ConstructorInjectionArg $arg): array
    {
        return [
            "parameterName" => $arg->getParameter()->name,
            "type" => $arg->getType(),
            "qualifier" => $arg->getQualifier()
        ];
    }
}
