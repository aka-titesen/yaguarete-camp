# 📊 Sprint 3: Componentes Base y Sistema de Diseño

**Duración**: 2 semanas  
**Fecha inicio**: [Definir]  
**Fecha fin**: [Definir]  
**Responsable**: [Asignar]  
**Dependencias**: Sprint 1 y 2 completados

---

## 🎯 Objetivos del Sprint

1. Crear librería completa de componentes base reutilizables
2. Implementar Storybook para documentación de componentes
3. Configurar Zustand stores para estado global
4. Migrar y optimizar todos los estilos CSS existentes a SCSS
5. Establecer sistema de iconos consistente con React Icons

---

## 📋 Backlog del Sprint

### 🧱 **Componentes Base (Estimación: 40h)**

#### 1. Layout Components (8h)

- [ ] **1.1** `<Layout />` - Container principal:
  ```jsx
  const Layout = ({ children, sidebar = false, className }) => {
    return (
      <div className={`layout ${className}`}>
        <Header />
        <main className={sidebar ? "with-sidebar" : ""}>{children}</main>
        <Footer />
      </div>
    );
  };
  ```
- [ ] **1.2** `<Header />` - Navbar responsive:
  - Logo y navegación principal
  - Buscador con autocomplete
  - Carrito dropdown
  - Menu usuario/login
  - Menu mobile hamburger
- [ ] **1.3** `<Footer />` - Footer completo:
  - Newsletter signup
  - Métodos de pago
  - Links útiles
  - Redes sociales
- [ ] **1.4** `<Sidebar />` - Navegación lateral:
  - Categorías
  - Filtros
  - Collapsible sections

#### 2. Form Components (10h)

- [ ] **2.1** `<Input />` - Input base con variantes:
  ```jsx
  const Input = ({
    type = "text",
    size = "md",
    error,
    label,
    required,
    ...props
  }) => {
    return (
      <div className={`input-group ${size} ${error ? "error" : ""}`}>
        {label && (
          <label>
            {label} {required && "*"}
          </label>
        )}
        <input type={type} {...props} />
        {error && <span className="error-message">{error}</span>}
      </div>
    );
  };
  ```
- [ ] **2.2** `<Select />` - Select mejorado:
  - Búsqueda en opciones
  - Múltiple selección
  - Loading state
  - Custom styling
- [ ] **2.3** `<Textarea />` - Textarea con auto-resize
- [ ] **2.4** `<Checkbox />` y `<Radio />` - Controles personalizados
- [ ] **2.5** `<FormGroup />` - Wrapper para grupos de campos
- [ ] **2.6** `<SearchInput />` - Input de búsqueda especializado

#### 3. Button Components (6h)

- [ ] **3.1** `<Button />` - Botón base con variantes:
  ```jsx
  const Button = ({
    variant = "primary",
    size = "md",
    disabled,
    loading,
    icon,
    children,
    ...props
  }) => {
    return (
      <button
        className={`btn btn-${variant} btn-${size}`}
        disabled={disabled || loading}
        {...props}
      >
        {loading ? <Spinner /> : icon}
        {children}
      </button>
    );
  };
  ```
- [ ] **3.2** Variantes: `primary`, `secondary`, `outline`, `ghost`, `danger`
- [ ] **3.3** `<IconButton />` - Botón solo icono
- [ ] **3.4** `<ButtonGroup />` - Grupo de botones
- [ ] **3.5** `<FloatingActionButton />` - FAB para móvil

#### 4. Feedback Components (8h)

- [ ] **4.1** `<Alert />` - Alertas con variantes:
  ```jsx
  const Alert = ({ type = "info", dismissible, onClose, children }) => {
    return (
      <div className={`alert alert-${type}`}>
        <div className="alert-content">{children}</div>
        {dismissible && (
          <button onClick={onClose} className="alert-close">
            <CloseIcon />
          </button>
        )}
      </div>
    );
  };
  ```
- [ ] **4.2** `<Toast />` - Notificaciones temporales:
  - Auto-dismiss
  - Posicionamiento configurable
  - Queue de notificaciones
- [ ] **4.3** `<Modal />` - Modal reutilizable:
  - Diferentes tamaños
  - Backdrop configurable
  - Animaciones suaves
- [ ] **4.4** `<Loading />` - Estados de carga:
  - Spinner
  - Skeleton screens
  - Progress bars
- [ ] **4.5** `<Empty />` - Estado vacío con ilustraciones

#### 5. Data Display Components (8h)

- [ ] **5.1** `<Card />` - Tarjeta base:
  ```jsx
  const Card = ({
    image,
    title,
    subtitle,
    content,
    actions,
    hover = false,
    className,
  }) => {
    return (
      <div className={`card ${hover ? "hover" : ""} ${className}`}>
        {image && <div className="card-image">{image}</div>}
        <div className="card-body">
          {title && <h3 className="card-title">{title}</h3>}
          {subtitle && <p className="card-subtitle">{subtitle}</p>}
          {content && <div className="card-content">{content}</div>}
        </div>
        {actions && <div className="card-actions">{actions}</div>}
      </div>
    );
  };
  ```
- [ ] **5.2** `<ProductCard />` - Tarjeta específica para productos:
  - Imagen con lazy loading
  - Badge de estado/descuento
  - Rating stars
  - Quick actions (favorito, carrito)
- [ ] **5.3** `<Badge />` - Etiquetas pequeñas
- [ ] **5.4** `<Avatar />` - Avatar de usuario
- [ ] **5.5** `<Rating />` - Sistema de calificación con estrellas

---

### 🎨 **Sistema de Estilos (Estimación: 20h)**

#### 6. SCSS Architecture (8h)

- [ ] **6.1** Estructura de carpetas SCSS:
  ```
  styles/
  ├── abstracts/
  │   ├── _variables.scss      # Variables globales
  │   ├── _mixins.scss         # Mixins reutilizables
  │   └── _functions.scss      # Funciones SCSS
  ├── base/
  │   ├── _reset.scss          # Reset CSS
  │   ├── _typography.scss     # Tipografía base
  │   └── _utilities.scss      # Clases utilitarias
  ├── components/
  │   ├── _button.scss
  │   ├── _card.scss
  │   ├── _modal.scss
  │   └── ... (uno por componente)
  ├── layout/
  │   ├── _header.scss
  │   ├── _footer.scss
  │   └── _sidebar.scss
  ├── pages/
  │   ├── _home.scss
  │   ├── _products.scss
  │   └── ... (uno por página)
  └── vendor/
      └── _bootstrap-overrides.scss
  ```
- [ ] **6.2** Variables SCSS globales:

  ```scss
  // _variables.scss

  // Colores principales
  $color-primary: #2e5d3b;
  $color-secondary: #e1b91a;
  $color-accent: #8b4513;

  // Escala de grises
  $gray-100: #f8f9fa;
  $gray-200: #e9ecef;
  $gray-300: #dee2e6;
  $gray-400: #ced4da;
  $gray-500: #adb5bd;
  $gray-600: #6c757d;
  $gray-700: #495057;
  $gray-800: #343a40;
  $gray-900: #212529;

  // Espaciado
  $spacer: 1rem;
  $spacers: (
    0: 0,
    1: $spacer * 0.25,
    // 4px
    2: $spacer * 0.5,
    // 8px
    3: $spacer,
    // 16px
    4: $spacer * 1.5,
    // 24px
    5: $spacer * 3,
    // 48px
  );

  // Breakpoints
  $breakpoints: (
    xs: 0,
    sm: 576px,
    md: 768px,
    lg: 992px,
    xl: 1200px,
    xxl: 1400px,
  );
  ```

- [ ] **6.3** Mixins útiles:

  ```scss
  // _mixins.scss

  @mixin respond-to($breakpoint) {
    @if map-has-key($breakpoints, $breakpoint) {
      @media (min-width: map-get($breakpoints, $breakpoint)) {
        @content;
      }
    }
  }

  @mixin button-variant($color, $background, $border) {
    color: $color;
    background-color: $background;
    border-color: $border;

    &:hover {
      background-color: darken($background, 7.5%);
      border-color: darken($border, 10%);
    }
  }

  @mixin card-elevation($level) {
    box-shadow: 0 #{$level * 2}px #{$level * 4}px rgba(0, 0, 0, 0.1);
  }
  ```

#### 7. Migración de Estilos Existentes (8h)

- [ ] **7.1** Auditar CSS actual en `public/assets/css/`
- [ ] **7.2** Migrar `miestilo.css` a SCSS modules
- [ ] **7.3** Migrar `detalleProducto.css` a componentes específicos
- [ ] **7.4** Migrar `carrito-x-dark.css` a tema consistente
- [ ] **7.5** Optimizar y eliminar CSS no utilizado
- [ ] **7.6** Convertir estilos inline a clases reutilizables

#### 8. Sistema de Iconos (4h)

- [ ] **8.1** Configurar React Icons como reemplazo de Font Awesome:
  ```jsx
  // Mapeo de iconos comunes
  import {
    FaShoppingCart, // Carrito
    FaUser, // Usuario
    FaSearch, // Búsqueda
    FaHeart, // Favoritos
    FaStar, // Rating
    FaHome, // Home
    FaEnvelope, // Email
    FaPhone, // Teléfono
    FaMapMarkerAlt, // Ubicación
    FaCreditCard, // Pago
    FaCheck, // Success
    FaTimes, // Close
    FaExclamation, // Warning
    FaInfo, // Info
  } from "react-icons/fa";
  ```
- [ ] **8.2** Crear componente `<Icon />` wrapper:
  ```jsx
  const Icon = ({ name, size = 16, color, className, ...props }) => {
    const IconComponent = iconMap[name];
    return IconComponent ? (
      <IconComponent
        size={size}
        color={color}
        className={className}
        {...props}
      />
    ) : null;
  };
  ```
- [ ] **8.3** Documentar mapeo de iconos en Storybook
- [ ] **8.4** Reemplazar Font Awesome en templates existentes

---

### 🏪 **Estado Global (Estimación: 16h)**

#### 9. Configuración Zustand (6h)

- [ ] **9.1** Store de autenticación:

  ```javascript
  // store/authStore.js
  import { create } from "zustand";
  import { persist } from "zustand/middleware";

  export const useAuthStore = create(
    persist(
      (set, get) => ({
        user: null,
        token: null,
        isAuthenticated: false,

        login: (userData, token) => {
          set({
            user: userData,
            token,
            isAuthenticated: true,
          });
        },

        logout: () => {
          set({
            user: null,
            token: null,
            isAuthenticated: false,
          });
          // Clear localStorage, redirect, etc.
        },

        updateProfile: (userData) => {
          set((state) => ({
            user: { ...state.user, ...userData },
          }));
        },
      }),
      {
        name: "auth-storage",
        partialize: (state) => ({
          token: state.token,
          user: state.user,
          isAuthenticated: state.isAuthenticated,
        }),
      }
    )
  );
  ```

- [ ] **9.2** Store del carrito:
  ```javascript
  // store/cartStore.js
  export const useCartStore = create(
    persist(
      (set, get) => ({
        items: [],
        total: 0,
        itemCount: 0,

        addItem: (product, quantity = 1) => {
          const items = get().items;
          const existingItem = items.find((item) => item.id === product.id);

          if (existingItem) {
            set((state) => ({
              items: state.items.map((item) =>
                item.id === product.id
                  ? { ...item, quantity: item.quantity + quantity }
                  : item
              ),
            }));
          } else {
            set((state) => ({
              items: [...state.items, { ...product, quantity }],
            }));
          }

          get().calculateTotals();
        },

        removeItem: (productId) => {
          set((state) => ({
            items: state.items.filter((item) => item.id !== productId),
          }));
          get().calculateTotals();
        },

        updateQuantity: (productId, quantity) => {
          if (quantity <= 0) {
            get().removeItem(productId);
            return;
          }

          set((state) => ({
            items: state.items.map((item) =>
              item.id === productId ? { ...item, quantity } : item
            ),
          }));
          get().calculateTotals();
        },

        calculateTotals: () => {
          const items = get().items;
          const total = items.reduce(
            (sum, item) => sum + item.precio * item.quantity,
            0
          );
          const itemCount = items.reduce((sum, item) => sum + item.quantity, 0);

          set({ total, itemCount });
        },

        clearCart: () => {
          set({
            items: [],
            total: 0,
            itemCount: 0,
          });
        },
      }),
      {
        name: "cart-storage",
      }
    )
  );
  ```

#### 10. Store de UI (4h)

- [ ] **10.1** Store para estado de UI:
  ```javascript
  // store/uiStore.js
  export const useUIStore = create((set) => ({
    // Modals
    modals: {
      login: false,
      cart: false,
      productQuickView: false,
      contacto: false,
    },

    // Loading states
    loading: {
      global: false,
      products: false,
      auth: false,
    },

    // Notifications
    notifications: [],

    // Filtros y búsqueda
    searchQuery: "",
    filters: {
      categoria: null,
      precioMin: null,
      precioMax: null,
      ordenamiento: "relevancia",
    },

    // Actions
    openModal: (modalName) => {
      set((state) => ({
        modals: { ...state.modals, [modalName]: true },
      }));
    },

    closeModal: (modalName) => {
      set((state) => ({
        modals: { ...state.modals, [modalName]: false },
      }));
    },

    setLoading: (key, isLoading) => {
      set((state) => ({
        loading: { ...state.loading, [key]: isLoading },
      }));
    },

    addNotification: (notification) => {
      const id = Date.now();
      set((state) => ({
        notifications: [...state.notifications, { ...notification, id }],
      }));

      // Auto remove after 5 seconds
      setTimeout(() => {
        set((state) => ({
          notifications: state.notifications.filter((n) => n.id !== id),
        }));
      }, 5000);
    },

    removeNotification: (id) => {
      set((state) => ({
        notifications: state.notifications.filter((n) => n.id !== id),
      }));
    },

    setSearchQuery: (query) => {
      set({ searchQuery: query });
    },

    updateFilters: (newFilters) => {
      set((state) => ({
        filters: { ...state.filters, ...newFilters },
      }));
    },
  }));
  ```

#### 11. Context API Complementario (4h)

- [ ] **11.1** Context para tema y configuración:

  ```jsx
  // context/ThemeContext.jsx
  const ThemeContext = createContext();

  export const ThemeProvider = ({ children }) => {
    const [theme, setTheme] = useState("light");
    const [language, setLanguage] = useState("es");

    const toggleTheme = () => {
      setTheme((prev) => (prev === "light" ? "dark" : "light"));
    };

    return (
      <ThemeContext.Provider
        value={{
          theme,
          language,
          toggleTheme,
          setLanguage,
        }}
      >
        {children}
      </ThemeContext.Provider>
    );
  };
  ```

- [ ] **11.2** Context para configuración de aplicación
- [ ] **11.3** Hook personalizado `useTheme()`

#### 12. Integración con React Query (2h)

- [ ] **12.1** Sincronizar stores con server state
- [ ] **12.2** Optimistic updates para carrito
- [ ] **12.3** Cache invalidation en logout

---

### 📚 **Documentación - Storybook (Estimación: 12h)**

#### 13. Configuración Storybook (4h)

- [ ] **13.1** Instalar y configurar Storybook:
  ```bash
  npx storybook@latest init
  npm install --save-dev @storybook/addon-essentials
  npm install --save-dev @storybook/addon-a11y
  npm install --save-dev @storybook/addon-design-tokens
  ```
- [ ] **13.2** Configurar decorators para Bootstrap y temas
- [ ] **13.3** Configurar addons esenciales
- [ ] **13.4** Configurar build de Storybook para deploy

#### 14. Stories de Componentes (8h)

- [ ] **14.1** Stories para todos los Button components:

  ```javascript
  // components/Button/Button.stories.js
  export default {
    title: "Components/Button",
    component: Button,
    parameters: {
      docs: {
        description: {
          component:
            "Componente Button base con múltiples variantes y estados.",
        },
      },
    },
    argTypes: {
      variant: {
        control: { type: "select" },
        options: ["primary", "secondary", "outline", "ghost", "danger"],
      },
      size: {
        control: { type: "select" },
        options: ["sm", "md", "lg"],
      },
    },
  };

  export const Primary = {
    args: {
      variant: "primary",
      children: "Button Primary",
    },
  };

  export const AllVariants = () => (
    <div style={{ display: "flex", gap: "1rem", flexWrap: "wrap" }}>
      <Button variant="primary">Primary</Button>
      <Button variant="secondary">Secondary</Button>
      <Button variant="outline">Outline</Button>
      <Button variant="ghost">Ghost</Button>
      <Button variant="danger">Danger</Button>
    </div>
  );
  ```

- [ ] **14.2** Stories para Form components con validaciones
- [ ] **14.3** Stories para Cards con diferentes contenidos
- [ ] **14.4** Stories para Modal con diferentes tamaños
- [ ] **14.5** Stories para estados Loading y Empty
- [ ] **14.6** Stories para Layout components

---

## ✅ Criterios de Aceptación

### Componentes:

- [ ] Todos los componentes base funcionan correctamente
- [ ] Componentes son responsive en móvil, tablet y desktop
- [ ] Props están bien tipados y documentados
- [ ] Componentes manejan estados loading y error apropiadamente
- [ ] Accesibilidad básica implementada (ARIA labels, keyboard navigation)

### Estilos:

- [ ] Sistema SCSS bien estructurado y escalable
- [ ] Variables y mixins funcionan correctamente
- [ ] Estilos migrados mantienen apariencia visual
- [ ] No hay conflictos entre Bootstrap y estilos custom
- [ ] Bundle CSS optimizado (<200KB)

### Estado:

- [ ] Stores Zustand funcionan sin memory leaks
- [ ] Persistencia en localStorage funciona correctamente
- [ ] Estado se sincroniza entre componentes
- [ ] Actions tienen nombres descriptivos y funcionan apropiadamente

### Storybook:

- [ ] Todos los componentes base documentados
- [ ] Stories cubren casos principales de uso
- [ ] Controles interactivos funcionan
- [ ] Documentación es clara y útil
- [ ] Build de Storybook funciona para deploy

---

## 🎯 Definition of Done

### Para Componentes:

1. **Funcionalidad**: Componente funciona según especificación
2. **Props**: Props bien definidos con defaults apropiados
3. **Styling**: Estilos responsive y consistentes con design system
4. **Accessibility**: ARIA labels, keyboard support, contrast ratios apropiados
5. **Testing**: Unit tests para lógica crítica
6. **Stories**: Documentado en Storybook con ejemplos
7. **Performance**: No causa re-renders innecesarios

### Para Stores:

1. **Funcionalidad**: Actions funcionan correctamente
2. **Persistence**: State persiste apropiadamente
3. **Performance**: No memory leaks o performance issues
4. **Testing**: Logic testeada con unit tests
5. **Documentation**: Actions y state documentados
6. **Integration**: Integra bien con components y hooks

---

## 📊 Métricas del Sprint

### Component Library:

- **Coverage**: 100% de componentes base cubiertos
- **Reusability**: Cada componente usado en al menos 2 lugares
- **Bundle Impact**: <150KB adicionales al bundle
- **Accessibility Score**: >90 en auditoría automática

### Code Quality:

- **TypeScript Errors**: 0 (si usando TypeScript)
- **ESLint Errors**: 0
- **Test Coverage**: >85% en componentes
- **Storybook Coverage**: 100% de componentes documentados

### Performance:

- **Render Performance**: <16ms para re-renders
- **Bundle Size**: Componentes tree-shakeable
- **Memory Usage**: No leaks detectables
- **Load Time**: Storybook carga en <3 segundos

---

## 🚨 Riesgos y Mitigaciones

### Riesgo 1: Complejidad excesiva en componentes

**Probabilidad**: Media | **Impacto**: Alto
**Mitigación**: Mantener componentes simples, usar composition over inheritance

### Riesgo 2: Conflictos entre Bootstrap y estilos custom

**Probabilidad**: Alta | **Impacto**: Medio
**Mitigación**: Usar CSS modules, namespace apropiado, testing visual

### Riesgo 3: Performance issues con re-renders

**Probabilidad**: Media | **Impacto**: Alto
**Mitigación**: Usar React.memo, useMemo, useCallback apropiadamente

### Riesgo 4: Stores demasiado complejos

**Probabilidad**: Baja | **Impacto**: Alto
**Mitigación**: Mantener stores simples, separar concerns, testing extensivo

---

_Sprint creado: [Fecha]_  
_Última actualización: [Fecha]_
