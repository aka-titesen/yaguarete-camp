# 🔌 Sprint 2: API Backend y Servicios Base

**Duración**: 2 semanas  
**Fecha inicio**: [Definir]  
**Fecha fin**: [Definir]  
**Responsable**: [Asignar]  
**Dependencias**: Sprint 1 completado

---

## 🎯 Objetivos del Sprint

1. Crear endpoints REST en CodeIgniter 4 para todas las entidades principales
2. Implementar sistema de autenticación JWT robusto
3. Configurar servicios frontend para consumir API
4. Establecer React Query para manejo de estado servidor
5. Crear custom hooks para operaciones comunes

---

## 📋 Backlog del Sprint

### 🔧 **Tareas Backend - CodeIgniter (Estimación: 32h)**

#### 1. Configuración Base API (8h)

- [ ] **1.1** Crear estructura de carpetas para API:
  ```
  app/Controllers/API/
  ├── BaseApiController.php
  ├── ProductosController.php
  ├── CategoriasController.php
  ├── UsuariosController.php
  ├── VentasController.php
  └── AuthController.php
  ```
- [ ] **1.2** Crear `BaseApiController` con métodos comunes
- [ ] **1.3** Configurar CORS en `app/Config/Cors.php`
- [ ] **1.4** Configurar rutas API en `app/Config/Routes.php`
- [ ] **1.5** Crear middleware para validación API

#### 2. Autenticación JWT (10h)

- [ ] **2.1** Instalar Firebase JWT library:
  ```bash
  composer require firebase/php-jwt
  ```
- [ ] **2.2** Crear `AuthController` con endpoints:
  - `POST /api/auth/login`
  - `POST /api/auth/registro`
  - `POST /api/auth/refresh`
  - `POST /api/auth/logout`
- [ ] **2.3** Crear middleware JWT para proteger rutas
- [ ] **2.4** Implementar refresh token mechanism
- [ ] **2.5** Configurar tiempo de expiración y claves secretas

#### 3. Endpoints de Productos (8h)

- [ ] **3.1** `ProductosController` con endpoints:
  ```php
  GET    /api/productos              # Listar con filtros
  GET    /api/productos/{id}         # Detalle específico
  GET    /api/productos/destacados   # Productos destacados
  GET    /api/productos/relacionados/{id} # Productos relacionados
  GET    /api/productos/buscar       # Búsqueda con query
  ```
- [ ] **3.2** Implementar paginación estándar
- [ ] **3.3** Agregar filtros por categoría, precio, estado
- [ ] **3.4** Optimizar queries para performance
- [ ] **3.5** Agregar validación de parámetros

#### 4. Endpoints de Categorías (3h)

- [ ] **4.1** `CategoriasController` con endpoints:
  ```php
  GET /api/categorias              # Listar todas
  GET /api/categorias/{id}         # Detalle categoría
  GET /api/categorias/{id}/productos # Productos de categoría
  ```
- [ ] **4.2** Incluir subcategorías en response
- [ ] **4.3** Contar productos por categoría

#### 5. Endpoints de Usuario (3h)

- [ ] **5.1** `UsuariosController` con endpoints:
  ```php
  GET    /api/usuario/perfil       # Perfil del usuario logueado
  PUT    /api/usuario/perfil       # Actualizar perfil
  GET    /api/usuario/pedidos      # Historial de pedidos
  POST   /api/usuario/direccion    # Agregar dirección
  ```
- [ ] **5.2** Proteger endpoints con middleware JWT
- [ ] **5.3** Validar permisos de usuario

---

### 🔧 **Tareas Frontend - React (Estimación: 28h)**

#### 6. Configuración de Axios (6h)

- [ ] **6.1** Crear instancia de Axios configurada:
  ```javascript
  // services/api.js
  const api = axios.create({
    baseURL: import.meta.env.VITE_API_URL,
    timeout: 10000,
    headers: {
      "Content-Type": "application/json",
    },
  });
  ```
- [ ] **6.2** Configurar interceptores de request:
  - Agregar token JWT automáticamente
  - Logging de requests en desarrollo
- [ ] **6.3** Configurar interceptores de response:
  - Manejo global de errores
  - Refresh automático de tokens
  - Normalización de responses
- [ ] **6.4** Crear tipos de error personalizados
- [ ] **6.5** Configurar retry automático para fallos temporales

#### 7. Servicios de API (10h)

- [ ] **7.1** Crear `authService.js`:
  ```javascript
  export const authService = {
    login: (credentials) => api.post("/auth/login", credentials),
    register: (userData) => api.post("/auth/registro", userData),
    logout: () => api.post("/auth/logout"),
    refreshToken: () => api.post("/auth/refresh"),
    getProfile: () => api.get("/usuario/perfil"),
  };
  ```
- [ ] **7.2** Crear `productosService.js`:
  ```javascript
  export const productosService = {
    getAll: (params) => api.get("/productos", { params }),
    getById: (id) => api.get(`/productos/${id}`),
    getDestacados: () => api.get("/productos/destacados"),
    search: (query) => api.get("/productos/buscar", { params: { q: query } }),
  };
  ```
- [ ] **7.3** Crear `categoriasService.js`
- [ ] **7.4** Crear `usuarioService.js`
- [ ] **7.5** Agregar TypeScript types para responses (opcional)

#### 8. Configuración React Query (6h)

- [ ] **8.1** Configurar QueryClient en `main.jsx`:

  ```javascript
  import { QueryClient, QueryClientProvider } from "@tanstack/react-query";

  const queryClient = new QueryClient({
    defaultOptions: {
      queries: {
        staleTime: 5 * 60 * 1000, // 5 minutos
        retry: 2,
        refetchOnWindowFocus: false,
      },
    },
  });
  ```

- [ ] **8.2** Configurar React Query DevTools
- [ ] **8.3** Crear query keys constants:
  ```javascript
  export const QUERY_KEYS = {
    PRODUCTOS: ["productos"],
    PRODUCTO: (id) => ["productos", id],
    CATEGORIAS: ["categorias"],
    USER_PROFILE: ["user", "profile"],
  };
  ```
- [ ] **8.4** Configurar error boundary para queries
- [ ] **8.5** Implementar cache invalidation strategies

#### 9. Custom Hooks (6h)

- [ ] **9.1** Crear `useAuth()` hook:
  ```javascript
  export const useAuth = () => {
    const login = useMutation({
      mutationFn: authService.login,
      onSuccess: (data) => {
        // Store token, redirect user
      },
    });

    const { data: user } = useQuery({
      queryKey: QUERY_KEYS.USER_PROFILE,
      queryFn: authService.getProfile,
      enabled: !!token,
    });

    return { login, user, logout, isAuthenticated };
  };
  ```
- [ ] **9.2** Crear `useProductos()` hook:
  ```javascript
  export const useProductos = (filters = {}) => {
    return useQuery({
      queryKey: [...QUERY_KEYS.PRODUCTOS, filters],
      queryFn: () => productosService.getAll(filters),
      keepPreviousData: true,
    });
  };
  ```
- [ ] **9.3** Crear `useProducto(id)` hook para detalle
- [ ] **9.4** Crear `useCategorias()` hook
- [ ] **9.5** Agregar loading y error states a todos los hooks

---

### 🔒 **Tareas de Seguridad (Estimación: 8h)**

#### 10. Seguridad Backend (6h)

- [ ] **10.1** Configurar CSRF protection para formularios
- [ ] **10.2** Implementar rate limiting en endpoints críticos
- [ ] **10.3** Validar y sanitizar todos los inputs
- [ ] **10.4** Configurar headers de seguridad:
  ```php
  header('X-Content-Type-Options: nosniff');
  header('X-Frame-Options: DENY');
  header('X-XSS-Protection: 1; mode=block');
  ```
- [ ] **10.5** Logging de intentos de login fallidos
- [ ] **10.6** Configurar HTTPS en producción

#### 11. Seguridad Frontend (2h)

- [ ] **11.1** Configurar Content Security Policy básico
- [ ] **11.2** Validar tokens JWT en frontend
- [ ] **11.3** Implementar logout automático por inactividad
- [ ] **11.4** Evitar XSS en rendering de datos de API

---

### 📚 **Tareas de Documentación (Estimación: 8h)**

#### 12. Documentación de API (6h)

- [ ] **12.1** Crear documentación Swagger/OpenAPI
- [ ] **12.2** Documentar todos los endpoints con ejemplos:
  ```yaml
  /api/productos:
    get:
      summary: Lista productos con filtros
      parameters:
        - name: categoria
          in: query
          schema:
            type: integer
        - name: precio_min
          in: query
          schema:
            type: number
      responses:
        200:
          description: Lista de productos
          content:
            application/json:
              schema:
                type: object
                properties:
                  data:
                    type: array
                    items:
                      $ref: "#/components/schemas/Producto"
  ```
- [ ] **12.3** Documentar esquemas de autenticación
- [ ] **12.4** Incluir códigos de error estándar
- [ ] **12.5** Crear ejemplos de request/response

#### 13. Documentación Frontend (2h)

- [ ] **13.1** Documentar servicios y hooks en código
- [ ] **13.2** Crear README de servicios con ejemplos de uso
- [ ] **13.3** Documentar error handling patterns

---

## ✅ Criterios de Aceptación

### Backend:

- [ ] Todos los endpoints responden correctamente con status codes apropiados
- [ ] Autenticación JWT funciona end-to-end
- [ ] CORS configurado correctamente para desarrollo y producción
- [ ] Paginación funciona en endpoints que retornan listas
- [ ] Validación de datos funciona en todos los endpoints
- [ ] Rate limiting funciona en endpoints críticos

### Frontend:

- [ ] Servicios pueden hacer calls exitosos a todos los endpoints
- [ ] React Query cachea datos correctamente
- [ ] Error handling funciona para casos como 401, 404, 500
- [ ] Custom hooks proporcionan interface limpia para componentes
- [ ] Token refresh funciona automáticamente
- [ ] Loading states se muestran apropiadamente

### Seguridad:

- [ ] Endpoints protegidos requieren autenticación válida
- [ ] Tokens JWT expiran y se refrescan correctamente
- [ ] No hay vulnerabilidades básicas (SQL injection, XSS)
- [ ] HTTPS funciona en ambiente de staging

### Performance:

- [ ] Endpoints responden en <200ms para queries simples
- [ ] React Query evita requests duplicados
- [ ] Paginación maneja listas grandes eficientemente
- [ ] Bundle size no aumentó significativamente

---

## 🎯 Definition of Done

### Para Endpoints:

1. **Funcionalidad**: Endpoint responde según especificación
2. **Validación**: Valida correctamente inputs y retorna errores apropiados
3. **Seguridad**: Implementa autenticación/autorización si es necesario
4. **Testing**: Tests unitarios y de integración pasando
5. **Documentación**: Documentado en Swagger con ejemplos
6. **Performance**: Optimizado para cargas esperadas

### Para Servicios Frontend:

1. **Funcionalidad**: Service conecta exitosamente con API
2. **Error Handling**: Maneja todos los casos de error gracefully
3. **Types**: TypeScript types definidos (si aplica)
4. **Testing**: Unit tests para lógica crítica
5. **Hook Integration**: Integrado con custom hooks apropiados
6. **Documentation**: Ejemplos de uso documentados

---

## 🚨 Riesgos y Mitigaciones

### Riesgo 1: Performance de API con datos reales

**Probabilidad**: Media | **Impacto**: Alto
**Mitigación**: Implementar paginación desde el inicio, optimizar queries, agregar indices de BD

### Riesgo 2: Complejidad del refresh token

**Probabilidad**: Alta | **Impacto**: Medio
**Mitigación**: Usar librería probada, implementar fallback a re-login manual

### Riesgo 3: CORS issues en diferentes ambientes

**Probabilidad**: Media | **Impacto**: Medio
**Mitigación**: Configurar CORS correctamente desde el inicio, testear en múltiples ambientes

### Riesgo 4: Sincronización estado cliente-servidor

**Probabilidad**: Media | **Impacto**: Alto
**Mitigación**: Usar React Query apropiadamente, implementar optimistic updates donde sea apropiado

---

## 📊 Métricas del Sprint

### API Performance:

- **Response Time**: <200ms para 95% de requests
- **Throughput**: >100 requests/segundo
- **Error Rate**: <1%
- **Uptime**: >99.5%

### Frontend Performance:

- **First API Call**: <500ms
- **Cache Hit Rate**: >80% después del primer load
- **Bundle Size Increase**: <100KB
- **Memory Usage**: No memory leaks detectables

### Quality:

- **Test Coverage**: >85% en código nuevo
- **API Documentation Coverage**: 100% de endpoints documentados
- **Security Scan**: 0 vulnerabilidades críticas o altas

---

## 📅 Hitos Importantes

### Fin Semana 1:

- [ ] Endpoints básicos funcionando (productos, categorías)
- [ ] Autenticación JWT implementada
- [ ] Servicios frontend conectando exitosamente

### Fin Semana 2:

- [ ] Todos los endpoints completos y documentados
- [ ] React Query configurado y funcionando
- [ ] Custom hooks creados y testeados
- [ ] Documentación completa

---

## 🔄 Daily Checklist

### Para Backend:

- [ ] ¿Endpoint responde con formato JSON consistente?
- [ ] ¿Validaciones de input implementadas?
- [ ] ¿Headers de seguridad configurados?
- [ ] ¿Logging apropiado agregado?

### Para Frontend:

- [ ] ¿Service maneja errors gracefully?
- [ ] ¿React Query configurado apropiadamente?
- [ ] ¿Loading states implementados?
- [ ] ¿Tipos de datos consistentes?

---

## 📖 Recursos de Referencia

### Backend:

- [CodeIgniter 4 RESTful Guide](https://codeigniter.com/user_guide/incoming/restful.html)
- [Firebase JWT PHP Library](https://github.com/firebase/php-jwt)
- [API Security Best Practices](https://owasp.org/www-project-api-security/)

### Frontend:

- [React Query Documentation](https://tanstack.com/query/v4)
- [Axios Documentation](https://axios-http.com/docs/intro)
- [Custom Hooks Best Practices](https://reactjs.org/docs/hooks-custom.html)

---

_Sprint creado: [Fecha]_  
_Última actualización: [Fecha]_
