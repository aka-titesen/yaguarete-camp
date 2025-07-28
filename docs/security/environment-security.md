# 🔐 Buenas Prácticas de Seguridad .env

## ✅ Estado Actual

### Implementado Correctamente:

- ✅ `.gitignore` configurado para excluir archivos `.env`
- ✅ `.env.example` y `.env.production.example` como templates públicos
- ✅ Scripts generan `.env` automáticamente para desarrollo
- ✅ Variables de entorno centralizadas en Docker Compose
- ✅ Separación clara entre desarrollo y producción

## 🛡️ Políticas de Seguridad

### 1. Archivos de Entorno

#### Estructura Actual:

```
.env.example              # ✅ Template básico (en git)
.env.production.example   # ✅ Template de producción (en git)
.env                      # ❌ NUNCA en git (generado por script)
```

#### Variables de Desarrollo vs Producción:

| Variable         | Desarrollo              | Producción            |
| ---------------- | ----------------------- | --------------------- |
| `CI_ENVIRONMENT` | `development`           | `production`          |
| `DB_PASSWORD`    | `dev_password_123`      | Password seguro       |
| `CI_DEBUG`       | `true`                  | `false`               |
| `APP_URL`        | `http://localhost:8080` | `https://dominio.com` |
| `MAIL_HOST`      | `mailhog`               | SMTP real             |

### 2. Contraseñas Seguras

#### ❌ Contraseñas de Desarrollo (OK para desarrollo):

```bash
dev_password_123          # Simple, identificable como desarrollo
root                      # Simple para desarrollo local
```

#### ✅ Contraseñas de Producción (ejemplo):

```bash
SuperSeguroPassword123!   # Complejo, aleatorio
Kx9#mP2$vL8@qR5!wN3&     # Generado automáticamente
```

#### Herramientas para generar passwords seguros:

```bash
# Generar password seguro
openssl rand -base64 24

# Generar clave de aplicación
openssl rand -hex 32

# Generar JWT secret
openssl rand -base64 64
```

### 3. Variables Críticas

#### Variables que SIEMPRE deben cambiarse en producción:

- `DB_PASSWORD` - Password de base de datos
- `APP_KEY` - Clave de cifrado de la aplicación
- `JWT_SECRET` - Clave para tokens JWT
- `MAIL_PASSWORD` - Password del servidor de email

#### Flujo de configuración:

**Para desarrollo:**

```bash
# Automático - El script genera todo
./deploy.sh start
```

**Para producción:**

```bash
# 1. Copiar template
cp .env.production.example .env

# 2. Generar passwords seguros
openssl rand -base64 24  # Para DB_PASSWORD
openssl rand -hex 32     # Para APP_KEY
openssl rand -base64 64  # Para JWT_SECRET

# 3. Editar .env con los valores generados
nano .env

# 4. Desplegar
docker-compose -f docker-compose.prod.yml up -d
```

## 🔄 Migración Desarrollo → Producción

### Checklist de Seguridad:

- [ ] Copiar `.env.production.example` a `.env`
- [ ] Cambiar `CI_ENVIRONMENT=production`
- [ ] Generar password seguro para `DB_PASSWORD`
- [ ] Generar nueva `APP_KEY`
- [ ] Generar nuevo `JWT_SECRET`
- [ ] Configurar SMTP real en `MAIL_HOST`
- [ ] Cambiar `CI_DEBUG=false`
- [ ] Configurar dominio real en `APP_URL`
- [ ] Usar `docker-compose.prod.yml`
- [ ] NO exponer puertos de BD (3306)
- `APP_SECRET_KEY` / `encryption.key`
- `JWT_SECRET`
- `MAIL_PASSWORD`

### 4. Configuración por Entorno

#### Desarrollo Local (.env):

```bash
CI_ENVIRONMENT=development
DEBUG_MODE=true
LOG_LEVEL=debug
```

#### Producción (.env.production):

```bash
CI_ENVIRONMENT=production
DEBUG_MODE=false
LOG_LEVEL=error
```

## 🚀 Scripts de Generación

### Linux/Mac:

```bash
./scripts/setup/generate-env.sh
```

### Windows:

```batch
scripts\setup\generate-env.bat
```

## ⚠️ Reglas de Oro

1. **NUNCA** hardcodear credenciales en código
2. **NUNCA** commitear archivos `.env` reales
3. **SIEMPRE** usar `.env.example` como template
4. **SIEMPRE** generar passwords únicos y fuertes
5. **ROTAR** passwords periódicamente en producción

## 🔍 Verificación de Seguridad

### Comando para verificar que .env no está en git:

```bash
git ls-files | grep ".env$"
# No debe retornar nada
```

### Verificar .gitignore:

```bash
git check-ignore .env
# Debe retornar: .env
```

## 📚 Recursos Adicionales

- [OWASP Environment Variables Guide](https://owasp.org/www-community/vulnerabilities/Insecure_Storage_of_Sensitive_Information)
- [12 Factor App Config](https://12factor.net/config)
- [Security Best Practices](https://security.secure.org/practices/environment-variables)
