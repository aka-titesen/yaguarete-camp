import api, { type ApiResponse } from "./api";

export interface Pedido {
  id: number;
  fecha: string;
  total: number;
  estado: string;
  created_at: string;
  total_formateado: string;
  fecha_formateada: string;
  total_items: number;
  detalles: PedidoDetalle[];
}

export interface PedidoDetalle {
  cantidad: number;
  precio: number;
  subtotal: number;
  subtotal_formateado: string;
  precio_formateado: string;
  producto_nombre: string;
  producto_imagen: string;
  producto_imagen_url: string;
}

export interface UsuarioPerfil {
  id: number;
  nombre: string;
  apellido: string;
  email: string;
  telefono?: string;
  direccion?: string;
  fecha_alta: string;
  perfil_id: number;
  estadisticas: {
    total_compras: number;
    total_gastado: number;
    total_gastado_formateado: string;
  };
}

export interface ActualizarPerfilData {
  nombre: string;
  apellido: string;
  email: string;
  telefono?: string;
  direccion?: string;
}

export interface CambiarPasswordData {
  password_actual: string;
  password_nuevo: string;
  password_confirmacion: string;
}

export interface AgregarDireccionData {
  direccion: string;
  ciudad: string;
  codigo_postal?: string;
  provincia?: string;
  es_principal?: boolean;
}

export interface PedidosParams {
  page?: number;
  limit?: number;
}

/**
 * Servicio para manejo de operaciones de usuario
 * Proporciona métodos para interactuar con la API de usuario autenticado
 */
export const usuarioService = {
  /**
   * Obtener perfil del usuario actual
   */
  getPerfil: async (): Promise<ApiResponse<UsuarioPerfil>> => {
    const response = await api.get<UsuarioPerfil>("/usuario/perfil");
    return response.data;
  },

  /**
   * Actualizar perfil del usuario
   */
  actualizarPerfil: async (
    data: ActualizarPerfilData
  ): Promise<ApiResponse<UsuarioPerfil>> => {
    const response = await api.put<UsuarioPerfil>("/usuario/perfil", data);
    return response.data;
  },

  /**
   * Obtener historial de pedidos del usuario
   */
  getPedidos: async (
    params?: PedidosParams
  ): Promise<ApiResponse<Pedido[]>> => {
    const response = await api.get<Pedido[]>("/usuario/pedidos", { params });
    return response.data;
  },

  /**
   * Cambiar contraseña del usuario
   */
  cambiarPassword: async (
    data: CambiarPasswordData
  ): Promise<ApiResponse<null>> => {
    const response = await api.put<null>("/usuario/cambiar-password", data);
    return response.data;
  },

  /**
   * Agregar nueva dirección del usuario
   */
  agregarDireccion: async (
    data: AgregarDireccionData
  ): Promise<ApiResponse<{ direccion: string }>> => {
    const response = await api.post<{ direccion: string }>(
      "/usuario/direccion",
      data
    );
    return response.data;
  },

  /**
   * Obtener estadísticas del usuario
   */
  getEstadisticas: async (): Promise<
    ApiResponse<UsuarioPerfil["estadisticas"]>
  > => {
    const response = await api.get<UsuarioPerfil>("/usuario/perfil");

    if (response.data.status === "success") {
      return {
        ...response.data,
        data: response.data.data.estadisticas,
      };
    }

    return response.data;
  },

  /**
   * Obtener resumen de actividad del usuario
   */
  getResumenActividad: async (): Promise<{
    compras_recientes: Pedido[];
    estadisticas: UsuarioPerfil["estadisticas"];
    perfil: Omit<UsuarioPerfil, "estadisticas">;
  }> => {
    const [perfilResponse, pedidosResponse] = await Promise.all([
      usuarioService.getPerfil(),
      usuarioService.getPedidos({ limit: 5 }),
    ]);

    if (
      perfilResponse.status === "success" &&
      pedidosResponse.status === "success"
    ) {
      const { estadisticas, ...perfil } = perfilResponse.data;

      return {
        compras_recientes: pedidosResponse.data,
        estadisticas,
        perfil,
      };
    }

    throw new Error("Error al obtener resumen de actividad");
  },
};

export default usuarioService;
