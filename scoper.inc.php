<?php

use Isolated\Symfony\Component\Finder\Finder;

return [
    'prefix' => 'WPMUDEV_LastScan_Prefix',
    'finders' => [
        Finder::create()->files()->in('vendor'),
    ],
    'patchers' => [
        // This patcher is necessary to correct the autoload paths in the generated classmap
        function (string $filePath, string $prefix, string $content) {
            if ($filePath === __DIR__ . '/vendor/composer/autoload_classmap.php') {
                return str_replace(
                    "'$prefix\\",
                    "'",
                    $content
                );
            }

            return $content;
        },
    ],
    'expose-global-constants' => false,
    'expose-global-classes' => false,
    'expose-global-functions' => false,
];
