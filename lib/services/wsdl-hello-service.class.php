<?php

namespace SoapTest\Services;

/**
 * WSDL Hello Service - implementuje SOAP operace s jednoduchou autentizací
 * Autentizace je součástí SOAP message parametrů
 */
class WSDLHelloService
{
    private $users;

    public function __construct()
    {
        // Jednoduché uživatelské účty
        $this->users = [
            'admin' => 'password123',
            'user' => 'user123',
            'test' => 'test123',
            'soap' => 'soap123'
        ];
    }

    /**
     * Ověří přihlašovací údaje z HTTP headers
     * @return array|false User info nebo false při neúspěchu
     * @throws \SoapFault
     */
    private function authenticate()
    {
        // Získej credentials z HTTP Basic Auth nebo custom headers
        $username = $_SERVER['PHP_AUTH_USER'] ?? $_SERVER['HTTP_X_USERNAME'] ?? '';
        $password = $_SERVER['PHP_AUTH_PW'] ?? $_SERVER['HTTP_X_PASSWORD'] ?? '';

        if (empty($username) || empty($password)) {
            throw new \SoapFault('Client', 'Authentication required via HTTP headers (PHP_AUTH_USER/PHP_AUTH_PW or X-Username/X-Password)');
        }

        if (!isset($this->users[$username]) || $this->users[$username] !== $password) {
            throw new \SoapFault('Client', 'Invalid username or password');
        }

        return ['username' => $username, 'password' => $password];
    }

    /**
     * Pozdrav uživatele s HTTP autentizací
     * @param string $name Jméno pro pozdrav
     * @return string Pozdrav
     */
    public function sayHello($name)
    {
        $user = $this->authenticate();

        if (empty($name)) {
            $name = "World";
        }

        return "Hello, " . htmlspecialchars($name) . "! (Authenticated as: " . htmlspecialchars($user['username']) . ")";
    }

    /**
     * Získá aktuální čas s HTTP autentizací
     * @return string Aktuální čas
     */
    public function getCurrentTime()
    {
        $user = $this->authenticate();

        return date('Y-m-d H:i:s') . " (User: " . htmlspecialchars($user['username']) . ")";
    }

    /**
     * Sčítání dvou čísel s HTTP autentizací
     * @param float $a První číslo
     * @param float $b Druhé číslo
     * @return float Součet
     */
    public function add($a, $b)
    {
        $user = $this->authenticate();

        return (float)$a + (float)$b;
    }

    /**
     * Získá informace o serveru s HTTP autentizací
     * @return array Informace o serveru
     */
    public function getServerInfo()
    {
        $user = $this->authenticate();

        return [
            'server_time' => date('Y-m-d H:i:s'),
            'php_version' => PHP_VERSION,
            'server_name' => $_SERVER['SERVER_NAME'] ?? 'localhost',
            'authenticated_user' => htmlspecialchars($user['username']),
            'authentication_method' => 'HTTP headers'
        ];
    }
}
