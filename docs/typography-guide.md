# 🎨 Guía de Tipografía y Estilos - Yaguareté Camp

## 📖 Nueva Familia Tipográfica

### 🎯 **Tipografías Seleccionadas**

#### **Inter** - Tipografía Principal

- **Uso**: Textos generales, UI, navegación, formularios
- **Características**: Moderna, altamente legible, optimizada para pantallas
- **Weights**: 300 (Light), 400 (Regular), 500 (Medium), 600 (Semi-bold), 700 (Bold)
- **Razón**: Excelente legibilidad en todos los tamaños, diseñada específicamente para interfaces digitales

#### **Montserrat** - Tipografía para Títulos

- **Uso**: Títulos principales, CTAs, headers, branding
- **Características**: Geométrica, fuerte personalidad, impactante
- **Weights**: 400 (Regular), 500 (Medium), 600 (Semi-bold), 700 (Bold), 800 (Extra-bold)
- **Razón**: Perfecta para captar atención, transmite profesionalismo y modernidad

#### **Source Code Pro** - Tipografía Técnica (Opcional)

- **Uso**: Códigos de producto, números de orden, datos técnicos
- **Características**: Monospace, altamente legible para datos
- **Weights**: 400 (Regular), 500 (Medium), 600 (Semi-bold)

---

## 🎨 Sistema de Colores Outdoor

### **Paleta Principal**

```scss
// Identidad de marca
$verde-selva: #2e5d3b; // Color principal - Naturaleza
$dorado-tierra: #e1b91a; // Color secundario - Aventura
$marron-corteza: #8b4513; // Color terciario - Madera

// Colores de apoyo
$verde-musgo: #5a7c5a; // Verde más suave
$beige-arena: #d2b48c; // Neutro cálido
$gris-piedra: #696969; // Neutro frío
```

### **Paleta Extendida**

```scss
// Feedback colors
$success: #28a745; // Verde éxito
$warning: #ffc107; // Amarillo advertencia
$danger: #dc3545; // Rojo error
$info: #17a2b8; // Azul información

// Escala de grises
$white: #ffffff;
$gray-50: #f8f9fa;
$gray-100: #f1f3f4;
$gray-200: #e9ecef;
$gray-300: #dee2e6;
$gray-400: #ced4da;
$gray-500: #adb5bd;
$gray-600: #6c757d;
$gray-700: #495057;
$gray-800: #343a40;
$gray-900: #212529;
$black: #000000;
```

---

## 📏 Escala Tipográfica

### **Tamaños de Fuente**

```scss
// Mobile First approach
$font-size-xs: 0.75rem; // 12px - Metadatos
$font-size-sm: 0.875rem; // 14px - Texto secundario
$font-size-base: 1rem; // 16px - Texto base
$font-size-lg: 1.125rem; // 18px - Texto destacado
$font-size-xl: 1.25rem; // 20px - Subtítulos
$font-size-2xl: 1.5rem; // 24px - Títulos H3
$font-size-3xl: 1.875rem; // 30px - Títulos H2
$font-size-4xl: 2.25rem; // 36px - Títulos H1
$font-size-5xl: 3rem; // 48px - Hero titles
$font-size-6xl: 3.75rem; // 60px - Display titles
```

### **Line Heights**

```scss
$line-height-tight: 1.25; // Títulos
$line-height-snug: 1.375; // Subtítulos
$line-height-normal: 1.5; // Texto base
$line-height-relaxed: 1.625; // Texto largo
$line-height-loose: 2; // Texto espaciado
```

### **Font Weights**

```scss
$font-weight-light: 300; // Texto sutil
$font-weight-normal: 400; // Texto normal
$font-weight-medium: 500; // Texto medio
$font-weight-semibold: 600; // Texto destacado
$font-weight-bold: 700; // Texto fuerte
$font-weight-extrabold: 800; // Títulos impactantes
```

---

## 🎯 Uso de Tipografías por Contexto

### **E-commerce Específico**

#### **Productos**

- **Nombre de producto**: Montserrat Medium 18px
- **Precio**: Montserrat Bold 20px (verde-selva)
- **Descripción**: Inter Regular 14px
- **Especificaciones**: Inter Regular 13px

#### **Navegación**

- **Menu principal**: Inter Medium 16px
- **Breadcrumbs**: Inter Regular 14px (gray-600)
- **Enlaces footer**: Inter Regular 14px

#### **Formularios**

- **Labels**: Inter Medium 14px
- **Input text**: Inter Regular 16px
- **Help text**: Inter Regular 13px (gray-600)
- **Error messages**: Inter Medium 13px (danger)

#### **CTAs y Botones**

- **Botón primario**: Montserrat Semi-bold 16px
- **Botón secundario**: Inter Medium 16px
- **Links**: Inter Medium 16px

---

## 📱 Responsive Typography

### **Breakpoint Scaling**

```scss
// Títulos responsive
h1,
.h1 {
  font-size: $font-size-3xl;
  @include respond-to(md) {
    font-size: $font-size-4xl;
  }
  @include respond-to(lg) {
    font-size: $font-size-5xl;
  }
}

h2,
.h2 {
  font-size: $font-size-2xl;
  @include respond-to(md) {
    font-size: $font-size-3xl;
  }
}

// Texto base responsive
.text-responsive {
  font-size: $font-size-sm;
  @include respond-to(md) {
    font-size: $font-size-base;
  }
  @include respond-to(lg) {
    font-size: $font-size-lg;
  }
}
```

---

## 🎨 Aplicación de Colores

### **Jerarquía Visual**

1. **Verde Selva** (#2e5d3b): CTAs principales, navegación activa, precios
2. **Dorado Tierra** (#e1b91a): Badges especiales, ofertas, iconos destacados
3. **Marrón Corteza** (#8b4513): Enlaces secundarios, decoraciones
4. **Grises**: Textos generales, bordes, fondos

### **Estados Interactivos**

```scss
// Botones
.btn-primary {
  background-color: $verde-selva;
  &:hover {
    background-color: darken($verde-selva, 10%);
  }
  &:active {
    background-color: darken($verde-selva, 15%);
  }
}

// Enlaces
a {
  color: $verde-selva;
  &:hover {
    color: $dorado-tierra;
    text-decoration: underline;
  }
}
```

---

## 🔧 Implementación Técnica

### **Carga de Fuentes**

```html
<!-- En el HTML head -->
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link
  href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Montserrat:wght@400;500;600;700;800&display=swap"
  rel="stylesheet"
/>
```

### **CSS Variables**

```css
:root {
  /* Tipografías */
  --font-primary: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
  --font-headings: "Montserrat", Georgia, serif;
  --font-mono: "Source Code Pro", "Monaco", "Consolas", monospace;

  /* Colores */
  --color-primary: #2e5d3b;
  --color-secondary: #e1b91a;
  --color-accent: #8b4513;

  /* Espaciado */
  --spacing-xs: 0.25rem;
  --spacing-sm: 0.5rem;
  --spacing-md: 1rem;
  --spacing-lg: 1.5rem;
  --spacing-xl: 3rem;
}
```

### **Utilidades SCSS**

```scss
// Mixins para consistencia
@mixin text-outdoor-title {
  font-family: $font-headings;
  font-weight: $font-weight-semibold;
  color: $verde-selva;
  line-height: $line-height-tight;
}

@mixin text-outdoor-body {
  font-family: $font-primary;
  font-weight: $font-weight-normal;
  color: $gray-700;
  line-height: $line-height-normal;
}

@mixin text-outdoor-price {
  font-family: $font-headings;
  font-weight: $font-weight-bold;
  color: $verde-selva;
  font-size: $font-size-xl;
}
```

---

## 📐 Espaciado y Layout

### **Sistema de Espaciado**

```scss
$spacer: 1rem; // 16px base

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
  6: $spacer * 4,
  // 64px
  7: $spacer * 5,
  // 80px
  8: $spacer * 6 // 96px,
);
```

### **Contenedores**

```scss
.container-outdoor {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 $spacer;

  @include respond-to(sm) {
    padding: 0 $spacer * 1.5;
  }

  @include respond-to(lg) {
    padding: 0 $spacer * 2;
  }
}
```

---

## ✅ Checklist de Implementación

### **Fase 1: Setup Básico**

- [ ] Configurar imports de Google Fonts
- [ ] Crear variables SCSS de tipografía
- [ ] Crear variables SCSS de colores
- [ ] Configurar CSS custom properties

### **Fase 2: Componentes Base**

- [ ] Aplicar tipografías a componentes básicos
- [ ] Crear mixins para patrones comunes
- [ ] Implementar escalado responsive
- [ ] Testear en diferentes dispositivos

### **Fase 3: Optimización**

- [ ] Optimizar carga de fuentes (font-display: swap)
- [ ] Eliminar pesos de fuente no utilizados
- [ ] Comprimir y optimizar CSS final
- [ ] Validar contraste de colores (WCAG AA)

---

## 🎯 Beneficios Esperados

### **UX Mejorada**

- **Legibilidad superior**: Inter optimizada para pantallas
- **Jerarquía clara**: Montserrat para destacar elementos importantes
- **Consistencia visual**: Sistema de colores coherente
- **Responsive perfecto**: Escalado apropiado en todos los dispositivos

### **Performance**

- **Carga optimizada**: Solo los pesos necesarios
- **Fallbacks apropiados**: System fonts como backup
- **Compresión**: WOFF2 para mejor performance

### **Branding**

- **Identidad moderna**: Tipografías actuales y profesionales
- **Tema outdoor**: Colores que evocan naturaleza y aventura
- **Diferenciación**: Se distingue de competidores genéricos

---

_Última actualización: [Fecha]_
