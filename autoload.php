<?php
/**
 * Responsible for auto loading classes
 * For more information, checkout
 * http://www.php-fig.org/psr/psr-0/
 *
 * I'm using PSR-0 autoloader implementation,
 * but PSR-4 is recommended now.
 * However this is back to the developer to
 * implement what suits him/her.
 *
 * PSR-4 implementation is commented below.
 * For more information, checkout
 * http://www.php-fig.org/psr/psr-4/
 */

/**
 * Defining a constant with the path to the
 * dbconfig.ini file
 */
define('DB_INI', 'config/dbconfig.ini');


function autoload($className)
{
  $className = ltrim($className, '\\');
  $fileName  = '';
  $namespace = '';
  if ($lastNsPos = strrpos($className, '\\')) {
    $namespace = substr($className, 0, $lastNsPos);
    $className = substr($className, $lastNsPos + 1);
    $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
  }
  $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

  require $fileName;
}
spl_autoload_register('autoload');

// PSR-4 implementation:
/*spl_autoload_register(function ($class) {
  // project-specific namespace prefix
  $prefix = 'Foo\\Bar\\';

  // base directory for the namespace prefix
  $base_dir = __DIR__ . '/src/';

  // does the class use the namespace prefix?
  $len = strlen($prefix);
  if (strncmp($prefix, $class, $len) !== 0) {
    // no, move to the next registered autoloader
    return;
  }

  // get the relative class name
  $relative_class = substr($class, $len);

  // replace the namespace prefix with the base directory, replace namespace
  // separators with directory separators in the relative class name, append
  // with .php
  $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

  // if the file exists, require it
  if (file_exists($file)) {
    require $file;
  }
});*/