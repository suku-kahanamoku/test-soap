<?php

header('Content-Type: application/json; charset=utf-8');

try {
    // Přihlašovací údaje pro WSDL HTTP autentizaci (bez defaultů = zobrazí chybu)
    $username = $_GET['username'] ?? $_POST['username'] ?? '';
    $password = $_GET['password'] ?? $_POST['password'] ?? '';

    $wsdlPath = __DIR__ . '/../wsdl/hello.wsdl';

    if (!file_exists($wsdlPath)) {
        throw new Exception("WSDL file not found");
    }

    // Vytvoř SOAP klienta s HTTP autentizací v options
    $client = new SoapClient($wsdlPath, [
        'login' => $username,
        'password' => $password,
        'authentication' => SOAP_AUTHENTICATION_BASIC,
        'trace' => true,
        'exceptions' => true
    ]);

    $results = [];

    // Test 1: sayHello s HTTP autentizací (bez parametrů username/password)
    try {
        $greeting = $client->sayHello('SOAP Learner');
        $results['sayHello'] = [
            'success' => true,
            'result' => $greeting,
            'request' => $client->__getLastRequest(),
            'response' => $client->__getLastResponse()
        ];
    } catch (Exception $e) {
        $results['sayHello'] = [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }

    // Test 2: getCurrentTime s HTTP autentizací
    try {
        $time = $client->getCurrentTime();
        $results['getCurrentTime'] = [
            'success' => true,
            'result' => $time,
            'request' => $client->__getLastRequest(),
            'response' => $client->__getLastResponse()
        ];
    } catch (Exception $e) {
        $results['getCurrentTime'] = [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }

    // Test 3: add s HTTP autentizací
    try {
        $sum = $client->add(5.5, 3.2);
        $results['add'] = [
            'success' => true,
            'result' => $sum,
            'input' => ['a' => 5.5, 'b' => 3.2],
            'request' => $client->__getLastRequest(),
            'response' => $client->__getLastResponse()
        ];
    } catch (Exception $e) {
        $results['add'] = [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }

    echo json_encode([
        'success' => true,
        'authenticated_user' => $username,
        'authentication_method' => 'HTTP Authentication (SoapClient options)',
        'wsdl_path' => $wsdlPath,
        'endpoint' => 'http://localhost/php/test-soap/api/hello.php',
        'tests' => $results
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
