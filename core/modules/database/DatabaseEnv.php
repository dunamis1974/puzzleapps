<?php
if (!function_exists("databaseConfigFromEnvironment")) {
    function databaseConfigFromEnvironment() {
        $variables = array(
            "DB_DRIVER",
            "DB_HOST",
            "DB_PORT",
            "DB_NAME",
            "DB_USER",
            "DB_PASSWORD",
            "DB_CHARSET",
        );
        $environment = array();
        $hasConfiguration = false;
        foreach ($variables as $variable) {
            $value = getenv($variable);
            if ($value === false || $value === "") {
                $value = "";
            } else {
                $hasConfiguration = true;
            }
            $environment[$variable] = $value;
        }

        if (!$hasConfiguration) {
            return null;
        }

        if (!class_exists("DatabaseException", false)) {
            require_once __DIR__ . "/DatabaseException.php";
        }

        $driver = $environment["DB_DRIVER"];
        if ($driver === "") {
            throw new DatabaseException("Database environment configuration is incomplete. Missing: DB_DRIVER.");
        }
        if (!in_array($driver, array("mysql", "pgsql", "sqlite"), true)) {
            throw new DatabaseException("DB_DRIVER must be mysql, pgsql, or sqlite.");
        }

        $requiredVariables = $driver === "sqlite"
            ? array("DB_NAME")
            : array("DB_HOST", "DB_NAME", "DB_USER");
        $missingVariables = array();
        foreach ($requiredVariables as $variable) {
            $value = $environment[$variable];
            if ($variable === "DB_HOST" || $variable === "DB_NAME") {
                $value = trim($value);
            }
            if ($value === "") {
                $missingVariables[] = $variable;
            }
        }
        if (count($missingVariables) > 0) {
            throw new DatabaseException("Database environment configuration is incomplete. Missing: " . implode(", ", $missingVariables) . ".");
        }

        $config = array(
            "driver" => $driver,
            "host" => "",
            "port" => "",
            "database" => "",
            "user" => "",
            "password" => "",
            "sqlite_path" => "",
            "charset" => "",
        );

        if ($driver === "sqlite") {
            $config["sqlite_path"] = trim($environment["DB_NAME"]);
            $config["charset"] = trim($environment["DB_CHARSET"]);
            if ($config["charset"] === "") {
                $config["charset"] = "UTF8";
            }
        } else {
            $config["host"] = trim($environment["DB_HOST"]);
            $config["database"] = trim($environment["DB_NAME"]);
            $config["user"] = $environment["DB_USER"];
            $config["password"] = $environment["DB_PASSWORD"];

            $port = trim($environment["DB_PORT"]);
            if ($port === "") {
                $port = (string)($driver === "mysql" ? 3306 : 5432);
            }
            if (!preg_match('/^[0-9]+$/D', $port) || (int)$port < 1 || (int)$port > 65535 || (string)(int)$port !== $port) {
                throw new DatabaseException("DB_PORT must be an integer between 1 and 65535.");
            }
            $config["port"] = (int)$port;

            $config["charset"] = trim($environment["DB_CHARSET"]);
            if ($config["charset"] === "") {
                $config["charset"] = $driver === "mysql" ? "utf8mb4" : "UTF8";
            }
        }

        if (!preg_match('/^[A-Za-z0-9_-]+$/', $config["charset"])) {
            throw new DatabaseException("DB_CHARSET may contain only letters, numbers, underscores, and hyphens.");
        }

        return $config;
    }
}
