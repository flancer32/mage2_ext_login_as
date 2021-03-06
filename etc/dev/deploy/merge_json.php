<?php
/**
 * Get one JSON file (arg #1), unset keys read from text file (arg. #2) and merge with other JSON file (arg. #3).
 * Backup first JSON and save result with the same name (as first JSON).
 */

if ($argc >= 3) {
    /* parse arguments: $src $additions */
    $fileComposer = $argv[1];
    $fileUnset = $argv[2];
    $fileOpts = $argv[3];
    /* load original 'composer.json' */
    $main = load_json($fileComposer);
    /* Load list to filter extra data and unset it */
    $unset = load_json($fileUnset);
    unset_node($main, $unset);
    /* load additional options */
    $opts = load_json($fileOpts);
    /* merge both JSONs and save as source with suffix '.merged' */
    $arrMerged = array_merge_recursive($main, $opts);
    $jsonMerged = json_encode($arrMerged, JSON_UNESCAPED_SLASHES);
    file_put_contents($fileComposer . '.merged.json', $jsonMerged);
    /* backup original source file and replace it by merged */
    $tstamp = date('.YmdHis');
    rename($fileComposer, $fileComposer . $tstamp);
    rename($fileComposer . '.merged.json', $fileComposer);
} else {
    $iAm = __FILE__;
    echo "\nUsage: $iAm 'source.json' 'unset.json' 'opts_to_add.json'";
}
return;

/**
 * Unset node in source array.
 *
 * @param array $sourceArr
 * @param mixed $node
 */
function unset_node(&$sourceArr, $node)
{
    if (is_array($node)) {
        foreach ($node as $key => $item) {
            if (isset($sourceArr[$key])) {
                unset_node($sourceArr[$key], $item);
            } elseif (!is_array($item) && isset($sourceArr[$item])) {
                unset_node($sourceArr, $item);
            } else {
                unset_node($sourceArr, $item);
            }
        }
    } else {
        if (isset($sourceArr[$node])) {
            unset($sourceArr[$node]);
        }
    }
}

/**
 * Load file with JSON and parse into array.
 *
 * @param string $file
 * @return mixed
 */
function load_json($file)
{
    $jsonFile = file_get_contents($file);
    $result = json_decode($jsonFile, true);
    return $result;
}