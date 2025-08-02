import { QueryClient, QueryClientProvider } from "@tanstack/react-query";
import { ReactQueryDevtools } from "@tanstack/react-query-devtools";
import "bootstrap/dist/css/bootstrap.min.css";
import "./styles/main.scss";

// Crear cliente de React Query
const queryClient = new QueryClient({
  defaultOptions: {
    queries: {
      staleTime: 5 * 60 * 1000, // 5 minutos
      retry: 1,
      refetchOnWindowFocus: false,
    },
  },
});

function App() {
  return (
    <QueryClientProvider client={queryClient}>
      <div className="App">
        {/* Navbar temporal */}
        <nav className="navbar-yaguarete">
          <div className="navbar-content">
            <div className="navbar-brand">
              <span className="brand-name">Yaguareté Camp</span>
            </div>
            <nav className="navbar-nav">
              <div className="nav-item">
                <a href="#" className="nav-link active">
                  Inicio
                </a>
              </div>
              <div className="nav-item">
                <a href="#" className="nav-link">
                  Proyectos
                </a>
              </div>
              <div className="nav-item">
                <a href="#" className="nav-link">
                  Contacto
                </a>
              </div>
            </nav>
            <div className="navbar-actions">
              <button className="user-menu">
                <div
                  className="user-avatar"
                  style={{
                    width: "2.5rem",
                    height: "2.5rem",
                    borderRadius: "50%",
                    backgroundColor: "rgba(255, 255, 255, 0.2)",
                    display: "flex",
                    alignItems: "center",
                    justifyContent: "center",
                    color: "white",
                    border: "none",
                    cursor: "pointer",
                  }}
                >
                  U
                </div>
              </button>
            </div>
          </div>
        </nav>

        {/* Contenido principal */}
        <main className="container-fluid section-padding">
          <div className="row">
            <div className="col-12 text-center">
              <h1 className="gradient-text mb-4">
                ¡Bienvenido a Yaguareté Camp!
              </h1>
              <p className="lead mb-5">
                Migración exitosa a React + TypeScript con sistema de colores
                avanzado
              </p>

              {/* Demo de componentes */}
              <div className="row g-4 mb-5">
                <div className="col-md-4">
                  <div className="card-yaguarete card-interactive">
                    <h5>🎨 Sistema de Colores</h5>
                    <p>
                      Paleta completa basada en el verde original del navbar
                      (#2A5C45)
                    </p>
                    <div className="d-flex gap-2 justify-content-center">
                      <div
                        style={{
                          width: "20px",
                          height: "20px",
                          backgroundColor: "#2A5C45",
                          borderRadius: "4px",
                        }}
                      ></div>
                      <div
                        style={{
                          width: "20px",
                          height: "20px",
                          backgroundColor: "#37a15e",
                          borderRadius: "4px",
                        }}
                      ></div>
                      <div
                        style={{
                          width: "20px",
                          height: "20px",
                          backgroundColor: "#5abb7e",
                          borderRadius: "4px",
                        }}
                      ></div>
                      <div
                        style={{
                          width: "20px",
                          height: "20px",
                          backgroundColor: "#8cd4a8",
                          borderRadius: "4px",
                        }}
                      ></div>
                    </div>
                  </div>
                </div>

                <div className="col-md-4">
                  <div className="card-yaguarete card-interactive">
                    <h5>⚡ React + TypeScript</h5>
                    <p>
                      Configuración moderna con Vite, tipos estrictos y
                      desarrollo rápido
                    </p>
                    <div
                      className="skeleton skeleton-text"
                      style={{ height: "8px", width: "80%", margin: "0 auto" }}
                    ></div>
                  </div>
                </div>

                <div className="col-md-4">
                  <div className="card-yaguarete card-interactive">
                    <h5>🎯 Componentes Listos</h5>
                    <p>
                      Botones, tarjetas, formularios y skeletons con diseño
                      consistente
                    </p>
                    <button className="btn-primary-yaguarete btn-sm">
                      Demo Button
                    </button>
                  </div>
                </div>
              </div>

              {/* Demo de botones */}
              <div className="mb-5">
                <h3 className="mb-4">Sistema de Botones</h3>
                <div className="d-flex flex-wrap gap-3 justify-content-center">
                  <button className="btn-primary-yaguarete">Primario</button>
                  <button className="btn-secondary-yaguarete">
                    Secundario
                  </button>
                  <button className="btn-gradient-primary">Gradiente</button>
                  <button className="btn-outline-primary-yaguarete">
                    Outline
                  </button>
                  <button className="btn-ghost-primary">Ghost</button>
                </div>
              </div>

              {/* Demo de skeleton */}
              <div className="mb-5">
                <h3 className="mb-4">Skeleton Loading</h3>
                <div className="row g-3">
                  <div className="col-md-6">
                    <div className="skeleton-product-card">
                      <div className="skeleton skeleton-image"></div>
                      <div className="skeleton skeleton-title"></div>
                      <div className="skeleton skeleton-text"></div>
                      <div className="skeleton skeleton-text"></div>
                      <div className="skeleton skeleton-price"></div>
                      <div className="skeleton skeleton-button"></div>
                    </div>
                  </div>
                  <div className="col-md-6">
                    <div className="skeleton-user-profile">
                      <div className="skeleton skeleton-avatar"></div>
                      <div className="skeleton skeleton-name"></div>
                      <div className="skeleton skeleton-role"></div>
                      <div className="skeleton-stats">
                        <div className="skeleton-stat">
                          <div className="skeleton skeleton-number"></div>
                          <div className="skeleton skeleton-label"></div>
                        </div>
                        <div className="skeleton-stat">
                          <div className="skeleton skeleton-number"></div>
                          <div className="skeleton skeleton-label"></div>
                        </div>
                        <div className="skeleton-stat">
                          <div className="skeleton skeleton-number"></div>
                          <div className="skeleton skeleton-label"></div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              {/* Estado del proyecto */}
              <div className="glass p-4 rounded">
                <h4>✅ Sprint 1 - Configuración Completada</h4>
                <div className="row text-start mt-4">
                  <div className="col-md-6">
                    <h6>✅ Completado:</h6>
                    <ul className="list-unstyled">
                      <li>✅ Proyecto React + TypeScript con Vite</li>
                      <li>✅ Bootstrap 5 + React Bootstrap</li>
                      <li>✅ Sistema de colores avanzado (SCSS)</li>
                      <li>✅ Componentes base (botones, cards, forms)</li>
                      <li>✅ Skeleton loading animations</li>
                      <li>✅ Tipos TypeScript completos</li>
                      <li>✅ React Query configurado</li>
                      <li>✅ Estructura de carpetas organizada</li>
                    </ul>
                  </div>
                  <div className="col-md-6">
                    <h6>🔄 Siguientes pasos (Sprint 2):</h6>
                    <ul className="list-unstyled">
                      <li>🔄 Componentes React funcionales</li>
                      <li>🔄 Integración con backend CI4</li>
                      <li>🔄 Sistema de routing</li>
                      <li>🔄 Estado global con Zustand</li>
                      <li>🔄 Formularios con React Hook Form</li>
                      <li>🔄 Testing básico</li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </main>
      </div>

      {/* React Query Devtools solo en desarrollo */}
      {import.meta.env.DEV && <ReactQueryDevtools initialIsOpen={false} />}
    </QueryClientProvider>
  );
}

export default App;
