<?php

/**
 * Converts strings to UTF-8 recursively for containing types
 * 
 * Credit: Stack Overflow user "Adam Bubela"
 * https://stackoverflow.com/questions/19361282/why-would-json-encode-return-an-empty-string
 * @param mixed $d the array or object to convert to UTF-8
 * @return mixed the original object with all strings converted to UTF-8
 */
function ncode_strings($d)
{
    if (is_array($d)) {
        foreach ($d as $k => $v) {
            $d[$k] = ncode_strings($v);
        }
    } elseif (is_object($d)) {
        foreach ($d as $k => $v) {
            $d->$k = ncode_strings($v);
        }
    } elseif (is_string($d)) {
        return utf8_encode($d);
    }
    return $d;
}

/**
 * Creates a json string out of the provided object. Essentially json_encode()
 * with an extra step to ensure UTF-8 encoding.
 * @param mixed $d the object to convert to json
 * @return string json encoded string of object
 */
function ncode_json($d){
    $u8 = ncode_strings($d);
    return json_encode($u8);
}