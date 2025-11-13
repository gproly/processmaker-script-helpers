# processmaker-script-helpers

Helper class para facilitar el listado de requests (casos) en Script Tasks de ProcessMaker 4.

## Descripción

Este paquete proporciona una clase helper estática que simplifica el acceso a ProcessRequests desde los Script Tasks usando la API de ProcessMaker.

## Instalación

### En el Script Executor

1. Ve a **Admin** > **Script Executors** en ProcessMaker
2. Edita el script-executor PHP
3. En el campo **Config**, agrega:

```dockerfile
RUN composer config repositories.script-helpers vcs https://github.com/gproly/processmaker-script-helpers.git
RUN composer require processmaker/script-helpers:dev-main
```

4. Guarda y espera a que se reconstruya el script-executor

## Uso

### Importar la clase

```php
<?php

use ProcessMaker\ScriptHelpers\RequestLister;
```

### Ejemplos básicos

#### Obtener todos los requests
```php
// Obtener requests paginados (10 por página)
$requests = RequestLister::all();

// Obtener todos los requests sin paginación
$allRequests = RequestLister::all([], 0);
```

#### Filtrar requests
```php
// Filtrar por status
$activeRequests = RequestLister::all(['status' => 'ACTIVE']);

// Filtrar por proceso y usuario
$filteredRequests = RequestLister::all([
    'status' => 'ACTIVE',
    'process_id' => 123,
    'user_id' => 456,
    'order_by' => 'created_at',
    'order_direction' => 'desc'
], 20);
```

#### Buscar un request específico
```php
$request = RequestLister::find(789);

if ($request) {
    echo $request['case_number'];
    echo $request['status'];
}
```

#### Contar requests
```php
// Contar todos
$total = RequestLister::count();

// Contar con filtros
$activeCount = RequestLister::count(['status' => 'ACTIVE']);
```

## Notas Importantes

- **Los métodos devuelven arrays, no objetos.** Usa `$request['id']` en lugar de `$request->id`
- **Paginación**: Por defecto, `all()` devuelve 10 resultados por página. Usa `0` como `$perPage` para obtener todos
- **Filtros**: Todos los filtros son opcionales y se pasan como array asociativo

## Requisitos

- PHP 7.3 o superior
- ProcessMaker 4
- Script Executor PHP configurado
- GuzzleHttp (se instala automáticamente)

## Licencia

AGPL-3.0-or-later
