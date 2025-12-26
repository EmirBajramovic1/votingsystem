<?php

class Config {
    public static function get_env($name, $default = "") {
        return getenv($name) ?: $default;
    }

    public static function is_local() {
        return in_array($_SERVER['SERVER_NAME'], ['localhost', '127.0.0.1']);
    }

    public static function DB_HOST() {
        return self::is_local() ? "127.0.0.1" : self::get_env("DB_HOST");
    }

    public static function DB_NAME() {
        return self::is_local() ? "securevote_db" : self::get_env("DB_NAME");
    }

    public static function DB_USER() {
        return self::is_local() ? "root" : self::get_env("DB_USER");
    }

    public static function DB_PASSWORD() {
        return self::is_local() ? "" : self::get_env("DB_PASSWORD");
    }

    public static function DB_PORT() {
        return self::is_local() ? "3306" : self::get_env("DB_PORT");
    }

    public static function JWT_SECRET() {
        return self::get_env("JWT_SECRET", "securevote_local_secret_2025");
    }
}

class Database {
    private static $connection = null;

    public static function connect() {
        if (self::$connection === null) {

            $dsn = "mysql:host=" . Config::DB_HOST() .
                   ";port=" . Config::DB_PORT() .
                   ";dbname=" . Config::DB_NAME() . ";charset=utf8mb4";

            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ];

            if (!Config::is_local()) {
                $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
                $options[PDO::MYSQL_ATTR_SSL_CA] = null;
            }

            self::$connection = new PDO(
                $dsn,
                Config::DB_USER(),
                Config::DB_PASSWORD(),
                $options
            );
        }
        return self::$connection;
    }
}
?>