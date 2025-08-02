import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";
import {
  categoriasService,
  type Categoria,
  type CategoriasParams,
} from "../services/categoriasService";
import { queryKeys } from "../lib/react-query";

/**
 * Hook para obtener lista de categorías
 */
export function useCategorias(params?: CategoriasParams) {
  return useQuery({
    queryKey: queryKeys.categorias.list(params || {}),
    queryFn: async () => {
      const response = await categoriasService.getAll(params);
      if (response.status === "success") {
        return response.data;
      }
      throw new Error(response.message || "Error al obtener categorías");
    },
    enabled: true,
    staleTime: 10 * 60 * 1000, // 10 minutos (categorías cambian poco)
  });
}

/**
 * Hook para obtener una categoría por ID
 */
export function useCategoria(id: number) {
  return useQuery({
    queryKey: queryKeys.categorias.detail(id),
    queryFn: async () => {
      const response = await categoriasService.getById(id);
      if (response.status === "success") {
        return response.data;
      }
      throw new Error(response.message || "Error al obtener categoría");
    },
    enabled: !!id && id > 0,
    staleTime: 15 * 60 * 1000, // 15 minutos
  });
}

/**
 * Hook para obtener categorías activas (para navegación)
 */
export function useCategoriasActivas() {
  return useQuery({
    queryKey: [...queryKeys.categorias.lists(), "activas"],
    queryFn: async () => {
      const response = await categoriasService.getAll({ activo: true });
      if (response.status === "success") {
        return response.data;
      }
      throw new Error(
        response.message || "Error al obtener categorías activas"
      );
    },
    staleTime: 15 * 60 * 1000, // 15 minutos
  });
}

/**
 * Hook para obtener jerarquía de categorías
 */
export function useJerarquiaCategorias() {
  return useQuery({
    queryKey: [...queryKeys.categorias.lists(), "jerarquia"],
    queryFn: async () => {
      const response = await categoriasService.getJerarquia();
      if (response.status === "success") {
        return response.data;
      }
      throw new Error(response.message || "Error al obtener jerarquía");
    },
    staleTime: 20 * 60 * 1000, // 20 minutos
  });
}

/**
 * Hook para crear una nueva categoría (Admin)
 */
export function useCrearCategoria() {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: async (
      data: Omit<Categoria, "id" | "created_at" | "updated_at">
    ) => {
      const response = await categoriasService.create(data);
      if (response.status === "success") {
        return response.data;
      }
      throw new Error(response.message || "Error al crear categoría");
    },
    onSuccess: () => {
      // Invalidar todas las queries de categorías
      queryClient.invalidateQueries({ queryKey: queryKeys.categorias.all });
    },
  });
}

/**
 * Hook para actualizar una categoría (Admin)
 */
export function useActualizarCategoria() {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: async ({
      id,
      data,
    }: {
      id: number;
      data: Partial<Omit<Categoria, "id" | "created_at" | "updated_at">>;
    }) => {
      const response = await categoriasService.update(id, data);
      if (response.status === "success") {
        return response.data;
      }
      throw new Error(response.message || "Error al actualizar categoría");
    },
    onSuccess: (_, variables) => {
      // Invalidar cache específico y general
      queryClient.invalidateQueries({
        queryKey: queryKeys.categorias.detail(variables.id),
      });
      queryClient.invalidateQueries({ queryKey: queryKeys.categorias.all });
    },
  });
}

/**
 * Hook para eliminar una categoría (Admin)
 */
export function useEliminarCategoria() {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: async (id: number) => {
      const response = await categoriasService.delete(id);
      if (response.status === "success") {
        return response.data;
      }
      throw new Error(response.message || "Error al eliminar categoría");
    },
    onSuccess: (_, id) => {
      // Remover del cache y invalidar lista
      queryClient.removeQueries({ queryKey: queryKeys.categorias.detail(id) });
      queryClient.invalidateQueries({ queryKey: queryKeys.categorias.all });
    },
  });
}

/**
 * Hook combinado para manejo completo de categorías
 */
export function useCategoriasManager() {
  const crearCategoria = useCrearCategoria();
  const actualizarCategoria = useActualizarCategoria();
  const eliminarCategoria = useEliminarCategoria();

  return {
    // Mutaciones
    crear: crearCategoria.mutate,
    actualizar: actualizarCategoria.mutate,
    eliminar: eliminarCategoria.mutate,

    // Estados de carga
    isCreating: crearCategoria.isPending,
    isUpdating: actualizarCategoria.isPending,
    isDeleting: eliminarCategoria.isPending,
    isLoading:
      crearCategoria.isPending ||
      actualizarCategoria.isPending ||
      eliminarCategoria.isPending,

    // Estados de error
    createError: crearCategoria.error,
    updateError: actualizarCategoria.error,
    deleteError: eliminarCategoria.error,

    // Funciones asíncronas
    crearAsync: crearCategoria.mutateAsync,
    actualizarAsync: actualizarCategoria.mutateAsync,
    eliminarAsync: eliminarCategoria.mutateAsync,

    // Reset de estados
    resetCreate: crearCategoria.reset,
    resetUpdate: actualizarCategoria.reset,
    resetDelete: eliminarCategoria.reset,
  };
}

/**
 * Hook para obtener estadísticas de categorías (Admin)
 */
export function useEstadisticasCategorias() {
  return useQuery({
    queryKey: [...queryKeys.categorias.all, "estadisticas"],
    queryFn: async () => {
      const response = await categoriasService.getEstadisticas();
      if (response.status === "success") {
        return response.data;
      }
      throw new Error(response.message || "Error al obtener estadísticas");
    },
    staleTime: 5 * 60 * 1000, // 5 minutos
  });
}

/**
 * Hook para prefetch de categorías
 */
export function usePrefetchCategorias() {
  const queryClient = useQueryClient();

  const prefetchCategoria = async (id: number) => {
    await queryClient.prefetchQuery({
      queryKey: queryKeys.categorias.detail(id),
      queryFn: async () => {
        const response = await categoriasService.getById(id);
        if (response.status === "success") {
          return response.data;
        }
        throw new Error(response.message || "Error al obtener categoría");
      },
      staleTime: 15 * 60 * 1000,
    });
  };

  const prefetchCategoriasActivas = async () => {
    await queryClient.prefetchQuery({
      queryKey: [...queryKeys.categorias.lists(), "activas"],
      queryFn: async () => {
        const response = await categoriasService.getAll({ activo: true });
        if (response.status === "success") {
          return response.data;
        }
        throw new Error(
          response.message || "Error al obtener categorías activas"
        );
      },
      staleTime: 15 * 60 * 1000,
    });
  };

  return {
    prefetchCategoria,
    prefetchCategoriasActivas,
  };
}
