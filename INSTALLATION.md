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

### Paso 3: Guardar y Reconstruir

1. Haz clic en **Save**
2. El sistema automáticamente iniciará la reconstrucción del script-executor
3. Espera a que termine la construcción (puede tomar varios minutos)

## Opción 2: Mediante API

```bash
curl -X PUT "http://tu-servidor/api/v1/script-executors/{ID}" \
  -H "Authorization: Bearer TU_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "config": "RUN composer config repositories.script-helpers vcs https://github.com/gproly/processmaker-script-helpers.git\nRUN composer require processmaker/script-helpers:dev-main"
  }'
```

## Verificar la Instalación

Después de reconstruir el script-executor, puedes verificar que el paquete está instalado:

```php
<?php

use ProcessMaker\ScriptHelpers\RequestLister;

// Si no hay error, el paquete está instalado correctamente
$count = RequestLister::count();
return ['status' => 'success', 'total_requests' => $count];
```

## Solución de Problemas

### Error: Class not found
- Verifica que el paquete esté instalado en el script-executor
- Asegúrate de que el script-executor se haya reconstruido completamente
- Verifica que estés usando el namespace correcto: `ProcessMaker\ScriptHelpers\`

### Error: API_HOST or API_TOKEN not set
- Estos se configuran automáticamente por ProcessMaker
- Asegúrate de que el script se ejecute en el contexto de un proceso o script task
