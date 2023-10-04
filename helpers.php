<?php

use Autorepublicate\Services\Short_Code_Service;
use ElementorPro\Modules\ThemeBuilder\Documents\Error_404;

if (!function_exists('autopublicate_loader')) {
    /**
     * Autoload all the files inside a path that is provided in the params
     * 
     * @param string $path
     * @return void
     */
    function autopublicate_loader($path)
    {
        //Append the plugin dir
        $baseDir = plugin_dir_path(__FILE__);
        $path = $baseDir . $path;

        $classes = [];
        foreach (glob("$path/*") as $filename) {
            if (preg_match('/\.php$/', $filename)) {
                require_once $filename;

                //Filter out all the classes
                if (str_contains($filename, '.class.')) {
                    $classes[] = str_replace(' ', '_', ucwords(str_replace(['.class.php', '-'], ['', ' '], basename($filename))));
                }
            } elseif (is_dir($filename)) {
                autopublicate_loader(str_replace($baseDir, '', $filename));
            }
        }
    }
}

if (!function_exists('autopublicate_file_loader')) {

    /**
     * Autoload the file inside a path that is provided in the params
     * 
     * @param string $filePath
     * @return void
     */
    function autopublicate_file_loader($filePath, $data = [])
    {
        extract($data);
        require_once plugin_dir_path(__FILE__) . trim($filePath, '/');
    }
}

if (!function_exists('abort')) {
    function abort()
    {
        global $wp_query;
        $wp_query->is_404 = true;
    }
}
