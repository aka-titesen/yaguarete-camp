# React + TypeScript + Vite

This template provides a minimal setup to get React working in Vite with HMR and some ESLint rules.

Currently, two official plugins are available:

- [@vitejs/plugin-react](https://github.com/vitejs/vite-plugin-react/blob/main/packages/plugin-react) uses [Babel](https://babeljs.io/) for Fast Refresh
- [@vitejs/plugin-react-swc](https://github.com/vitejs/vite-plugin-react/blob/main/packages/plugin-react-swc) uses [SWC](https://swc.rs/) for Fast Refresh

# 🐆 Yaguareté Camp - Frontend React + TypeScript

## 📋 Descripción

Frontend moderno construido con React 18, TypeScript, Vite y Bootstrap 5. Este proyecto forma parte de la migración del frontend estático a una aplicación React dinámica y reactiva.

## 🚀 Características Principales

### ✅ Completado en Sprint 1

- **React 18 + TypeScript**: Framework moderno con tipado estricto
- **Vite**: Build tool ultra-rápido para desarrollo
- **Bootstrap 5**: Framework CSS responsive con componentes personalizados
- **Sistema de Colores Avanzado**: Paleta completa basada en el verde del navbar (#2A5C45)
- **Componentes SCSS**: Botones, cards, forms, skeletons y navbar
- **React Query**: Manejo de estado servidor y cache inteligente
- **Tipos TypeScript**: Sistema completo de tipos para todo el proyecto

### 🔄 Próximamente (Sprint 2-8)

- Zustand para estado global
- React Hook Form para formularios
- React Router para navegación
- Integración con backend CodeIgniter 4
- Testing con Vitest
- Componentes React funcionales
- PWA capabilities

## 🎨 Sistema de Colores

Basado en el color original del navbar `#2A5C45`, se ha desarrollado una paleta completa:

### Paleta Verde (Primaria)

```scss
$green-50: #f0f9f4; // Muy claro
$green-100: #dcf2e3; // Claro
$green-200: #bbe5ca; // Suave
$green-300: #8cd4a8; // Medio claro
$green-400: #5abb7e; // Medio
$green-500: #37a15e; // Medio oscuro
$green-600: #2a5c45; // Original (navbar)
$green-700: #1f4433; // Oscuro
$green-800: #1a372a; // Muy oscuro
$green-900: #162d23; // Ultra oscuro
```

### Otras Paletas

- **Azul**: Colores secundarios y complementarios
- **Neutral**: Grises para texto y fondos
- **Amber**: Alertas y destacados
- **Rojo**: Errores y estados críticos

## 🏗️ Estructura del Proyecto

```
frontend/
├── src/
│   ├── components/          # Componentes React reutilizables
│   │   ├── common/         # Componentes comunes
│   │   └── layout/         # Componentes de layout
│   ├── hooks/              # Custom hooks
│   ├── services/           # Servicios API
│   ├── store/              # Estado global (Zustand)
│   ├── styles/             # Estilos SCSS
│   │   ├── abstracts/      # Variables y mixins
│   │   ├── components/     # Estilos por componente
│   │   └── main.scss       # Archivo principal
│   ├── types/              # Definiciones TypeScript
│   ├── utils/              # Utilidades y helpers
│   └── App.tsx             # Componente principal
├── public/                 # Archivos estáticos
└── package.json           # Dependencias del proyecto
```

## 🛠️ Tecnologías

### Core

- **React 18**: Biblioteca principal
- **TypeScript**: Tipado estático
- **Vite**: Build tool y dev server
- **Bootstrap 5**: Framework CSS

### Librerías Principales

- **@tanstack/react-query**: Manejo de estado servidor
- **axios**: Cliente HTTP
- **zustand**: Estado global (próximamente)
- **react-hook-form**: Formularios (próximamente)
- **react-icons**: Iconos
- **sass**: Preprocesador CSS

### Desarrollo

- **ESLint**: Linter
- **Prettier**: Formateador (próximamente)
- **@tanstack/react-query-devtools**: Debugging

## 🚦 Comandos

### Desarrollo

```bash
npm run dev          # Servidor de desarrollo
npm run build        # Build de producción
npm run preview      # Preview del build
npm run lint         # Ejecutar ESLint
```

### Instalación

```bash
# Instalar dependencias
npm install

# Iniciar desarrollo
npm run dev
```

## 📱 Responsive Design

El sistema está diseñado mobile-first con breakpoints:

- **Mobile**: < 576px
- **Tablet**: 576px - 991px
- **Desktop**: 992px - 1199px
- **Large Desktop**: ≥ 1200px

## 🎯 Componentes Disponibles

### Botones

- `btn-primary-yaguarete`: Botón primario verde
- `btn-secondary-yaguarete`: Botón secundario azul
- `btn-gradient-primary`: Botón con gradiente
- `btn-outline-primary-yaguarete`: Botón outline
- `btn-ghost-primary`: Botón fantasma
- `btn-glass`: Botón con efecto glass morphism

### Cards

- `card-yaguarete`: Card base
- `product-card`: Card para productos
- `user-card`: Card para perfiles
- `stat-card`: Card para estadísticas
- `article-card`: Card para artículos

### Forms

- `input-yaguarete`: Input personalizado
- `form-yaguarete`: Formulario base
- `form-floating`: Labels flotantes
- `file-upload`: Carga de archivos

### Skeletons

- `skeleton-product-card`: Loading para productos
- `skeleton-user-profile`: Loading para perfiles
- `skeleton-article-list`: Loading para listas
- `skeleton-table`: Loading para tablas

## 🎨 Efectos y Animaciones

### Gradientes

```scss
$gradient-primary: linear-gradient(135deg, #37a15e 0%, #1f4433 100%);
$gradient-hero: linear-gradient(135deg, #2a5c45 0%, #2563eb 100%);
```

### Sombras

- `shadow-xs` a `shadow-2xl`: Sombras por elevación
- `shadow-primary`: Sombra coloreada primaria
- `shadow-glass`: Sombra para glass morphism

### Mixins Útiles

- `@include hover-lift()`: Efecto de elevación en hover
- `@include shimmer()`: Animación shimmer para skeletons
- `@include glass-morphism()`: Efecto glass morphism
- `@include gradient-text()`: Texto con gradiente

## 🔗 Integración con Backend

El frontend está diseñado para integrarse perfectamente con el backend CodeIgniter 4 existente:

- **API REST**: Comunicación vía endpoints JSON
- **Autenticación**: Manteniendo el sistema existente
- **Rutas**: Respetando la estructura actual
- **Assets**: Coexistencia con assets actuales

## 📈 Roadmap

### Sprint 2 (Próximo)

- [ ] Componentes React funcionales
- [ ] Sistema de routing
- [ ] Integración básica con backend
- [ ] Formularios dinámicos

### Sprint 3-4

- [ ] Estado global completo
- [ ] Testing unitario
- [ ] Optimizaciones de performance
- [ ] PWA básico

### Sprint 5-8

- [ ] Funcionalidades avanzadas
- [ ] Dark mode
- [ ] Internacionalización
- [ ] Analytics y monitoreo

## 🤝 Contribución

Este proyecto sigue las mejores prácticas de desarrollo:

1. **Commits semánticos**: feat:, fix:, docs:, etc.
2. **TypeScript estricto**: Todos los tipos definidos
3. **SCSS modular**: Un archivo por componente
4. **Componentes reutilizables**: DRY principle
5. **Responsive first**: Mobile-first approach

## 📞 Soporte

Para dudas o problemas:

- Revisar la documentación en `/docs/`
- Consultar los tipos en `/src/types/`
- Ver ejemplos en `App.tsx`

---

**Desarrollado con ❤️ para Yaguareté Camp**  
_Sprint 1 completado - Enero 2025_

You can also install [eslint-plugin-react-x](https://github.com/Rel1cx/eslint-react/tree/main/packages/plugins/eslint-plugin-react-x) and [eslint-plugin-react-dom](https://github.com/Rel1cx/eslint-react/tree/main/packages/plugins/eslint-plugin-react-dom) for React-specific lint rules:

```js
// eslint.config.js
import reactX from "eslint-plugin-react-x";
import reactDom from "eslint-plugin-react-dom";

export default tseslint.config([
  globalIgnores(["dist"]),
  {
    files: ["**/*.{ts,tsx}"],
    extends: [
      // Other configs...
      // Enable lint rules for React
      reactX.configs["recommended-typescript"],
      // Enable lint rules for React DOM
      reactDom.configs.recommended,
    ],
    languageOptions: {
      parserOptions: {
        project: ["./tsconfig.node.json", "./tsconfig.app.json"],
        tsconfigRootDir: import.meta.dirname,
      },
      // other options...
    },
  },
]);
```
