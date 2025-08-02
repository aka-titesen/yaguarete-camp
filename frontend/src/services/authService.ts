import api, { type ApiResponse, tokenStorage } from "./api";

export interface LoginCredentials {
  email: string;
  password: string;
}

export interface RegisterData {
  nombre: string;
  apellido: string;
  email: string;
  password: string;
  password_confirmacion: string;
  telefono?: string;
  direccion?: string;
}

export interface User {
  id: number;
  nombre: string;
  apellido: string;
  email: string;
  perfil_id: number;
  telefono?: string;
  direccion?: string;
  fecha_alta?: string;
  estadisticas?: {
    total_compras: number;
    total_gastado: number;
    total_gastado_formateado: string;
  };
}

export interface AuthResponse {
  user: User;
  access_token: string;
  refresh_token: string;
  expires_in: number;
  token_type: string;
}

export interface RefreshTokenRequest {
  refresh_token: string;
}

export interface RefreshTokenResponse {
  access_token: string;
  expires_in: number;
  token_type: string;
}

/**
 * Servicio de autenticación
 * Maneja login, registro, logout y refresh de tokens
 */
export const authService = {
  /**
   * Iniciar sesión
   */
  login: async (
    credentials: LoginCredentials
  ): Promise<ApiResponse<AuthResponse>> => {
    const response = await api.post<AuthResponse>("/auth/login", credentials);

    // Guardar tokens y datos del usuario
    if (response.data.status === "success" && response.data.data) {
      const { user, access_token, refresh_token } = response.data.data;

      tokenStorage.set(access_token);
      tokenStorage.setRefresh(refresh_token);
      localStorage.setItem("user_data", JSON.stringify(user));
    }

    return response.data;
  },

  /**
   * Registrar nuevo usuario
   */
  register: async (
    userData: RegisterData
  ): Promise<ApiResponse<AuthResponse>> => {
    const response = await api.post<AuthResponse>("/auth/registro", userData);

    // Guardar tokens y datos del usuario
    if (response.data.status === "success" && response.data.data) {
      const { user, access_token, refresh_token } = response.data.data;

      tokenStorage.set(access_token);
      tokenStorage.setRefresh(refresh_token);
      localStorage.setItem("user_data", JSON.stringify(user));
    }

    return response.data;
  },

  /**
   * Cerrar sesión
   */
  logout: async (): Promise<void> => {
    try {
      await api.post("/auth/logout");
    } catch (error) {
      console.warn("Error during logout:", error);
    } finally {
      // Limpiar datos locales siempre
      tokenStorage.clear();
    }
  },

  /**
   * Renovar token de acceso
   */
  refreshToken: async (): Promise<ApiResponse<RefreshTokenResponse>> => {
    const refreshToken = tokenStorage.getRefresh();

    if (!refreshToken) {
      throw new Error("No refresh token available");
    }

    const response = await api.post<RefreshTokenResponse>("/auth/refresh", {
      refresh_token: refreshToken,
    });

    // Actualizar access token
    if (response.data.status === "success" && response.data.data) {
      tokenStorage.set(response.data.data.access_token);
    }

    return response.data;
  },

  /**
   * Obtener perfil del usuario actual
   */
  getProfile: async (): Promise<ApiResponse<User>> => {
    const response = await api.get<User>("/usuario/perfil");

    // Actualizar datos del usuario en localStorage
    if (response.data.status === "success" && response.data.data) {
      localStorage.setItem("user_data", JSON.stringify(response.data.data));
    }

    return response.data;
  },

  /**
   * Obtener usuario desde localStorage
   */
  getCurrentUser: (): User | null => {
    const userData = localStorage.getItem("user_data");
    return userData ? JSON.parse(userData) : null;
  },

  /**
   * Verificar si el usuario está autenticado
   */
  isAuthenticated: (): boolean => {
    const token = tokenStorage.get();
    const user = authService.getCurrentUser();
    return !!(token && user);
  },

  /**
   * Verificar si el usuario tiene un perfil específico
   */
  hasProfile: (perfilId: number): boolean => {
    const user = authService.getCurrentUser();
    return user ? user.perfil_id === perfilId : false;
  },

  /**
   * Verificar si el usuario es administrador
   */
  isAdmin: (): boolean => {
    return authService.hasProfile(1); // Asumiendo que perfil_id 1 es admin
  },

  /**
   * Verificar si el usuario es cliente
   */
  isCliente: (): boolean => {
    return authService.hasProfile(2); // Asumiendo que perfil_id 2 es cliente
  },

  /**
   * Actualizar perfil del usuario
   */
  updateProfile: async (
    userData: Partial<User>
  ): Promise<ApiResponse<User>> => {
    const response = await api.put<User>("/usuario/perfil", userData);

    // Actualizar datos locales
    if (response.data.status === "success" && response.data.data) {
      localStorage.setItem("user_data", JSON.stringify(response.data.data));
    }

    return response.data;
  },

  /**
   * Cambiar contraseña
   */
  changePassword: async (passwords: {
    password_actual: string;
    password_nuevo: string;
    password_confirmacion: string;
  }): Promise<ApiResponse<null>> => {
    const response = await api.put<null>(
      "/usuario/cambiar-password",
      passwords
    );
    return response.data;
  },

  /**
   * Agregar dirección del usuario
   */
  addAddress: async (addressData: {
    direccion: string;
    ciudad: string;
    codigo_postal?: string;
    provincia?: string;
    es_principal?: boolean;
  }): Promise<ApiResponse<{ direccion: string }>> => {
    const response = await api.post<{ direccion: string }>(
      "/usuario/direccion",
      addressData
    );
    return response.data;
  },
};

export default authService;
