<?php
/**
 * Created by PhpStorm.
 * User: karmis
 * Date: 01.10.14
 * Time: 5:18
 */
namespace BS\Common {
    class Autoload
    {
        public function __construct()
        {
        }

        public static function autoload($file)
        {
            $file = str_replace('\\', '/', $file);
            $path = $_SERVER['DOCUMENT_ROOT'] . '/classes';
            $filepath = $_SERVER['DOCUMENT_ROOT'] . '/classes/' . $file . '.php';

            if (file_exists($filepath)) {
                require_once($filepath);

            } else {
                $flag = true;
                Autoload::recursive_autoload($file, $path, $flag);
            }
        }

        public static function recursive_autoload($file, $path, $flag)
        {
            if (FALSE !== ($handle = opendir($path)) && $flag) {
                while (FAlSE !== ($dir = readdir($handle)) && $flag) {

                    if (strpos($dir, '.') === FALSE) {
                        $path2 = $path . '/' . $dir;
                        $filepath = $path2 . '/' . $file . '.php';
                        if (file_exists($filepath)) {
                            $flag = FALSE;
                            require_once($filepath);
                            break;
                        }
                        Autoload::recursive_autoload($file, $path2, $flag);
                    }
                }
                closedir($handle);
            }
        }
    }

    \spl_autoload_register('BS\Common\Autoload::autoload');
}