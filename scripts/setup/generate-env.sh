#!/bin/bash

# Script para generar archivo .env seguro
# Uso: ./scripts/setup/generate-env.sh

echo "🔐 Generando archivo .env seguro..."

# Función para generar password aleatorio
generate_password() {
    openssl rand -base64 32 | tr -d "=+/" | cut -c1-32
}

# Función para generar clave secreta
generate_secret() {
    openssl rand -hex 32
}

# Copiar template
cp .env.example .env

# Generar passwords seguros
DB_ROOT_PASS=$(generate_password)
DB_APP_PASS=$(generate_password)
APP_SECRET=$(generate_secret)
JWT_SECRET=$(generate_secret)

# Reemplazar valores en .env
sed -i "s/CHANGE_ME_STRONG_PASSWORD/$DB_ROOT_PASS/g" .env
sed -i "s/CHANGE_ME_APP_PASSWORD/$DB_APP_PASS/g" .env
sed -i "s/CHANGE_ME_ROOT_PASSWORD/$DB_ROOT_PASS/g" .env
sed -i "s/CHANGE_ME_32_CHAR_SECRET_KEY/$APP_SECRET/g" .env
sed -i "s/CHANGE_ME_JWT_SECRET_KEY/$JWT_SECRET/g" .env

echo "✅ Archivo .env generado con passwords seguros"
echo "📋 Passwords generados:"
echo "   - DB Root: $DB_ROOT_PASS"
echo "   - DB App: $DB_APP_PASS"
echo ""
echo "⚠️  IMPORTANTE:"
echo "   - Guarda estos passwords en un lugar seguro"
echo "   - NO los compartas en chat/email"
echo "   - El archivo .env NO se subirá a git"
echo ""
echo "🚀 Ahora puedes ejecutar: docker-compose up -d"
