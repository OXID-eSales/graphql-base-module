<?php

$finder = PhpCsFixer\Finder::create()
    ->in('src/')
    ->in('tests/')
;

return PhpCsFixer\Config::create()
    ->setRules([
        '@PSR2' => true,
        'ordered_imports' => true,
        'no_unused_imports' => true,
        'array_syntax' => [
            'syntax' => 'short'
        ],
    ])
    ->setFinder($finder)
;
