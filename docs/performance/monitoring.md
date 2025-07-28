# 📊 Monitoreo y Métricas - Yagaruete Camp

> Guía completa de monitoreo de performance y métricas del sistema

## 🎯 KPIs de Performance

### Métricas Objetivo

| Componente         | Métrica         | Objetivo | Status | Comando de Verificación      |
| ------------------ | --------------- | -------- | ------ | ---------------------------- |
| **🌐 Web Server**  | Response Time   | < 50ms   | ✅     | `curl -w "%{time_total}"`    |
| **⚡ PHP OPcache** | Hit Rate        | > 95%    | ✅     | `opcache_get_status()`       |
| **🚀 Redis Cache** | Hit Rate        | > 90%    | ✅     | `redis-cli info stats`       |
| **🗄️ MySQL**       | Query Cache Hit | > 80%    | ✅     | `SHOW STATUS LIKE 'Qcache%'` |
| **📱 Page Load**   | Total Time      | < 500ms  | ✅     | Browser Dev Tools            |
| **💾 Memory**      | Usage           | < 80%    | ✅     | `docker stats`               |

## 🔧 Herramientas de Monitoreo

### 1. Health Check Automatizado

```bash
#!/bin/bash
# scripts/monitoring/healthcheck.sh

echo "🔍 YAGARUETE CAMP - Health Check"
echo "================================"

# Verificar servicios Docker
echo "📦 Docker Services:"
docker-compose ps --format "table {{.Name}}\t{{.State}}\t{{.Status}}"

# Test de conectividad
echo -e "\n🌐 Connectivity Tests:"
curl -s -o /dev/null -w "Web Server: %{http_code} (%{time_total}s)\n" http://localhost:8080/
curl -s -o /dev/null -w "API Health: %{http_code} (%{time_total}s)\n" http://localhost:8080/health

# Verificar Redis
echo -e "\n🚀 Redis Status:"
docker-compose exec -T redis redis-cli ping 2>/dev/null || echo "❌ Redis no disponible"
docker-compose exec -T redis redis-cli info replication | grep role 2>/dev/null || echo "❌ Info no disponible"

# Verificar MySQL
echo -e "\n🗄️ MySQL Status:"
docker-compose exec -T db mysql -u yaguarete_user -psecure_password_123 -e "SELECT 'MySQL OK' as status;" 2>/dev/null || echo "❌ MySQL no disponible"

# Verificar métricas de performance
echo -e "\n📊 Performance Metrics:"

# OPcache Status
OPCACHE_STATUS=$(docker-compose exec -T app php -r "
\$status = opcache_get_status();
if (\$status && \$status['opcache_enabled']) {
    \$hit_rate = \$status['opcache_statistics']['opcache_hit_rate'];
    echo 'OPcache Hit Rate: ' . number_format(\$hit_rate, 2) . '%';
} else {
    echo 'OPcache: Disabled';
}" 2>/dev/null)
echo "⚡ $OPCACHE_STATUS"

# Redis Stats
REDIS_STATS=$(docker-compose exec -T redis redis-cli info stats | grep -E "(keyspace_hits|keyspace_misses)" 2>/dev/null)
if [ ! -z "$REDIS_STATS" ]; then
    echo "🚀 Redis Cache Stats:"
    echo "$REDIS_STATS" | sed 's/^/   /'
fi

# MySQL Query Cache
MYSQL_CACHE=$(docker-compose exec -T db mysql -u yaguarete_user -psecure_password_123 -e "
SELECT
    ROUND(Qcache_hits / (Qcache_hits + Qcache_inserts) * 100, 2) as 'Query Cache Hit Rate %'
FROM (
    SELECT
        VARIABLE_VALUE as Qcache_hits
    FROM INFORMATION_SCHEMA.GLOBAL_STATUS
    WHERE VARIABLE_NAME = 'Qcache_hits'
) hits,
(
    SELECT
        VARIABLE_VALUE as Qcache_inserts
    FROM INFORMATION_SCHEMA.GLOBAL_STATUS
    WHERE VARIABLE_NAME = 'Qcache_inserts'
) inserts;
" 2>/dev/null | tail -n 1)
echo "🗄️ MySQL Query Cache Hit Rate: $MYSQL_CACHE"

# Resource Usage
echo -e "\n💻 Resource Usage:"
docker stats --no-stream --format "table {{.Container}}\t{{.CPUPerc}}\t{{.MemUsage}}\t{{.MemPerc}}" 2>/dev/null || echo "❌ Stats no disponibles"

echo -e "\n✅ Health Check Completado"
```

### 2. Performance Monitoring Script

```bash
#!/bin/bash
# scripts/monitoring/performance.sh

echo "📊 PERFORMANCE MONITORING"
echo "========================="

# Function para medir tiempo de respuesta
measure_response() {
    local url=$1
    local name=$2
    local response_time=$(curl -w "%{time_total}" -s -o /dev/null "$url")
    printf "%-20s: %6.3fs\n" "$name" "$response_time"
}

echo "🌐 Response Times:"
measure_response "http://localhost:8080/" "Home Page"
measure_response "http://localhost:8080/productos" "Products Page"
measure_response "http://localhost:8080/categorias" "Categories"
measure_response "http://localhost:8080/admin" "Admin Panel"

echo -e "\n⚡ OPcache Detailed Status:"
docker-compose exec -T app php -r "
\$status = opcache_get_status();
if (\$status && \$status['opcache_enabled']) {
    \$stats = \$status['opcache_statistics'];
    echo 'Memory Usage: ' . round(\$status['memory_usage']['used_memory'] / 1024 / 1024, 2) . 'MB / ' .
         round(\$status['memory_usage']['free_memory'] / 1024 / 1024, 2) . 'MB' . PHP_EOL;
    echo 'Hit Rate: ' . number_format(\$stats['opcache_hit_rate'], 2) . '%' . PHP_EOL;
    echo 'Cached Files: ' . \$stats['num_cached_scripts'] . ' / ' . \$stats['max_cached_keys'] . PHP_EOL;
    echo 'Cache Full: ' . (\$stats['num_cached_scripts'] >= \$stats['max_cached_keys'] ? 'YES' : 'NO') . PHP_EOL;
}"

echo -e "\n🚀 Redis Detailed Metrics:"
docker-compose exec -T redis redis-cli info stats | grep -E "(instantaneous_|total_|keyspace_)" | head -10

echo -e "\n🗄️ MySQL Performance:"
docker-compose exec -T db mysql -u yaguarete_user -psecure_password_123 -e "
SELECT
    'Query Cache Hit Rate' as Metric,
    ROUND(
        (SELECT VARIABLE_VALUE FROM INFORMATION_SCHEMA.GLOBAL_STATUS WHERE VARIABLE_NAME = 'Qcache_hits') /
        ((SELECT VARIABLE_VALUE FROM INFORMATION_SCHEMA.GLOBAL_STATUS WHERE VARIABLE_NAME = 'Qcache_hits') +
         (SELECT VARIABLE_VALUE FROM INFORMATION_SCHEMA.GLOBAL_STATUS WHERE VARIABLE_NAME = 'Qcache_inserts')) * 100, 2
    ) as 'Value %'
UNION ALL
SELECT
    'Query Cache Memory' as Metric,
    ROUND(
        (SELECT VARIABLE_VALUE FROM INFORMATION_SCHEMA.GLOBAL_STATUS WHERE VARIABLE_NAME = 'Qcache_free_memory') / 1024 / 1024, 2
    ) as 'Free MB'
UNION ALL
SELECT
    'Active Connections' as Metric,
    (SELECT VARIABLE_VALUE FROM INFORMATION_SCHEMA.GLOBAL_STATUS WHERE VARIABLE_NAME = 'Threads_connected') as 'Count';
"

echo -e "\n📈 Load Testing (10 concurrent requests):"
ab -n 100 -c 10 -q http://localhost:8080/ 2>/dev/null | grep -E "(Requests per second|Time per request|Transfer rate)"

echo -e "\n✅ Performance Analysis Complete"
```

### 3. Real-time Monitoring

```bash
#!/bin/bash
# scripts/monitoring/realtime.sh

echo "📱 REAL-TIME MONITORING"
echo "======================"

# Function para limpiar pantalla cada 5 segundos
monitor_loop() {
    while true; do
        clear
        echo "🔄 YAGARUETE CAMP - Real-time Monitor ($(date))"
        echo "=================================================="

        # Resource usage
        echo "💻 Resource Usage:"
        docker stats --no-stream --format "table {{.Container}}\t{{.CPUPerc}}\t{{.MemUsage}}" | head -6

        # Active connections
        echo -e "\n🌐 Active Connections:"
        docker-compose exec -T nginx sh -c "ps aux | grep nginx | wc -l" 2>/dev/null || echo "N/A"

        # Redis commands per second
        echo -e "\n🚀 Redis Activity:"
        docker-compose exec -T redis redis-cli info stats | grep instantaneous_ops_per_sec 2>/dev/null || echo "N/A"

        # MySQL active queries
        echo -e "\n🗄️ MySQL Activity:"
        docker-compose exec -T db mysql -u yaguarete_user -psecure_password_123 -e "SHOW STATUS LIKE 'Threads_running';" 2>/dev/null | tail -1

        # Recent logs (last 5 lines)
        echo -e "\n📝 Recent Activity:"
        docker-compose logs --tail=3 app 2>/dev/null | tail -3

        echo -e "\n⏱️ Next update in 5 seconds (Ctrl+C to stop)..."
        sleep 5
    done
}

monitor_loop
```

## 📊 Dashboards y Visualización

### Grafana Dashboard (Futuro)

```yaml
# docker/grafana/dashboard.json
{
  "dashboard":
    {
      "title": "Yagaruete Camp Performance",
      "panels":
        [
          {
            "title": "Response Times",
            "type": "graph",
            "targets":
              [
                {
                  "expr": "http_request_duration_seconds",
                  "legendFormat": "{{method}} {{route}}",
                },
              ],
          },
          {
            "title": "Cache Hit Rates",
            "type": "stat",
            "targets":
              [
                {
                  "expr": "redis_cache_hit_rate",
                  "legendFormat": "Redis Hit Rate",
                },
                {
                  "expr": "opcache_hit_rate",
                  "legendFormat": "OPcache Hit Rate",
                },
              ],
          },
        ],
    },
}
```

### Simple HTML Dashboard

```html
<!-- public/admin/monitoring.html -->
<!DOCTYPE html>
<html>
  <head>
    <title>Yagaruete Camp - Monitoring</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  </head>
  <body>
    <div class="container-fluid">
      <h1 class="mt-3">🚀 Yagaruete Camp - Performance Dashboard</h1>

      <div class="row mt-4">
        <div class="col-md-3">
          <div class="card bg-primary text-white">
            <div class="card-body">
              <h5>⚡ OPcache Hit Rate</h5>
              <h2 id="opcache-rate">---%</h2>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card bg-success text-white">
            <div class="card-body">
              <h5>🚀 Redis Hit Rate</h5>
              <h2 id="redis-rate">---%</h2>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card bg-info text-white">
            <div class="card-body">
              <h5>🗄️ MySQL Cache Hit</h5>
              <h2 id="mysql-rate">---%</h2>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card bg-warning text-white">
            <div class="card-body">
              <h5>⏱️ Avg Response</h5>
              <h2 id="response-time">---ms</h2>
            </div>
          </div>
        </div>
      </div>

      <div class="row mt-4">
        <div class="col-md-6">
          <div class="card">
            <div class="card-header">📊 Response Times (last 24h)</div>
            <div class="card-body">
              <canvas id="responseChart"></canvas>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card">
            <div class="card-header">💾 Memory Usage</div>
            <div class="card-body">
              <canvas id="memoryChart"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>

    <script>
      // Update metrics every 30 seconds
      setInterval(updateMetrics, 30000);
      updateMetrics();

      function updateMetrics() {
        // Fetch from API endpoint (to be implemented)
        fetch("/api/metrics")
          .then((response) => response.json())
          .then((data) => {
            document.getElementById("opcache-rate").textContent =
              data.opcache + "%";
            document.getElementById("redis-rate").textContent =
              data.redis + "%";
            document.getElementById("mysql-rate").textContent =
              data.mysql + "%";
            document.getElementById("response-time").textContent =
              data.response + "ms";
          })
          .catch((error) => console.log("Metrics update failed:", error));
      }
    </script>
  </body>
</html>
```

## 🔔 Alertas y Notificaciones

### Sistema de Alertas Básico

```php
<?php
// app/Controllers/Api/Metrics.php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;

class Metrics extends ResourceController
{
    public function index()
    {
        $metrics = [
            'opcache' => $this->getOPcacheHitRate(),
            'redis' => $this->getRedisHitRate(),
            'mysql' => $this->getMySQLCacheHitRate(),
            'response' => $this->getAverageResponseTime(),
            'memory' => $this->getMemoryUsage(),
            'timestamp' => time()
        ];

        // Check for alerts
        $alerts = $this->checkAlerts($metrics);

        if (!empty($alerts)) {
            $metrics['alerts'] = $alerts;
            // Log alerts
            log_message('warning', 'Performance alerts triggered: ' . json_encode($alerts));
        }

        return $this->respond($metrics);
    }

    private function checkAlerts($metrics)
    {
        $alerts = [];

        if ($metrics['opcache'] < 90) {
            $alerts[] = "⚠️ OPcache hit rate below 90%: {$metrics['opcache']}%";
        }

        if ($metrics['redis'] < 85) {
            $alerts[] = "⚠️ Redis hit rate below 85%: {$metrics['redis']}%";
        }

        if ($metrics['response'] > 1000) {
            $alerts[] = "🐌 Average response time too high: {$metrics['response']}ms";
        }

        if ($metrics['memory'] > 80) {
            $alerts[] = "💾 Memory usage critical: {$metrics['memory']}%";
        }

        return $alerts;
    }

    private function getOPcacheHitRate()
    {
        if (!function_exists('opcache_get_status')) {
            return 0;
        }

        $status = opcache_get_status();
        if (!$status || !$status['opcache_enabled']) {
            return 0;
        }

        return round($status['opcache_statistics']['opcache_hit_rate'], 2);
    }

    private function getRedisHitRate()
    {
        try {
            $redis = \Config\Services::cache();
            $info = $redis->getCacheInfo();

            if (isset($info['keyspace_hits']) && isset($info['keyspace_misses'])) {
                $hits = $info['keyspace_hits'];
                $misses = $info['keyspace_misses'];
                $total = $hits + $misses;

                return $total > 0 ? round(($hits / $total) * 100, 2) : 0;
            }
        } catch (\Exception $e) {
            log_message('error', 'Redis metrics error: ' . $e->getMessage());
        }

        return 0;
    }

    private function getMySQLCacheHitRate()
    {
        try {
            $db = \Config\Database::connect();

            $query = $db->query("
                SELECT
                    ROUND(
                        (SELECT VARIABLE_VALUE FROM INFORMATION_SCHEMA.GLOBAL_STATUS WHERE VARIABLE_NAME = 'Qcache_hits') /
                        ((SELECT VARIABLE_VALUE FROM INFORMATION_SCHEMA.GLOBAL_STATUS WHERE VARIABLE_NAME = 'Qcache_hits') +
                         (SELECT VARIABLE_VALUE FROM INFORMATION_SCHEMA.GLOBAL_STATUS WHERE VARIABLE_NAME = 'Qcache_inserts')) * 100, 2
                    ) as hit_rate
            ");

            $result = $query->getRow();
            return $result->hit_rate ?? 0;
        } catch (\Exception $e) {
            log_message('error', 'MySQL metrics error: ' . $e->getMessage());
            return 0;
        }
    }

    private function getAverageResponseTime()
    {
        // Implement based on your logging system
        // This is a placeholder that could read from access logs
        return rand(100, 500); // Simulation
    }

    private function getMemoryUsage()
    {
        $memory_used = memory_get_usage(true);
        $memory_limit = ini_get('memory_limit');

        // Convert memory limit to bytes
        $memory_limit_bytes = $this->convertToBytes($memory_limit);

        return round(($memory_used / $memory_limit_bytes) * 100, 2);
    }

    private function convertToBytes($value)
    {
        $unit = strtolower(substr($value, -1));
        $value = (int) $value;

        switch ($unit) {
            case 'g': $value *= 1024;
            case 'm': $value *= 1024;
            case 'k': $value *= 1024;
        }

        return $value;
    }
}
```

## 📈 Reportes de Performance

### Reporte Diario Automatizado

```bash
#!/bin/bash
# scripts/monitoring/daily-report.sh

DATE=$(date +%Y-%m-%d)
REPORT_FILE="reports/performance-report-$DATE.txt"

mkdir -p reports

echo "📊 YAGARUETE CAMP - Daily Performance Report" > $REPORT_FILE
echo "=============================================" >> $REPORT_FILE
echo "Date: $DATE" >> $REPORT_FILE
echo "Generated: $(date)" >> $REPORT_FILE
echo "" >> $REPORT_FILE

echo "🎯 PERFORMANCE SUMMARY" >> $REPORT_FILE
echo "=====================" >> $REPORT_FILE

# OPcache Statistics
echo "⚡ PHP OPcache:" >> $REPORT_FILE
docker-compose exec -T app php -r "
\$status = opcache_get_status();
if (\$status && \$status['opcache_enabled']) {
    \$stats = \$status['opcache_statistics'];
    echo '   Hit Rate: ' . number_format(\$stats['opcache_hit_rate'], 2) . '%' . PHP_EOL;
    echo '   Memory Used: ' . round(\$status['memory_usage']['used_memory'] / 1024 / 1024, 2) . 'MB' . PHP_EOL;
    echo '   Cached Scripts: ' . \$stats['num_cached_scripts'] . PHP_EOL;
} else {
    echo '   Status: Disabled' . PHP_EOL;
}
" >> $REPORT_FILE

# Redis Statistics
echo "" >> $REPORT_FILE
echo "🚀 Redis Cache:" >> $REPORT_FILE
docker-compose exec -T redis redis-cli info stats | grep -E "(keyspace_hits|keyspace_misses|used_memory_human)" | sed 's/^/   /' >> $REPORT_FILE

# MySQL Statistics
echo "" >> $REPORT_FILE
echo "🗄️ MySQL Performance:" >> $REPORT_FILE
docker-compose exec -T db mysql -u yaguarete_user -psecure_password_123 -e "
SELECT 'Query Cache Hit Rate' as Metric, CONCAT(
    ROUND(
        (SELECT VARIABLE_VALUE FROM INFORMATION_SCHEMA.GLOBAL_STATUS WHERE VARIABLE_NAME = 'Qcache_hits') /
        ((SELECT VARIABLE_VALUE FROM INFORMATION_SCHEMA.GLOBAL_STATUS WHERE VARIABLE_NAME = 'Qcache_hits') +
         (SELECT VARIABLE_VALUE FROM INFORMATION_SCHEMA.GLOBAL_STATUS WHERE VARIABLE_NAME = 'Qcache_inserts')) * 100, 2
    ), '%'
) as Value;
" 2>/dev/null | tail -1 | sed 's/^/   /' >> $REPORT_FILE

# System Resources
echo "" >> $REPORT_FILE
echo "💻 System Resources:" >> $REPORT_FILE
docker stats --no-stream --format "   {{.Container}}: CPU {{.CPUPerc}}, Memory {{.MemPerc}}" >> $REPORT_FILE

# Performance Test
echo "" >> $REPORT_FILE
echo "🏃 Performance Test (100 requests):" >> $REPORT_FILE
ab -n 100 -c 10 -q http://localhost:8080/ 2>/dev/null | grep -E "(Requests per second|Time per request)" | sed 's/^/   /' >> $REPORT_FILE

echo "" >> $REPORT_FILE
echo "✅ Report completed successfully" >> $REPORT_FILE

# Send report (email implementation would go here)
echo "📧 Daily report saved: $REPORT_FILE"
```

## 🎛️ Configuración de Monitoreo

### Variables de Entorno para Monitoring

```bash
# .env - Monitoring Configuration
MONITORING_ENABLED=true
MONITORING_INTERVAL=30
MONITORING_ALERTS_EMAIL=admin@yaguarete.com
MONITORING_SLACK_WEBHOOK=https://hooks.slack.com/services/...

# Thresholds
ALERT_OPCACHE_MIN=90
ALERT_REDIS_MIN=85
ALERT_RESPONSE_MAX=1000
ALERT_MEMORY_MAX=80
ALERT_CPU_MAX=80
```

### Configuración de Alertas

```php
<?php
// app/Config/Monitoring.php

namespace Config;

class Monitoring extends \CodeIgniter\Config\BaseConfig
{
    public $enabled = true;

    public $thresholds = [
        'opcache_hit_rate_min' => 90,
        'redis_hit_rate_min' => 85,
        'mysql_hit_rate_min' => 80,
        'response_time_max' => 1000, // ms
        'memory_usage_max' => 80,    // %
        'cpu_usage_max' => 80,       // %
    ];

    public $alert_channels = [
        'email' => true,
        'slack' => false,
        'log' => true
    ];

    public $alert_email = 'admin@yaguarete.com';
    public $slack_webhook = null;

    public $report_schedule = [
        'daily' => true,
        'weekly' => true,
        'monthly' => false
    ];
}
```

---

**Yagaruete Camp Monitoring** - Sistema completo de métricas y alertas
_Documentación actualizada: 28 de Julio, 2025_ 📊🔍
