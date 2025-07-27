#!/bin/bash

# =============================================================================
# YAGARUETE CAMP - GENERADOR .ENV SIMPLE
# =============================================================================
# Descripción: Genera archivo .env básico para desarrollo
# Uso: ./generate-env.sh
# =============================================================================

echo "� Generando archivo .env para desarrollo..."
echo

if [[ -f ".env" ]]; then
    echo "⚠️  El archivo .env ya existe"
    read -p "¿Sobrescribir? (y/N): " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        echo "Operación cancelada"
        exit 0
    fi
fi

# Crear archivo .env básico
cat > .env << EOF
# =============================================================================
# YAGARUETE CAMP - CONFIGURACIÓN DE DESARROLLO
# =============================================================================
# IMPORTANTE: Este archivo NO se sube a git
# Para producción, crear un .env separado con passwords seguros
# =============================================================================

# Entorno
CI_ENVIRONMENT=development

# Base de datos (Docker - Solo desarrollo)
DB_DATABASE=bd_yagaruete_camp
DB_USERNAME=root
DB_PASSWORD=dev_password_123
DB_HOSTNAME=db
DB_PORT=3306

# URLs
APP_URL=http://localhost:8080

# Redis
REDIS_HOST=redis
REDIS_PORT=6379

# Email (MailHog para desarrollo)
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=
MAIL_PASSWORD=

# Claves de aplicación (generar nuevas para producción)
APP_KEY=dev_key_32_characters_long_abc123
JWT_SECRET=dev_jwt_secret_key_for_tokens_xyz789

# Debug (solo desarrollo)
CI_DEBUG=true

# =============================================================================
# PARA PRODUCCIÓN: Copiar a .env.production.example
# =============================================================================
EOF

echo "✅ Archivo .env creado correctamente"
echo
echo "📋 Configuración básica lista para desarrollo"
echo "🚀 Ejecuta: ./deploy.sh start"
echo
