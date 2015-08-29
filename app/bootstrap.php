<?php

require_once __DIR__ . '/../vendor/autoload.php';

// setup environment
$debug = TRUE;
$temp = __DIR__ . '/../temp';
$cache = $temp . '/cache';
Tracy\Debugger::enable(!$debug);

// register robotloader
$loader = (new \Nette\Loaders\RobotLoader())
	->setCacheStorage(new Nette\Caching\Storages\FileStorage($cache))
	->addDirectory(__DIR__);
$loader->autoRebuild = $debug;
$loader->register();

// compile container
$class = (new \Nette\DI\ContainerLoader($cache, $debug))->load('SystemContainer', function(\Nette\DI\Compiler $compiler) use ($debug) {
	$compiler->getContainerBuilder()->parameters['debugMode'] = $debug;
	$compiler->getContainerBuilder()->addExcludedClasses(['stdClass']);
	$compiler->addExtension('extensions', new \Nette\DI\Extensions\ExtensionsExtension);
	$compiler->loadConfig(__DIR__ . '/config.neon');
});

// create container
$container = new $class;
$container->initialize();

return $container;
