new Vue({
  el: '#dashboard',
  data() {
    return {
      estadisticas: {
        total_productos: 0,
        total_clientes: 0,
        total_entradas: 0,
        total_salidas: 0,
        valor_inventario: 0,
        stock_bajo: 0
      },
      loading: true,
      charts: {
        entradasSalidas: null,
        productosMasVendidos: null,
        clientesActivos: null,
        movimientosCategorias: null
      },
      inventarioBajo: [],
      ultimasTransacciones: []
    }
  },
  created() {
    this.cargarDatos();
  },
  mounted() {
    // Inicializar gráficas después de que el DOM esté listo
    this.$nextTick(() => {
      this.inicializarGraficas();
    });
  },
  methods: {
    cargarDatos() {
      this.loading = true;
      
      // Cargar estadísticas generales
      this.getEstadisticas();
      
      // Cargar datos para gráficas
      this.getEntradasSalidasMes();
      this.getProductosMasVendidos();
      this.getInventarioBajo();
      this.getUltimasTransacciones();
      this.getClientesActivos();
      this.getMovimientosPorCategoria();
      
      this.loading = false;
    },
    
    getEstadisticas() {
      axios.post('controlador/dashboard.php?op=getEstadisticas').then(resp => {
        this.estadisticas = resp.data;
      });
    },
    
    getEntradasSalidasMes() {
      axios.post('controlador/dashboard.php?op=getEntradasSalidasMes').then(resp => {
        this.renderGraficaEntradasSalidas(resp.data);
      });
    },
    
    getProductosMasVendidos() {
      axios.post('controlador/dashboard.php?op=getProductosMasVendidos').then(resp => {
        this.renderGraficaProductosMasVendidos(resp.data);
      });
    },
    
    getInventarioBajo() {
      axios.post('controlador/dashboard.php?op=getInventarioBajo').then(resp => {
        this.inventarioBajo = resp.data;
      });
    },
    
    getUltimasTransacciones() {
      axios.post('controlador/dashboard.php?op=getUltimasTransacciones').then(resp => {
        this.ultimasTransacciones = resp.data;
      });
    },
    
    getClientesActivos() {
      axios.post('controlador/dashboard.php?op=getClientesActivos').then(resp => {
        this.renderGraficaClientesActivos(resp.data);
      });
    },
    
    getMovimientosPorCategoria() {
      axios.post('controlador/dashboard.php?op=getMovimientosPorCategoria').then(resp => {
        this.renderGraficaMovimientosCategorias(resp.data);
      });
    },
    
    inicializarGraficas() {
      // Las gráficas se inicializarán cuando lleguen los datos
    },
    
    renderGraficaEntradasSalidas(data) {
      const ctx = document.getElementById('chartEntradasSalidas');
      if (!ctx) return;
      
      if (this.charts.entradasSalidas) {
        this.charts.entradasSalidas.destroy();
      }
      
      this.charts.entradasSalidas = new Chart(ctx, {
        type: 'line',
        data: {
          labels: data.map(item => item.mes),
          datasets: [
            {
              label: 'Entradas',
              data: data.map(item => item.entradas),
              borderColor: 'rgb(75, 192, 192)',
              backgroundColor: 'rgba(75, 192, 192, 0.2)',
              tension: 0.4
            },
            {
              label: 'Salidas',
              data: data.map(item => item.salidas),
              borderColor: 'rgb(255, 99, 132)',
              backgroundColor: 'rgba(255, 99, 132, 0.2)',
              tension: 0.4
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'top',
            },
            title: {
              display: true,
              text: 'Entradas vs Salidas (Últimos 6 meses)'
            }
          },
          scales: {
            y: {
              beginAtZero: true
            }
          }
        }
      });
    },
    
    renderGraficaProductosMasVendidos(data) {
      const ctx = document.getElementById('chartProductosMasVendidos');
      if (!ctx) return;
      
      if (this.charts.productosMasVendidos) {
        this.charts.productosMasVendidos.destroy();
      }
      
      this.charts.productosMasVendidos = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: data.map(item => item.nombre.substring(0, 20) + '...'),
          datasets: [{
            label: 'Unidades Vendidas',
            data: data.map(item => item.cantidad),
            backgroundColor: [
              'rgba(255, 99, 132, 0.7)',
              'rgba(54, 162, 235, 0.7)',
              'rgba(255, 206, 86, 0.7)',
              'rgba(75, 192, 192, 0.7)',
              'rgba(153, 102, 255, 0.7)',
              'rgba(255, 159, 64, 0.7)',
              'rgba(199, 199, 199, 0.7)',
              'rgba(83, 102, 255, 0.7)',
              'rgba(255, 99, 255, 0.7)',
              'rgba(99, 255, 132, 0.7)'
            ],
            borderColor: [
              'rgba(255, 99, 132, 1)',
              'rgba(54, 162, 235, 1)',
              'rgba(255, 206, 86, 1)',
              'rgba(75, 192, 192, 1)',
              'rgba(153, 102, 255, 1)',
              'rgba(255, 159, 64, 1)',
              'rgba(199, 199, 199, 1)',
              'rgba(83, 102, 255, 1)',
              'rgba(255, 99, 255, 1)',
              'rgba(99, 255, 132, 1)'
            ],
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false
            },
            title: {
              display: true,
              text: 'Top 10 Productos Más Vendidos (Este Mes)'
            }
          },
          scales: {
            y: {
              beginAtZero: true
            }
          }
        }
      });
    },
    
    renderGraficaClientesActivos(data) {
        const ctx = document.getElementById('chartClientesActivos');
        if (!ctx) return;
        
        if (this.charts.clientesActivos) {
            this.charts.clientesActivos.destroy();
        }
        
        this.charts.clientesActivos = new Chart(ctx, {
            type: 'bar', // Cambiado de 'horizontalBar' a 'bar'
            data: {
            labels: data.map(item => item.cliente.substring(0, 20)),
            datasets: [
                {
                label: 'Entradas',
                data: data.map(item => item.entradas),
                backgroundColor: 'rgba(75, 192, 192, 0.7)',
                },
                {
                label: 'Salidas',
                data: data.map(item => item.salidas),
                backgroundColor: 'rgba(255, 99, 132, 0.7)',
                }
            ]
            },
            options: {
            indexAxis: 'y', // Esta línea hace que las barras sean horizontales
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                position: 'top',
                },
                title: {
                display: true,
                text: 'Top 10 Clientes Más Activos (Este Mes)'
                }
            },
            scales: {
                x: {
                stacked: true,
                beginAtZero: true
                },
                y: {
                stacked: true
                }
            }
            }
        });
    },
    
    renderGraficaMovimientosCategorias(data) {
      const ctx = document.getElementById('chartMovimientosCategorias');
      if (!ctx) return;
      
      if (this.charts.movimientosCategorias) {
        this.charts.movimientosCategorias.destroy();
      }
      
      this.charts.movimientosCategorias = new Chart(ctx, {
        type: 'doughnut',
        data: {
          labels: data.map(item => item.categoria),
          datasets: [{
            data: data.map(item => item.cantidad),
            backgroundColor: [
              'rgba(255, 99, 132, 0.7)',
              'rgba(54, 162, 235, 0.7)',
              'rgba(255, 206, 86, 0.7)',
              'rgba(75, 192, 192, 0.7)',
              'rgba(153, 102, 255, 0.7)',
              'rgba(255, 159, 64, 0.7)'
            ],
            borderColor: [
              'rgba(255, 99, 132, 1)',
              'rgba(54, 162, 235, 1)',
              'rgba(255, 206, 86, 1)',
              'rgba(75, 192, 192, 1)',
              'rgba(153, 102, 255, 1)',
              'rgba(255, 159, 64, 1)'
            ],
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'right',
            },
            title: {
              display: true,
              text: 'Movimientos por Categoría (Este Mes)'
            }
          }
        }
      });
    },
    
    formatearNumero(numero) {
      return new Intl.NumberFormat('es-CO').format(numero);
    },
    
    formatearFecha(fecha) {
      const date = new Date(fecha);
      return date.toLocaleDateString('es-CO', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      });
    }
  }
});