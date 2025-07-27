# 🔐 Buenas Prácticas de Seguridad .env

## ✅ Estado Actual

### Implementado Correctamente:
- ✅ `.gitignore` configurado para excluir archivos `.env`
- ✅ `.env.example` como template público
- ✅ `.dockerignore` excluye archivos de entorno
- ✅ Archivos `.env` reales no están en git

## 🛡️ Políticas de Seguridad

### 1. Archivos de Entorno

#### Estructura Recomendada:
```
.env.example          # ✅ Template público (en git)
.env                  # ❌ NUNCA en git
.env.local            # ❌ NUNCA en git  
.env.production       # ❌ NUNCA en git
.env.docker           # ❌ NUNCA en git
```

### 2. Contraseñas Seguras

#### Requisitos Mínimos:
- **Longitud**: Mínimo 16 caracteres
- **Complejidad**: Mayúsculas, minúsculas, números, símbolos
- **Aleatoriedad**: Generadas automáticamente
- **Únicas**: Diferentes para cada servicio

#### ❌ Contraseñas Débiles (NO usar):
```bash
password123
root
admin
123456
app_password
```

#### ✅ Contraseñas Fuertes (ejemplo):
```bash
Kx9#mP2$vL8@qR5!wN3&zB7*cF4^jH6%
```

### 3. Variables Críticas

#### Variables que SIEMPRE deben cambiarse:
- `DB_PASSWORD` / `database.default.password`
- `DB_ROOT_PASSWORD`
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
