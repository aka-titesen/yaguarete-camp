# Configuración Apache para Yaguarete Camp

Este proyecto utiliza Apache como servidor web en lugar de Nginx, manteniendo la tradicional integración con PHP que es muy común en proyectos PHP.

## Estructura de Archivos

```
docker/apache/
├── vhosts.conf      # Virtual Host para HTTP
├── Dockerfile       # Dockerfile personalizado para Apache
└── README.md        # Esta documentación
```

## Características

### Seguridad

- Headers de seguridad configurados (X-Frame-Options, X-XSS-Protection, etc.)
- Acceso denegado a directorios sensibles (`app/`, `system/`, `writable/`)
- Acceso denegado a archivos sensibles (`.htaccess`, `.env`, etc.)
- ServerTokens y ServerSignature deshabilitados

### Rendimiento

- Compresión deflate habilitada para archivos estáticos
- Cache headers configurados para archivos estáticos
- Módulos de rendimiento habilitados (deflate, expires)

### Integración con PHP

- Configurado para trabajar con PHP-FPM
- Proxy configurado hacia el contenedor `app:9000`
- Soporte completo para CodeIgniter 4

## URLs de Acceso

- **HTTP**: http://localhost:8080

## Comandos Docker

### Desarrollo

```bash
docker-compose up -d
```

### Producción

```bash
docker-compose -f docker-compose.prod.yml up -d
```

## Archivos de Log

Los logs de Apache se guardan en:

- Error log: `/var/log/apache2/yagaruete_camp_error.log`
- Access log: `/var/log/apache2/yagaruete_camp_access.log`

## Configuración .htaccess

El archivo `public/.htaccess` incluye:

- Reglas de reescritura para CodeIgniter
- Headers de seguridad
- Compresión de archivos
- Cache para archivos estáticos
- Protección de directorios sensibles

## Migración desde Nginx

Este proyecto fue migrado desde Nginx a Apache manteniendo:

- El mismo puerto HTTP (8080)
- La misma integración con PHP-FPM
- Las mismas reglas de seguridad
- El mismo nivel de rendimiento

## Personalización

Para personalizar la configuración:

1. **Modificar Virtual Host**: Editar `docker/apache/vhosts.conf`
2. **Agregar módulos**: Modificar `docker/apache/Dockerfile`
3. **Cambiar reglas de reescritura**: Editar `public/.htaccess`

Después de cualquier cambio, reconstruir el contenedor:

```bash
docker-compose down
docker-compose up --build -d
```

## Agregar HTTPS (Futuro)

Para agregar soporte SSL/HTTPS:

1. Generar certificados SSL
2. Habilitar módulo SSL en Dockerfile
3. Agregar VirtualHost para puerto 443 en vhosts.conf
4. Configurar puertos en docker-compose.yml
