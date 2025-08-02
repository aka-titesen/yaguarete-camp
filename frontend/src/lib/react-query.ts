import {
  QueryClient,
  useQuery,
  useMutation,
  useQueryClient,
} from "@tanstack/react-query";
import type { ReactNode } from "react";

/**
 * Configuración global de React Query
 */
export const queryClient = new QueryClient({
  defaultOptions: {
    queries: {
      // Tiempo de vida de cache: 5 minutos
      staleTime: 5 * 60 * 1000,
      // Tiempo antes de limpiar cache: 10 minutos
      gcTime: 10 * 60 * 1000,
      // Reintentar 2 veces en caso de error
      retry: 2,
      // No refetch automático al cambiar ventana
      refetchOnWindowFocus: false,
      // Refetch al reconectar
      refetchOnReconnect: true,
      // Tiempo de timeout: 30 segundos
      networkMode: "online",
    },
    mutations: {
      // Reintentar mutaciones 1 vez
      retry: 1,
      // Tiempo de timeout para mutaciones: 1 minuto
      networkMode: "online",
    },
  },
});

/**
 * Provider de React Query con configuración personalizada
 * Nota: Este provider debe ser usado en el componente principal de la app
 */
export interface QueryProviderProps {
  children: ReactNode;
}

// El QueryProvider será implementado en el Sprint 3 con los componentes React

/**
 * Hook personalizado para invalidar queries
 */
export function useInvalidateQueries() {
  const queryClient = useQueryClient();

  return {
    invalidateAll: () => queryClient.invalidateQueries(),
    invalidateProducts: () =>
      queryClient.invalidateQueries({ queryKey: ["productos"] }),
    invalidateCategories: () =>
      queryClient.invalidateQueries({ queryKey: ["categorias"] }),
    invalidateUser: () =>
      queryClient.invalidateQueries({ queryKey: ["usuario"] }),
    invalidateAuth: () => queryClient.invalidateQueries({ queryKey: ["auth"] }),
    clearAll: () => queryClient.clear(),
  };
}

/**
 * Hook para manejo global de errores de queries
 */
export function useGlobalQueryError() {
  return {
    onError: (error: Error) => {
      console.error("Query Error:", error);

      // Aquí se pueden manejar errores globales
      if (
        error.message.includes("401") ||
        error.message.includes("Unauthorized")
      ) {
        // Redirigir a login si hay error de autenticación
        window.location.href = "/login";
      }

      if (
        error.message.includes("403") ||
        error.message.includes("Forbidden")
      ) {
        // Mostrar mensaje de permisos insuficientes
        console.warn("Permisos insuficientes");
      }

      if (
        error.message.includes("500") ||
        error.message.includes("Internal Server Error")
      ) {
        // Mostrar mensaje de error del servidor
        console.error("Error interno del servidor");
      }
    },
  };
}

/**
 * Keys para queries organizadas por dominio
 */
export const queryKeys = {
  // Productos
  productos: {
    all: ["productos"] as const,
    lists: () => [...queryKeys.productos.all, "list"] as const,
    list: (filters: Record<string, any>) =>
      [...queryKeys.productos.lists(), { filters }] as const,
    details: () => [...queryKeys.productos.all, "detail"] as const,
    detail: (id: number) => [...queryKeys.productos.details(), id] as const,
  },

  // Categorías
  categorias: {
    all: ["categorias"] as const,
    lists: () => [...queryKeys.categorias.all, "list"] as const,
    list: (filters: Record<string, any>) =>
      [...queryKeys.categorias.lists(), { filters }] as const,
    details: () => [...queryKeys.categorias.all, "detail"] as const,
    detail: (id: number) => [...queryKeys.categorias.details(), id] as const,
  },

  // Usuario
  usuario: {
    all: ["usuario"] as const,
    perfil: () => [...queryKeys.usuario.all, "perfil"] as const,
    pedidos: () => [...queryKeys.usuario.all, "pedidos"] as const,
    pedidosList: (filters: Record<string, any>) =>
      [...queryKeys.usuario.pedidos(), { filters }] as const,
    estadisticas: () => [...queryKeys.usuario.all, "estadisticas"] as const,
    resumen: () => [...queryKeys.usuario.all, "resumen"] as const,
  },

  // Autenticación
  auth: {
    all: ["auth"] as const,
    user: () => [...queryKeys.auth.all, "user"] as const,
    permissions: () => [...queryKeys.auth.all, "permissions"] as const,
  },
} as const;

/**
 * Utilidades para prefetching
 */
export function usePrefetchQueries() {
  const queryClient = useQueryClient();

  return {
    prefetchProducts: async (filters?: Record<string, any>) => {
      await queryClient.prefetchQuery({
        queryKey: queryKeys.productos.list(filters || {}),
        // queryFn se definirá en los hooks específicos
      });
    },

    prefetchCategories: async () => {
      await queryClient.prefetchQuery({
        queryKey: queryKeys.categorias.lists(),
        // queryFn se definirá en los hooks específicos
      });
    },

    prefetchUserProfile: async () => {
      await queryClient.prefetchQuery({
        queryKey: queryKeys.usuario.perfil(),
        // queryFn se definirá en los hooks específicos
      });
    },
  };
}

/**
 * Hook para manejo de estado de carga global
 */
export function useGlobalLoadingState() {
  const queryClient = useQueryClient();

  // Verificar si hay queries pendientes
  const isFetching = queryClient.isFetching();
  const isMutating = queryClient.isMutating();

  return {
    isLoading: isFetching > 0 || isMutating > 0,
    isFetching: isFetching > 0,
    isMutating: isMutating > 0,
  };
}

export { useQuery, useMutation, useQueryClient };
export type { UseQueryResult, UseMutationResult } from "@tanstack/react-query";
