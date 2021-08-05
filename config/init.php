<?php
include "global.php";

// class autoload
spl_autoload_register(function ($class) {
    $directories = getSubDirectories(ROOT."class");
    $class_arr = explode("\\", $class);
    $len = count($class_arr);
    $classFile = $class_arr[($len - 1)];
    foreach ($directories as $dir) {
        $file = $dir . "/" . checkFileExtension($classFile);        
        if (is_file($file)) {             
            require_once($file); 
        }
    }
});
/**
 * @param $dir : directory name where to check
 * @return array|false 
 */
function getSubDirectories($dir)
{
    $subDir = array();
    $directories = array_filter(glob($dir), 'is_dir');
    $subDir = array_merge($subDir, $directories);
    foreach ($directories as $directory) $subDir = array_merge($subDir, getSubDirectories($directory . '/*'));
    return $subDir;
}
/**
 * @param $fileName : return file with valid extension
 * @return mixed|string
 */
function checkFileExtension($fileName, $extention = "php")
{
    $file_parts = pathinfo($fileName);
    $file = (isset($file_parts['extension']) && $file_parts['extension'] == $extention) ? $fileName : $fileName . ".$extention";
    return $file;
}