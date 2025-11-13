# Instalación en Script Executors

Para usar este paquete en los script tasks de ProcessMaker 4, necesitas instalarlo en el script-executor correspondiente.

## Opción 1: Desde la Interfaz Web (Recomendado)

### Paso 1: Editar el Script Executor

1. Ve a **Admin** > **Script Executors** en ProcessMaker
2. Selecciona el script-executor de PHP que quieres modificar
3. Haz clic en **Edit**

### Paso 2: Agregar el Paquete en el Campo Config

En el campo **Config** (que contiene instrucciones Dockerfile), agrega:

```dockerfile
RUN composer config repositories.script-helpers vcs https://github.com/gproly/processmaker-script-helpers.git
RUN composer require processmaker/script-helpers:dev-main
```

**Ejemplo completo del campo Config:**

```dockerfile
# Instalar paquetes adicionales
RUN composer require processmaker/script-helpers
```

### Paso 3: Guardar y Reconstruir

1. Haz clic en **Save**
2. El sistema automáticamente iniciará la reconstrucción del script-executor
3. Espera a que termine la construcción (puede tomar varios minutos)

## Opción 2: Mediante API

### Paso 1: Obtener el ID del Script Executor

```bash
curl -X GET "http://tu-servidor/api/v1/script-executors" \
  -H "Authorization: Bearer TU_TOKEN"
```

### Paso 2: Actualizar el Script Executor

```bash
curl -X PUT "http://tu-servidor/api/v1/script-executors/{ID}" \
  -H "Authorization: Bearer TU_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "PHP Executor",
    "description": "PHP Executor con script-helpers",
    "language": "php",
    "config": "RUN composer require processmaker/script-helpers"
  }'
```

Esto iniciará automáticamente la reconstrucción.

## Opción 3: Si el Paquete está en un Repositorio Privado

Si el paquete está en un repositorio privado (GitHub, GitLab, etc.), necesitas configurar el repositorio primero:

```dockerfile
# Configurar repositorio privado
RUN composer config repositories.script-helpers vcs https://github.com/tu-org/processmaker-script-helpers.git

# Instalar el paquete
RUN composer require processmaker/script-helpers:dev-main
```

O si usas un token de acceso:

```dockerfile
# Configurar autenticación (si es necesario)
RUN composer config --global github-oauth.github.com TU_GITHUB_TOKEN

# Configurar repositorio
RUN composer config repositories.script-helpers vcs https://github.com/tu-org/processmaker-script-helpers.git

# Instalar el paquete
RUN composer require processmaker/script-helpers:dev-main
```

## Opción 4: Si el Paquete está Localmente (Desarrollo)

Si el paquete está en una ruta local del servidor, puedes copiarlo al contenedor:

```dockerfile
# Copiar el paquete local
COPY /ruta/local/processmaker-script-helpers /tmp/processmaker-script-helpers

# Instalar desde path local
RUN composer config repositories.script-helpers path /tmp/processmaker-script-helpers && \
    composer require processmaker/script-helpers:@dev
```

## Opción 5: Publicar en Packagist (Más Fácil)

1. Sube el código a GitHub/GitLab
2. Regístralo en [Packagist.org](https://packagist.org)
3. Luego simplemente usa:

```dockerfile
RUN composer require processmaker/script-helpers
```

## Verificar la Instalación

Después de reconstruir el script-executor, puedes verificar que el paquete está instalado creando un script de prueba:

```php
<?php

use ProcessMaker\ScriptHelpers\RequestLister;
use ProcessMaker\ScriptHelpers\TaskLister;
use ProcessMaker\ScriptHelpers\UserLister;

// Si no hay error, el paquete está instalado correctamente
$requests = RequestLister::count();
$tasks = TaskLister::count();
$users = UserLister::count();

return [
    'status' => 'success',
    'package_installed' => true,
    'requests_count' => $requests,
    'tasks_count' => $tasks,
    'users_count' => $users
];
```

## Comandos Útiles

### Reconstruir Script Executor Manualmente

Si necesitas reconstruir manualmente desde la línea de comandos:

```bash
php artisan processmaker:build-script-executor php --rebuild
```

### Ver Logs de Construcción

Los logs de construcción se muestran en la interfaz web, pero también puedes verlos en:

```bash
docker logs -f <container-id>
```

## Solución de Problemas

### Error: Package not found

- Verifica que el paquete esté publicado en Packagist
- O que el repositorio esté configurado correctamente
- Verifica que el nombre del paquete sea exacto: `processmaker/script-helpers`

### Error: Authentication required

- Si es un repositorio privado, configura el token de acceso
- O usa un repositorio público en Packagist

### El paquete no se carga en los scripts

- Verifica que el script-executor se haya reconstruido completamente
- Asegúrate de que el script esté usando el script-executor correcto
- Verifica que el namespace sea correcto: `ProcessMaker\ScriptHelpers\`

## Notas Importantes

1. **Cada vez que agregues un paquete**, debes reconstruir el script-executor
2. **El proceso de reconstrucción puede tardar varios minutos**
3. **Todos los scripts que usen ese script-executor tendrán acceso al paquete**
4. **Si tienes múltiples script-executors de PHP**, debes agregar el paquete a cada uno que lo necesite

