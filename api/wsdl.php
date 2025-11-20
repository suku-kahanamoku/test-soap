<?php

require_once __DIR__ . '/../lib/services/hello-service.class.php';

use SoapTest\Services\HelloService;

/**
 * Zabezpečený WSDL endpoint - vyžaduje HTTP Basic Auth
 */

// Zkontroluj HTTP Basic autentizaci
$username = $_SERVER['PHP_AUTH_USER'] ?? '';
$password = $_SERVER['PHP_AUTH_PW'] ?? '';

// Testovací uživatelé pro WSDL přístup
$wsdlUsers = [
    'admin' => 'password123',
    'developer' => 'dev123',
    'api' => 'api123'
];

// Pokud nejsou credentials, požadej autentizaci
if (empty($username) || empty($password)) {
    header('WWW-Authenticate: Basic realm="WSDL Access Required"');
    header('HTTP/1.0 401 Unauthorized');
    echo json_encode([
        'error' => 'Authentication required',
        'message' => 'You must provide valid credentials to access WSDL',
        'realm' => 'WSDL Access Required'
    ], JSON_PRETTY_PRINT);
    exit;
}

// Ověř credentials
if (!isset($wsdlUsers[$username]) || $wsdlUsers[$username] !== $password) {
    header('WWW-Authenticate: Basic realm="WSDL Access Required"');
    header('HTTP/1.0 401 Unauthorized');
    echo json_encode([
        'error' => 'Invalid credentials',
        'message' => 'Username or password is incorrect',
        'provided_username' => $username
    ], JSON_PRETTY_PRINT);
    exit;
}

// Autentizace úspěšná - načti a zobraz WSDL
try {
    $wsdlPath = __DIR__ . '/../wsdl/hello.wsdl';

    if (!file_exists($wsdlPath)) {
        throw new Exception("WSDL file not found: $wsdlPath");
    }

    // Přečti WSDL obsah
    $wsdlContent = file_get_contents($wsdlPath);

    if ($wsdlContent === false) {
        throw new Exception("Could not read WSDL file");
    }

    // Nastav správný Content-Type pro XML
    header('Content-Type: application/xml; charset=utf-8');
    header('Content-Disposition: inline; filename="hello.wsdl"');

    // Přidej komentář o autentizaci
    $authComment = "<!-- Accessed by authenticated user: $username at " . date('Y-m-d H:i:s') . " -->\n";

    // Vlož komentář za XML deklaraci
    $wsdlContent = preg_replace(
        '/(<\?xml[^>]+\?>)/',
        '$1' . "\n" . $authComment,
        $wsdlContent
    );

    echo $wsdlContent;
} catch (Exception $e) {
    header('Content-Type: application/json; charset=utf-8');
    header('HTTP/1.0 500 Internal Server Error');
    echo json_encode([
        'error' => 'WSDL Error',
        'message' => $e->getMessage(),
        'authenticated_user' => $username,
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_PRETTY_PRINT);
}
