import api, { type ApiResponse } from "./api";
import { type Producto } from "./productosService";

export interface Categoria {
  id: number;
  nombre: string;
  descripcion: string;
  imagen: string;
  created_at: string;
  total_productos: number;
  imagen_url: string;
  productos_destacados?: Producto[];
  precio_promedio?: number;
  precio_promedio_formateado?: string;
}

export interface CategoriaDetalle extends Categoria {
  productos_destacados: Producto[];
}

export interface CategoriaProductos {
  categoria: {
    id: number;
    nombre: string;
    descripcion: string;
  };
  productos: Producto[];
}

export interface CategoriaEstadisticas extends Categoria {
  productos_destacados: number;
  precio_promedio: number;
  precio_promedio_formateado: string;
}

/**
 * Servicio para manejo de categorías
 * Proporciona métodos para interactuar con la API de categorías
 */
export const categoriasService = {
  /**
   * Obtener todas las categorías
   */
  getAll: async (): Promise<ApiResponse<Categoria[]>> => {
    const response = await api.get<Categoria[]>("/categorias");
    return response.data;
  },

  /**
   * Obtener categoría por ID
   */
  getById: async (id: number): Promise<ApiResponse<CategoriaDetalle>> => {
    const response = await api.get<CategoriaDetalle>(`/categorias/${id}`);
    return response.data;
  },

  /**
   * Obtener productos de una categoría específica
   */
  getProductos: async (
    id: number,
    params?: {
      page?: number;
      limit?: number;
      precio_min?: number;
      precio_max?: number;
      orden?:
        | "precio_asc"
        | "precio_desc"
        | "nombre"
        | "destacados"
        | "created_at";
    }
  ): Promise<ApiResponse<CategoriaProductos>> => {
    const response = await api.get<CategoriaProductos>(
      `/categorias/${id}/productos`,
      { params }
    );
    return response.data;
  },

  /**
   * Obtener estadísticas de todas las categorías
   */
  getEstadisticas: async (): Promise<ApiResponse<CategoriaEstadisticas[]>> => {
    const response = await api.get<CategoriaEstadisticas[]>(
      "/categorias/estadisticas"
    );
    return response.data;
  },

  /**
   * Obtener categorías con productos en stock
   */
  getConProductos: async (): Promise<ApiResponse<Categoria[]>> => {
    const response = await api.get<Categoria[]>("/categorias");

    // Filtrar solo categorías que tienen productos
    if (response.data.status === "success") {
      const categoriasConProductos = response.data.data.filter(
        (categoria) => categoria.total_productos > 0
      );

      return {
        ...response.data,
        data: categoriasConProductos,
      };
    }

    return response.data;
  },

  /**
   * Buscar categorías por nombre
   */
  search: async (query: string): Promise<ApiResponse<Categoria[]>> => {
    const response = await api.get<Categoria[]>("/categorias");

    if (response.data.status === "success") {
      const categoriasFiltradas = response.data.data.filter(
        (categoria) =>
          categoria.nombre.toLowerCase().includes(query.toLowerCase()) ||
          categoria.descripcion.toLowerCase().includes(query.toLowerCase())
      );

      return {
        ...response.data,
        data: categoriasFiltradas,
      };
    }

    return response.data;
  },
};

export default categoriasService;
