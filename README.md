# malaika-booking-back

API REST de la plataforma de reservas de Malaika Viatges: viajes, salidas, clientes, pasaportes, rooming, formularios públicos de inscripción, emails y exportación a Excel.

- Producción: https://api.malaika-clients.com (cPanel)
- Consumidor: [malaika-booking-front](../malaika-booking-front/README.md)
- Documentación común del proyecto: [docs/README.md](../docs/README.md)

## Stack

| Componente | Versión (composer.lock) |
| --- | --- |
| PHP | ≥ 8.1 ([composer.json](composer.json)) |
| Laravel | 10.13.2 |
| Laravel Passport | 11.8.7 (autenticación por token Bearer) |
| maatwebsite/excel | 3.1.50 (exportación del rooming) |
| darkaonline/l5-swagger | 8.5.1 (OpenAPI en `/api/documentation`) |
| Base de datos | MySQL 8 o MariaDB |

## Requisitos

- PHP 8.1 o superior (se recomienda 8.2 o 8.3) con las extensiones `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `bcmath` y las que pide PhpSpreadsheet: `ctype`, `dom`, `fileinfo`, `gd`, `iconv`, `libxml`, `simplexml`, `xml`, `xmlreader`, `xmlwriter`, `zip`, `zlib`
- Composer 2
- MySQL 8 o MariaDB
- Un SMTP de pruebas, por ejemplo [Mailpit](https://mailpit.axllent.org/) en el puerto 1025 (es el valor por defecto de `.env.example`)

## Puesta en marcha desde un clon limpio

```bash
composer install
cp .env.example .env
php artisan key:generate
# Crear la base de datos (por defecto: laravel) y ajustar DB_* en .env
php artisan migrate --seed      # tablas + catálogos + datos ficticios + passport:install
php artisan storage:link        # sirve storage/app/public en /storage
php artisan serve               # http://127.0.0.1:8000
```

`migrate --seed` ejecuta [DatabaseSeeder](database/seeders/DatabaseSeeder.php), que carga:

- Los catálogos de idiomas, estados de viaje y de salida, tipos de habitación y tipos de cliente.
- 35 viajes ficticios con salidas y clientes ficticios.
- Al final, `passport:install`, que genera las claves `storage/oauth-*.key` y el cliente de acceso personal.

### Usuario administrador de prueba

Los seeders no crean roles ni usuarios. El rol de administrador debe tener el id 1 ([EnsureUserIsAdmin](app/Http/Middleware/EnsureUserIsAdmin.php)):

```bash
php artisan tinker
```

```php
DB::table('roles')->insert(['id' => 1, 'name' => 'admin', 'description' => 'Administrador', 'created_at' => now(), 'updated_at' => now()]);
$u = App\Models\User::create(['name' => 'Admin', 'email' => 'admin@example.test', 'password' => bcrypt('cambia-esta-clave')]);
$u->roles()->attach(1);
```

Prueba rápida:

```bash
curl http://127.0.0.1:8000/api/forms/current-trip
curl -X POST http://127.0.0.1:8000/api/auth/login -H 'Accept: application/json' \
     -d email=admin@example.test -d password=cambia-esta-clave
```

## Variables de entorno

Se indican los nombres y su finalidad, nunca los valores de producción.

| Variable | Finalidad | Sensible |
| --- | --- | --- |
| `APP_KEY` | Cifrado de Laravel | Sí |
| `APP_ENV`, `APP_DEBUG`, `APP_URL` | Entorno. En producción: `production`, `false` y `https://api.malaika-clients.com` | No |
| `LOG_CHANNEL`, `LOG_LEVEL` | Logs (`stack` → canal `daily`: `storage/logs/laravel-AAAA-MM-DD.log`, 14 días) | No |
| `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | Base de datos | `DB_PASSWORD` sí |
| `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_ENCRYPTION`, `MAIL_FROM_ADDRESS`, `MAIL_FROM_NAME` | SMTP para los emails de inscripción. En local, Mailpit o `MAIL_MAILER=log` | `MAIL_PASSWORD` sí |
| `QUEUE_CONNECTION` | `sync`: los emails se envían dentro de la petición | No |
| `CORS_ALLOWED_ORIGINS` | Orígenes que pueden llamar a la API desde el navegador, separados por comas. En producción: `https://malaika-booking-front.vercel.app`. Sin definir: cualquier origen | No |
| `L5_SWAGGER_USER`, `L5_SWAGGER_PASSWORD` | Basic auth de `/api/documentation`. Si están vacías, la documentación queda bloqueada | Sí |
| `PASSPORT_PRIVATE_KEY`, `PASSPORT_PUBLIC_KEY` (opcionales) | Claves de Passport como variables, en lugar de `storage/oauth-*.key` | Sí |
| `PASSPORT_PERSONAL_ACCESS_CLIENT_ID`, `PASSPORT_PERSONAL_ACCESS_CLIENT_SECRET` (opcionales) | Cliente de acceso personal de Passport | Sí |

Las variables `REDIS_*`, `AWS_*`, `PUSHER_*` y `MEMCACHED_*` vienen de la plantilla de Laravel y no se usan.

## Estructura

| Ruta | Contenido |
| --- | --- |
| [routes/api.php](routes/api.php) | Todas las rutas de la API (prefijo `/api`) |
| [app/Http/Controllers](app/Http/Controllers) | Controladores, con anotaciones OpenAPI `@OA` |
| [app/Services](app/Services) | Lógica de negocio por entidad |
| [app/Models](app/Models) | Modelos Eloquent |
| [app/Http/Resources](app/Http/Resources) | Serialización de las respuestas |
| [app/Mail](app/Mail) + [resources/views/emails](resources/views/emails) | Emails de inscripción y lista de espera (en catalán) |
| [app/Exports](app/Exports) | Excel del rooming (se usa `DeparturesExport2`) |
| [database/migrations](database/migrations) | Esquema |
| [storage/api-docs/api-docs.json](storage/api-docs/api-docs.json) | OpenAPI generado |

Código heredado que no forma parte del funcionamiento actual: las vistas Blade de `resources/views/{auth,client,departure,modal,passport,trip}`, `DBMigrationController` y `DatabaseMigrationService`. Ver [Estado al entregar](../docs/07-estado-entrega.md).

## API

| Grupo | Rutas | Autenticación |
| --- | --- | --- |
| `auth` | `POST login`; `POST register`, `POST create`, `PUT {id}/update` | `login` es público; el resto requiere `auth:api` + `admin` |
| `forms` | `GET current-trip`, `GET trips/{id}`, `POST process` | Público (formulario de inscripción) |
| `room-types` | `GET /`, `GET /{id}` | Público |
| `trips`, `departures`, `clients`, `passports`, `client-types`, `languages`, `exports` | CRUD y operaciones de rooming | `auth:api` |

La referencia completa está en Swagger (`/api/documentation`). Si se modifican las anotaciones, se regenera con `php artisan l5-swagger:generate`.

## Tests

```bash
php artisan test
```

Solo están los tests de ejemplo de Laravel ([tests](tests)). [phpunit.xml](phpunit.xml) usa SQLite en memoria. No hay CI.

## Convenciones

- Controlador → Servicio → Modelo; las respuestas se devuelven con `sendResponse` y `sendError` ([Controller.php](app/Http/Controllers/Controller.php)).
- Validación con `Validator::make(...)->validate()` en el controlador.
- Mensajes de commit en formato Conventional Commits.
- Formato de código: Laravel Pint (`./vendor/bin/pint`), disponible pero no aplicado de forma sistemática.

## Despliegue

Manual, en cPanel. El procedimiento, el orden respecto al front, las comprobaciones y el rollback están en [Infraestructura y despliegue](../docs/05-infraestructura-despliegue.md).
