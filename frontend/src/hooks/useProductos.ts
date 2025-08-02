import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";
import {
  productosService,
  type Producto,
  type ProductosParams,
} from "../services/productosService";
import { queryKeys } from "../lib/react-query";

/**
 * Hook para obtener lista de productos con filtros
 */
export function useProductos(params?: ProductosParams) {
  return useQuery({
    queryKey: queryKeys.productos.list(params || {}),
    queryFn: async () => {
      const response = await productosService.getProductos(params);
      if (response.status === "success") {
        return response.data;
      }
      throw new Error(response.message || "Error al obtener productos");
    },
    enabled: true,
  });
}

/**
 * Hook para obtener un producto por ID
 */
export function useProducto(id: number) {
  return useQuery({
    queryKey: queryKeys.productos.detail(id),
    queryFn: async () => {
      const response = await productosService.getProducto(id);
      if (response.status === "success") {
        return response.data;
      }
      throw new Error(response.message || "Error al obtener producto");
    },
    enabled: !!id && id > 0,
  });
}

/**
 * Hook para buscar productos
 */
export function useBuscarProductos(termino: string, enabled = true) {
  return useQuery({
    queryKey: [...queryKeys.productos.lists(), "buscar", termino],
    queryFn: async () => {
      const response = await productosService.buscarProductos(termino);
      if (response.status === "success") {
        return response.data;
      }
      throw new Error(response.message || "Error en búsqueda");
    },
    enabled: enabled && termino.length >= 2,
    staleTime: 30000, // 30 segundos
  });
}

/**
 * Hook para productos populares
 */
export function useProductosPopulares() {
  return useQuery({
    queryKey: [...queryKeys.productos.lists(), "populares"],
    queryFn: async () => {
      const response = await productosService.getProductosPopulares();
      if (response.status === "success") {
        return response.data;
      }
      throw new Error(
        response.message || "Error al obtener productos populares"
      );
    },
    staleTime: 10 * 60 * 1000, // 10 minutos
  });
}

/**
 * Hook para productos en oferta
 */
export function useProductosEnOferta() {
  return useQuery({
    queryKey: [...queryKeys.productos.lists(), "ofertas"],
    queryFn: async () => {
      const response = await productosService.getProductosEnOferta();
      if (response.status === "success") {
        return response.data;
      }
      throw new Error(response.message || "Error al obtener ofertas");
    },
    staleTime: 5 * 60 * 1000, // 5 minutos
  });
}

/**
 * Hook para crear un nuevo producto (Admin)
 */
export function useCrearProducto() {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: async (
      data: Omit<Producto, "id" | "created_at" | "updated_at">
    ) => {
      const response = await productosService.crearProducto(data);
      if (response.status === "success") {
        return response.data;
      }
      throw new Error(response.message || "Error al crear producto");
    },
    onSuccess: () => {
      // Invalidar cache de productos
      queryClient.invalidateQueries({ queryKey: queryKeys.productos.all });
    },
  });
}

/**
 * Hook para actualizar un producto (Admin)
 */
export function useActualizarProducto() {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: async ({
      id,
      data,
    }: {
      id: number;
      data: Partial<Omit<Producto, "id" | "created_at" | "updated_at">>;
    }) => {
      const response = await productosService.actualizarProducto(id, data);
      if (response.status === "success") {
        return response.data;
      }
      throw new Error(response.message || "Error al actualizar producto");
    },
    onSuccess: (_, variables) => {
      // Invalidar cache específico del producto y lista general
      queryClient.invalidateQueries({
        queryKey: queryKeys.productos.detail(variables.id),
      });
      queryClient.invalidateQueries({ queryKey: queryKeys.productos.all });
    },
  });
}

/**
 * Hook para eliminar un producto (Admin)
 */
export function useEliminarProducto() {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: async (id: number) => {
      const response = await productosService.eliminarProducto(id);
      if (response.status === "success") {
        return response.data;
      }
      throw new Error(response.message || "Error al eliminar producto");
    },
    onSuccess: (_, id) => {
      // Remover del cache y invalidar lista
      queryClient.removeQueries({ queryKey: queryKeys.productos.detail(id) });
      queryClient.invalidateQueries({ queryKey: queryKeys.productos.all });
    },
  });
}

/**
 * Hook combinado para manejo completo de productos
 */
export function useProductosManager() {
  const crearProducto = useCrearProducto();
  const actualizarProducto = useActualizarProducto();
  const eliminarProducto = useEliminarProducto();

  return {
    // Mutaciones
    crear: crearProducto.mutate,
    actualizar: actualizarProducto.mutate,
    eliminar: eliminarProducto.mutate,

    // Estados de carga
    isCreating: crearProducto.isPending,
    isUpdating: actualizarProducto.isPending,
    isDeleting: eliminarProducto.isPending,
    isLoading:
      crearProducto.isPending ||
      actualizarProducto.isPending ||
      eliminarProducto.isPending,

    // Estados de error
    createError: crearProducto.error,
    updateError: actualizarProducto.error,
    deleteError: eliminarProducto.error,

    // Funciones asíncronas
    crearAsync: crearProducto.mutateAsync,
    actualizarAsync: actualizarProducto.mutateAsync,
    eliminarAsync: eliminarProducto.mutateAsync,

    // Reset de estados
    resetCreate: crearProducto.reset,
    resetUpdate: actualizarProducto.reset,
    resetDelete: eliminarProducto.reset,
  };
}

/**
 * Hook para prefetch de productos relacionados
 */
export function usePrefetchProductos() {
  const queryClient = useQueryClient();

  const prefetchProducto = async (id: number) => {
    await queryClient.prefetchQuery({
      queryKey: queryKeys.productos.detail(id),
      queryFn: async () => {
        const response = await productosService.getProducto(id);
        if (response.status === "success") {
          return response.data;
        }
        throw new Error(response.message || "Error al obtener producto");
      },
      staleTime: 5 * 60 * 1000, // 5 minutos
    });
  };

  const prefetchProductosCategoria = async (categoriaId: number) => {
    await queryClient.prefetchQuery({
      queryKey: queryKeys.productos.list({ categoria_id: categoriaId }),
      queryFn: async () => {
        const response = await productosService.getProductos({
          categoria_id: categoriaId,
        });
        if (response.status === "success") {
          return response.data;
        }
        throw new Error(response.message || "Error al obtener productos");
      },
      staleTime: 5 * 60 * 1000,
    });
  };

  return {
    prefetchProducto,
    prefetchProductosCategoria,
  };
}
