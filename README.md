# 📦 DECA - Sistema de Gestión Logística

<div align="center">

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Vue.js](https://img.shields.io/badge/Vue.js-35495E?style=for-the-badge&logo=vue.js&logoColor=4FC08D)
![MySQL](https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white)

*Sistema integral de gestión de inventarios, bodegas y logística empresarial*

</div>

## 📋 Descripción

DECA es un sistema de gestión logística completo desarrollado para optimizar los procesos de inventario, control de bodegas, gestión de entradas y salidas de productos, así como el seguimiento detallado de movimientos mediante kardex. El sistema está diseñado para empresas que requieren un control preciso de sus operaciones logísticas.

## ✨ Características Principales

### 🏢 Gestión de Clientes y Bodegas
- **Gestión de Clientes**: Registro y administración completa de clientes
- **Gestión de Bodegas**: Control de múltiples bodegas por cliente
- **Asignación Flexible**: Vinculación dinámica entre clientes y bodegas

### 📦 Gestión de Inventario
- **Productos**: CRUD completo de productos con códigos múltiples (EAN, COVA, COAR)
- **Inventario en Tiempo Real**: Control de existencias por bodega
- **Categorización**: Organización por categorías y tipos de producto
- **Unidades de Medida**: Soporte para diferentes UMB y conversiones

### 📈 Movimientos de Inventario
- **Entradas**: Registro de ingresos con soporte para fechas de vencimiento
- **Salidas**: Control de egresos con validación de existencias
- **Devoluciones**: Gestión de devoluciones totales y parciales
- **Kardex**: Trazabilidad completa de movimientos por producto

### 📊 Reportes y Documentación
- **Reportes PDF**: Generación automática de reportes de entrada, salida y devolución
- **Exportación Excel**: Exportación de datos para análisis externo
- **Kardex Detallado**: Historial completo de movimientos por producto
- **Listas de Recuento**: Generación de listas para inventarios físicos

### 🔐 Seguridad y Control
- **Sistema de Usuarios**: Control de acceso con roles y permisos
- **Auditoría**: Registro de todas las operaciones del sistema
- **Validaciones**: Controles de integridad de datos y existencias

## 🛠️ Tecnologías Utilizadas

### Backend
- **PHP 7.4+**: Lenguaje principal del servidor
- **MySQL 5.7+**: Base de datos relacional
- **PDO**: Interfaz de base de datos
- **FPDF**: Generación de documentos PDF

### Frontend
- **Vue.js 2.x**: Framework JavaScript reactivo
- **AdminLTE 3.x**: Panel de administración responsivo
- **Bootstrap 4.x**: Framework CSS
- **jQuery**: Manipulación DOM y AJAX
- **DataTables**: Tablas interactivas
- **SweetAlert2**: Alertas y notificaciones
- **Axios**: Cliente HTTP para API

### Librerías Adicionales
- **PHPExcel**: Manipulación de archivos Excel
- **SpreadsheetReader**: Lectura de archivos de hoja de cálculo
- **InputMask**: Máscaras de entrada de datos

## 📋 Requisitos del Sistema

### Servidor Web
- **Apache 2.4+** o **Nginx 1.18+**
- **PHP 7.4+** con extensiones:
  - PDO MySQL
  - JSON
  - MBString
  - GD (para manipulación de imágenes)
  - ZIP (para archivos comprimidos)

### Base de Datos
- **MySQL 5.7+** o **MariaDB 10.3+**

### Cliente
- Navegador web moderno (Chrome 60+, Firefox 55+, Safari 12+, Edge 79+)
- JavaScript habilitado

## 🚀 Instalación

### 1. Clonación del Repositorio
```bash
git clone https://github.com/tu-usuario/deca-sistema-logistica.git
cd deca-sistema-logistica
```

### 2. Configuración del Servidor Web
```apache
# Configuración Apache (.htaccess incluido)
DocumentRoot /ruta/al/proyecto
```

### 3. Configuración de Base de Datos
```php
// modelo/dbconnect.php
private $host = 'localhost';
private $dbname = 'db_deca';
private $user = 'tu_usuario';
private $password = 'tu_contraseña';
```

### 4. Importación de Base de Datos
```sql
-- Crear base de datos
CREATE DATABASE db_deca CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Importar estructura (archivo SQL no incluido - debe ser creado)
```

### 5. Configuración de Permisos
```bash
chmod 755 upload_files/
chmod 755 fpdf/
```

## ⚙️ Configuración

### Archivo de Configuración Principal
```json
// includes/web.json
{
    "system_active": "true",
    "maintenance_page": "false",
    "website_title": "DECA",
    "social_link_facebook": "https://decasoluciones.co/"
}
```

### Usuario Administrador por Defecto
- **Usuario**: admin
- **Contraseña**: admin123
- *Cambiar credenciales después del primer acceso*

## 📖 Guía de Uso

### Acceso al Sistema
1. Navegue a `http://tu-dominio.com`
2. Ingrese credenciales de acceso
3. Acceda al panel de administración

### Flujo de Trabajo Recomendado
1. **Configuración Inicial**:
   - Crear clientes
   - Configurar bodegas
   - Registrar usuarios

2. **Gestión de Productos**:
   - Registrar productos
   - Asignar categorías
   - Configurar unidades de medida

3. **Operaciones Diarias**:
   - Registrar entradas
   - Procesar salidas
   - Gestionar devoluciones

4. **Control y Reportes**:
   - Revisar kardex
   - Generar reportes
   - Realizar recuentos

## 📁 Estructura del Proyecto

```
deca-sistema-logistica/
├── assets/                 # Recursos estáticos
│   ├── css/               # Hojas de estilo
│   ├── js/                # Scripts JavaScript/Vue.js
│   ├── img/               # Imágenes
│   └── plugins/           # Librerías externas
├── controlador/           # Controladores PHP
├── modelo/                # Modelos de datos
├── vista/                 # Vistas HTML
├── fpdf/                  # Generación de PDFs
├── includes/              # Archivos de configuración
├── upload_files/          # Archivos subidos
├── rp_inventario/         # Reportes de inventario
├── index.php              # Página de login
├── home.php               # Dashboard principal
└── 500.php                # Página de mantenimiento
```

## 🤝 Contribución

### Cómo Contribuir
1. Fork el proyecto
2. Cree una rama para su feature (`git checkout -b feature/AmazingFeature`)
3. Commit sus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abra un Pull Request

### Estándares de Código
- Seguir PSR-4 para PHP
- Usar nomenclatura camelCase para JavaScript
- Documentar funciones complejas
- Mantener consistencia en el estilo

## 🐛 Reportar Problemas

Para reportar bugs o solicitar features:
1. Verifique que no exista un issue similar
2. Proporcione descripción detallada
3. Incluya pasos para reproducir
4. Adjunte capturas de pantalla si es necesario

## 📝 Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo `LICENSE` para más detalles.

## 👥 Derechos de Autor

**© 2024 DECA Soluciones Logística. Todos los derechos reservados.**

### Desarrollado por:
- **Empresa**: DECA Soluciones Logística
- **Sitio Web**: [decasoluciones.co](https://decasoluciones.co/)
- **Tecnología**: unnamedJG

### Propiedad Intelectual
- El código fuente es propiedad de DECA Soluciones Logística
- Uso permitido bajo los términos de la licencia MIT
- Marca "DECA" es propiedad registrada

### Atribuciones
- AdminLTE: [adminlte.io](https://adminlte.io/)
- Vue.js: [vuejs.org](https://vuejs.org/)
- Bootstrap: [getbootstrap.com](https://getbootstrap.com/)

## 📞 Soporte y Contacto

### Soporte Técnico
- **Email**: soporte@decasoluciones.co
- **Teléfono**: +57 (5) 123-4567
- **Horario**: Lunes a Viernes, 8:00 AM - 6:00 PM COT

### Enlaces Útiles
- [Documentación Técnica](https://docs.decasoluciones.co/)
- [Centro de Ayuda](https://ayuda.decasoluciones.co/)
- [Portal del Cliente](https://portal.decasoluciones.co/)

---

<div align="center">

**⭐ Si este proyecto te ha sido útil, considera darle una estrella en GitHub ⭐**

*Hecho con ❤️ por el equipo de DECA Soluciones Logística*

</div>
