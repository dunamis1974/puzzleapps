<?php
/**
 * Single exception type for database configuration and PDO failures.
 *
 * Messages are safe to display: they never contain credentials or DSN text.
 */
class DatabaseException extends RuntimeException
{
}
