# SOAP Test Module

Jednoduchý SOAP test modul pro učení SOAP a WSDL v PHP s autentizací.

## Struktura

```
test-soap/
├── composer.json          # Composer konfigurace
├── README.md              # Dokumentace
├── api/                   # API endpointy
│   ├── hello.php         # SOAP server endpoint (s autentizací)
│   ├── auth.php          # Authentication API
│   ├── test.php          # Test WSDL a připojení
│   └── client-test.php   # Test SOAP klienta
├── lib/                   # Knihovny
│   ├── test-soap.class.php
│   └── services/
│       ├── service-manager.class.php
│       ├── hello-service.class.php
│       ├── soap-service.class.php
│       └── auth-service.class.php
└── wsdl/                  # WSDL soubory
    └── hello.wsdl        # Základní Hello World WSDL
```

## Instalace

1. Přejděte do složky modulu:
```bash
cd test-soap
```

2. Nainstalujte závislosti:
```bash
composer install
```

## 🔐 Autentizace

### Test uživatelé:
- `admin` / `password123`
- `user` / `user123` 
- `test` / `test123`
- `soap` / `soap123`

### Postup přihlášení:

1. **Přihlášení**: 
```
POST http://localhost/php/test-soap/api/auth.php
Content-Type: application/x-www-form-urlencoded

action=login&username=admin&password=password123
```

2. **Použití tokenu**:
```
GET http://localhost/php/test-soap/api/hello.php?action=hello&name=Test&token=YOUR_TOKEN
```

## Použití

### 1. Authentication API
```
http://localhost/php/test-soap/api/auth.php
```

**Dostupné akce:**
- `?action=login` - Přihlášení (POST username & password)
- `?action=validate&token=TOKEN` - Ověření tokenu
- `?action=logout&token=TOKEN` - Odhlášení
- `?action=users` - Seznam uživatelů
- `?action=sessions` - Aktivní session

### 2. Hello Service (s autentizací)
```
http://localhost/php/test-soap/api/hello.php
```

**Veřejné akce:**
- `?action=info` - Informace o službě
- `?action=auth-info` - Informace o autentizaci

**Chráněné akce (vyžadují token):**
- `?action=hello&name=Jmeno&token=TOKEN` - Pozdrav
- `?action=time&token=TOKEN` - Aktuální čas
- `?action=add&a=5&b=3&token=TOKEN` - Sčítání

### 3. Test endpointy
- **Test WSDL**: `http://localhost/php/test-soap/api/test.php`
- **SOAP klient**: `http://localhost/php/test-soap/api/client-test.php`

## 🔑 Příklad použití s autentizací

### 1. Přihlášení
```bash
curl -X POST http://localhost/php/test-soap/api/auth.php \
     -d "action=login&username=admin&password=password123"
```

**Odpověď:**
```json
{
    "success": true,
    "message": "Login successful",
    "username": "admin",
    "token": "abc123def456..."
}
```

### 2. Volání chráněné operace
```bash
curl "http://localhost/php/test-soap/api/hello.php?action=hello&name=World&token=abc123def456"
```

**Odpověď:**
```json
{
    "success": true,
    "action": "hello",
    "result": "Hello, World!",
    "user": "admin"
}
```

## Dostupné SOAP operace

### sayHello
- **Input**: string name
- **Output**: string greeting
- **Auth**: Ne (SOAP requests jsou bez autentizace)

### getCurrentTime  
- **Input**: žádný
- **Output**: string current_time
- **Auth**: Ne

## Učení SOAP/WSDL s autentizací

1. **Autentizace** - začněte s `auth.php` API
2. **WSDL struktura** - prohlédněte si `wsdl/hello.wsdl`
3. **Chráněné REST API** - `hello.php` s token autentizací
4. **SOAP Server** - SOAP requesty v `hello.php` (bez autentizace)
5. **Service implementace** - business logika v services/
