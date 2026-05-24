# Link Shortener API

Proyecto universitario para la materia Integración Continua.

El objetivo principal es demostrar el uso de Docker, GitHub, Jenkins, Travis CI y herramientas relacionadas con integración continua.

Este proyecto implementa un acortador de links como API REST usando PHP 8.2 vanilla, sin frameworks, con una arquitectura MVC simple.

## Tecnologías utilizadas

- PHP 8.2
- Apache
- PostgreSQL 16
- Docker
- Docker Compose
- PDO
- pdo_pgsql

## Arquitectura

El proyecto utiliza dos contenedores Docker:

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





# Resumen de comandos Docker - Link Shortener API

Proyecto: API REST de acortador de links con PHP 8.2, Apache, PostgreSQL 16 y Docker Compose.

---

## 1. Construir y levantar los contenedores

Este comando construye la imagen de la API y levanta los servicios definidos en `docker-compose.yml`.

```terminal
docker compose up -d --build
```

- `up`: levanta los servicios.
- `-d`: ejecuta los contenedores en segundo plano.
- `--build`: fuerza la construcción de la imagen.

---

## 2. Levantar los contenedores sin reconstruir

Usar cuando no se ha cambiado el Dockerfile ni la configuración base.

```terminal
docker compose up -d
```

---

## 3. Ver contenedores activos

```terminal
docker ps
```

Deberían aparecer:

```txt
link_shortener_api
link_shortener_db
```

---

## 4. Ver todos los contenedores, incluso detenidos

```terminal
docker ps -a
```

---

## 5. Detener los contenedores

Detiene y elimina los contenedores, pero conserva los datos del volumen de PostgreSQL.

```terminal
docker compose down
```

---

## 6. Detener contenedores y eliminar volumen de base de datos

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
docker exec -it link_shortener_api bash
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
docker exec -it link_shortener_db bash
```

Salir del contenedor:

```bash
exit
```

---

## 15. Entrar a PostgreSQL desde el contenedor

Primero entrar al contenedor:

```terminal
docker exec -it link_shortener_db bash
```

Luego ejecutar:

```bash
psql -U postgres -d link_shortener_db
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

## 25. Reiniciar un contenedor específico

API:

```terminal
docker restart link_shortener_api
```

Base de datos:

```terminal
docker restart link_shortener_db
```

---

## 26. Detener un contenedor específico

API:

```terminal
docker stop link_shortener_api
```

Base de datos:

```terminal
docker stop link_shortener_db
```

---

## 27. Iniciar un contenedor detenido

API:

```terminal
docker start link_shortener_api
```

Base de datos:

```terminal
docker start link_shortener_db
```

---

## 28. Eliminar contenedores específicos

Primero deben estar detenidos.

```terminal
docker rm link_shortener_api
docker rm link_shortener_db
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









