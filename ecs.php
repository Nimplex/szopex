<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use PhpCsFixer\Fixer\ListNotation\ListSyntaxFixer;
use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withPaths([
        __DIR__ . '/public',
        __DIR__ . '/src',
    ])

    // add a single rule
    ->withRules([
        NoUnusedImportsFixer::class,
        ListSyntaxFixer::class,
    ])

    ->withConfiguredRule(
        ArraySyntaxFixer::class,
        array('syntax' => 'short')
    )

    // add sets - group of rules, from easiest to more complex ones
    // uncomment one, apply one, commit, PR, merge and repeat
    ->withPreparedSets(
          docblocks: true,
          arrays: true,
          // spaces: true,
          // namespaces: true,
          // comments: true,
    )
    ;
