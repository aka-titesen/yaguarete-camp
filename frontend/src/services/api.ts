import axios, {
  AxiosInstance,
  AxiosRequestConfig,
  AxiosResponse,
  AxiosError,
} from "axios";

/**
 * Configuración base de la API
 */
const API_BASE_URL =
  import.meta.env.VITE_API_URL || "http://localhost:8080/api";

/**
 * Tipos de error personalizados
 */
export interface ApiError {
  status: string;
  message: string;
  errors?: { [key: string]: string };
  code?: number;
  timestamp?: string;
}

export interface ApiResponse<T = any> {
  status: string;
  message: string;
  data: T;
  pagination?: {
    page: number;
    limit: number;
    total_items: number;
    total_pages: number;
    has_next: boolean;
    has_prev: boolean;
  };
  timestamp: string;
}

/**
 * Instancia principal de Axios configurada para la API
 */
const api: AxiosInstance = axios.create({
  baseURL: API_BASE_URL,
  timeout: 10000,
  headers: {
    "Content-Type": "application/json",
    Accept: "application/json",
  },
});

/**
 * Token storage utilities
 */
export const tokenStorage = {
  get: (): string | null => localStorage.getItem("access_token"),
  set: (token: string): void => localStorage.setItem("access_token", token),
  remove: (): void => localStorage.removeItem("access_token"),

  getRefresh: (): string | null => localStorage.getItem("refresh_token"),
  setRefresh: (token: string): void =>
    localStorage.setItem("refresh_token", token),
  removeRefresh: (): void => localStorage.removeItem("refresh_token"),

  clear: (): void => {
    localStorage.removeItem("access_token");
    localStorage.removeItem("refresh_token");
    localStorage.removeItem("user_data");
  },
};

/**
 * Request interceptor - Agregar token JWT automáticamente
 */
api.interceptors.request.use(
  (config: AxiosRequestConfig) => {
    const token = tokenStorage.get();

    if (token && config.headers) {
      config.headers["Authorization"] = `Bearer ${token}`;
    }

    // Log de requests en desarrollo
    if (import.meta.env.DEV) {
      console.log(`🚀 ${config.method?.toUpperCase()} ${config.url}`, {
        headers: config.headers,
        data: config.data,
        params: config.params,
      });
    }

    return config;
  },
  (error: AxiosError) => {
    if (import.meta.env.DEV) {
      console.error("❌ Request Error:", error);
    }
    return Promise.reject(error);
  }
);

/**
 * Flag para evitar múltiples intentos de refresh
 */
let isRefreshing = false;
let refreshSubscribers: Array<(token: string) => void> = [];

/**
 * Agregar suscriptor para cuando el token se renueve
 */
const subscribeTokenRefresh = (callback: (token: string) => void) => {
  refreshSubscribers.push(callback);
};

/**
 * Ejecutar todos los callbacks de renovación de token
 */
const onTokenRefreshed = (token: string) => {
  refreshSubscribers.map((callback) => callback(token));
  refreshSubscribers = [];
};

/**
 * Intentar renovar el token de acceso
 */
const refreshAccessToken = async (): Promise<string | null> => {
  try {
    const refreshToken = tokenStorage.getRefresh();

    if (!refreshToken) {
      throw new Error("No refresh token available");
    }

    const response = await axios.post(`${API_BASE_URL}/auth/refresh`, {
      refresh_token: refreshToken,
    });

    if (response.data.status === "success") {
      const newToken = response.data.data.access_token;
      tokenStorage.set(newToken);
      return newToken;
    }

    throw new Error("Failed to refresh token");
  } catch (error) {
    console.error("Error refreshing token:", error);

    // Si falla el refresh, limpiar todo y redirigir al login
    tokenStorage.clear();
    window.location.href = "/login";

    return null;
  }
};

/**
 * Response interceptor - Manejo global de errores y refresh automático
 */
api.interceptors.response.use(
  (response: AxiosResponse<ApiResponse>) => {
    // Log de responses exitosas en desarrollo
    if (import.meta.env.DEV) {
      console.log(
        `✅ ${response.config.method?.toUpperCase()} ${response.config.url}`,
        {
          status: response.status,
          data: response.data,
        }
      );
    }

    return response;
  },
  async (error: AxiosError<ApiError>) => {
    const originalRequest = error.config;

    // Log de errores en desarrollo
    if (import.meta.env.DEV) {
      console.error(
        `❌ ${originalRequest?.method?.toUpperCase()} ${originalRequest?.url}`,
        {
          status: error.response?.status,
          data: error.response?.data,
          message: error.message,
        }
      );
    }

    // Manejar error 401 (Unauthorized) con refresh automático
    if (
      error.response?.status === 401 &&
      originalRequest &&
      !originalRequest._retry
    ) {
      if (isRefreshing) {
        // Si ya se está renovando el token, esperar
        return new Promise((resolve) => {
          subscribeTokenRefresh((token: string) => {
            if (originalRequest.headers) {
              originalRequest.headers["Authorization"] = `Bearer ${token}`;
            }
            resolve(api(originalRequest));
          });
        });
      }

      originalRequest._retry = true;
      isRefreshing = true;

      try {
        const newToken = await refreshAccessToken();

        if (newToken) {
          isRefreshing = false;
          onTokenRefreshed(newToken);

          if (originalRequest.headers) {
            originalRequest.headers["Authorization"] = `Bearer ${newToken}`;
          }

          return api(originalRequest);
        }
      } catch (refreshError) {
        isRefreshing = false;
        return Promise.reject(refreshError);
      }
    }

    // Para otros errores, devolver error formateado
    const apiError: ApiError = {
      status: "error",
      message:
        error.response?.data?.message || error.message || "Error de conexión",
      errors: error.response?.data?.errors,
      code: error.response?.status,
      timestamp: new Date().toISOString(),
    };

    return Promise.reject(apiError);
  }
);

/**
 * Retry automático para fallos temporales
 */
const retryRequest = async (
  requestFn: () => Promise<AxiosResponse>,
  maxRetries = 3,
  delay = 1000
): Promise<AxiosResponse> => {
  let lastError: any;

  for (let attempt = 1; attempt <= maxRetries; attempt++) {
    try {
      return await requestFn();
    } catch (error: any) {
      lastError = error;

      // Solo reintentar para errores de red o 5xx
      const shouldRetry =
        !error.response ||
        (error.response.status >= 500 && error.response.status < 600) ||
        error.code === "NETWORK_ERROR" ||
        error.code === "TIMEOUT";

      if (!shouldRetry || attempt === maxRetries) {
        throw error;
      }

      // Esperar antes del siguiente intento con backoff exponencial
      await new Promise((resolve) =>
        setTimeout(resolve, delay * Math.pow(2, attempt - 1))
      );
    }
  }

  throw lastError;
};

/**
 * Wrapper para requests con retry automático
 */
export const apiWithRetry = {
  get: <T>(url: string, config?: AxiosRequestConfig) =>
    retryRequest(() => api.get<ApiResponse<T>>(url, config)),

  post: <T>(url: string, data?: any, config?: AxiosRequestConfig) =>
    retryRequest(() => api.post<ApiResponse<T>>(url, data, config)),

  put: <T>(url: string, data?: any, config?: AxiosRequestConfig) =>
    retryRequest(() => api.put<ApiResponse<T>>(url, data, config)),

  delete: <T>(url: string, config?: AxiosRequestConfig) =>
    retryRequest(() => api.delete<ApiResponse<T>>(url, config)),
};

export default api;
