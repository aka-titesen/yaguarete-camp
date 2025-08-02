# 🎨 Sistema de Colores Avanzado - Yaguareté Camp

## 🌿 **Color Principal: Verde Navbar**

**Base**: `#2A5C45` (Verde Selva - Color navbar actual)

---

## 🎯 **Paleta Completa Generada**

### **Verde Principal (Identidad)**

```scss
// Verde base del navbar
$green-primary: #2a5c45; // Color principal navbar
$green-50: #f0f8f4; // Verde muy claro (fondos sutiles)
$green-100: #dcf1e6; // Verde claro (hover suave)
$green-200: #b8e3ce; // Verde medio claro
$green-300: #8dd4b1; // Verde medio
$green-400: #5bbe8e; // Verde vibrante
$green-500: #2a5c45; // Verde principal (navbar)
$green-600: #245240; // Verde oscuro (hover activo)
$green-700: #1e463a; // Verde más oscuro
$green-800: #183a33; // Verde muy oscuro
$green-900: #122e2b; // Verde profundo (textos)
```

### **Amarillo Terroso (Secundario)**

```scss
// Basado en el amarillo actual #D4A418
$yellow-primary: #d4a418; // Amarillo terroso actual
$yellow-50: #fef8e7; // Amarillo muy claro
$yellow-100: #fdf1c7; // Amarillo claro (fondos)
$yellow-200: #fce68a; // Amarillo suave
$yellow-300: #fadb5f; // Amarillo medio
$yellow-400: #f7d034; // Amarillo vibrante
$yellow-500: #d4a418; // Amarillo principal
$yellow-600: #b08914; // Amarillo oscuro
$yellow-700: #8c6e10; // Amarillo más oscuro
$yellow-800: #68530c; // Amarillo muy oscuro
$yellow-900: #443808; // Amarillo profundo
```

### **Naranja Ardiente (Acento)**

```scss
// Basado en el naranja actual #E67E22
$orange-primary: #e67e22; // Naranja ardiente actual
$orange-50: #fff5f0; // Naranja muy claro
$orange-100: #ffead9; // Naranja claro
$orange-200: #ffd4b2; // Naranja suave
$orange-300: #ffbe8a; // Naranja medio
$orange-400: #ffa762; // Naranja vibrante
$orange-500: #e67e22; // Naranja principal
$orange-600: #c2661d; // Naranja oscuro
$orange-700: #9e4f18; // Naranja más oscuro
$orange-800: #7a3713; // Naranja muy oscuro
$orange-900: #56200e; // Naranja profundo
```

### **Azul Profundo (Complemento)**

```scss
// Basado en el azul actual #2C3E50
$blue-primary: #2c3e50; // Azul profundo actual
$blue-50: #f1f4f7; // Azul muy claro
$blue-100: #e2e9f1; // Azul claro
$blue-200: #c4d2e3; // Azul suave
$blue-300: #a7bcd5; // Azul medio
$blue-400: #89a5c7; // Azul vibrante
$blue-500: #2c3e50; // Azul principal
$blue-600: #253544; // Azul oscuro
$blue-700: #1e2c38; // Azul más oscuro
$blue-800: #17232c; // Azul muy oscuro
$blue-900: #101a20; // Azul profundo
```

### **Neutros Modernos**

```scss
// Escala de grises cálidos (con tinte verde)
$neutral-50: #fafbfa; // Blanco verdoso
$neutral-100: #f4f6f4; // Gris muy claro
$neutral-200: #e8ece8; // Gris claro
$neutral-300: #d1d8d1; // Gris medio claro
$neutral-400: #9ca89c; // Gris medio
$neutral-500: #6b786b; // Gris principal
$neutral-600: #556055; // Gris oscuro
$neutral-700: #404940; // Gris más oscuro
$neutral-800: #2b322b; // Gris muy oscuro
$neutral-900: #161b16; // Negro verdoso
```

---

## 🎛️ **Estados y Variantes por Componente**

### **Botones - Sistema Completo**

```scss
// Botón Primario (Verde)
.btn-primary {
  background: $green-500;
  border: 1px solid $green-600;
  color: white;

  &:hover {
    background: linear-gradient(135deg, $green-600 0%, $green-700 100%);
    border-color: $green-700;
    box-shadow: 0 4px 12px rgba($green-500, 0.25);
    transform: translateY(-1px);
  }

  &:active {
    background: $green-700;
    border-color: $green-800;
    transform: translateY(0);
    box-shadow: 0 2px 4px rgba($green-500, 0.15);
  }

  &:focus {
    box-shadow: 0 0 0 3px rgba($green-500, 0.25);
    outline: none;
  }

  &:disabled {
    background: $neutral-300;
    border-color: $neutral-400;
    color: $neutral-500;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
  }
}

// Botón Secundario (Amarillo)
.btn-secondary {
  background: $yellow-500;
  border: 1px solid $yellow-600;
  color: $green-900;

  &:hover {
    background: linear-gradient(135deg, $yellow-400 0%, $yellow-500 100%);
    box-shadow: 0 4px 12px rgba($yellow-500, 0.3);
  }
}

// Botón Outline
.btn-outline-primary {
  background: transparent;
  border: 2px solid $green-500;
  color: $green-500;

  &:hover {
    background: $green-500;
    color: white;
    box-shadow: 0 4px 12px rgba($green-500, 0.25);
  }
}

// Botón Ghost
.btn-ghost {
  background: transparent;
  border: none;
  color: $green-500;

  &:hover {
    background: rgba($green-500, 0.1);
    color: $green-600;
  }
}

// Botón Danger
.btn-danger {
  background: #dc3545;
  border: 1px solid #c82333;
  color: white;

  &:hover {
    background: linear-gradient(135deg, #c82333 0%, #a71e2a 100%);
    box-shadow: 0 4px 12px rgba(#dc3545, 0.25);
  }
}
```

### **Inputs y Formularios**

```scss
// Input Base
.form-control {
  background: $neutral-50;
  border: 2px solid $neutral-200;
  color: $neutral-800;
  border-radius: 8px;
  transition: all 0.2s ease;

  &:focus {
    background: white;
    border-color: $green-500;
    box-shadow: 0 0 0 3px rgba($green-500, 0.1);
    outline: none;
  }

  &:hover {
    border-color: $neutral-300;
  }

  &.is-invalid {
    border-color: #dc3545;
    background: #fff5f5;

    &:focus {
      box-shadow: 0 0 0 3px rgba(#dc3545, 0.1);
    }
  }

  &.is-valid {
    border-color: $green-500;
    background: rgba($green-50, 0.5);

    &:focus {
      box-shadow: 0 0 0 3px rgba($green-500, 0.1);
    }
  }

  &:disabled {
    background: $neutral-100;
    border-color: $neutral-200;
    color: $neutral-400;
    cursor: not-allowed;
  }
}

// Labels
.form-label {
  color: $neutral-700;
  font-weight: 500;
  margin-bottom: 0.5rem;

  &.required::after {
    content: " *";
    color: #dc3545;
  }
}

// Search Input Especializado
.search-input {
  background: white;
  border: 2px solid $neutral-200;
  border-radius: 25px;
  padding: 12px 20px 12px 45px;

  &:focus {
    border-color: $green-500;
    box-shadow: 0 0 0 3px rgba($green-500, 0.1);
  }

  &::placeholder {
    color: $neutral-400;
  }
}
```

### **Cards y Productos**

```scss
// Card Base
.card {
  background: white;
  border: 1px solid $neutral-200;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba($neutral-500, 0.08);
  transition: all 0.3s ease;
  overflow: hidden;

  &:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba($neutral-500, 0.15);
    border-color: $green-200;
  }
}

// Product Card Específico
.product-card {
  position: relative;

  .product-image {
    position: relative;
    overflow: hidden;

    img {
      transition: transform 0.3s ease;
    }

    &:hover img {
      transform: scale(1.05);
    }
  }

  .product-badge {
    position: absolute;
    top: 12px;
    right: 12px;

    &.sale {
      background: linear-gradient(135deg, $orange-500 0%, $orange-600 100%);
      color: white;
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 0.85rem;
      font-weight: 600;
    }

    &.new {
      background: linear-gradient(135deg, $green-500 0%, $green-600 100%);
      color: white;
    }

    &.out-of-stock {
      background: linear-gradient(135deg, $neutral-500 0%, $neutral-600 100%);
      color: white;
    }
  }

  .product-price {
    color: $green-600;
    font-weight: 700;
    font-size: 1.25rem;

    .original-price {
      color: $neutral-400;
      text-decoration: line-through;
      font-size: 0.9rem;
      margin-right: 8px;
    }
  }

  .product-actions {
    .btn-add-cart {
      background: linear-gradient(135deg, $green-500 0%, $green-600 100%);
      border: none;
      color: white;
      border-radius: 25px;
      padding: 8px 20px;
      font-weight: 500;

      &:hover {
        background: linear-gradient(135deg, $green-600 0%, $green-700 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba($green-500, 0.3);
      }
    }

    .btn-favorite {
      background: transparent;
      border: 2px solid $neutral-300;
      color: $neutral-400;
      border-radius: 50%;
      width: 40px;
      height: 40px;

      &:hover {
        border-color: $orange-500;
        color: $orange-500;
      }

      &.active {
        background: $orange-500;
        border-color: $orange-500;
        color: white;
      }
    }
  }
}
```

### **Navegación y Headers**

```scss
// Navbar Principal
.navbar-main {
  background: linear-gradient(90deg, $green-500 0%, $green-600 100%);
  backdrop-filter: blur(10px);
  border-bottom: 1px solid rgba($green-700, 0.3);

  .navbar-brand {
    color: white;
    font-weight: 700;

    &:hover {
      color: $yellow-300;
    }
  }

  .nav-link {
    color: rgba(white, 0.9);
    font-weight: 500;
    transition: all 0.2s ease;

    &:hover {
      color: $yellow-300;
      text-shadow: 0 1px 3px rgba(black, 0.2);
    }

    &.active {
      color: $yellow-400;
      font-weight: 600;
    }
  }

  .cart-icon {
    position: relative;

    .cart-count {
      position: absolute;
      top: -8px;
      right: -8px;
      background: linear-gradient(135deg, $orange-500 0%, $orange-600 100%);
      color: white;
      border-radius: 50%;
      width: 20px;
      height: 20px;
      font-size: 0.75rem;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 600;
    }
  }
}

// Breadcrumbs
.breadcrumb {
  background: linear-gradient(135deg, $neutral-50 0%, $neutral-100 100%);
  border-radius: 25px;
  padding: 12px 20px;

  .breadcrumb-item {
    color: $neutral-600;

    &.active {
      color: $green-600;
      font-weight: 500;
    }

    a {
      color: $green-500;
      text-decoration: none;

      &:hover {
        color: $green-600;
        text-decoration: underline;
      }
    }
  }
}
```

---

## 🌈 **Gradientes Modernos**

### **Gradientes Principales**

```scss
// Gradientes de fondo
$gradient-hero: linear-gradient(
  135deg,
  rgba($green-500, 0.95) 0%,
  rgba($green-600, 0.9) 50%,
  rgba($blue-600, 0.85) 100%
);

$gradient-section: linear-gradient(
  160deg,
  $neutral-50 0%,
  rgba($green-50, 0.5) 50%,
  $neutral-100 100%
);

$gradient-card: linear-gradient(180deg, white 0%, $neutral-50 100%);

// Gradientes de botones
$gradient-btn-primary: linear-gradient(135deg, $green-500 0%, $green-600 100%);

$gradient-btn-secondary: linear-gradient(
  135deg,
  $yellow-500 0%,
  $yellow-600 100%
);

$gradient-btn-accent: linear-gradient(135deg, $orange-500 0%, $orange-600 100%);

// Gradientes de overlays
$gradient-overlay-dark: linear-gradient(
  180deg,
  transparent 0%,
  rgba($green-900, 0.7) 100%
);

$gradient-overlay-light: linear-gradient(
  180deg,
  rgba(white, 0.9) 0%,
  transparent 100%
);
```

---

## 💫 **Efectos de Loading y Skeletons**

### **Skeletons con Shimmer**

```scss
// Skeleton Base
.skeleton {
  background: linear-gradient(
    90deg,
    $neutral-200 25%,
    $neutral-100 50%,
    $neutral-200 75%
  );
  background-size: 200% 100%;
  animation: shimmer 1.5s infinite;
  border-radius: 4px;
}

@keyframes shimmer {
  0% {
    background-position: -200% 0;
  }
  100% {
    background-position: 200% 0;
  }
}

// Skeleton Variants
.skeleton-text {
  height: 1rem;
  margin-bottom: 0.5rem;

  &.title {
    height: 1.5rem;
    width: 70%;
  }

  &.subtitle {
    height: 1.25rem;
    width: 50%;
  }

  &.line {
    width: 100%;
  }

  &.short {
    width: 60%;
  }
}

.skeleton-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
}

.skeleton-image {
  width: 100%;
  aspect-ratio: 16/9;
  border-radius: 8px;
}

.skeleton-button {
  height: 40px;
  width: 120px;
  border-radius: 6px;
}

// Product Card Skeleton
.skeleton-product-card {
  .skeleton-image {
    aspect-ratio: 1;
    margin-bottom: 1rem;
  }

  .skeleton-title {
    height: 1.25rem;
    width: 80%;
    margin-bottom: 0.5rem;
  }

  .skeleton-price {
    height: 1.5rem;
    width: 40%;
    margin-bottom: 1rem;
  }

  .skeleton-button {
    width: 100%;
  }
}
```

### **Loading Spinners**

```scss
// Spinner Principal
.spinner {
  width: 40px;
  height: 40px;
  border: 4px solid $neutral-200;
  border-top: 4px solid $green-500;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}

// Dots Loader
.dots-loader {
  display: flex;
  gap: 4px;

  .dot {
    width: 8px;
    height: 8px;
    background: $green-500;
    border-radius: 50%;
    animation: dot-bounce 1.4s ease-in-out infinite both;

    &:nth-child(1) {
      animation-delay: -0.32s;
    }
    &:nth-child(2) {
      animation-delay: -0.16s;
    }
    &:nth-child(3) {
      animation-delay: 0s;
    }
  }
}

@keyframes dot-bounce {
  0%,
  80%,
  100% {
    transform: scale(0);
  }
  40% {
    transform: scale(1);
  }
}

// Progress Bar
.progress-bar {
  background: linear-gradient(90deg, $green-500 0%, $green-600 100%);
  height: 6px;
  border-radius: 3px;
  transition: width 0.3s ease;

  &.animated {
    background: linear-gradient(
      90deg,
      $green-500 0%,
      $green-400 50%,
      $green-500 100%
    );
    background-size: 200% 100%;
    animation: progress-shine 1.5s infinite;
  }
}

@keyframes progress-shine {
  0% {
    background-position: -200% 0;
  }
  100% {
    background-position: 200% 0;
  }
}
```

### **Glass Morphism y Blur Effects**

```scss
// Glass Card
.glass-card {
  background: rgba(white, 0.25);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(white, 0.18);
  box-shadow: 0 8px 32px rgba($green-500, 0.1);
  border-radius: 16px;
}

// Glass Navbar
.glass-navbar {
  background: rgba($green-500, 0.9);
  backdrop-filter: blur(15px);
  border-bottom: 1px solid rgba($green-600, 0.3);
}

// Blur Overlay
.blur-overlay {
  background: rgba($neutral-900, 0.5);
  backdrop-filter: blur(5px);
  position: fixed;
  inset: 0;
  z-index: 1000;
}

// Frosted Glass Effect
.frosted-glass {
  background: linear-gradient(
    135deg,
    rgba(white, 0.2) 0%,
    rgba(white, 0.1) 100%
  );
  backdrop-filter: blur(20px);
  border: 1px solid rgba(white, 0.2);
  box-shadow: 0 8px 32px rgba($green-500, 0.1), inset 0 1px 0 rgba(white, 0.3);
}
```

---

## 🎯 **Implementación CSS Custom Properties**

```scss
:root {
  // Colores principales
  --color-primary: #{$green-500};
  --color-primary-light: #{$green-400};
  --color-primary-dark: #{$green-600};

  --color-secondary: #{$yellow-500};
  --color-accent: #{$orange-500};

  // Estados
  --color-success: #{$green-500};
  --color-warning: #{$yellow-500};
  --color-danger: #dc3545;
  --color-info: #{$blue-500};

  // Neutros
  --color-text: #{$neutral-800};
  --color-text-light: #{$neutral-600};
  --color-text-muted: #{$neutral-400};

  --color-bg: white;
  --color-bg-light: #{$neutral-50};
  --color-bg-dark: #{$neutral-100};

  // Gradientes
  --gradient-primary: #{$gradient-btn-primary};
  --gradient-hero: #{$gradient-hero};
  --gradient-section: #{$gradient-section};

  // Sombras
  --shadow-sm: 0 2px 4px rgba(#{$neutral-500}, 0.1);
  --shadow-md: 0 4px 12px rgba(#{$neutral-500}, 0.15);
  --shadow-lg: 0 8px 25px rgba(#{$neutral-500}, 0.2);
  --shadow-xl: 0 16px 40px rgba(#{$neutral-500}, 0.25);

  // Transiciones
  --transition-fast: 0.15s ease;
  --transition-normal: 0.2s ease;
  --transition-slow: 0.3s ease;
}

// Dark mode support
@media (prefers-color-scheme: dark) {
  :root {
    --color-bg: #{$neutral-900};
    --color-bg-light: #{$neutral-800};
    --color-bg-dark: #{$neutral-700};

    --color-text: #{$neutral-100};
    --color-text-light: #{$neutral-300};
    --color-text-muted: #{$neutral-400};
  }
}
```

---

## ✅ **Checklist de Implementación**

### **Fase 1: Colores Base (Sprint 1)**

- [ ] Crear variables SCSS con todas las escalas de color
- [ ] Implementar CSS custom properties
- [ ] Configurar modo oscuro básico
- [ ] Documentar paleta en Storybook

### **Fase 2: Componentes Base (Sprint 3)**

- [ ] Aplicar colores a botones con todos los estados
- [ ] Styling completo de formularios e inputs
- [ ] Cards con efectos hover y estados
- [ ] Navegación con gradientes y efectos

### **Fase 3: Estados Avanzados (Sprint 3-4)**

- [ ] Implementar skeletons con shimmer
- [ ] Loading spinners y progress bars
- [ ] Glass morphism effects
- [ ] Hover y focus states refinados

### **Fase 4: Optimización (Sprint 8)**

- [ ] Purgar colores no utilizados
- [ ] Optimizar gradientes y efectos
- [ ] Testing de contraste (WCAG AA)
- [ ] Performance de animaciones

---

_Sistema diseñado para e-commerce outdoor moderno y sofisticado_  
_Última actualización: [Fecha]_
