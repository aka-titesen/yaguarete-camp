# ⚡ Quick Start - Yagaruete Camp

> Guía ultra-rápida para desarrolladores que quieren empezar inmediatamente

## 🚀 Setup en 60 segundos

### ✅ Prerequisitos

- Docker y Docker Compose instalados
- Git
- Terminal/PowerShell

### 🏃‍♂️ Pasos

```bash
# 1. Clonar proyecto
git clone https://github.com/aka-titesen/yaguarete-camp.git
cd yaguarete-camp

# 2. Configurar entorno
cp .env.example .env

# 3. Levantar todo (con optimizaciones incluidas)
docker-compose up -d --build

# 4. Esperar 30 segundos y configurar BD
docker-compose exec app php spark migrate
docker-compose exec app php spark db:seed DatabaseSeeder
```

**¡Listo!** 🎉

## 🌐 Accesos

| Servicio        | URL                   | Credenciales              |
| --------------- | --------------------- | ------------------------- |
| **🏠 App**      | http://localhost:8080 | admin@test.com / admin123 |
| **🗄️ DB Admin** | http://localhost:8081 | root / dev_password_123   |
| **📧 Email**    | http://localhost:8025 | -                         |

## 🛠️ Comandos Esenciales

```bash
# Ver estado
docker-compose ps

# Ver logs
docker-compose logs -f app

# Reiniciar
docker-compose restart

# Parar todo
docker-compose down

# Reset completo
docker-compose down -v && docker-compose up -d --build
```

## 🚀 Performance incluido

Este setup incluye automáticamente:

- ⚡ **OPcache** habilitado (60-80% más rápido)
- 🔴 **Redis Cache** activo
- 🗄️ **MySQL Query Cache** optimizado
- 🌐 **Apache mod_rewrite** con PHP-FPM proxy

## 🆘 ¿Problemas?

1. **Contenedores no inician**: `docker-compose logs`
2. **Error BD**: Esperar 30s más y repetir migraciones
3. **Puerto ocupado**: Cambiar puertos en docker-compose.yml
4. **Permisos**: `chmod -R 777 writable/` (Linux/Mac)

## 📚 Siguiente paso

Lee la **[Documentación Completa](docs/README.md)** para configuración avanzada y optimizaciones.

---

**¡Feliz coding!** 🏕️⚡
