# 🔬 TALLER EL CIENTÍFICO

Sistema web para facilitar la gestión y solicitud de cotizaciones de servicios técnicos de manera eficiente y profesional.

## 📋 Descripción

TALLER EL CIENTÍFICO es una aplicación web desarrollada en PHP y MySQL que permite a los usuarios registrarse, iniciar sesión y solicitar cotizaciones de servicios técnicos de forma rápida y organizada. El sistema incluye gestión de perfiles, verificación de correo electrónico, y un panel de administración de cotizaciones.

## ✨ Características Principales

- **Sistema de Autenticación Completo**
  - Registro de usuarios con validación
  - Inicio de sesión seguro
  - Verificación de email
  - Cierre de sesión

- **Gestión de Cotizaciones**
  - Crear nuevas cotizaciones
  - Seguimiento de cotizaciones solicitadas
  - Confirmación de cotización exitosa

- **Perfil de Usuario**
  - Visualización de cuenta
  - Edición de perfil
  - Historial de cotizaciones

- **Páginas Informativas**
  - Página principal (index)
  - Servicios ofrecidos
  - Ubicaciones y sedes
  - Formulario de contacto

## 🛠️ Tecnologías Utilizadas

### Backend
- **PHP** - Lenguaje del servidor
- **MySQL** - Base de datos
- **PDO** - Conexión segura a base de datos

### Frontend
- **HTML5 / CSS3**
- **Bootstrap 5.3.8** - Framework CSS responsivo
- **JavaScript**

### Dependencias
- **PHPMailer** - Envío de correos electrónicos
- **Composer** - Gestor de dependencias PHP

## 📁 Estructura del Proyecto

TALLER_EL_CIENTIFICO/
│
├── index.php # Página principal
├── login.php # Inicio de sesión
├── registro.php # Registro de usuarios
├── logout.php # Cerrar sesión
├── verificar_email.php # Verificación de email
│
├── cuenta.php # Perfil de usuario
├── editar_perfil.php # Editar información personal
│
├── crear_cotizacion.php # Formulario de cotización
├── cotizacion_exitosa.php # Confirmación de cotización
│
├── servicios.php # Catálogo de servicios
├── sedes.php # Ubicaciones del taller
├── contacto.php # Formulario de contacto
│
├── header.php # Encabezado compartido
├── footer.php # Pie de página compartido
├── style.css # Estilos personalizados
│
├── taller_el_cientifico.sql # Base de datos completa
│
├── bootstrap-5.3.8-dist/ # Framework CSS (no incluido)
├── vendor/ # PHPMailer y dependencias (no incluido)
└── composer.json # Archivo de configuración Composer


## 🚀 Instalación

### Requisitos Previos
- PHP 7.4 o superior
- MySQL 5.7 o superior
- Servidor web (Apache/Nginx)
- Composer instalado

### Pasos de Instalación

1. **Clonar o descargar el proyecto**

git clone [URL_DEL_REPOSITORIO]
cd TALLER_EL_CIENTIFICO


2. **Instalar dependencias con Composer**

composer install

Esto instalará automáticamente PHPMailer y otras dependencias necesarias.

3. **Descargar Bootstrap 5.3.8**
- Descargar desde: https://getbootstrap.com/docs/5.3/getting-started/download/
- Extraer en la carpeta `bootstrap-5.3.8-dist/`

4. **Configurar la Base de Datos**
mysql -u root -p
undefined

CREATE DATABASE taller_el_cientifico;
USE taller_el_cientifico;
SOURCE taller_el_cientifico.sql;


5. **Configurar archivo de conexión**
- Editar el archivo de configuración (generalmente `config.php` o en `header.php`)
- Actualizar credenciales de base de datos:
  ```
  define('DB_HOST', 'localhost');
  define('DB_NAME', 'taller_el_cientifico');
  define('DB_USER', 'tu_usuario');
  define('DB_PASS', 'tu_contraseña');
  ```

6. **Configurar PHPMailer**
- Editar configuración de correo en los archivos correspondientes
- Configurar credenciales SMTP para envío de emails

7. **Iniciar el servidor**
- Para desarrollo local con XAMPP: colocar en `htdocs/`
- Acceder a: `http://localhost/TALLER_EL_CIENTIFICO/`

## 📊 Base de Datos

El archivo `taller_el_cientifico.sql` contiene la estructura completa de la base de datos incluyendo:

- Tabla de usuarios
- Tabla de cotizaciones
- Tabla de servicios
- Tabla de sedes
- Relaciones y claves foráneas

## 🔐 Seguridad

- Contraseñas hasheadas con algoritmos seguros
- Protección contra inyección SQL mediante PDO
- Validación de sesiones
- Verificación de correo electrónico
- Sanitización de entradas de usuario

## 📧 Sistema de Correos

El sistema utiliza PHPMailer para:
- Verificación de email al registrarse
- Notificaciones de cotizaciones
- Contacto desde el formulario

## 🎨 Diseño

- Diseño responsivo con Bootstrap 5
- Estilos personalizados en `style.css`
- Compatibilidad con dispositivos móviles
- Interfaz intuitiva y moderna

## 📝 Uso

1. **Registro**: Los usuarios nuevos deben registrarse proporcionando sus datos
2. **Verificación**: Verificar el correo electrónico mediante el link enviado
3. **Login**: Iniciar sesión con credenciales
4. **Cotización**: Crear cotizaciones desde el formulario correspondiente
5. **Seguimiento**: Ver estado de cotizaciones en el perfil

## 🤝 Contribución

Para contribuir al proyecto:
1. Fork el repositorio
2. Crea una rama para tu feature (`git checkout -b feature/NuevaCaracteristica`)
3. Commit tus cambios (`git commit -m 'Añadir nueva característica'`)
4. Push a la rama (`git push origin feature/NuevaCaracteristica`)
5. Abre un Pull Request

## 📄 Licencia

Este proyecto está bajo licencia privada. Todos los derechos reservados.

## 👨‍💻 Autor

Desarrollado por Carlos - Estudiante de 11º Grado
Colegio Eugenio Ferro Falla, Colombia

## 📞 Contacto

Para consultas o soporte, utilizar el formulario de contacto en la aplicación.

---

**Nota**: Las carpetas `bootstrap-5.3.8-dist/`, `vendor/` (PHPMailer) y archivos de Composer no están incluidos en este repositorio. Deben instalarse siguiendo las instrucciones de instalación.
