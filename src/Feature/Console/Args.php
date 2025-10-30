<?php

declare(strict_types=1);

namespace Demo\Feature\Console;

class Args
{
    public static function get(string ...$required): array
    {
        $args = array_merge(
            array_combine($required, array_fill(0, count($required), null)),
            getopt('', array_map(fn ($arg) => $arg . ':', $required)),
        );
        foreach ($required as $arg) {
            if (is_null($args[$arg])) {
                echo sprintf('--%s must be provided.', $arg) . PHP_EOL;
                exit(-1);
            }
        }
        return $args;
    }
}
