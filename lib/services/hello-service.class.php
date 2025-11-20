<?php

namespace SoapTest\Services;

/**
 * Hello Service - implementuje základní SOAP operace
 * Obsahuje metody definované v hello.wsdl
 */
class HelloService
{
    /**
     * Pozdrav uživatele
     * @param string $name Jméno uživatele
     * @return string Pozdrav
     */
    public function sayHello($name)
    {
        if (empty($name)) {
            $name = "World";
        }

        return "Hello, " . htmlspecialchars($name) . "!";
    }

    /**
     * Získá aktuální čas
     * @return string Aktuální čas
     */
    public function getCurrentTime()
    {
        return date('Y-m-d H:i:s');
    }

    /**
     * Jednoduché sčítání dvou čísel
     * @param float $a První číslo
     * @param float $b Druhé číslo
     * @return float Součet
     */
    public function add($a, $b)
    {
        return (float)$a + (float)$b;
    }

    /**
     * Získá informace o serveru
     * @return array Informace o serveru
     */
    public function getServerInfo()
    {
        return [
            'server_time' => date('Y-m-d H:i:s'),
            'php_version' => PHP_VERSION,
            'server_name' => $_SERVER['SERVER_NAME'] ?? 'localhost'
        ];
    }
}
