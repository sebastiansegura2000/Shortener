# Link Shortener API

Proyecto universitario para la materia Integración Continua.

El objetivo principal es demostrar el uso de Docker, GitHub, Jenkins, Travis CI y herramientas relacionadas con integración continua.

Este proyecto implementa un acortador de links como API REST usando PHP 8.2 vanilla, sin frameworks, con una arquitectura MVC simple.

---

## Tecnologías utilizadas

- PHP 8.2
- Apache
- PostgreSQL 16
- Docker
- Docker Compose
- PDO
- pdo_pgsql
- Jenkins

---

## Arquitectura

El proyecto utiliza contenedores Docker para ejecutar la API, la base de datos y el entorno de integración continua.

### Entorno local

El entorno local utiliza dos contenedores principales:

1. Contenedor API
   - PHP 8.2
   - Apache
   - Expone el puerto 8080 del host hacia el puerto 80 del contenedor

2. Contenedor Base de Datos
   - PostgreSQL 16
   - Expone el puerto 5432
   - Inicializa la tabla `links` usando `init.sql`

Ambos contenedores se comunican por medio de una red interna creada por Docker Compose.

La API se conecta a PostgreSQL usando el nombre del servicio:

```env
DB_HOST=db
```

### Entorno de integración continua

El entorno CI utiliza Jenkins ejecutado también mediante Docker.

La arquitectura esperada es:

```txt
GitHub
↓
Jenkins
↓
Jenkinsfile
↓
Docker Compose CI
↓
API PHP
↓
PostgreSQL
```

---

# Resumen de comandos Docker - Link Shortener API

Proyecto: API REST de acortador de links con PHP 8.2, Apache, PostgreSQL 16, Docker Compose y Jenkins.

---

## 1. Construir y levantar los contenedores locales

Este comando construye la imagen de la API y levanta los servicios definidos en `docker-compose.yml`.

```terminal
docker compose up -d --build
```

- `up`: levanta los servicios.
- `-d`: ejecuta los contenedores en segundo plano.
- `--build`: fuerza la construcción de la imagen.

---

## 2. Levantar los contenedores locales sin reconstruir

Usar cuando no se ha cambiado el Dockerfile ni la configuración base.

```terminal
docker compose up -d
```

---

## 3. Ver contenedores activos

```terminal
docker ps
```

Deberían aparecer contenedores asociados a los servicios:

```txt
api
db
```

El nombre exacto del contenedor puede variar según el nombre del proyecto Docker Compose.

---

## 4. Ver todos los contenedores, incluso detenidos

```terminal
docker ps -a
```

---

## 5. Detener los contenedores locales

Detiene y elimina los contenedores, pero conserva los datos del volumen de PostgreSQL.

```terminal
docker compose down
```

---

## 6. Detener contenedores locales y eliminar volumen de base de datos

Este comando borra también los datos guardados en PostgreSQL.

```terminal
docker compose down -v
```

Útil cuando se quiere reiniciar la base de datos desde cero y volver a ejecutar `init.sql`.

---

## 7. Recrear todo desde cero

```terminal
docker compose down -v
docker compose up -d --build
```

Este flujo:

1. Detiene contenedores.
2. Elimina volumen de PostgreSQL.
3. Reconstruye la imagen de PHP.
4. Crea nuevamente los contenedores.
5. Ejecuta otra vez `docker/postgres/init.sql`.

---

## 8. Reconstruir imágenes

Usar cuando se modifica el `Dockerfile`.

```terminal
docker compose build
```

O levantar reconstruyendo directamente:

```terminal
docker compose up -d --build
```

---

## 9. Ver logs de todos los servicios

```terminal
docker compose logs
```

---

## 10. Ver logs de la API

```terminal
docker compose logs api
```

---

## 11. Ver logs de PostgreSQL

```terminal
docker compose logs db
```

---

## 12. Ver logs en tiempo real

Todos los servicios:

```terminal
docker compose logs -f
```

Solo API:

```terminal
docker compose logs -f api
```

Solo base de datos:

```terminal
docker compose logs -f db
```

---

## 13. Entrar al contenedor de la API

```terminal
docker compose exec api bash
```

Dentro del contenedor, la aplicación estará en:

```bash
/var/www/html
```

Salir del contenedor:

```bash
exit
```

---

## 14. Entrar al contenedor de PostgreSQL

```terminal
docker compose exec db bash
```

Salir del contenedor:

```bash
exit
```

---

## 15. Entrar a PostgreSQL desde el contenedor

Primero entrar al contenedor:

```terminal
docker compose exec db bash
```

Luego ejecutar:

```bash
psql -U postgres -d link_shortener_db
```

También se puede ejecutar directamente:

```terminal
docker compose exec db psql -U postgres -d link_shortener_db
```

---

## 16. Consultar registros en PostgreSQL

Dentro de `psql`:

```sql
SELECT * FROM links;
```

---

## 17. Ver estructura de la tabla

Dentro de `psql`:

```sql
\d links
```

---

## 18. Eliminar registros manualmente

Dentro de `psql`:

```sql
DELETE FROM links;
```

---

## 19. Reiniciar el contador SERIAL de la tabla

Dentro de `psql`:

```sql
TRUNCATE TABLE links RESTART IDENTITY;
```

---

## 20. Salir de PostgreSQL

Dentro de `psql`:

```sql
\q
```

---

## 21. Ver imágenes Docker

```terminal
docker images
```

---

## 22. Ver volúmenes Docker

```terminal
docker volume ls
```

---

## 23. Ver redes Docker

```terminal
docker network ls
```

---

## 24. Inspeccionar la red del proyecto

El nombre puede variar, pero normalmente será algo parecido a:

```terminal
docker network inspect shortener_link_shortener_network
```

También se puede revisar el nombre exacto con:

```terminal
docker network ls
```

---

## 25. Reiniciar un servicio específico

API:

```terminal
docker compose restart api
```

Base de datos:

```terminal
docker compose restart db
```

---

## 26. Detener un servicio específico

API:

```terminal
docker compose stop api
```

Base de datos:

```terminal
docker compose stop db
```

---

## 27. Iniciar un servicio detenido

API:

```terminal
docker compose start api
```

Base de datos:

```terminal
docker compose start db
```

---

## 28. Eliminar contenedores del proyecto local

Primero se deben detener los contenedores.

```terminal
docker compose down
```

Si también se desea eliminar el volumen de PostgreSQL:

```terminal
docker compose down -v
```

---

## 29. Probar conexión general desde navegador o Postman

API:

```txt
http://localhost:8080/health
```

PostgreSQL se comunica internamente con la API usando:

```env
DB_HOST=db
```

No se usa `localhost` dentro del contenedor PHP para conectarse a PostgreSQL.

---

# Integración Continua con Jenkins

Además del entorno local con Docker Compose, el proyecto incluye una configuración para ejecutar Jenkins completamente mediante Docker.

Jenkins se utiliza como herramienta de integración continua para automatizar las siguientes etapas:

1. Obtener el código desde GitHub.
2. Validar la sintaxis de los archivos PHP.
3. Construir las imágenes Docker.
4. Levantar los contenedores de la API y PostgreSQL.
5. Ejecutar un Health Check contra el endpoint `/health`.
6. Marcar el pipeline como exitoso o fallido según el resultado.

---

## 30. Archivos agregados para Jenkins

Para la integración continua se agregaron los siguientes archivos:

```txt
docker-compose.jenkins.yml
Jenkinsfile
docker/jenkins/Dockerfile
docker-compose.ci.yml
docker/postgres/Dockerfile
.env.example
```

### `docker-compose.jenkins.yml`

Define el contenedor de Jenkins.

Este archivo levanta Jenkins en Docker y lo expone en el puerto `8090`.

### `docker/jenkins/Dockerfile`

Define una imagen personalizada de Jenkins basada en `jenkins/jenkins:lts-jdk17`.

Esta imagen incluye:

- Jenkins LTS.
- Docker CLI.
- Docker Compose Plugin.

Esto permite que Jenkins pueda ejecutar comandos como:

```terminal
docker compose build
docker compose up -d
docker compose ps
```

### `Jenkinsfile`

Define el pipeline de integración continua.

El pipeline ejecuta las siguientes etapas:

```txt
Checkout
Preparar entorno
Validación PHP
Docker Build
Docker Deploy
Health Check
Resultado
```

### `docker-compose.ci.yml`

Archivo Docker Compose usado exclusivamente por Jenkins.

A diferencia del `docker-compose.yml` local, este archivo no usa volúmenes tipo bind mount para montar el código fuente, ya que Jenkins se ejecuta dentro de un contenedor.

En su lugar, la imagen de la API copia el código durante el proceso de build.

### `docker/postgres/Dockerfile`

Imagen personalizada para PostgreSQL usada por el entorno CI.

Este archivo copia `init.sql` dentro del contenedor para inicializar la base de datos durante el pipeline.

### `.env.example`

Archivo de ejemplo con las variables de entorno necesarias para ejecutar el proyecto.

Si Jenkins no encuentra el archivo `.env`, lo crea automáticamente copiando `.env.example`.

---

## 31. Levantar Jenkins con Docker Compose

Para iniciar Jenkins, ejecutar el siguiente comando desde la raíz del proyecto:

```terminal
docker compose -f docker-compose.jenkins.yml up -d --build
```

Este comando:

1. Construye la imagen personalizada de Jenkins.
2. Crea el contenedor `shortener_jenkins`.
3. Expone Jenkins en el puerto `8090`.
4. Permite que Jenkins use Docker mediante el socket `/var/run/docker.sock`.

Después de ejecutar el comando, Jenkins estará disponible en:

```txt
http://localhost:8090
```

---

## 32. Verificar que Jenkins esté corriendo

Para verificar que el contenedor de Jenkins está activo:

```terminal
docker ps
```

Debe aparecer un contenedor llamado:

```txt
shortener_jenkins
```

También se puede revisar desde Docker Desktop en la sección de contenedores.

---

## 33. Obtener la contraseña inicial de Jenkins

La primera vez que Jenkins se inicia, solicita una contraseña temporal de administrador.

Para obtenerla, ejecutar:

```terminal
docker logs shortener_jenkins
```

En los logs se debe buscar un bloque similar a este:

```txt
Please use the following password to proceed to installation:
xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

Copiar esa contraseña y pegarla en la pantalla inicial de Jenkins en:

```txt
http://localhost:8090
```

---

## 34. Configuración inicial de Jenkins

Después de ingresar la contraseña inicial:

1. Seleccionar la opción:

```txt
Install suggested plugins
```

2. Esperar a que Jenkins instale los plugins recomendados.

3. Crear el usuario administrador.

4. Confirmar la URL de Jenkins:

```txt
http://localhost:8090
```

5. Finalizar la configuración inicial.

---

## 35. Crear el pipeline en Jenkins

Desde el panel principal de Jenkins:

1. Seleccionar:

```txt
Nueva Tarea
```

2. Escribir el nombre del job:

```txt
Shortener
```

3. Seleccionar el tipo:

```txt
Pipeline
```

4. Entrar a la configuración del job.

5. En la sección Pipeline, seleccionar:

```txt
Pipeline script from SCM
```

6. Configurar:

```txt
SCM: Git
Repository URL: https://github.com/sebastiansegura2000/Shortener
Branch: main
Script Path: Jenkinsfile
```

7. Guardar la configuración.

---

## 36. Ejecutar el pipeline

Para ejecutar el pipeline manualmente:

1. Entrar al job `Shortener`.
2. Seleccionar:

```txt
Build Now
```

Jenkins ejecutará automáticamente las etapas definidas en el `Jenkinsfile`.

---

## 37. Etapas del pipeline

### Stage 1 - Checkout

Obtiene el código fuente desde GitHub.

```txt
GitHub → Jenkins
```

### Stage 2 - Preparar entorno

Verifica si existe el archivo `.env`.

Si no existe, lo crea copiando `.env.example`.

```terminal
cp .env.example .env
```

### Stage 3 - Validación PHP

Valida la sintaxis de los archivos PHP usando la imagen oficial `php:8.2-cli`.

Esto permite detectar errores de parseo antes de construir y desplegar la aplicación.

### Stage 4 - Docker Build

Construye las imágenes Docker necesarias para el entorno de CI.

```terminal
docker compose -p shortener_ci -f docker-compose.ci.yml build --no-cache
```

### Stage 5 - Docker Deploy

Levanta los contenedores de la API y PostgreSQL usando el archivo `docker-compose.ci.yml`.

```terminal
docker compose -p shortener_ci -f docker-compose.ci.yml up -d
```

### Stage 6 - Health Check

Valida que la API responda correctamente consumiendo el endpoint:

```txt
http://host.docker.internal:8080/health
```

El pipeline espera una respuesta similar a:

```json
{
  "status": "success",
  "database": "connected"
}
```

### Stage 7 - Resultado

Si todas las etapas se ejecutan correctamente, Jenkins marca el pipeline como:

```txt
SUCCESS
```

Si alguna etapa falla, Jenkins marca el pipeline como:

```txt
FAILED
```

---

## 38. Ver contenedores creados por Jenkins

El pipeline de Jenkins levanta los servicios de la API y la base de datos bajo el proyecto Docker Compose:

```txt
shortener_ci
```

Para verlos:

```terminal
docker compose -p shortener_ci -f docker-compose.ci.yml ps
```

También se pueden ver con:

```terminal
docker ps
```

Deberían aparecer contenedores asociados a:

```txt
api
db
```

---

## 39. Ver logs del entorno CI

Logs de todos los servicios del entorno CI:

```terminal
docker compose -p shortener_ci -f docker-compose.ci.yml logs
```

Logs de la API:

```terminal
docker compose -p shortener_ci -f docker-compose.ci.yml logs api
```

Logs de PostgreSQL:

```terminal
docker compose -p shortener_ci -f docker-compose.ci.yml logs db
```

---

## 40. Detener el entorno CI

Para detener los contenedores creados por Jenkins:

```terminal
docker compose -p shortener_ci -f docker-compose.ci.yml down
```

Para detenerlos y eliminar también el volumen de PostgreSQL usado en CI:

```terminal
docker compose -p shortener_ci -f docker-compose.ci.yml down -v
```

---

## 41. Detener Jenkins

Para detener Jenkins:

```terminal
docker compose -f docker-compose.jenkins.yml down
```

Este comando detiene el contenedor de Jenkins, pero conserva su volumen interno.

Por lo tanto, la configuración inicial de Jenkins, usuarios, plugins y jobs se mantienen.

---

## 42. Reiniciar Jenkins

Para volver a iniciar Jenkins:

```terminal
docker compose -f docker-compose.jenkins.yml up -d
```

Si se modifica el `Dockerfile` de Jenkins, se debe reconstruir:

```terminal
docker compose -f docker-compose.jenkins.yml up -d --build
```

---

## 43. Flujo general de integración continua

El flujo implementado es el siguiente:

```txt
GitHub
↓
Jenkins
↓
Jenkinsfile
↓
Validación PHP
↓
Docker Build
↓
Docker Compose CI
↓
API PHP + PostgreSQL
↓
Health Check
↓
SUCCESS / FAILED
```

---

## 44. Diferencia entre Docker local y Docker CI

El proyecto maneja dos archivos Docker Compose principales:

```txt
docker-compose.yml
docker-compose.ci.yml
```

### `docker-compose.yml`

Se usa para desarrollo local.

Permite ejecutar el proyecto normalmente con:

```terminal
docker compose up -d
```

Este archivo puede usar volúmenes locales para montar el código fuente dentro del contenedor.

### `docker-compose.ci.yml`

Se usa únicamente para Jenkins.

No monta el código fuente como volumen local, porque Jenkins se ejecuta dentro de un contenedor y controla Docker mediante el socket del host.

En este entorno, el código se copia directamente dentro de la imagen durante el proceso de build.

Esto permite que el pipeline sea más estable y reproducible.

---

## 45. Probar la API después del pipeline

Después de una ejecución exitosa del pipeline, la API queda disponible en:

```txt
http://localhost:8080
```

Endpoint principal de validación:

```txt
http://localhost:8080/health
```

También se puede probar desde Postman usando la colección del proyecto.

---

## 46. Comandos rápidos Jenkins

Levantar Jenkins:

```terminal
docker compose -f docker-compose.jenkins.yml up -d --build
```

Ver logs de Jenkins:

```terminal
docker logs shortener_jenkins
```

Obtener contraseña inicial:

```terminal
docker logs shortener_jenkins
```

Entrar a Jenkins:

```txt
http://localhost:8090
```

Detener Jenkins:

```terminal
docker compose -f docker-compose.jenkins.yml down
```

Reiniciar Jenkins:

```terminal
docker compose -f docker-compose.jenkins.yml up -d
```

Ejecutar entorno CI manualmente:

```terminal
docker compose -p shortener_ci -f docker-compose.ci.yml up -d --build
```

Detener entorno CI:

```terminal
docker compose -p shortener_ci -f docker-compose.ci.yml down -v
```

---

## 47. Nota importante sobre puertos

La API usa el puerto:

```txt
8080
```

Jenkins usa el puerto:

```txt
8090
```

PostgreSQL usa el puerto:

```txt
5432
```

Si el puerto `8080` ya está ocupado, el pipeline puede fallar al levantar la API.

En ese caso, detener los contenedores locales antes de ejecutar Jenkins:

```terminal
docker compose down
```

O detener el entorno CI anterior:

```terminal
docker compose -p shortener_ci -f docker-compose.ci.yml down -v
```

---

## 48. Solución de problemas comunes

### Jenkins se apaga durante el pipeline

Esto puede ocurrir si Jenkins queda dentro del mismo proyecto Docker Compose que la API y la base de datos.

Para evitarlo, Jenkins se ejecuta con un proyecto Compose separado mediante `docker-compose.jenkins.yml`.

El entorno de CI usa el nombre de proyecto:

```txt
shortener_ci
```

Jenkins usa el nombre de proyecto:

```txt
shortener_jenkins_stack
```

De esta forma, Jenkins puede apagar y recrear la API y PostgreSQL sin detenerse a sí mismo.

### La API responde 404 en Jenkins

Si en los logs aparece algo como:

```txt
DocumentRoot [/var/www/html/public] does not exist
```

significa que el contenedor de la API no tiene el código fuente montado correctamente.

Por esta razón, el entorno CI usa `docker-compose.ci.yml`, donde el código se copia dentro de la imagen durante el build y no depende de volúmenes locales.

### El archivo `init.sql` aparece como directorio

Si en los logs de PostgreSQL aparece:

```txt
init.sql: Is a directory
```

significa que Docker no encontró correctamente el archivo `init.sql` al usar un volumen local.

Por esta razón, el entorno CI usa `docker/postgres/Dockerfile`, que copia `init.sql` dentro de la imagen de PostgreSQL.

---

## 49. Descripción para sustentación

Jenkins se implementó como un contenedor independiente usando Docker Compose. Para que Jenkins pueda ejecutar comandos Docker, se creó una imagen personalizada basada en Jenkins LTS con Docker CLI y Docker Compose Plugin instalados.

El contenedor de Jenkins se comunica con Docker Desktop mediante el socket:

```txt
/var/run/docker.sock
```

El pipeline definido en el `Jenkinsfile` obtiene el código desde GitHub, valida la sintaxis de los archivos PHP, construye las imágenes Docker, despliega los servicios de la API y PostgreSQL usando `docker-compose.ci.yml`, y finalmente consume el endpoint `/health`.

Si el endpoint responde correctamente y confirma la conexión con PostgreSQL, Jenkins marca el pipeline como exitoso. Si alguna etapa falla, el pipeline se marca como fallido y muestra logs de los contenedores para facilitar el diagnóstico.