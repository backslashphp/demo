<?php

declare(strict_types=1);

namespace Demo\UI;

use InvalidArgumentException;
use Stringable;

class Table implements Stringable
{
    private array $rows;

    public function __construct(array $rows)
    {
        if (empty($rows)) {
            throw new InvalidArgumentException();
        }
        $widths = [];
        $colCount = count($rows[0]);
        foreach ($rows as $row) {
            if (count($row) !== $colCount) {
                throw new InvalidArgumentException();
            }
            foreach ($row as $i => $col) {
                if ($col && !is_scalar($col)) {
                    throw new InvalidArgumentException($col);
                }
                $widths[$i] = max($widths[$i] ?? 0, strlen((string) $col));
            }
        }
        foreach ($rows as &$row) {
            foreach ($row as $i => &$col) {
                $col = ' ' . str_pad((string) $col, $widths[$i], ' ') . ' ';
            }
        }

        $this->rows = $rows;
    }

    public function __toString(): string
    {
        $output = '';
        foreach ($this->rows as $row) {
            $output .= '|' . implode('|', $row) . '|' . PHP_EOL;
        }
        return $output;
    }
}
