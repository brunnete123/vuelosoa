# Sistema de reservas de vuelos

Este proyecto es una aplicacion web para registrar usuarios, consultar vuelos, reservar asientos y administrar reservas.

Esta construido con PHP, MySQL, HTML, CSS y JavaScript. Docker se utiliza para ejecutar el servidor web y la base de datos.

## Como iniciar el proyecto

Debes tener Docker Desktop abierto.

Abre una terminal en la carpeta del proyecto y ejecuta:

```powershell
docker compose up -d --build
```


Despues abre esta direccion en el navegador:

http://localhost:8080/

Para detener el proyecto utiliza:

```powershell
docker compose down
```


## Configuracion

Docker crea un servicio web con PHP 8.2 y Apache. La aplicacion se puede abrir en el puerto 8080.

Tambien crea un servicio MySQL 8.0. La base de datos se llama vuelos y utiliza el puerto 3307 del equipo.

PHP se conecta a MySQL con estos datos:

```text
Servidor: db
Usuario: root
Contrasena: rootpassword
Base de datos: vuelos
```


Los datos de MySQL se guardan en el volumen db_data. Por eso no se pierden al detener los contenedores.

Cuando se crea la base por primera vez, Docker ejecuta automaticamente el archivo app/schema.sql.

## Como funciona la aplicacion

1. El usuario puede crear una cuenta desde la pagina de registro.
2. Despues puede iniciar sesion usando su usuario o correo electronico.
3. Al iniciar sesion se guarda el usuario actual en el navegador.
4. La pantalla principal muestra los vuelos que tienen asientos disponibles.
5. El usuario puede filtrar por origen y destino o mostrar todos los vuelos.
6. Para reservar, selecciona la cantidad de asientos y un metodo de pago.
7. El sistema calcula el precio total y guarda la reserva.
8. En Mis reservas puede consultar sus viajes y cancelar una reserva confirmada.
9. Cuando se cancela una reserva, los asientos vuelven a estar disponibles.

El pago se registra dentro del sistema. Se puede seleccionar Tarjeta o Transferencia, pero el proyecto no esta conectado a una plataforma bancaria real.

## Servicios PHP

auth.php se encarga del registro, el inicio de sesion, el cierre de sesion y la validacion de usuarios y correos repetidos.

search_flights.php muestra los vuelos disponibles y permite aplicar filtros opcionales.

reserve_flight.php registra reservas de uno o varios asientos, calcula el total y guarda el metodo de pago.

manage_reservations.php muestra las reservas del usuario y permite cancelarlas.

## Paginas principales

login.html contiene el formulario de inicio de sesion.

register.html contiene el formulario para crear una cuenta.

search.html muestra los vuelos y permite hacer reservas.

reservations.html permite consultar y cancelar reservas.

scripts.js conecta las paginas con los servicios PHP.

styles.css contiene los estilos de la aplicacion.

## Base de datos

La base de datos se llama vuelos y contiene estas tablas:

Users guarda los usuarios registrados.

Flights guarda los vuelos, precios y asientos disponibles.

Reservations guarda las reservas, los asientos, el precio total y el estado del pago.

Para revisar las tablas desde PowerShell puedes ejecutar:

```powershell
docker exec mysql_vuelos mysql -uroot -prootpassword vuelos -e "SHOW TABLES;"
```


Para consultar los datos:

```powershell
docker exec mysql_vuelos mysql -uroot -prootpassword vuelos -e "SELECT * FROM Users; SELECT * FROM Flights; SELECT * FROM Reservations;"
```


## Cuentas iniciales

Usuario: Bruno

Correo: bruno@gmail.com

Contrasena: 123456789

Usuario: Carlos

Correo: carlos@gmail.com

Contrasena: 123456789