<?php

require_once __DIR__ . '/../lib/services/hello-service.class.php';
require_once __DIR__ . '/../lib/services/wsdl-hello-service.class.php';

use SoapTest\Services\HelloService;
use SoapTest\Services\WSDLHelloService;

// Zkontroluj, zda jde o SOAP request nebo HTTP GET
if ($_SERVER['REQUEST_METHOD'] === 'GET' || isset($_GET['action'])) {
    // HTTP GET request - vrať JSON
    header('Content-Type: application/json; charset=utf-8');

    try {
        $helloService = new HelloService();
        $action = $_GET['action'] ?? 'info';

        switch ($action) {
            case 'hello':
                $name = $_GET['name'] ?? 'World';
                $result = [
                    'success' => true,
                    'action' => 'hello',
                    'result' => $helloService->sayHello($name)
                ];
                break;

            case 'time':
                $result = [
                    'success' => true,
                    'action' => 'time',
                    'result' => $helloService->getCurrentTime()
                ];
                break;

            case 'add':
                $a = (float)($_GET['a'] ?? 0);
                $b = (float)($_GET['b'] ?? 0);
                $result = [
                    'success' => true,
                    'action' => 'add',
                    'result' => $helloService->add($a, $b),
                    'input' => ['a' => $a, 'b' => $b]
                ];
                break;

            case 'info':
            default:
                $result = [
                    'success' => true,
                    'service' => 'SOAP Test Hello Service',
                    'available_actions' => [
                        'hello' => 'GET ?action=hello&name=YourName',
                        'time' => 'GET ?action=time',
                        'add' => 'GET ?action=add&a=5&b=3',
                        'info' => 'GET ?action=info'
                    ],
                    'soap_endpoint' => 'POST to this URL for SOAP requests (with username/password in WSDL parameters)',
                    'wsdl' => 'http://localhost/php/test-soap/wsdl/hello.wsdl',
                    'server_info' => $helloService->getServerInfo()
                ];
                break;
        }

        echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
} else {
    // SOAP request - vrať XML
    header('Content-Type: text/xml; charset=utf-8');

    try {
        // Pro WSDL requests vždy požaduj HTTP autentizaci
        $wsdlPath = __DIR__ . '/../wsdl/hello.wsdl';

        if (!file_exists($wsdlPath)) {
            throw new Exception("WSDL file not found: $wsdlPath");
        }

        $server = new SoapServer($wsdlPath);
        $wsdlHelloService = new WSDLHelloService();
        $server->setObject($wsdlHelloService);

        // Zpracuj SOAP request
        $server->handle();
    } catch (Exception $e) {
        // V případě chyby vrať SOAP Fault
        $server = new SoapServer(null, ['uri' => 'http://localhost/test-soap/hello.php']);
        $server->fault('Server', 'SOAP Service Error: ' . $e->getMessage());
    }
}
