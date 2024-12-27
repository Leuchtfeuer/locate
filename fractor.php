<?php

use a9f\Fractor\Configuration\FractorConfiguration;
use a9f\FractorTypoScript\Configuration\TypoScriptProcessorOption;
use a9f\Typo3Fractor\Set\Typo3LevelSetList;
use Helmich\TypoScriptParser\Parser\Printer\PrettyPrinterConfiguration;

return FractorConfiguration::configure()
    ->withPaths([
        __DIR__ . '/*',
    ])
    ->withSkip([
        __DIR__ . '/.Build',
    ])
    ->withSets([
        Typo3LevelSetList::UP_TO_TYPO3_13
    ])
    ->withOptions([
        TypoScriptProcessorOption::INDENT_SIZE => 4,
        TypoScriptProcessorOption::INDENT_CHARACTER => PrettyPrinterConfiguration::INDENTATION_STYLE_SPACES,
        TypoScriptProcessorOption::ADD_CLOSING_GLOBAL => false,
        TypoScriptProcessorOption::INCLUDE_EMPTY_LINE_BREAKS => true,
        TypoScriptProcessorOption::INDENT_CONDITIONS => true,
    ]);
