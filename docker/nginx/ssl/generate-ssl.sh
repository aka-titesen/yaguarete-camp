# SSL Certificate Generation Script
# Generar certificados SSL auto-firmados para desarrollo local

# Crear directorio si no existe
mkdir -p ssl

# Generar clave privada
openssl genrsa -out ssl/localhost.key 2048

# Generar certificado auto-firmado
openssl req -new -x509 -key ssl/localhost.key -out ssl/localhost.crt -days 365 -subj "/C=MX/ST=State/L=City/O=Martinez Gonzalez/OU=IT/CN=localhost"

echo "Certificados SSL generados en docker/nginx/ssl/"
echo "- localhost.key (clave privada)"
echo "- localhost.crt (certificado)"
