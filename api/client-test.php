<?php

require_once __DIR__ . '/../lib/services/hello-service.class.php';

header('Content-Type: application/json; charset=utf-8');

use SoapTest\Services\HelloService;

try {
    // Pevně nastavené credentials pro SOAP test s autentizací  
    $username = 'test';
    $password = 'test123';

    $wsdlPath = __DIR__ . '/../wsdl/hello.wsdl';

    if (!file_exists($wsdlPath)) {
        throw new Exception("WSDL file not found");
    }

    // Vytvoř SOAP klienta s HTTP autentizací
    $client = new SoapClient($wsdlPath, [
        'login' => $username,
        'password' => $password,
        'authentication' => SOAP_AUTHENTICATION_BASIC,
        'trace' => true,
        'exceptions' => true
    ]);

    $results = [];

    // Test 1: sayHello s HTTP autentizací
    try {
        $greeting = $client->sayHello('SOAP Learner with Auth');
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
        $sum = $client->add(12.5, 7.3);
        $results['add'] = [
            'success' => true,
            'result' => $sum,
            'input' => ['a' => 12.5, 'b' => 7.3],
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
        'test_type' => 'SOAP with predefined credentials',
        'authenticated_user' => $username,
        'authentication_method' => 'HTTP Authentication (predefined credentials)',
        'wsdl_path' => $wsdlPath,
        'endpoint' => 'http://localhost:8080/api/hello.php',
        'note' => 'This test uses predefined credentials (test/test123)',
        'tests' => $results
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
