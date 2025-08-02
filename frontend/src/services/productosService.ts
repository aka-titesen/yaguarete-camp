import api, { type ApiResponse } from "./api";

export interface Producto {
  id: number;
  nombre: string;
  precio_venta: number;
  descripcion: string;
  stock: number;
  imagen: string;
  destacado: boolean;
  estado: string;
  created_at: string;
  categoria_nombre: string;
  categoria_id: number;
  imagen_url: string;
  precio_formateado: string;
  en_stock: boolean;
}

export interface ProductoDetalle extends Producto {
  // Campos adicionales para el detalle del producto
}

export interface ProductosParams {
  page?: number;
  limit?: number;
  categoria_id?: number;
  precio_min?: number;
  precio_max?: number;
  nombre?: string;
  estado?: string;
  destacado?: boolean;
  orden?: "precio_asc" | "precio_desc" | "nombre" | "created_at";
}

export interface ProductosResponse {
  productos?: Producto[]; // Para respuestas con categoría
  pagination?: {
    page: number;
    limit: number;
    total_items: number;
    total_pages: number;
    has_next: boolean;
    has_prev: boolean;
  };
}

export interface BusquedaParams {
  q: string;
}

export interface CreateProductoData {
  nombre: string;
  precio_venta: number;
  categoria_id: number;
  descripcion?: string;
  stock?: number;
  destacado?: boolean;
}

export interface UpdateProductoData extends Partial<CreateProductoData> {
  // Todos los campos son opcionales para actualización
}

/**
 * Servicio para manejo de productos
 * Proporciona métodos para interactuar con la API de productos
 */
export const productosService = {
  /**
   * Obtener lista de productos con filtros y paginación
   */
  getAll: async (
    params?: ProductosParams
  ): Promise<ApiResponse<Producto[]>> => {
    const response = await api.get<Producto[]>("/productos", { params });
    return response.data;
  },

  /**
   * Obtener producto por ID
   */
  getById: async (id: number): Promise<ApiResponse<ProductoDetalle>> => {
    const response = await api.get<ProductoDetalle>(`/productos/${id}`);
    return response.data;
  },

  /**
   * Obtener productos destacados
   */
  getDestacados: async (): Promise<ApiResponse<Producto[]>> => {
    const response = await api.get<Producto[]>("/productos/destacados");
    return response.data;
  },

  /**
   * Obtener productos relacionados
   */
  getRelacionados: async (id: number): Promise<ApiResponse<Producto[]>> => {
    const response = await api.get<Producto[]>(`/productos/relacionados/${id}`);
    return response.data;
  },

  /**
   * Buscar productos por término
   */
  search: async (query: string): Promise<ApiResponse<Producto[]>> => {
    const response = await api.get<Producto[]>("/productos/buscar", {
      params: { q: query },
    });
    return response.data;
  },

  /**
   * Crear nuevo producto (requiere autenticación)
   */
  create: async (
    data: CreateProductoData
  ): Promise<ApiResponse<{ id: number }>> => {
    const response = await api.post<{ id: number }>("/productos", data);
    return response.data;
  },

  /**
   * Actualizar producto existente (requiere autenticación)
   */
  update: async (
    id: number,
    data: UpdateProductoData
  ): Promise<ApiResponse<null>> => {
    const response = await api.put<null>(`/productos/${id}`, data);
    return response.data;
  },

  /**
   * Eliminar producto (requiere autenticación)
   */
  delete: async (id: number): Promise<ApiResponse<null>> => {
    const response = await api.delete<null>(`/productos/${id}`);
    return response.data;
  },

  /**
   * Obtener productos por categoría con filtros
   */
  getByCategoria: async (
    categoriaId: number,
    params?: Omit<ProductosParams, "categoria_id">
  ): Promise<ApiResponse<ProductosResponse>> => {
    const response = await api.get<ProductosResponse>(
      `/categorias/${categoriaId}/productos`,
      {
        params,
      }
    );
    return response.data;
  },
};

export default productosService;
