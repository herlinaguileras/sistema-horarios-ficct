# 🎓 Sistema de Gestión de Horarios - FICCT

Sistema web para la gestión de horarios académicos, asistencia docente y administración de aulas de la Facultad de Ingeniería de Ciencias de la Computación y Telecomunicaciones.

## 🚀 Características

- ✅ **Gestión de Horarios**: Creación y administración de horarios por semestre
- ✅ **Control de Asistencia**: Registro de asistencia docente mediante botón o código QR
- ✅ **Gestión de Aulas**: Control de ocupación y disponibilidad de aulas
- ✅ **Reportes**: Exportación a Excel y PDF de horarios y asistencias
- ✅ **Sistema de Roles**: Administradores y Docentes con permisos diferenciados
- ✅ **Multi-tenant**: Soporte para múltiples semestres

## 📋 Requisitos del Sistema

- **PHP** >= 8.4
- **PostgreSQL** >= 13
- **Composer** >= 2.0
- **Node.js** >= 20.x
- **NPM** >= 10.x

### Extensiones PHP Requeridas

```
- php-pgsql
- php-gd
- php-zip
- php-xml
- php-mbstring
- php-curl
```

## 🛠️ Instalación Local

### 1. Clonar el repositorio

```bash
git clone https://github.com/herlinaguileras/sistema-horarios-ficct.git
cd sistema-horarios-ficct
```

### 2. Instalar dependencias de PHP

```bash
composer install
```

### 3. Instalar dependencias de Node.js

```bash
npm install
```

### 4. Configurar variables de entorno

```bash
cp .env.example .env
```

Edita `.env` y configura:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=tu_base_datos
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña
```

### 5. Generar key de aplicación

```bash
php artisan key:generate
```

### 6. Ejecutar migraciones

```bash
php artisan migrate
```

### 7. Crear roles iniciales

```bash
php artisan db:seed --class=RoleSeeder
```

### 8. Compilar assets

```bash
npm run build
# O para desarrollo:
npm run dev
```

### 9. Iniciar servidor de desarrollo

```bash
php artisan serve
```

La aplicación estará disponible en: `http://localhost:8000`

## 🎯 Uso Rápido

### Crear Usuario Administrador

```bash
php artisan tinker
```

```php
$user = User::create([
    'name' => 'Admin',
    'email' => 'admin@ficct.edu.bo',
    'password' => Hash::make('password123')
]);

$adminRole = Role::where('name', 'admin')->first();
$user->roles()->attach($adminRole->id);
```

## 🚢 Deploy a Producción

### Opción 1: Railway (Recomendado)

1. Crear cuenta en [Railway.app](https://railway.app)
2. Instalar Railway CLI:

```bash
npm install -g @railway/cli
```

3. Login y deploy:

```bash
railway login
railway init
railway up
```

4. Agregar PostgreSQL:

```bash
railway add
# Seleccionar PostgreSQL
```

5. Configurar variables de entorno en Railway Dashboard usando `.env.production.example`

### Opción 2: Render

Ver documentación completa en `DEPLOYMENT.md`

## 📁 Estructura del Proyecto

```
materia/
├── app/
│   ├── Exports/          # Clases de exportación Excel/PDF
│   ├── Http/
│   │   ├── Controllers/  # Controladores
│   │   └── Middleware/   # Middleware personalizado
│   └── Models/           # Modelos Eloquent
├── database/
│   ├── migrations/       # Migraciones de base de datos
│   └── seeders/          # Seeders
├── resources/
│   ├── views/            # Vistas Blade
│   ├── js/               # JavaScript
│   └── css/              # Estilos
├── routes/
│   └── web.php           # Rutas web
└── public/               # Archivos públicos
```

## 🔐 Seguridad

- ✅ CSRF Protection en todos los formularios
- ✅ Validación de roles y permisos
- ✅ Passwords hasheados con bcrypt
- ✅ Sesiones seguras
- ✅ Variables de entorno protegidas

## 📚 Tecnologías Utilizadas

- **Backend**: Laravel 12
- **Frontend**: Blade, TailwindCSS, Alpine.js
- **Base de datos**: PostgreSQL
- **Autenticación**: Laravel Breeze
- **Exports**: Maatwebsite Excel, DomPDF
- **QR Codes**: SimpleSoftwareIO QR Code

## 🤝 Contribuir

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## 📝 Licencia

Este proyecto es privado y propietario de la FICCT.

## 👥 Autores

- **Herlin Aguilera** - *Desarrollo inicial* - [herlinaguileras](https://github.com/herlinaguileras)

## 📞 Soporte

Para reportar bugs o solicitar features, por favor abre un issue en el repositorio.

---

Desarrollado con ❤️ para la Facultad de Ingeniería de Ciencias de la Computación y Telecomunicaciones
