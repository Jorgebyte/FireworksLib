<?php

$header = <<<EOF
@package   FireworksLib
@author    Jorgebyte
@version   1.0.0
@api       5.0.0
@copyright (c) 2024 Jorgebyte. All rights reserved under the license.
@license   GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007
EOF;

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->name('*.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PER-CS2.0' => true,

        'declare_strict_types' => true,
        'strict_param' => true,
        'fully_qualified_strict_types' => true,
        'nullable_type_declaration_for_default_null_value' => ['use_nullable_type_declaration' => true],

        'header_comment' => [
            'header' => $header,
            'comment_type' => 'PHPDoc',
            'location' => 'after_declare_strict',
            'separate' => 'both',
        ],

        'no_unused_imports' => true,
        'ordered_imports' => ['sort_algorithm' => 'alpha', 'imports_order' => ['const', 'class', 'function']],
        'no_leading_import_slash' => true,
        'single_line_after_imports' => true,

        'array_syntax' => ['syntax' => 'short'],
        'no_multiline_whitespace_around_double_arrow' => true,
        'trim_array_spaces' => true,
        'whitespace_after_comma_in_array' => true,
        'trailing_comma_in_multiline' => ['elements' => ['arrays', 'arguments', 'parameters']],
        'no_trailing_comma_in_singleline' => true,

        'statement_indentation' => true,
        'no_extra_blank_lines' => [
            'tokens' => ['extra', 'throw', 'use', 'return', 'switch', 'case', 'default', 'break', 'continue'],
        ],
        'no_trailing_whitespace' => true,
        'single_blank_line_at_eof' => true,
        'no_whitespace_in_blank_line' => true,
        'compact_nullable_typehint' => true,
        'multiline_whitespace_before_semicolons' => ['strategy' => 'no_multi_line'],

        'phpdoc_align' => ['align' => 'vertical'],
        'phpdoc_order' => true,
        'phpdoc_scalar' => true,
        'phpdoc_separation' => ['groups' => [['deprecated', 'link', 'see', 'since'], ['author', 'copyright', 'license'], ['category', 'package', 'subpackage'], ['property', 'property-read', 'property-write'], ['param', 'return']]],
        'phpdoc_trim' => true,
        'phpdoc_to_comment' => false,
        'no_superfluous_phpdoc_tags' => ['allow_mixed' => true, 'allow_unused_params' => true],

        'braces_position' => [
            'allow_single_line_anonymous_functions' => true,
            'functions_opening_brace' => 'next_line_unless_newline_at_signature_end',
            'classes_opening_brace' => 'next_line_unless_newline_at_signature_end',
        ],
        'method_argument_space' => [
            'on_multiline' => 'ensure_fully_multiline',
            'keep_multiple_spaces_after_comma' => false,
        ],
        'visibility_required' => ['elements' => ['method', 'property', 'const']],
        'no_useless_else' => true,
        'no_useless_return' => true,
        'return_assignment' => true,

        'binary_operator_spaces' => ['default' => 'single_space'],
        'concat_space' => ['spacing' => 'one'],
        'object_operator_without_whitespace' => true,
        'yoda_style' => false,

        'cast_spaces' => ['space' => 'single'],
        'short_scalar_cast' => true,
        'native_function_casing' => true,
        'no_empty_statement' => true,
        'single_quote' => true,
        'explicit_string_variable' => true,
    ])
    ->setFinder($finder);