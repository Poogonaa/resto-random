<?php

use PhpCsFixer\Finder;
use PhpCsFixer\Config;

$finder = Finder::create()
    ->exclude('somedir')
    ->notPath('src/Symfony/Component/Translation/Tests/fixtures/resources.php')
    ->in(__DIR__)
;

$config = new Config();
return $config->setRules(rules: [
    '@PSR12' => true,
    'strict_param' => true,
    'array_syntax' => array('syntax' => 'short'),
    'protected_to_private' => true,
    'simplified_if_return' => true,
    'no_unused_imports' => true,
])
    ->setFinder($finder)
    ;