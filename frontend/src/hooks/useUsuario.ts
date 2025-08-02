import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";
import {
  usuarioService,
  type UsuarioPerfil,
  type ActualizarPerfilData,
  type CambiarPasswordData,
  type PedidosParams,
} from "../services/usuarioService";
import { queryKeys } from "../lib/react-query";

/**
 * Hook para obtener perfil del usuario autenticado
 */
export function useUsuarioPerfil() {
  return useQuery({
    queryKey: queryKeys.usuario.perfil(),
    queryFn: async () => {
      const response = await usuarioService.getPerfil();
      if (response.status === "success") {
        return response.data;
      }
      throw new Error(response.message || "Error al obtener perfil");
    },
    enabled: true,
    staleTime: 5 * 60 * 1000, // 5 minutos
  });
}

/**
 * Hook para obtener historial de pedidos del usuario
 */
export function useUsuarioPedidos(params?: PedidosParams) {
  return useQuery({
    queryKey: queryKeys.usuario.pedidosList(params || {}),
    queryFn: async () => {
      const response = await usuarioService.getPedidos(params);
      if (response.status === "success") {
        return response.data;
      }
      throw new Error(response.message || "Error al obtener pedidos");
    },
    enabled: true,
  });
}

/**
 * Hook para obtener estadísticas del usuario
 */
export function useUsuarioEstadisticas() {
  return useQuery({
    queryKey: queryKeys.usuario.estadisticas(),
    queryFn: async () => {
      const response = await usuarioService.getEstadisticas();
      if (response.status === "success") {
        return response.data;
      }
      throw new Error(response.message || "Error al obtener estadísticas");
    },
    enabled: true,
    staleTime: 10 * 60 * 1000, // 10 minutos
  });
}

/**
 * Hook para obtener resumen completo del usuario
 */
export function useUsuarioResumen() {
  return useQuery({
    queryKey: queryKeys.usuario.resumen(),
    queryFn: async () => {
      return await usuarioService.getResumenActividad();
    },
    enabled: true,
    staleTime: 5 * 60 * 1000,
  });
}

/**
 * Hook para actualizar perfil del usuario
 */
export function useActualizarPerfil() {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: async (data: ActualizarPerfilData) => {
      const response = await usuarioService.actualizarPerfil(data);
      if (response.status === "success") {
        return response.data;
      }
      throw new Error(response.message || "Error al actualizar perfil");
    },
    onSuccess: () => {
      // Invalidar cache del perfil y resumen
      queryClient.invalidateQueries({ queryKey: queryKeys.usuario.perfil() });
      queryClient.invalidateQueries({ queryKey: queryKeys.usuario.resumen() });
    },
  });
}

/**
 * Hook para cambiar contraseña del usuario
 */
export function useCambiarPassword() {
  return useMutation({
    mutationFn: async (data: CambiarPasswordData) => {
      const response = await usuarioService.cambiarPassword(data);
      if (response.status === "success") {
        return response.data;
      }
      throw new Error(response.message || "Error al cambiar contraseña");
    },
  });
}

/**
 * Hook para agregar dirección del usuario
 */
export function useAgregarDireccion() {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: async (data: {
      direccion: string;
      ciudad: string;
      codigo_postal?: string;
      provincia?: string;
      es_principal?: boolean;
    }) => {
      const response = await usuarioService.agregarDireccion(data);
      if (response.status === "success") {
        return response.data;
      }
      throw new Error(response.message || "Error al agregar dirección");
    },
    onSuccess: () => {
      // Invalidar perfil para refrescar direcciones
      queryClient.invalidateQueries({ queryKey: queryKeys.usuario.perfil() });
    },
  });
}

/**
 * Hook combinado para manejo completo del usuario
 */
export function useUsuarioManager() {
  const actualizarPerfil = useActualizarPerfil();
  const cambiarPassword = useCambiarPassword();
  const agregarDireccion = useAgregarDireccion();

  return {
    // Mutaciones
    actualizarPerfil: actualizarPerfil.mutate,
    cambiarPassword: cambiarPassword.mutate,
    agregarDireccion: agregarDireccion.mutate,

    // Estados de carga
    isUpdatingProfile: actualizarPerfil.isPending,
    isChangingPassword: cambiarPassword.isPending,
    isAddingAddress: agregarDireccion.isPending,
    isLoading:
      actualizarPerfil.isPending ||
      cambiarPassword.isPending ||
      agregarDireccion.isPending,

    // Estados de error
    profileError: actualizarPerfil.error,
    passwordError: cambiarPassword.error,
    addressError: agregarDireccion.error,

    // Estados de éxito
    profileSuccess: actualizarPerfil.isSuccess,
    passwordSuccess: cambiarPassword.isSuccess,
    addressSuccess: agregarDireccion.isSuccess,

    // Funciones asíncronas
    actualizarPerfilAsync: actualizarPerfil.mutateAsync,
    cambiarPasswordAsync: cambiarPassword.mutateAsync,
    agregarDireccionAsync: agregarDireccion.mutateAsync,

    // Reset de estados
    resetProfile: actualizarPerfil.reset,
    resetPassword: cambiarPassword.reset,
    resetAddress: agregarDireccion.reset,
  };
}

/**
 * Hook para prefetch de datos del usuario
 */
export function usePrefetchUsuario() {
  const queryClient = useQueryClient();

  const prefetchPerfil = async () => {
    await queryClient.prefetchQuery({
      queryKey: queryKeys.usuario.perfil(),
      queryFn: async () => {
        const response = await usuarioService.getPerfil();
        if (response.status === "success") {
          return response.data;
        }
        throw new Error(response.message || "Error al obtener perfil");
      },
      staleTime: 5 * 60 * 1000,
    });
  };

  const prefetchPedidos = async (params?: PedidosParams) => {
    await queryClient.prefetchQuery({
      queryKey: queryKeys.usuario.pedidosList(params || {}),
      queryFn: async () => {
        const response = await usuarioService.getPedidos(params);
        if (response.status === "success") {
          return response.data;
        }
        throw new Error(response.message || "Error al obtener pedidos");
      },
      staleTime: 5 * 60 * 1000,
    });
  };

  const prefetchEstadisticas = async () => {
    await queryClient.prefetchQuery({
      queryKey: queryKeys.usuario.estadisticas(),
      queryFn: async () => {
        const response = await usuarioService.getEstadisticas();
        if (response.status === "success") {
          return response.data;
        }
        throw new Error(response.message || "Error al obtener estadísticas");
      },
      staleTime: 10 * 60 * 1000,
    });
  };

  return {
    prefetchPerfil,
    prefetchPedidos,
    prefetchEstadisticas,
  };
}

/**
 * Hook para validaciones de formularios de usuario
 */
export function useUsuarioValidaciones() {
  const validarEmail = (email: string): boolean => {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
  };

  const validarPassword = (
    password: string
  ): { isValid: boolean; errors: string[] } => {
    const errors: string[] = [];

    if (password.length < 8) {
      errors.push("La contraseña debe tener al menos 8 caracteres");
    }

    if (!/[A-Z]/.test(password)) {
      errors.push("La contraseña debe contener al menos una mayúscula");
    }

    if (!/[a-z]/.test(password)) {
      errors.push("La contraseña debe contener al menos una minúscula");
    }

    if (!/\d/.test(password)) {
      errors.push("La contraseña debe contener al menos un número");
    }

    return {
      isValid: errors.length === 0,
      errors,
    };
  };

  const validarTelefono = (telefono: string): boolean => {
    // Formato básico para teléfonos
    const telefonoRegex = /^[\+]?[0-9\s\-\(\)]{8,15}$/;
    return telefonoRegex.test(telefono);
  };

  return {
    validarEmail,
    validarPassword,
    validarTelefono,
  };
}
