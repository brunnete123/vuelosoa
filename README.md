# Vuelos SOA

Sistema de gestión de reservas de vuelos desarrollado con PHP, MySQL, HTML, CSS y JavaScript. La aplicación permite registrar usuarios, buscar vuelos, reservar varios asientos, simular pagos y administrar reservas desde una interfaz web.

## Tecnologías

- PHP 8.2 + Apache
- MySQL 8.0
- Docker + Docker Compose
- HTML, CSS y JavaScript

## Requisitos

- Docker Desktop instalado y ejecutándose
- Cuenta de ngrok y ngrok instalado y autenticado
- Git

## Inicio rápido

1. Abre una terminal en la carpeta del proyecto.
2. Ejecuta:

```powershell
docker compose up -d --build
```

3. Abre la aplicación en el navegador:

```text
http://localhost:8085/
```

4. Para publicar la aplicación mediante ngrok, abre otra terminal y ejecuta:

```powershell
ngrok http 8085
```

5. Abre en el navegador la URL HTTPS que ngrok muestra, por ejemplo:

```text
https://xxxx-xxxx.ngrok-free.app/
```

La URL pública cambia cada vez que se reinicia el túnel. La terminal donde se ejecuta ngrok debe permanecer abierta mientras se usa la aplicación.

6. Para detener la aplicación y el túnel:

```powershell
Ctrl+C
docker compose down
```

## Configuración de Docker

El proyecto crea dos servicios:

- web: servidor PHP + Apache
- db: base de datos MySQL

Los valores de conexión usados por la aplicación son estos:

```text
DB_HOST=db
DB_USER=root
DB_PASSWORD=rootpassword
DB_NAME=vuelos
```

La base de datos se expone en el puerto local:

```text
3307:3306
```

El archivo app/schema.sql se monta automáticamente en el contenedor de MySQL y se ejecuta al crear la base por primera vez.

## Funcionalidades

- Registro de usuarios
- Inicio y cierre de sesión
- Validación de usuario y correo duplicado
- Búsqueda de vuelos por origen y destino
- Filtros opcionales
- Reserva de uno o varios asientos
- Cálculo de total según cantidad y precio
- Selección de método de pago
- Visualización de reservas del usuario
- Cancelación de reservas
- Reapertura de asientos disponibles al cancelar

El sistema simula pagos, sin integración con una pasarela bancaria real.

## Servicios PHP principales

- app/auth.php: registro, login, logout y validaciones
- app/search_flights.php: consulta de vuelos con filtros
- app/reserve_flight.php: creación de reservas y pago
- app/manage_reservations.php: listado y cancelación de reservas

## Páginas principales

- app/login.html: login
- app/register.html: registro
- app/search.html: búsqueda y reserva de vuelos
- app/reservations.html: gestión de reservas
- app/scripts.js: conexión entre frontend y servicios PHP
- app/styles.css: estilos visuales de la aplicación

## Base de datos

La base de datos se llama vuelos y contiene estas tablas:

- Users: usuarios registrados
- Flights: vuelos, precios y asientos disponibles
- Reservations: reservas, cantidad de asientos, total y estado

Para revisar las tablas desde PowerShell:

```powershell
docker exec mysql_vuelos mysql -uroot -prootpassword -D vuelos -e "SHOW TABLES;"
```

Para consultar datos:

```powershell
docker exec mysql_vuelos mysql -uroot -prootpassword -D vuelos -e "SELECT * FROM Users; SELECT * FROM Flights; SELECT * FROM Reservations;"
```

## Usuarios de prueba

```text
Usuario: Bruno
Correo: bruno@gmail.com
Contraseña: 123456789

Usuario: Carlos
Correo: carlos@gmail.com
Contraseña: 123456789
```

## Nota importante

Este proyecto se ejecuta con Docker y se publica externamente mediante un túnel ngrok hacia el puerto local `8085`. La base de datos permanece dentro de Docker y no se expone a través de ngrok.
