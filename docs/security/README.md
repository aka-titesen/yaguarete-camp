# 🔒 Seguridad - Yagaruete Camp

> Guía completa de configuración de seguridad para desarrollo y producción

## 🛡️ Características de Seguridad Implementadas

### Autenticación y Autorización

- ✅ **Password Hashing** con bcrypt (cost factor 12)
- ✅ **Session Management** seguro con httpOnly cookies
- ✅ **Role-based Access Control** (Admin/Customer)
- ✅ **Login Throttling** para prevenir ataques de fuerza bruta
- ✅ **Secure Session Storage** con Redis/File handler

### Protección de Datos

- ✅ **SQL Injection Prevention** con prepared statements
- ✅ **XSS Protection** con output escaping automático
- ✅ **CSRF Protection** en todos los formularios
- ✅ **Input Validation** estricta en todos los endpoints
- ✅ **File Upload Security** con validación de tipos

### Configuración Segura

- ✅ **Environment Variables** para datos sensibles
- ✅ **Database User** con permisos mínimos necesarios
- ✅ **Secure Headers** configurados en Apache
- ✅ **Error Handling** sin exposición de datos sensibles

## 🔐 Configuración de Autenticación

### Password Hashing

```php
// app/Models/UserModel.php
public function hashPassword(string $password): string
{
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

public function verifyPassword(string $password, string $hash): bool
{
    return password_verify($password, $hash);
}
```

### Session Configuration

```php
// app/Config/App.php
public string $sessionDriver = 'CodeIgniter\Session\Handlers\RedisHandler';
public string $sessionCookieName = 'yaguarete_session';
public int $sessionExpiration = 7200; // 2 horas
public bool $sessionMatchIP = false;
public bool $sessionTimeToUpdate = 300;
public bool $sessionRegenerateDestroy = false;

// Configuración de cookies seguras
public bool $cookieSecure = false;   // true en producción con HTTPS
public bool $cookieHTTPOnly = true;
public string $cookieSameSite = 'Lax';
```

### Role-based Access Control

```php
// app/Filters/AuthFilter.php
class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Debes iniciar sesión');
        }

        // Verificar rol si es necesario
        if ($arguments && !empty($arguments)) {
            $requiredRole = $arguments[0];
            $userRole = session()->get('userRole');

            if ($userRole !== $requiredRole && $userRole !== 'admin') {
                throw new \CodeIgniter\Exceptions\PageNotFoundException();
            }
        }
    }
}
```

## 🚫 Protección Contra Ataques

### SQL Injection Prevention

```php
// ✅ CORRECTO - Usando Query Builder
$products = $this->db->table('products')
                    ->where('category_id', $categoryId)
                    ->where('status', 'active')
                    ->get()->getResultArray();

// ✅ CORRECTO - Prepared Statements
$query = $this->db->query("
    SELECT * FROM products
    WHERE category_id = ? AND status = ?
    ORDER BY created_at DESC
", [$categoryId, 'active']);

// ❌ INCORRECTO - Concatenación directa
// $query = "SELECT * FROM products WHERE id = " . $id; // ¡NUNCA!
```

### XSS Protection

```php
// En vistas - Escape automático habilitado
<?= esc($userInput) ?>           // Escape HTML
<?= esc($userInput, 'attr') ?>   // Escape para atributos
<?= esc($userInput, 'js') ?>     // Escape para JavaScript

// Configuración global
// app/Config/View.php
public array $filters = [
    'esc' => 'CodeIgniter\Filters\DebugToolbar',
];
```

### CSRF Protection

```php
// app/Config/Filters.php
public array $globals = [
    'before' => [
        'csrf' => ['except' => ['api/*']], // Excepto APIs
    ],
];

// En formularios
<?= csrf_field() ?>

// Verificación manual
if (!$this->request->is('post') || !$this->validate(['csrf_token' => 'required'])) {
    throw new \RuntimeException('CSRF token mismatch');
}
```

### File Upload Security

```php
// app/Config/Upload.php
public array $fileTypeConfig = [
    'images' => [
        'upload_path' => WRITEPATH . 'uploads/images/',
        'allowed_types' => 'jpg|jpeg|png|gif|webp',
        'max_size' => 5120,      // 5MB
        'max_width' => 2048,
        'max_height' => 2048,
        'encrypt_name' => true,   // Nombres aleatorios
    ],
    'documents' => [
        'upload_path' => WRITEPATH . 'uploads/docs/',
        'allowed_types' => 'pdf|doc|docx',
        'max_size' => 10240,     // 10MB
        'encrypt_name' => true,
    ]
];

// Validación adicional
public function validateUpload($file)
{
    // Verificar tipo MIME real
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file->getTempName());

    $allowedMimes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($mimeType, $allowedMimes)) {
        throw new \RuntimeException('Tipo de archivo no permitido');
    }

    return true;
}
```

## 🔑 Configuración de Variables de Entorno

### Archivo .env Seguro

```bash
# === SEGURIDAD ===
# Generar con: php spark key:generate
app.encryptionKey = tu-clave-de-32-caracteres-aqui

# Configuración de sesiones
app.sessionDriver = CodeIgniter\Session\Handlers\RedisHandler
app.sessionCookieName = yaguarete_session
app.sessionExpiration = 7200
app.sessionSavePath = tcp://redis:6379

# CSRF Protection
app.CSRFProtection = true
app.CSRFTokenName = csrf_token
app.CSRFHeaderName = X-CSRF-TOKEN
app.CSRFExpire = 7200

# Security Headers
app.forceGlobalSecureRequests = false  # true en producción
app.CSPEnabled = false                 # habilitar en producción

# === BASE DE DATOS ===
database.default.hostname = db
database.default.database = yaguarete_camp
database.default.username = yaguarete_user
database.default.password = secure_password_123
database.default.DBDriver = MySQLi
database.default.DBPrefix =
database.default.pConnect = false
database.default.DBDebug = true      # false en producción
database.default.charset = utf8mb4
database.default.DBCollat = utf8mb4_general_ci

# === REDIS ===
REDIS_HOST = redis
REDIS_PORT = 6379
REDIS_PASSWORD =
REDIS_DATABASE = 0

# === EMAIL (DESARROLLO) ===
email.protocol = smtp
email.SMTPHost = mailhog
email.SMTPPort = 1025
email.SMTPUser =
email.SMTPPass =
email.SMTPCrypto =

# === LOGGING ===
CI_LOG_THRESHOLD = 4    # 1=ERROR, 2=WARNING, 3=INFO, 4=DEBUG
```

### Variables de Producción

```bash
# .env.production
CI_ENVIRONMENT = production
CI_DEBUG = false

# Seguridad mejorada
app.forceGlobalSecureRequests = true
app.sessionCookieSecure = true
app.CSRFProtection = true
app.CSPEnabled = true

# Logging reducido
CI_LOG_THRESHOLD = 1  # Solo errores críticos

# Base de datos con SSL
database.default.hostname = tu-servidor-db.com
database.default.username = yaguarete_prod
database.default.password = password-super-seguro-aqui
database.default.DBDebug = false
database.default.encrypt = true

# Email con SSL
email.protocol = smtp
email.SMTPHost = smtp.tuproveedor.com
email.SMTPPort = 587
email.SMTPUser = noreply@tudominio.com
email.SMTPPass = password-email-seguro
email.SMTPCrypto = tls
```

## 🌐 Configuración de Apache Segura

### Security Headers

```apache
# docker/apache/vhosts.conf
<VirtualHost *:80>
    ServerName localhost
    DocumentRoot /var/www/html/public

    # Security Headers
    Header always set X-Frame-Options "SAMEORIGIN"
    Header always set X-Content-Type-Options "nosniff"
    Header always set X-XSS-Protection "1; mode=block"
    Header always set Referrer-Policy "no-referrer-when-downgrade"
    Header always set Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:;"

    # Ocultar información del servidor
    ServerTokens Prod
    ServerSignature Off

    # Protección de directorios sensibles
        limit_req zone=login burst=5 nodelay;
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Denegar acceso a archivos sensibles
    location ~ /\. {
        deny all;
        access_log off;
        log_not_found off;
    }

    location ~ \.(htaccess|htpasswd|ini|log|sh|inc|bak)$ {
        deny all;
    }

    # Proteger directorio de uploads
    location /writable/uploads {
        location ~ \.(php|php5|phtml|pl|py|jsp|asp|sh|cgi)$ {
            deny all;
        }
    }
}

# Rate limiting
http {
    limit_req_zone $binary_remote_addr zone=login:10m rate=1r/s;
    limit_req_zone $binary_remote_addr zone=api:10m rate=10r/s;
}
```

### SSL/TLS Configuration (Producción)

```apache
# Configuración HTTPS para producción
<VirtualHost *:443>
    ServerName tudominio.com
    DocumentRoot /var/www/html/public

    # Habilitar SSL
    SSLEngine on
    SSLCertificateFile /etc/apache2/ssl/cert.pem
    SSLCertificateKeyFile /etc/apache2/ssl/key.pem

    # Configuración SSL moderna
    SSLProtocol all -SSLv3 -TLSv1 -TLSv1.1
    SSLCipherSuite ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512:ECDHE-RSA-AES256-GCM-SHA384:DHE-RSA-AES256-GCM-SHA384
    SSLHonorCipherOrder off
    SSLSessionCache shmcb:/var/cache/apache2/ssl_gcache_data(512000)
    SSLSessionTickets off

    # HSTS (HTTP Strict Transport Security)
    Header always set Strict-Transport-Security "max-age=63072000; includeSubDomains; preload"

    # Incluir configuración PHP-FPM
    ProxyPassMatch ^/(.*\.php(/.*)?)$ fcgi://app:9000/var/www/html/public/$1
    DirectoryIndex index.php index.html
</VirtualHost>

# Redirección HTTP a HTTPS
<VirtualHost *:80>
    ServerName tudominio.com
    Redirect permanent / https://tudominio.com/
</VirtualHost>
```

## 🔍 Auditoría y Logging

### Configuración de Logs

```php
// app/Config/Logger.php
public array $handlers = [
    'file' => [
        'class' => 'CodeIgniter\Log\Handlers\FileHandler',
        'path' => WRITEPATH . 'logs/',
        'level' => 'debug',
        'dateFormat' => 'Y-m-d H:i:s',
        'filename' => 'log-{date}.log',
        'permissions' => 0644,
    ],
    'security' => [
        'class' => 'CodeIgniter\Log\Handlers\FileHandler',
        'path' => WRITEPATH . 'logs/',
        'level' => 'warning',
        'filename' => 'security-{date}.log',
        'permissions' => 0644,
    ],
];
```

### Eventos de Seguridad a Monitorear

```php
// app/Controllers/BaseController.php
protected function logSecurityEvent(string $event, array $data = [])
{
    log_message('warning', "SECURITY: {$event}", [
        'user_id' => session()->get('userId'),
        'ip' => $this->request->getIPAddress(),
        'user_agent' => $this->request->getUserAgent()->getAgentString(),
        'timestamp' => date('Y-m-d H:i:s'),
        'data' => $data
    ]);
}

// Ejemplos de uso:
// Login fallido
$this->logSecurityEvent('LOGIN_FAILED', ['email' => $email]);

// Acceso denegado
$this->logSecurityEvent('ACCESS_DENIED', ['route' => $route]);

// Cambio de password
$this->logSecurityEvent('PASSWORD_CHANGED', ['user_id' => $userId]);
```

## 🛠️ Herramientas de Seguridad

### Validación de Entrada

```php
// app/Config/Validation.php
public array $ruleSets = [
    \CodeIgniter\Validation\Rules::class,
    \CodeIgniter\Validation\FormatRules::class,
    \CodeIgniter\Validation\FileRules::class,
    \CodeIgniter\Validation\CreditCardRules::class,
];

// Reglas personalizadas
public array $customRules = [
    'strong_password' => 'App\Validation\CustomRules::strongPassword',
    'safe_html' => 'App\Validation\CustomRules::safeHtml',
];

// app/Validation/CustomRules.php
public function strongPassword(string $str, string $fields, array $data): bool
{
    // Mínimo 8 caracteres, al menos 1 mayúscula, 1 minúscula, 1 número
    return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d@$!%*?&]{8,}$/', $str);
}
```

### Sanitización de Datos

```php
// app/Helpers/SecurityHelper.php
function sanitizeInput(string $input): string
{
    // Remover tags HTML peligrosos
    $input = strip_tags($input, '<p><br><strong><em><ul><ol><li>');

    // Escapar caracteres especiales
    $input = htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');

    // Remover caracteres de control
    $input = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $input);

    return trim($input);
}

function sanitizeFilename(string $filename): string
{
    // Remover caracteres peligrosos
    $filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);

    // Prevenir nombres especiales
    $dangerous = ['..', '/', '\\', ':', '*', '?', '"', '<', '>', '|'];
    $filename = str_replace($dangerous, '', $filename);

    return $filename;
}
```

## 📋 Checklist de Seguridad

### Desarrollo ✅

- [x] Variables de entorno configuradas
- [x] CSRF protection habilitado
- [x] Input validation en todos los formularios
- [x] Output escaping en todas las vistas
- [x] Password hashing implementado
- [x] Session security configurado
- [x] SQL injection prevention
- [x] File upload validation

### Pre-Producción 🔄

- [ ] Cambiar todas las passwords por defecto
- [ ] Configurar HTTPS/SSL
- [ ] Habilitar security headers
- [ ] Configurar rate limiting
- [ ] Disable debug mode
- [ ] Configurar logging de seguridad
- [ ] Auditoría de dependencias
- [ ] Backup strategy implementada

### Producción 🔒

- [ ] SSL/TLS certificates instalados
- [ ] Firewall configurado
- [ ] Monitoring de seguridad activo
- [ ] Backup automatizado
- [ ] Incident response plan
- [ ] Regular security updates
- [ ] Penetration testing realizado
- [ ] Security training para el equipo

---

**Yagaruete Camp Security** - Configuración segura para desarrollo y producción
_Documentación actualizada: 28 de Julio, 2025_ 🔒🛡️
