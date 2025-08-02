# 🔧 Sprint 1: Configuración Base y Setup del Proyecto

**Duración**: 3 semanas  
**Fecha inicio**: Agosto 1, 2025  
**Fecha fin**: Agosto 22, 2025  
**Responsable**: Solo Developer

---

## 🎯 Objetivos del Sprint

1. Configurar entorno de desarrollo React + TypeScript con Vite
2. Establecer arquitectura base del proyecto frontend tipada
3. Implementar sistema de colores robusto basado en verde navbar (#2A5C45)
4. Implementar nueva tipografía Inter + Montserrat
5. Crear primer componente React funcional con TypeScript
6. Establecer comunicación básica con API CodeIgniter
7. Implementar skeletons y loading states básicos

---

## 📋 Backlog del Sprint

### 🔧 **Tareas Técnicas (Estimación: 40h)**

#### 1. Setup del Proyecto React + TypeScript (8h)

- [ ] **1.1** Crear proyecto Vite + React + TypeScript en carpeta `/frontend`
  ```bash
  npm create vite@latest frontend -- --template react-ts
  cd frontend
  npm install
  ```
- [ ] **1.2** Configurar estructura de carpetas estándar con TypeScript
- [ ] **1.3** Configurar ESLint + Prettier para TypeScript
- [ ] **1.4** Configurar Git hooks con Husky
- [ ] **1.5** Crear script de desarrollo con proxy

#### 2. Instalación de Dependencias Base (4h)

- [ ] **2.1** Instalar React Router DOM
  ```bash
  npm install react-router-dom
  ```
- [ ] **2.2** Instalar React Bootstrap + Bootstrap
  ```bash
  npm install react-bootstrap bootstrap
  ```
- [ ] **2.3** Instalar herramientas de estado y data fetching
  ```bash
  npm install @tanstack/react-query axios zustand
  ```
- [ ] **2.4** Instalar utilidades de formularios
  ```bash
  npm install react-hook-form
  ```
- [ ] **2.5** Instalar iconos, estilos y tipos
  ```bash
  npm install react-icons sass
  npm install @types/node
  ```

#### 3. Configuración de Desarrollo (8h)

- [ ] **3.1** Configurar proxy hacia CodeIgniter (localhost:8080)
- [ ] **3.2** Configurar variables de entorno (.env)
- [ ] **3.3** Configurar Vite config para desarrollo y producción
- [ ] **3.4** Configurar alias para imports (`@/components`, `@/services`, etc.)
- [ ] **3.5** Configurar hot reload y fast refresh

#### 4. Estructura Base del Proyecto (12h)

- [ ] **4.1** Crear estructura de carpetas:
  ```
  frontend/src/
  ├── components/           # Componentes reutilizables
  │   ├── common/          # Botones, inputs, cards
  │   ├── forms/           # Formularios específicos
  │   └── layout/          # Header, footer, sidebar
  ├── pages/               # Páginas principales
  │   ├── Home/
  │   ├── Products/
  │   ├── Auth/
  │   └── User/
  ├── hooks/               # Custom hooks
  ├── services/            # API calls y servicios
  ├── store/               # Estado global (Zustand)
  ├── styles/              # SCSS files
  │   ├── components/      # Estilos por componente
  │   ├── pages/           # Estilos por página
  │   ├── utils/           # Mixins, variables
  │   └── vendor/          # Overrides de Bootstrap
  ├── utils/               # Funciones utilitarias
  └── constants/           # Constantes de la app
  ```
- [ ] **4.2** Crear archivo de constantes base
- [ ] **4.3** Configurar barrel exports (index.js) en cada carpeta
- [ ] **4.4** Crear README.md del frontend con instrucciones

#### 5. Testing Setup (8h)

- [ ] **5.1** Configurar Jest + React Testing Library
- [ ] **5.2** Configurar Vitest como alternativa a Jest
- [ ] **5.3** Crear tests básicos de setup
- [ ] **5.4** Configurar coverage reports
- [ ] **5.5** Integrar testing en CI/CD pipeline básico

---

### 🎨 **Tareas de Diseño (Estimación: 20h)**

#### 6. Sistema de Tipografía (8h)

- [ ] **6.1** Investigar y seleccionar fuentes:
  - **Inter**: Para textos generales y UI
  - **Montserrat**: Para títulos y CTAs
  - **Source Code Pro**: Para código y números (opcional)
- [ ] **6.2** Configurar importación de Google Fonts
- [ ] **6.3** Crear variables SCSS para tipografía:

  ```scss
  // _typography.scss
  $font-primary: "Inter", -apple-system, BlinkMacSystemFont, sans-serif;
  $font-headings: "Montserrat", Georgia, serif;
  $font-code: "Source Code Pro", monospace;

  $font-size-xs: 0.75rem; // 12px
  $font-size-sm: 0.875rem; // 14px
  $font-size-base: 1rem; // 16px
  $font-size-lg: 1.125rem; // 18px
  $font-size-xl: 1.25rem; // 20px
  // ... más tamaños
  ```

- [ ] **6.4** Crear mixins para tipografía responsiva
- [ ] **6.5** Documentar sistema tipográfico en Storybook

#### 7. Sistema de Colores (8h)

- [ ] **7.1** Extraer paleta de colores del diseño actual
- [ ] **7.2** Crear variables SCSS de colores:

  ```scss
  // _colors.scss
  $verde-selva: #2e5d3b; // Color principal
  $dorado: #e1b91a; // Color secundario
  $gris-oscuro: #2c3e50; // Textos
  $gris-claro: #f8f9fa; // Fondos

  // Paleta extendida
  $success: #28a745;
  $warning: #ffc107;
  $danger: #dc3545;
  $info: #17a2b8;
  ```

- [ ] **7.3** Crear utilidades de color para componentes
- [ ] **7.4** Configurar variables CSS custom properties
- [ ] **7.5** Crear modo oscuro básico (opcional)

#### 8. Bootstrap Customization (4h)

- [ ] **8.1** Crear archivo de override de Bootstrap
- [ ] **8.2** Personalizar variables Bootstrap principales
- [ ] **8.3** Configurar tema personalizado
- [ ] **8.4** Optimizar bundle eliminando componentes no usados

---

### 📱 **Tareas de UX/UI (Estimación: 16h)**

#### 9. Auditoría UX Actual (6h)

- [ ] **9.1** Analizar flujos críticos actuales:
  - Navegación principal
  - Búsqueda de productos
  - Proceso de compra
  - Login/registro
- [ ] **9.2** Identificar pain points y fricciones
- [ ] **9.3** Documentar métricas actuales (si están disponibles)
- [ ] **9.4** Crear user journey maps
- [ ] **9.5** Definir KPIs para medir mejoras

#### 10. Definición de Componentes Base (6h)

- [ ] **10.1** Crear inventario de componentes necesarios:
  - **Layout**: Header, Footer, Sidebar, Container
  - **Navigation**: Navbar, Breadcrumbs, Pagination
  - **Forms**: Input, Select, Textarea, Checkbox, Radio
  - **Feedback**: Alert, Toast, Modal, Loading
  - **Data Display**: Card, Table, Badge, Avatar
  - **Buttons**: Primary, Secondary, Outline, Icon
- [ ] **10.2** Definir props y variantes de cada componente
- [ ] **10.3** Crear wireframes básicos de componentes críticos
- [ ] **10.4** Establecer naming conventions
- [ ] **10.5** Documentar patrones de diseño

#### 11. Wireframes de Componentes Críticos (4h)

- [ ] **11.1** Wireframe de Header/Navbar responsive
- [ ] **11.2** Wireframe de Card de producto
- [ ] **11.3** Wireframe de Modal genérico
- [ ] **11.4** Wireframe de formularios base
- [ ] **11.5** Validar wireframes con equipo

---

### 🔧 **Tareas de Integración (Estimación: 12h)**

#### 12. Configuración de Comunicación con API (8h)

- [ ] **12.1** Configurar Axios con configuración base
- [ ] **12.2** Crear interceptores para requests/responses
- [ ] **12.3** Configurar manejo de errores global
- [ ] **12.4** Crear servicio base para API calls
- [ ] **12.5** Testear conexión básica con endpoints existentes

#### 13. Primer Componente React Funcional (4h)

- [ ] **13.1** Crear componente `<ProductCard />` básico
- [ ] **13.2** Integrar con API para mostrar productos
- [ ] **13.3** Aplicar estilos con nueva tipografía
- [ ] **13.4** Hacer componente responsive
- [ ] **13.5** Crear test básico del componente

---

## ✅ Criterios de Aceptación

### Técnicos:

- [ ] React app corre correctamente en `http://localhost:3000`
- [ ] Comunicación exitosa con API CodeIgniter en `http://localhost:8080`
- [ ] ESLint y Prettier configurados sin errores
- [ ] Build de producción genera bundle optimizado
- [ ] Tests básicos pasan correctamente

### Diseño:

- [ ] Tipografías Inter y Montserrat se cargan correctamente
- [ ] Sistema de colores aplicado consistentemente
- [ ] Variables SCSS funcionando en todos los componentes
- [ ] Responsive design funciona en mobile/tablet/desktop

### UX:

- [ ] Navegación básica entre páginas funciona
- [ ] Primer componente muestra datos reales de la API
- [ ] Loading states implementados
- [ ] Error boundaries configurados

### Documentación:

- [ ] README actualizado con instrucciones de setup
- [ ] Documentación de arquitectura creada
- [ ] Guía de contribución definida
- [ ] Convenciones de código establecidas

---

## 🎯 Definition of Done

Un task está "Done" cuando:

1. **Código completo**: Funcionalidad implementada según especificación
2. **Tests pasando**: Unit tests escritos y pasando
3. **Code review**: Revisado y aprobado por al menos 1 reviewer
4. **Documentación**: Documentado en código y/o wiki
5. **Testing manual**: Probado manualmente en diferentes browsers
6. **Performance**: No impacta negativamente el performance
7. **Accesibilidad**: Cumple estándares básicos de a11y
8. **Responsive**: Funciona en móvil, tablet y desktop

---

## 🚨 Riesgos y Mitigaciones

### Riesgo 1: Conflictos entre Bootstrap y React Bootstrap

**Probabilidad**: Media | **Impacto**: Alto
**Mitigación**: Testear integración temprano, usar React Bootstrap como librería principal

### Riesgo 2: Performance impact por tipografías externas

**Probabilidad**: Baja | **Impacto**: Medio  
**Mitigación**: Implementar font-display: swap, preload critical fonts

### Riesgo 3: Configuración compleja de proxy desarrollo

**Probabilidad**: Media | **Impacto**: Medio
**Mitigación**: Documentar well la configuración, crear docker-compose si es necesario

---

## 📊 Métricas del Sprint

### Velocity:

- **Story Points planificados**: 88h (≈ 22 SP si 1 SP = 4h)
- **Story Points completados**: [Medir al final]
- **Burndown**: [Trackear diariamente]

### Quality:

- **Test coverage**: >80% en código nuevo
- **Lint errors**: 0
- **Bundle size**: <2MB inicial
- **Lighthouse performance**: >90

### Team:

- **Bloqueadores**: [Documentar cuando aparezcan]
- **Retrospective items**: [Completar al final del sprint]
- **Learning goals**: Cada dev debe aprender al menos 1 concepto React nuevo

---

## 📅 Calendario del Sprint

### Semana 1:

- **Días 1-2**: Setup inicial y configuración (Tasks 1-5)
- **Días 3-4**: Sistema de diseño (Tasks 6-8)
- **Día 5**: Auditoría UX y planning componentes (Task 9)

### Semana 2:

- **Días 1-2**: Wireframes y definición componentes (Tasks 10-11)
- **Días 3-4**: Integración API y primer componente (Tasks 12-13)
- **Día 5**: Testing, documentación y retrospective

---

## 🔄 Daily Standup Template

**¿Qué hice ayer?**

- [Task completado]
- [Progreso en task X]

**¿Qué voy a hacer hoy?**

- [Próximas tasks]
- [Foco del día]

**¿Hay algún bloqueador?**

- [Impedimentos técnicos]
- [Dependencias externas]
- [Necesidad de clarificación]

---

_Sprint creado: [Fecha]_  
_Última actualización: [Fecha]_
