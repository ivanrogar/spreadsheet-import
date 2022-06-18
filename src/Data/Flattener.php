<?php

declare(strict_types=1);

namespace App\Data;

class Flattener
{
    /**
     * @param array<array<string, mixed>> $data
     * @return array<mixed>
     */
    public function flatten(array $data): array
    {
        $flattenedData = [];

        // add headers
        // for the sake of this exercise, we have wishful thinking that each entry has same
        // headers and in the same order
        if (count($data)) {
            $flattenedData[] = array_keys(current($data));
        }

        foreach ($data as $item) {
            if (is_array($item)) {
                $flattenedData[] = array_map(function ($value) {
                    if ($value === null) {
                        $value = '';
                    }

                    return $value;
                }, array_values($item));
            }
        }

        return $flattenedData;
    }
}
