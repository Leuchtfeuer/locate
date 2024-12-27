<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    die('This script supports command line usage only. Please check your command.');
}

$headerComment = <<<COMMENT
This file is part of the "Locate" extension for TYPO3 CMS.

For the full copyright and license information, please read the
LICENSE.txt file that was distributed with this source code.

Team YD <dev@Leuchtfeuer.com>, Leuchtfeuer Digital Marketing
COMMENT;

$finder = (new PhpCsFixer\Finder())
    ->name('*.php')
    ->in(__DIR__)
    ->exclude('Configuration')
    ->exclude('Libraries')
    ->exclude('Resources')
    ->notName('ext_emconf.php')
    ->notName('ext_tables.php')
    ->notName('ext_localconf.php')
    ->notName('php-cs-fixer.php');

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@DoctrineAnnotation' => true,
        // @todo: Switch to @PER-CS2.0 once php-cs-fixer's todo list is done: https://github.com/PHP-CS-Fixer/PHP-CS-Fixer/issues/7247
        '@PER-CS1.0' => true,
        'array_indentation' => true,
        'array_syntax' => ['syntax' => 'short'],
        'braces_position' => true,
        'cast_spaces' => ['space' => 'none'],
        // @todo: Can be dropped once we enable @PER-CS2.0
        'compact_nullable_type_declaration' => true,
        'concat_space' => ['spacing' => 'one'],
        'control_structure_braces' => true,
        'control_structure_continuation_position' => true,
        'declare_equal_normalize' => ['space' => 'none'],
        'declare_parentheses' => true,
        'dir_constant' => true,
        // @todo: Can be dropped once we enable @PER-CS2.0
        'function_declaration' => [
            'closure_fn_spacing' => 'none',
        ],
        'function_to_constant' => ['functions' => ['get_called_class', 'get_class', 'get_class_this', 'php_sapi_name', 'phpversion', 'pi']],
        'type_declaration_spaces' => true,
        'global_namespace_import' => ['import_classes' => false, 'import_constants' => false, 'import_functions' => false],
        'header_comment' => [
            'header' => $headerComment,
            'comment_type' => 'comment',
            'location' => 'after_declare_strict',
            'separate' => 'both',
        ],
        'list_syntax' => ['syntax' => 'short'],
        // @todo: Can be dropped once we enable @PER-CS2.0
        'method_argument_space' => true,
        'modernize_strpos' => true,
        'modernize_types_casting' => true,
        'native_function_casing' => true,
        'new_with_parentheses' => true,
        'no_alias_functions' => true,
        'no_blank_lines_after_phpdoc' => true,
        'no_empty_phpdoc' => true,
        'no_empty_statement' => true,
        'no_extra_blank_lines' => true,
        'no_leading_namespace_whitespace' => true,
        'no_multiple_statements_per_line' => true,
        'no_null_property_initialization' => true,
        'no_short_bool_cast' => true,
        'no_singleline_whitespace_before_semicolons' => true,
        'no_superfluous_elseif' => true,
        'no_trailing_comma_in_singleline' => true,
        'no_unneeded_control_parentheses' => true,
        'no_unused_imports' => true,
        'no_useless_else' => true,
        'no_useless_nullsafe_operator' => true,
        'ordered_imports' => ['imports_order' => ['class', 'function', 'const'], 'sort_algorithm' => 'alpha'],
        'php_unit_construct' => ['assertions' => ['assertEquals', 'assertSame', 'assertNotEquals', 'assertNotSame']],
        'php_unit_mock_short_will_return' => true,
        'php_unit_test_case_static_method_calls' => ['call_type' => 'self'],
        'phpdoc_no_access' => true,
        'phpdoc_no_empty_return' => true,
        'phpdoc_no_package' => true,
        'phpdoc_scalar' => true,
        'phpdoc_trim' => true,
        'phpdoc_types' => true,
        'phpdoc_types_order' => ['null_adjustment' => 'always_last', 'sort_algorithm' => 'none'],
        'return_type_declaration' => ['space_before' => 'none'],
        'single_quote' => true,
        'single_space_around_construct' => true,
        'single_line_comment_style' => ['comment_types' => ['hash']],
        // @todo: Can be dropped once we enable @PER-CS2.0
        'single_line_empty_body' => true,
        'statement_indentation' => true,
        'trailing_comma_in_multiline' => ['elements' => ['arrays']],
        'whitespace_after_comma_in_array' => ['ensure_single_space' => true],
        'yoda_style' => ['equal' => false, 'identical' => false, 'less_and_greater' => false],
    ])
    ->setFinder($finder);
