<?php
/**
 * 
 * 数据库工厂类
 * @author monky
 */
class DBFactory {

    static $db = array();

    /**
     * @param type $name
     * @return PDO
     */
    public static function factory($name, $select) {

        $buffer = "{$name}_{$select}";

        if (isset(self::$db[$buffer])) {
            return self::$db[$buffer];
        }

        if(!isset($GLOBALS['databases'])){
            Output::jsonStr(Error::ERROR_SYSTEM_FAIL, 'The config datebases is null!');
        }

        if(!isset($GLOBALS['databases'][$name])){
            Output::jsonStr(Error::ERROR_SYSTEM_FAIL, "The datebase $name is unknow!");
        }
        
        $databases = $GLOBALS['databases'][$name];
        $c = (object)$databases[$select];
        $charset = (isset($c->charset) && $c->charset != null) ? $c->charset : 'utf8mb4';
        $dsn = sprintf("%s:dbname=%s;host=%s;port=%s"
                , $c->driver, $c->dbname, self::choiceHost($c->host), $c->port);
        $instance = new PDO($dsn, $c->username, $c->password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES '{$charset}'"));
        $instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $instance->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
        self::$db[$buffer] = $instance;
        return self::$db[$buffer];
    }

    private static function choiceHost($host) {
        $hosts = explode('|', $host);
        $noOfHost = sizeof($hosts);
        if ($noOfHost > 1) {
            return $hosts[time() % $noOfHost];
        }
        return $host;
    }

}
