# Guía de Uso - processmaker-script-helpers

Este documento explica cómo usar las clases helper del paquete `processmaker/script-helpers` en los Script Tasks de ProcessMaker 4.

## Requisitos Previos

1. El paquete debe estar instalado en el script-executor (ver `INSTALLATION.md`)
2. El script-executor debe estar reconstruido después de instalar el paquete

## Nota Importante

**Las clases devuelven arrays, no objetos Eloquent.** Los métodos `all()`, `find()`, etc. devuelven arrays asociativos con los datos de la API de ProcessMaker. Para acceder a los datos, usa sintaxis de array: `$request['id']` en lugar de `$request->id`.

## Importar las Clases

En tus Script Tasks, importa las clases que necesites usando `use`:

```php
<?php

use ProcessMaker\ScriptHelpers\RequestLister;
use ProcessMaker\ScriptHelpers\TaskLister;
use ProcessMaker\ScriptHelpers\UserLister;
```

## Ejemplos de Uso

### 1. RequestLister - Listar Requests (Casos)

#### Obtener todos los requests
```php
<?php

use ProcessMaker\ScriptHelpers\RequestLister;

// Obtener todos los requests (paginados, 10 por página)
$requests = RequestLister::all();

// Obtener todos los requests sin paginación
$allRequests = RequestLister::all([], 0);

// Obtener requests con paginación personalizada (20 por página, página 2)
$requests = RequestLister::all([], 20, 2);
```

#### Filtrar requests
```php
<?php

use ProcessMaker\ScriptHelpers\RequestLister;

// Filtrar por status
$activeRequests = RequestLister::byStatus('ACTIVE');

// Filtrar por proceso
$processRequests = RequestLister::byProcess(123);

// Filtrar por usuario
$userRequests = RequestLister::byUser(456);

// Filtrar con múltiples criterios
$filteredRequests = RequestLister::all([
    'status' => 'ACTIVE',
    'process_id' => 123,
    'user_id' => 456,
    'created_from' => '2024-01-01',
    'created_to' => '2024-12-31',
    'order_by' => 'created_at',
    'order_direction' => 'desc'
], 15);
```

#### Buscar un request específico
```php
<?php

use ProcessMaker\ScriptHelpers\RequestLister;

// Buscar por ID
$request = RequestLister::find(789);

if ($request) {
    return [
        'id' => $request['id'],
        'case_number' => $request['case_number'],
        'status' => $request['status'],
        'process_id' => $request['process_id']
    ];
}
```

#### Contar requests
```php
<?php

use ProcessMaker\ScriptHelpers\RequestLister;

// Contar todos los requests
$total = RequestLister::count();

// Contar con filtros
$activeCount = RequestLister::count(['status' => 'ACTIVE']);
$processCount = RequestLister::count(['process_id' => 123]);
```

### 2. TaskLister - Listar Tasks (Tareas)

#### Obtener todas las tareas
```php
<?php

use ProcessMaker\ScriptHelpers\TaskLister;

// Obtener todas las tareas activas
$activeTasks = TaskLister::active();

// Obtener todas las tareas completadas
$completedTasks = TaskLister::completed();

// Obtener todas las tareas (paginadas)
$allTasks = TaskLister::all();
```

#### Filtrar tareas
```php
<?php

use ProcessMaker\ScriptHelpers\TaskLister;

// Filtrar por usuario
$userTasks = TaskLister::byUser(456);

// Filtrar por request (caso)
$requestTasks = TaskLister::byRequest(789);

// Filtrar tareas vencidas
$overdueTasks = TaskLister::overdue();

// Filtrar con múltiples criterios
$filteredTasks = TaskLister::all([
    'status' => 'ACTIVE',
    'user_id' => 456,
    'process_id' => 123,
    'overdue' => true,
    'due_from' => '2024-01-01',
    'due_to' => '2024-12-31',
    'element_type' => 'task',
    'element_name' => 'Aprobación',
    'order_by' => 'due_at',
    'order_direction' => 'asc'
], 20);
```

#### Buscar una tarea específica
```php
<?php

use ProcessMaker\ScriptHelpers\TaskLister;

// Buscar por ID
$task = TaskLister::find(101112);

if ($task) {
    return [
        'id' => $task['id'],
        'status' => $task['status'],
        'user_id' => $task['user_id'],
        'due_at' => $task['due_at'],
        'element_name' => $task['element_name']
    ];
}
```

#### Contar tareas
```php
<?php

use ProcessMaker\ScriptHelpers\TaskLister;

// Contar todas las tareas activas
$activeCount = TaskLister::count(['status' => 'ACTIVE']);

// Contar tareas vencidas
$overdueCount = TaskLister::count(['overdue' => true]);

// Contar tareas de un usuario
$userTaskCount = TaskLister::count(['user_id' => 456]);
```

### 3. UserLister - Listar Usuarios

#### Obtener todos los usuarios
```php
<?php

use ProcessMaker\ScriptHelpers\UserLister;

// Obtener todos los usuarios activos
$activeUsers = UserLister::active();

// Obtener todos los usuarios (paginados)
$allUsers = UserLister::all();

// Obtener todos los usuarios sin paginación
$allUsers = UserLister::all([], 0);
```

#### Buscar usuarios
```php
<?php

use ProcessMaker\ScriptHelpers\UserLister;

// Buscar por username
$user = UserLister::byUsername('johndoe');

// Buscar por email
$user = UserLister::byEmail('john@example.com');

// Buscar por ID
$user = UserLister::find(456);

// Búsqueda general (por nombre, apellido, username o email)
$users = UserLister::search('john', 10);
```

#### Filtrar usuarios
```php
<?php

use ProcessMaker\ScriptHelpers\UserLister;

// Filtrar por grupo
$groupUsers = UserLister::byGroup(789);

// Filtrar con múltiples criterios
$filteredUsers = UserLister::all([
    'status' => 'ACTIVE',
    'group_id' => 789,
    'username' => 'john',
    'firstname' => 'John',
    'lastname' => 'Doe',
    'email' => 'john@example.com',
    'order_by' => 'username',
    'order_direction' => 'asc'
], 20);
```

#### Contar usuarios
```php
<?php

use ProcessMaker\ScriptHelpers\UserLister;

// Contar todos los usuarios activos
$activeCount = UserLister::count(['status' => 'ACTIVE']);

// Contar usuarios de un grupo
$groupCount = UserLister::count(['group_id' => 789]);
```

## Ejemplos Completos de Script Tasks

### Ejemplo 1: Obtener estadísticas de requests
```php
<?php

use ProcessMaker\ScriptHelpers\RequestLister;

$stats = [
    'total' => RequestLister::count(),
    'active' => RequestLister::count(['status' => 'ACTIVE']),
    'completed' => RequestLister::count(['status' => 'COMPLETED']),
    'recent' => RequestLister::all([
        'order_by' => 'created_at',
        'order_direction' => 'desc'
    ], 5)
];

return $stats;
```

### Ejemplo 2: Obtener tareas pendientes de un usuario
```php
<?php

use ProcessMaker\ScriptHelpers\TaskLister;

// Obtener el ID del usuario actual desde el contexto
$userId = $userId ?? 1; // Ajusta según tu contexto

$pendingTasks = TaskLister::all([
    'user_id' => $userId,
    'status' => 'ACTIVE',
    'order_by' => 'due_at',
    'order_direction' => 'asc'
], 10);

// Extract data from API response
$tasksData = isset($pendingTasks['data']) ? $pendingTasks['data'] : $pendingTasks;

return [
    'count' => count($tasksData),
    'tasks' => array_map(function($task) {
        return [
            'id' => $task['id'],
            'element_name' => $task['element_name'],
            'due_at' => $task['due_at'],
            'process_request_id' => $task['process_request_id']
        ];
    }, $tasksData)
];
```

### Ejemplo 3: Buscar usuarios y sus tareas
```php
<?php

use ProcessMaker\ScriptHelpers\UserLister;
use ProcessMaker\ScriptHelpers\TaskLister;

// Buscar usuario
$searchTerm = $searchTerm ?? 'john';
$users = UserLister::search($searchTerm, 5);

$usersData = isset($users['data']) ? $users['data'] : $users;
$result = [];
foreach ($usersData as $user) {
    $taskCount = TaskLister::count([
        'user_id' => $user['id'],
        'status' => 'ACTIVE'
    ]);
    
    $result[] = [
        'user' => [
            'id' => $user['id'],
            'username' => $user['username'],
            'fullname' => $user['firstname'] . ' ' . $user['lastname'],
            'email' => $user['email']
        ],
        'active_tasks' => $taskCount
    ];
}

return $result;
```

### Ejemplo 4: Obtener información de un request y sus tareas
```php
<?php

use ProcessMaker\ScriptHelpers\RequestLister;
use ProcessMaker\ScriptHelpers\TaskLister;

$requestId = $requestId ?? null;

if (!$requestId) {
    return ['error' => 'requestId is required'];
}

// Obtener el request
$request = RequestLister::find($requestId);

if (!$request) {
    return ['error' => 'Request not found'];
}

// Obtener las tareas del request
$tasks = TaskLister::byRequest($requestId);

$tasksData = isset($tasks['data']) ? $tasks['data'] : $tasks;

return [
    'request' => [
        'id' => $request['id'],
        'case_number' => $request['case_number'],
        'status' => $request['status'],
        'process_id' => $request['process_id'],
        'created_at' => $request['created_at']
    ],
    'tasks' => array_map(function($task) {
        return [
            'id' => $task['id'],
            'status' => $task['status'],
            'element_name' => $task['element_name'],
            'user_id' => $task['user_id'],
            'due_at' => $task['due_at']
        ];
    }, $tasksData)
];
```

## Notas Importantes

1. **Paginación**: Por defecto, los métodos `all()` devuelven resultados paginados (10 por página). Usa `0` como `$perPage` para obtener todos los resultados.

2. **Filtros**: Todos los filtros son opcionales. Puedes combinar múltiples filtros en un solo array.

3. **Ordenamiento**: Usa `order_by` y `order_direction` en el array de filtros para ordenar los resultados.

4. **Retorno**: Los métodos devuelven arrays:
   - Array con estructura `['data' => [...], 'meta' => [...]]` cuando hay paginación
   - Array simple de elementos cuando no hay paginación
   - Array asociativo cuando se busca un elemento específico con `find()`
   - `null` si no se encuentra el elemento

5. **Contexto del Script**: Asegúrate de tener acceso a las variables necesarias (como `$userId`, `$requestId`, etc.) desde el contexto del proceso.

## Solución de Problemas

### Error: Class not found
- Verifica que el paquete esté instalado en el script-executor
- Asegúrate de que el script-executor se haya reconstruido después de instalar el paquete
- Verifica que estés usando el namespace correcto: `ProcessMaker\ScriptHelpers\`

### Error: Method not found
- Verifica que estés usando métodos estáticos (con `::`)
- Revisa la documentación de cada clase para ver los métodos disponibles

### No se devuelven resultados
- Verifica que los filtros sean correctos
- Asegúrate de que los IDs existan en la base de datos
- Verifica los permisos del usuario que ejecuta el script

