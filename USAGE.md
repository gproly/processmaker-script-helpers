# Guía de Uso - processmaker-script-helpers

Guía rápida para usar `RequestLister` en Script Tasks de ProcessMaker 4.

## Importar la clase

```php
<?php

use ProcessMaker\ScriptHelpers\RequestLister;
```

## Métodos disponibles

### `all($filters, $perPage, $page)`

Obtiene una lista de requests con filtros opcionales.

**Parámetros:**
- `$filters` (array): Filtros opcionales (status, process_id, user_id, order_by, order_direction)
- `$perPage` (int): Número de resultados por página (0 para todos)
- `$page` (int): Número de página

**Retorna:** Array con estructura `['data' => [...], 'meta' => [...]]` o array simple

**Ejemplos:**
```php
// Todos los requests (paginados)
$requests = RequestLister::all();

// Todos sin paginación
$all = RequestLister::all([], 0);

// Con filtros
$active = RequestLister::all(['status' => 'ACTIVE'], 20);
```

### `find($id)`

Busca un request por su ID.

**Parámetros:**
- `$id` (string|int): ID del request

**Retorna:** Array asociativo con los datos del request o `null` si no existe

**Ejemplo:**
```php
$request = RequestLister::find(123);
if ($request) {
    echo $request['case_number'];
}
```

### `count($filters)`

Cuenta el total de requests con filtros opcionales.

**Parámetros:**
- `$filters` (array): Filtros opcionales

**Retorna:** Número entero

**Ejemplos:**
```php
$total = RequestLister::count();
$active = RequestLister::count(['status' => 'ACTIVE']);
```

## Ejemplo completo

```php
<?php

use ProcessMaker\ScriptHelpers\RequestLister;

// Obtener requests activos del proceso 123
$requests = RequestLister::all([
    'status' => 'ACTIVE',
    'process_id' => 123,
    'order_by' => 'created_at',
    'order_direction' => 'desc'
], 10);

// Extraer datos de la respuesta
$requestsData = isset($requests['data']) ? $requests['data'] : $requests;

// Procesar resultados
$result = [];
foreach ($requestsData as $request) {
    $result[] = [
        'id' => $request['id'],
        'case_number' => $request['case_number'],
        'status' => $request['status']
    ];
}

return $result;
```

## Filtros disponibles

- `status`: Estado del request (ACTIVE, COMPLETED, etc.)
- `process_id`: ID del proceso
- `user_id`: ID del usuario
- `case_number`: Número de caso
- `created_from`: Fecha desde (formato YYYY-MM-DD)
- `created_to`: Fecha hasta (formato YYYY-MM-DD)
- `order_by`: Campo para ordenar
- `order_direction`: Dirección del orden (asc, desc)

## Notas

- Los métodos devuelven **arrays**, no objetos Eloquent
- Usa sintaxis de array: `$request['id']` en lugar de `$request->id`
- La paginación devuelve `['data' => [...], 'meta' => [...]]`
- Sin paginación (`$perPage = 0`) devuelve un array simple
