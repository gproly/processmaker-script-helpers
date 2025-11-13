# processmaker-script-helpers

Helper classes para facilitar el listado de requests, tasks y users en Script Tasks de ProcessMaker 4.

## Descripción

Este paquete proporciona clases helper estáticas que simplifican el acceso a datos comunes de ProcessMaker 4 desde los Script Tasks:

- **RequestLister**: Listar y filtrar ProcessRequests (casos)
- **TaskLister**: Listar y filtrar ProcessRequestTokens (tareas)
- **UserLister**: Listar y filtrar Users (usuarios)

## Instalación

Para instalar este paquete en un Script Executor de ProcessMaker 4, consulta [INSTALLATION.md](INSTALLATION.md).

## Uso

Para ver ejemplos detallados de cómo usar las clases en tus Script Tasks, consulta [USAGE.md](USAGE.md).

### Ejemplo Rápido

```php
<?php

use ProcessMaker\ScriptHelpers\RequestLister;
use ProcessMaker\ScriptHelpers\TaskLister;
use ProcessMaker\ScriptHelpers\UserLister;

// Obtener requests activos
$activeRequests = RequestLister::byStatus('ACTIVE');

// Obtener tareas de un usuario
$userTasks = TaskLister::byUser(123);

// Buscar un usuario
$user = UserLister::byUsername('johndoe');
```

## Requisitos

- PHP 7.3 o superior
- ProcessMaker 4
- Script Executor PHP configurado

## Clases Disponibles

### RequestLister

Métodos principales:
- `all($filters, $perPage, $page)` - Obtener todos los requests con filtros opcionales
- `find($id)` - Buscar un request por ID
- `byStatus($status, $perPage)` - Filtrar por status
- `byProcess($processId, $perPage)` - Filtrar por proceso
- `byUser($userId, $perPage)` - Filtrar por usuario
- `count($filters)` - Contar requests con filtros opcionales

### TaskLister

Métodos principales:
- `all($filters, $perPage, $page)` - Obtener todas las tareas con filtros opcionales
- `find($id)` - Buscar una tarea por ID
- `active($filters, $perPage)` - Obtener tareas activas
- `completed($filters, $perPage)` - Obtener tareas completadas
- `byUser($userId, $perPage)` - Filtrar por usuario
- `byRequest($requestId, $perPage)` - Filtrar por request
- `overdue($filters, $perPage)` - Obtener tareas vencidas
- `count($filters)` - Contar tareas con filtros opcionales

### UserLister

Métodos principales:
- `all($filters, $perPage, $page)` - Obtener todos los usuarios con filtros opcionales
- `find($id)` - Buscar un usuario por ID
- `byUsername($username)` - Buscar por username
- `byEmail($email)` - Buscar por email
- `active($filters, $perPage)` - Obtener usuarios activos
- `byGroup($groupId, $perPage)` - Filtrar por grupo
- `search($search, $perPage)` - Búsqueda general
- `count($filters)` - Contar usuarios con filtros opcionales

## Documentación

- [INSTALLATION.md](INSTALLATION.md) - Guía de instalación
- [USAGE.md](USAGE.md) - Guía de uso con ejemplos detallados

## Licencia

AGPL-3.0-or-later

## Autor

ProcessMaker
