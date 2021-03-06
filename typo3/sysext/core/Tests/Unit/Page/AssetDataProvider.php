<?php
declare(strict_types = 1);

namespace TYPO3\CMS\Core\Tests\Unit\Page;

class AssetDataProvider
{
    public static function filesDataProvider(): array
    {
        return [
            '1 file from fileadmin' => [
                [
                    ['file1', 'fileadmin/foo.ext', [], []]
                ],
                [
                    'file1' => [
                        'source' => 'fileadmin/foo.ext',
                        'attributes' => [],
                        'options' => [],
                    ]
                ],
                [
                    'css_no_prio' => '<link href="fileadmin/foo.ext" rel="stylesheet" type="text/css" >',
                    'css_prio' => '',
                    'js_no_prio' => '<script src="fileadmin/foo.ext" type="text/javascript"></script>',
                    'js_prio' => '',
                ]
            ],
            '1 file from extension' => [
                [
                    ['file1', 'EXT:core/Resource/Public/foo.ext', [], []]
                ],
                [
                    'file1' => [
                        'source' => 'EXT:core/Resource/Public/foo.ext',
                        'attributes' => [],
                        'options' => [],
                    ]
                ],
                [
                    'css_no_prio' => '<link href="typo3/sysext/core/Resource/Public/foo.ext" rel="stylesheet" type="text/css" >',
                    'css_prio' => '',
                    'js_no_prio' => '<script src="typo3/sysext/core/Resource/Public/foo.ext" type="text/javascript"></script>',
                    'js_prio' => '',
                ]
            ],
            '2 files' => [
                [
                    ['file1', 'fileadmin/foo.ext', [], []],
                    ['file2', 'EXT:core/Resource/Public/foo.ext', [], []]
                ],
                [
                    'file1' => [
                        'source' => 'fileadmin/foo.ext',
                        'attributes' => [],
                        'options' => [],
                    ],
                    'file2' => [
                        'source' => 'EXT:core/Resource/Public/foo.ext',
                        'attributes' => [],
                        'options' => [],
                    ]
                ],
                [
                    'css_no_prio' => '<link href="fileadmin/foo.ext" rel="stylesheet" type="text/css" >' . LF . '<link href="typo3/sysext/core/Resource/Public/foo.ext" rel="stylesheet" type="text/css" >',
                    'css_prio' => '',
                    'js_no_prio' => '<script src="fileadmin/foo.ext" type="text/javascript"></script>' . LF . '<script src="typo3/sysext/core/Resource/Public/foo.ext" type="text/javascript"></script>',
                    'js_prio' => '',
                ]
            ],
            '2 files with override' => [
                [
                    ['file1', 'fileadmin/foo.ext', [], []],
                    ['file2', 'EXT:core/Resource/Public/foo.ext', [], []],
                    ['file1', 'EXT:core/Resource/Public/bar.ext', [], []]
                ],
                [
                    'file1' => [
                        'source' => 'EXT:core/Resource/Public/bar.ext',
                        'attributes' => [],
                        'options' => [],
                    ],
                    'file2' => [
                        'source' => 'EXT:core/Resource/Public/foo.ext',
                        'attributes' => [],
                        'options' => [],
                    ]
                ],
                [
                    'css_no_prio' => '<link href="typo3/sysext/core/Resource/Public/bar.ext" rel="stylesheet" type="text/css" >' . LF . '<link href="typo3/sysext/core/Resource/Public/foo.ext" rel="stylesheet" type="text/css" >',
                    'css_prio' => '',
                    'js_no_prio' => '<script src="typo3/sysext/core/Resource/Public/bar.ext" type="text/javascript"></script>' . LF . '<script src="typo3/sysext/core/Resource/Public/foo.ext" type="text/javascript"></script>',
                    'js_prio' => '',
                ]
            ],
            '1 file with attributes' => [
                [
                    ['file1', 'fileadmin/foo.ext', ['rel' => 'foo'], []]
                ],
                [
                    'file1' => [
                        'source' => 'fileadmin/foo.ext',
                        'attributes' => [
                            'rel' => 'foo'
                        ],
                        'options' => [],
                    ]
                ],
                [
                    'css_no_prio' => '<link rel="foo" href="fileadmin/foo.ext" type="text/css" >',
                    'css_prio' => '',
                    'js_no_prio' => '<script rel="foo" src="fileadmin/foo.ext" type="text/javascript"></script>',
                    'js_prio' => '',
                ]
            ],
            '1 file with attributes override' => [
                [
                    ['file1', 'fileadmin/foo.ext', ['rel' => 'foo', 'another' => 'keep on override'], []],
                    ['file1', 'fileadmin/foo.ext', ['rel' => 'bar'], []]
                ],
                [
                    'file1' => [
                        'source' => 'fileadmin/foo.ext',
                        'attributes' => [
                            'rel' => 'bar',
                            'another' => 'keep on override'
                        ],
                        'options' => [],
                    ]
                ],
                [
                    'css_no_prio' => '<link rel="bar" another="keep on override" href="fileadmin/foo.ext" type="text/css" >',
                    'css_prio' => '',
                    'js_no_prio' => '<script rel="bar" another="keep on override" src="fileadmin/foo.ext" type="text/javascript"></script>',
                    'js_prio' => '',
                ]
            ],
            '1 file with options' => [
                [
                    ['file1', 'fileadmin/foo.ext', [], ['priority' => true]]
                ],
                [
                    'file1' => [
                        'source' => 'fileadmin/foo.ext',
                        'attributes' => [],
                        'options' => [
                            'priority' => true
                        ],
                    ]
                ],
                [
                    'css_no_prio' => '',
                    'css_prio' => '<link href="fileadmin/foo.ext" rel="stylesheet" type="text/css" >',
                    'js_no_prio' => '',
                    'js_prio' => '<script src="fileadmin/foo.ext" type="text/javascript"></script>',
                ]
            ],
            '1 file with options override' => [
                [
                    ['file1', 'fileadmin/foo.ext', [], ['priority' => true, 'another' => 'keep on override']],
                    ['file1', 'fileadmin/foo.ext', [], ['priority' => false]]
                ],
                [
                    'file1' => [
                        'source' => 'fileadmin/foo.ext',
                        'attributes' => [],
                        'options' => [
                            'priority' => false,
                            'another' => 'keep on override'
                        ],
                    ]
                ],
                [
                    'css_no_prio' => '<link href="fileadmin/foo.ext" rel="stylesheet" type="text/css" >',
                    'css_prio' => '',
                    'js_no_prio' => '<script src="fileadmin/foo.ext" type="text/javascript"></script>',
                    'js_prio' => '',
                ]
            ],
        ];
    }

    public static function inlineDataProvider(): array
    {
        return [
            'simple data' => [
                [
                    ['identifier_1', 'foo bar baz', [], []]
                ],
                [
                    'identifier_1' => [
                        'source' => 'foo bar baz',
                        'attributes' => [],
                        'options' => [],
                    ]
                ],
                [
                    'css_no_prio' => '<style>foo bar baz</style>',
                    'css_prio' => '',
                    'js_no_prio' => '<script type="text/javascript">foo bar baz</script>',
                    'js_prio' => '',
                ]
            ],
            '2 times simple data' => [
                [
                    ['identifier_1', 'foo bar baz', [], []],
                    ['identifier_2', 'bar baz foo', [], []]
                ],
                [
                    'identifier_1' => [
                        'source' => 'foo bar baz',
                        'attributes' => [],
                        'options' => [],
                    ],
                    'identifier_2' => [
                        'source' => 'bar baz foo',
                        'attributes' => [],
                        'options' => [],
                    ]
                ],
                [
                    'css_no_prio' => '<style>foo bar baz</style>' . LF . '<style>bar baz foo</style>',
                    'css_prio' => '',
                    'js_no_prio' => '<script type="text/javascript">foo bar baz</script>' . LF . '<script type="text/javascript">bar baz foo</script>',
                    'js_prio' => '',
                ]
            ],
            '2 times simple data with override' => [
                [
                    ['identifier_1', 'foo bar baz', [], []],
                    ['identifier_2', 'bar baz foo', [], []],
                    ['identifier_1', 'baz foo bar', [], []],
                ],
                [
                    'identifier_1' => [
                        'source' => 'baz foo bar',
                        'attributes' => [],
                        'options' => [],
                    ],
                    'identifier_2' => [
                        'source' => 'bar baz foo',
                        'attributes' => [],
                        'options' => [],
                    ]
                ],
                [
                    'css_no_prio' => '<style>baz foo bar</style>' . LF . '<style>bar baz foo</style>',
                    'css_prio' => '',
                    'js_no_prio' => '<script type="text/javascript">baz foo bar</script>' . LF . '<script type="text/javascript">bar baz foo</script>',
                    'js_prio' => '',
                ]
            ],
            'simple data with attributes' => [
                [
                    ['identifier_1', 'foo bar baz', ['rel' => 'foo'], []],
                ],
                [
                    'identifier_1' => [
                        'source' => 'foo bar baz',
                        'attributes' => [
                            'rel' => 'foo'
                        ],
                        'options' => [],
                    ]
                ],
                [
                    'css_no_prio' => '<style rel="foo">foo bar baz</style>',
                    'css_prio' => '',
                    'js_no_prio' => '<script rel="foo" type="text/javascript">foo bar baz</script>',
                    'js_prio' => '',
                ]
            ],
            'simple data with attributes override' => [
                [
                    ['identifier_1', 'foo bar baz', ['rel' => 'foo', 'another' => 'keep on override'], []],
                    ['identifier_1', 'foo bar baz', ['rel' => 'bar'], []],
                ],
                [
                    'identifier_1' => [
                        'source' => 'foo bar baz',
                        'attributes' => [
                            'rel' => 'bar',
                            'another' => 'keep on override'
                        ],
                        'options' => [],
                    ]
                ],
                [
                    'css_no_prio' => '<style rel="bar" another="keep on override">foo bar baz</style>',
                    'css_prio' => '',
                    'js_no_prio' => '<script rel="bar" another="keep on override" type="text/javascript">foo bar baz</script>',
                    'js_prio' => '',
                ]
            ],
            'simple data with options' => [
                [
                    ['identifier_1', 'foo bar baz', [], ['priority' => true]]
                ],
                [
                    'identifier_1' => [
                        'source' => 'foo bar baz',
                        'attributes' => [],
                        'options' => [
                            'priority' => true
                        ],
                    ]
                ],
                [
                    'css_no_prio' => '',
                    'css_prio' => '<style>foo bar baz</style>',
                    'js_no_prio' => '',
                    'js_prio' => '<script type="text/javascript">foo bar baz</script>',
                ]
            ],
            'simple data with options override' => [
                [
                    ['identifier_1', 'foo bar baz', [], ['priority' => true, 'another' => 'keep on override']],
                    ['identifier_1', 'foo bar baz', [], ['priority' => false]]
                ],
                [
                    'identifier_1' => [
                        'source' => 'foo bar baz',
                        'attributes' => [],
                        'options' => [
                            'priority' => false,
                            'another' => 'keep on override'
                        ],
                    ]
                ],
                [
                    'css_no_prio' => '<style>foo bar baz</style>',
                    'css_prio' => '',
                    'js_no_prio' => '<script type="text/javascript">foo bar baz</script>',
                    'js_prio' => '',
                ]
            ],
        ];
    }

    public static function mediaDataProvider(): array
    {
        return [
            '1 image no additional information' => [
                [
                    ['fileadmin/foo.png', []]
                ],
                [
                    'fileadmin/foo.png' => []
                ]
            ],
            '2 images no additional information' => [
                [
                    ['fileadmin/foo.png', []],
                    ['fileadmin/bar.png', []],
                ],
                [
                    'fileadmin/foo.png' => [],
                    'fileadmin/bar.png' => [],
                ]
            ],
            '1 image with additional information' => [
                [
                    ['fileadmin/foo.png', ['foo' => 'bar']]
                ],
                [
                    'fileadmin/foo.png' => ['foo' => 'bar']
                ]
            ],
            '2 images with additional information' => [
                [
                    ['fileadmin/foo.png', ['foo' => 'bar']],
                    ['fileadmin/bar.png', ['foo' => 'baz']],
                ],
                [
                    'fileadmin/foo.png' => ['foo' => 'bar'],
                    'fileadmin/bar.png' => ['foo' => 'baz'],
                ]
            ],
            '2 images with additional information override' => [
                [
                    ['fileadmin/foo.png', ['foo' => 'bar']],
                    ['fileadmin/bar.png', ['foo' => 'baz']],
                    ['fileadmin/foo.png', ['foo' => 'baz']],
                ],
                [
                    'fileadmin/foo.png' => ['foo' => 'baz'],
                    'fileadmin/bar.png' => ['foo' => 'baz'],
                ]
            ],
            '2 images with additional information override keep existing' => [
                [
                    ['fileadmin/foo.png', ['foo' => 'bar', 'bar' => 'baz']],
                    ['fileadmin/bar.png', ['foo' => 'baz']],
                    ['fileadmin/foo.png', ['foo' => 'baz']],
                ],
                [
                    'fileadmin/foo.png' => ['foo' => 'baz', 'bar' => 'baz'],
                    'fileadmin/bar.png' => ['foo' => 'baz'],
                ]
            ],
        ];
    }
}
