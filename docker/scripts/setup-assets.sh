#!/bin/bash

# Script para mover assets al directorio public
# Este script se ejecuta al iniciar el contenedor

echo "Configurando assets en public..."

# Verificar si assets está en el directorio raíz
if [ -d "/var/www/html/assets" ]; then
    echo "Assets encontrados en directorio raíz"
    
    # Eliminar directorio público assets si existe y está vacío
    if [ -d "/var/www/html/public/assets" ]; then
        echo "Eliminando directorio público assets existente"
        rm -rf /var/www/html/public/assets
    fi
    
    # Mover assets al directorio public
    echo "Moviendo assets a public/assets..."
    mv /var/www/html/assets /var/www/html/public/assets
    
    echo "✅ Assets movidos correctamente a public/assets"
elif [ -d "/var/www/html/public/assets" ]; then
    echo "✅ Assets ya están en public/assets"
else
    echo "⚠️ No se encontraron assets"
fi

# Verificar que los assets CSS son accesibles
if [ -d "/var/www/html/public/assets/css" ]; then
    echo "✅ Assets CSS accesibles correctamente"
    echo "📁 Archivos CSS encontrados:"
    ls -la /var/www/html/public/assets/css/*.css | head -3
else
    echo "❌ Error: Assets CSS no accesibles"
fi

echo "Configuración de assets completada"
