<?php
namespace App;

class SimilarText
{
    public function similar(array $strings): string
    {
        $collection = collect($strings);
        $firstString = $collection->first();
        $originalKeys = $collection->keys()->all();
        $firstKey = array_key_first($strings);
        
        $matches = [];
        foreach ($collection as $key => $string) {
            if ($firstKey == $key) {
                continue;
            }

            for ($start = 1; $start <= strlen($firstString); $start++) {
                for ($i = $start; $i <= strlen($firstString); $i++) { 
                    $part = substr($firstString, $start - 1, $i);
                    if (strpos($string, $part) !== false) {
                        $matches[$part][$firstKey] = true;
                        $matches[$part][$key] = true;
                    }
                }
            }
        }

        foreach ($matches as $key => $partMatches) {
            if (count(array_diff($originalKeys, array_keys($partMatches))) != 0) {
                unset($matches[$key]);
            }
        }

        $matchesWithLength = collect($matches)
            ->keys()
            ->mapWithKeys(function ($item) {
                return [$item => strlen($item)];
            })
        ;
        $maxLengthOfMatch = $matchesWithLength->max();

        return $matchesWithLength->search($maxLengthOfMatch);
    }
}
