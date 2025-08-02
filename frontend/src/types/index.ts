// ===================================================================
// TIPOS GLOBALES - YAGUARETÉ CAMP
// ===================================================================

// ===================================================================
// 1. TIPOS DE USUARIO Y AUTENTICACIÓN
// ===================================================================

export interface User {
  id: number;
  name: string;
  email: string;
  avatar?: string;
  role: UserRole;
  createdAt: string;
  updatedAt: string;
  isActive: boolean;
}

export type UserRole = "admin" | "moderator" | "user" | "guest";

export interface AuthState {
  user: User | null;
  isAuthenticated: boolean;
  isLoading: boolean;
  token: string | null;
}

export interface LoginCredentials {
  email: string;
  password: string;
  remember?: boolean;
}

export interface RegisterData {
  name: string;
  email: string;
  password: string;
  confirmPassword: string;
}

// ===================================================================
// 2. TIPOS DE RESPUESTA DE API
// ===================================================================

export interface ApiResponse<T = any> {
  success: boolean;
  data?: T;
  message?: string;
  errors?: Record<string, string[]>;
  meta?: {
    pagination?: PaginationMeta;
    total?: number;
    page?: number;
    perPage?: number;
  };
}

export interface PaginationMeta {
  currentPage: number;
  totalPages: number;
  totalItems: number;
  itemsPerPage: number;
  hasNextPage: boolean;
  hasPreviousPage: boolean;
}

export interface PaginatedResponse<T> {
  data: T[];
  meta: PaginationMeta;
}

// ===================================================================
// 3. TIPOS DE FORMULARIOS
// ===================================================================

export interface FormField {
  name: string;
  label: string;
  type: FormFieldType;
  value: any;
  placeholder?: string;
  required?: boolean;
  disabled?: boolean;
  options?: FormOption[];
  validation?: ValidationRules;
}

export type FormFieldType =
  | "text"
  | "email"
  | "password"
  | "number"
  | "textarea"
  | "select"
  | "checkbox"
  | "radio"
  | "file"
  | "date"
  | "datetime-local";

export interface FormOption {
  value: string | number;
  label: string;
  disabled?: boolean;
}

export interface ValidationRules {
  min?: number;
  max?: number;
  minLength?: number;
  maxLength?: number;
  pattern?: RegExp;
  custom?: (value: any) => string | null;
}

export interface FormErrors {
  [fieldName: string]: string | undefined;
}

// ===================================================================
// 4. TIPOS DE COMPONENTES UI
// ===================================================================

export type ButtonVariant =
  | "primary"
  | "secondary"
  | "success"
  | "warning"
  | "danger"
  | "info"
  | "outline-primary"
  | "outline-secondary"
  | "ghost"
  | "gradient";

export type ButtonSize = "sm" | "md" | "lg";

export type AlertType = "success" | "warning" | "danger" | "info";

export interface Toast {
  id: string;
  type: AlertType;
  title?: string;
  message: string;
  duration?: number;
  closeable?: boolean;
}

export interface Modal {
  id: string;
  isOpen: boolean;
  title?: string;
  size?: "sm" | "md" | "lg" | "xl";
  closeable?: boolean;
  backdrop?: boolean;
}

// ===================================================================
// 5. TIPOS DE NAVEGACIÓN
// ===================================================================

export interface NavItem {
  id: string;
  label: string;
  path?: string;
  icon?: string;
  children?: NavItem[];
  isActive?: boolean;
  isDisabled?: boolean;
  badge?: {
    text: string;
    variant: "primary" | "secondary" | "success" | "warning" | "danger";
  };
}

export interface Breadcrumb {
  label: string;
  path?: string;
  isActive?: boolean;
}

// ===================================================================
// 6. TIPOS DE ESTADO DE LOADING
// ===================================================================

export type LoadingState = "idle" | "loading" | "success" | "error";

export interface AsyncState<T = any> {
  data: T | null;
  loading: boolean;
  error: string | null;
  lastFetch?: string;
}

export interface SkeletonConfig {
  rows?: number;
  avatar?: boolean;
  title?: boolean;
  paragraph?: boolean;
  loading?: boolean;
}

// ===================================================================
// 7. TIPOS DE CONFIGURACIÓN
// ===================================================================

export interface AppConfig {
  apiUrl: string;
  appName: string;
  version: string;
  environment: "development" | "staging" | "production";
  features: {
    darkMode: boolean;
    notifications: boolean;
    analytics: boolean;
  };
}

export interface ThemeConfig {
  mode: "light" | "dark" | "auto";
  primaryColor: string;
  secondaryColor: string;
  borderRadius: "none" | "sm" | "md" | "lg" | "full";
  animations: boolean;
}

// ===================================================================
// 8. TIPOS DE EVENTOS
// ===================================================================

export interface BaseEvent {
  id: string;
  type: string;
  timestamp: string;
  userId?: number;
}

export interface ClickEvent extends BaseEvent {
  type: "click";
  elementId: string;
  coordinates: {
    x: number;
    y: number;
  };
}

export interface FormSubmitEvent extends BaseEvent {
  type: "form_submit";
  formId: string;
  formData: Record<string, any>;
  success: boolean;
}

// ===================================================================
// 9. TIPOS DE FILTROS Y BÚSQUEDA
// ===================================================================

export interface SearchFilters {
  query?: string;
  category?: string;
  status?: string;
  dateFrom?: string;
  dateTo?: string;
  sortBy?: string;
  sortOrder?: "asc" | "desc";
  page?: number;
  perPage?: number;
}

export interface SearchResult<T = any> {
  items: T[];
  total: number;
  query: string;
  filters: SearchFilters;
  searchTime: number;
}

// ===================================================================
// 10. TIPOS UTILITARIOS
// ===================================================================

// Hacer todas las propiedades opcionales
export type Partial<T> = {
  [P in keyof T]?: T[P];
};

// Hacer todas las propiedades requeridas
export type Required<T> = {
  [P in keyof T]-?: T[P];
};

// Omitir propiedades específicas
export type Omit<T, K extends keyof T> = Pick<T, Exclude<keyof T, K>>;

// Seleccionar solo propiedades específicas
export type Pick<T, K extends keyof T> = {
  [P in K]: T[P];
};

// Tipo para IDs
export type ID = string | number;

// Tipo para timestamps
export type Timestamp = string; // ISO 8601 format

// Tipo para colores CSS
export type CSSColor = string;

// Tipo para callbacks vacíos
export type VoidCallback = () => void;

// Tipo para callbacks con parámetro
export type Callback<T = any> = (data: T) => void;

// ===================================================================
// 11. TIPOS ESPECÍFICOS DEL PROYECTO
// ===================================================================

// Estos tipos se pueden expandir según las necesidades específicas
// del proyecto Yaguareté Camp

export interface ProjectEntity {
  id: ID;
  name: string;
  description?: string;
  createdAt: Timestamp;
  updatedAt: Timestamp;
  createdBy: ID;
}

export interface ProjectStats {
  totalUsers: number;
  activeUsers: number;
  totalContent: number;
  growthRate: number;
}

// ===================================================================
// 12. DECLARACIONES DE MÓDULOS
// ===================================================================

declare module "*.scss" {
  const content: Record<string, string>;
  export default content;
}

declare module "*.css" {
  const content: Record<string, string>;
  export default content;
}

declare module "*.svg" {
  const content: React.FunctionComponent<React.SVGAttributes<SVGElement>>;
  export default content;
}

declare module "*.png" {
  const content: string;
  export default content;
}

declare module "*.jpg" {
  const content: string;
  export default content;
}

declare module "*.jpeg" {
  const content: string;
  export default content;
}

declare module "*.gif" {
  const content: string;
  export default content;
}

declare module "*.webp" {
  const content: string;
  export default content;
}
