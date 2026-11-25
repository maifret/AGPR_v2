<?php

const AGPR_PRIVILEGIO_ADMIN = 'empleados.privilegios';
const AGPR_PRIVILEGIO_VER_EMPLEADOS = 'empleados.ver';

function listarPrivilegios(PDO $db): array
{
    return $db->query(
        'SELECT id, clave, nombre, descripcion, grupo
         FROM privilegios
         ORDER BY grupo, id'
    )->fetchAll();
}

function listarPuestos(PDO $db): array
{
    return $db->query(
        'SELECT p.id, p.nombre, p.descripcion,
                (SELECT COUNT(*) FROM empleados e WHERE e.puesto_id = p.id) AS empleados,
                (SELECT COUNT(*) FROM puesto_privilegio pp WHERE pp.puesto_id = p.id) AS privilegios
         FROM puestos p
         ORDER BY p.id'
    )->fetchAll();
}

function obtenerPuesto(PDO $db, int $id): ?array
{
    $consulta = $db->prepare(
        'SELECT id, nombre, descripcion FROM puestos WHERE id = ?'
    );
    $consulta->execute([$id]);
    $puesto = $consulta->fetch();

    return $puesto ?: null;
}

function clavesDePuesto(PDO $db, int $puestoId): array
{
    $consulta = $db->prepare(
        'SELECT pr.clave
         FROM puesto_privilegio pp
         INNER JOIN privilegios pr ON pr.id = pp.privilegio_id
         WHERE pp.puesto_id = ?
         ORDER BY pr.id'
    );
    $consulta->execute([$puestoId]);

    return array_column($consulta->fetchAll(), 'clave');
}

function mapaClavesPorPuesto(PDO $db): array
{
    $filas = $db->query(
        'SELECT pp.puesto_id, pr.clave
         FROM puesto_privilegio pp
         INNER JOIN privilegios pr ON pr.id = pp.privilegio_id'
    )->fetchAll();

    $mapa = [];
    foreach ($filas as $fila) {
        $mapa[(int) $fila['puesto_id']][] = $fila['clave'];
    }

    return $mapa;
}

function overridesDeEmpleado(PDO $db, int $empleadoId): array
{
    $consulta = $db->prepare(
        'SELECT pr.clave, ep.modo
         FROM empleado_privilegio ep
         INNER JOIN privilegios pr ON pr.id = ep.privilegio_id
         WHERE ep.empleado_id = ?'
    );
    $consulta->execute([$empleadoId]);

    $mapa = [];
    foreach ($consulta->fetchAll() as $fila) {
        $mapa[$fila['clave']] = $fila['modo'];
    }

    return $mapa;
}

function clavesEfectivas(array $delPuesto, array $overrides): array
{
    $efectivas = [];

    foreach ($delPuesto as $clave) {
        if (($overrides[$clave] ?? '') !== 'revocar') {
            $efectivas[] = $clave;
        }
    }

    foreach ($overrides as $clave => $modo) {
        if ($modo === 'conceder' && !in_array($clave, $efectivas, true)) {
            $efectivas[] = $clave;
        }
    }

    return array_values($efectivas);
}

function calcularOverrides(array $delPuesto, array $marcados, array $catalogo): array
{
    $marcado = array_fill_keys($marcados, true);
    $enPuesto = array_fill_keys($delPuesto, true);
    $overrides = [];

    foreach ($catalogo as $privilegio) {
        $clave = $privilegio['clave'];
        $activo = isset($marcado[$clave]);
        $base = isset($enPuesto[$clave]);

        if ($activo && !$base) {
            $overrides[$clave] = 'conceder';
        } elseif (!$activo && $base) {
            $overrides[$clave] = 'revocar';
        }
    }

    return $overrides;
}

function puedeAdministrar(array $claves): bool
{
    return in_array(AGPR_PRIVILEGIO_VER_EMPLEADOS, $claves, true)
        && in_array(AGPR_PRIVILEGIO_ADMIN, $claves, true);
}

function mapaPersonal(PDO $db): array
{
    $empleados = $db->query(
        'SELECT id, puesto_id, activo FROM empleados'
    )->fetchAll();
    $porPuesto = mapaClavesPorPuesto($db);
    $filas = $db->query(
        'SELECT ep.empleado_id, pr.clave, ep.modo
         FROM empleado_privilegio ep
         INNER JOIN privilegios pr ON pr.id = ep.privilegio_id'
    )->fetchAll();

    $overrides = [];
    foreach ($filas as $fila) {
        $overrides[(int) $fila['empleado_id']][$fila['clave']] = $fila['modo'];
    }

    $mapa = [];
    foreach ($empleados as $empleado) {
        $id = (int) $empleado['id'];
        $puestoId = (int) $empleado['puesto_id'];
        $mapa[] = [
            'id' => $id,
            'puesto_id' => $puestoId,
            'activo' => (int) $empleado['activo'] === 1,
            'claves' => clavesEfectivas(
                $porPuesto[$puestoId] ?? [],
                $overrides[$id] ?? []
            ),
        ];
    }

    return $mapa;
}

function quedaAdministrador(array $mapa, ?int $ignorarId = null, ?array $reemplazo = null): bool
{
    foreach ($mapa as $empleado) {
        if ($ignorarId !== null && $empleado['id'] === $ignorarId) {
            continue;
        }

        $activo = $empleado['activo'];
        $claves = $empleado['claves'];

        if ($reemplazo && $empleado['id'] === $reemplazo['id']) {
            $activo = $reemplazo['activo'];
            $claves = $reemplazo['claves'];
        }

        if ($activo && puedeAdministrar($claves)) {
            return true;
        }
    }

    if ($reemplazo && $ignorarId === null) {
        $existe = false;
        foreach ($mapa as $empleado) {
            if ($empleado['id'] === $reemplazo['id']) {
                $existe = true;
                break;
            }
        }

        if (!$existe && $reemplazo['activo'] && puedeAdministrar($reemplazo['claves'])) {
            return true;
        }
    }

    return false;
}

function listarEmpleados(PDO $db): array
{
    $filas = $db->query(
        'SELECT e.id, e.nombre, e.correo, e.puesto_id, e.activo, p.nombre AS puesto
         FROM empleados e
         INNER JOIN puestos p ON p.id = e.puesto_id
         ORDER BY e.nombre'
    )->fetchAll();

    $lista = [];
    foreach ($filas as $fila) {
        $overrides = overridesDeEmpleado($db, (int) $fila['id']);
        $base = clavesDePuesto($db, (int) $fila['puesto_id']);
        $fila['id'] = (int) $fila['id'];
        $fila['puesto_id'] = (int) $fila['puesto_id'];
        $fila['activo'] = (int) $fila['activo'];
        $fila['privilegios'] = clavesEfectivas($base, $overrides);
        $lista[] = $fila;
    }

    return $lista;
}

function obtenerEmpleado(PDO $db, int $id): ?array
{
    $consulta = $db->prepare(
        'SELECT e.id, e.nombre, e.correo, e.puesto_id, e.activo, p.nombre AS puesto
         FROM empleados e
         INNER JOIN puestos p ON p.id = e.puesto_id
         WHERE e.id = ?'
    );
    $consulta->execute([$id]);
    $fila = $consulta->fetch();

    if (!$fila) {
        return null;
    }

    $fila['id'] = (int) $fila['id'];
    $fila['puesto_id'] = (int) $fila['puesto_id'];
    $fila['activo'] = (int) $fila['activo'];
    $fila['overrides'] = overridesDeEmpleado($db, $id);
    $fila['privilegios'] = clavesEfectivas(
        clavesDePuesto($db, $fila['puesto_id']),
        $fila['overrides']
    );

    return $fila;
}

function buscarEmpleadoPorCorreo(PDO $db, string $correo): ?array
{
    $consulta = $db->prepare(
        'SELECT e.id, e.nombre, e.correo, e.contrasena, e.activo, p.nombre AS puesto
         FROM empleados e
         INNER JOIN puestos p ON p.id = e.puesto_id
         WHERE e.correo = ?
         LIMIT 1'
    );
    $consulta->execute([$correo]);
    $fila = $consulta->fetch();

    return $fila ?: null;
}

function correoEmpleadoOcupado(PDO $db, string $correo, int $exceptoId = 0): bool
{
    $consulta = $db->prepare(
        'SELECT id FROM empleados WHERE correo = ? AND id <> ? LIMIT 1'
    );
    $consulta->execute([$correo, $exceptoId]);

    return (bool) $consulta->fetch();
}

function correoClienteOcupado(PDO $db, string $correo): bool
{
    $consulta = $db->prepare(
        'SELECT id FROM usuarios WHERE correo = ? LIMIT 1'
    );
    $consulta->execute([$correo]);

    return (bool) $consulta->fetch();
}

function guardarOverrides(PDO $db, int $empleadoId, array $overrides): void
{
    $db->prepare('DELETE FROM empleado_privilegio WHERE empleado_id = ?')
        ->execute([$empleadoId]);

    if (!$overrides) {
        return;
    }

    $ids = [];
    $consulta = $db->query('SELECT id, clave FROM privilegios');
    foreach ($consulta->fetchAll() as $fila) {
        $ids[$fila['clave']] = (int) $fila['id'];
    }

    $insertar = $db->prepare(
        'INSERT INTO empleado_privilegio (empleado_id, privilegio_id, modo)
         VALUES (?, ?, ?)'
    );

    foreach ($overrides as $clave => $modo) {
        if (!isset($ids[$clave])) {
            continue;
        }
        $insertar->execute([$empleadoId, $ids[$clave], $modo]);
    }
}

function guardarPuesto(PDO $db, array $datos, array $claves): void
{
    if (!empty($datos['id'])) {
        $consulta = $db->prepare(
            'UPDATE puestos SET nombre = ?, descripcion = ? WHERE id = ?'
        );
        $consulta->execute([
            $datos['nombre'],
            $datos['descripcion'],
            $datos['id'],
        ]);
        $puestoId = (int) $datos['id'];
    } else {
        $consulta = $db->prepare(
            'INSERT INTO puestos (nombre, descripcion) VALUES (?, ?)'
        );
        $consulta->execute([$datos['nombre'], $datos['descripcion']]);
        $puestoId = (int) $db->lastInsertId();
    }

    $db->prepare('DELETE FROM puesto_privilegio WHERE puesto_id = ?')
        ->execute([$puestoId]);

    if (!$claves) {
        return;
    }

    $insertar = $db->prepare(
        'INSERT INTO puesto_privilegio (puesto_id, privilegio_id)
         SELECT ?, id FROM privilegios WHERE clave = ?'
    );

    foreach ($claves as $clave) {
        $insertar->execute([$puestoId, $clave]);
    }
}

function eliminarPuesto(PDO $db, int $id): void
{
    $db->prepare('DELETE FROM puestos WHERE id = ?')->execute([$id]);
}

function crearEmpleado(PDO $db, array $datos, array $overrides): int
{
    $consulta = $db->prepare(
        'INSERT INTO empleados (nombre, correo, contrasena, puesto_id, activo)
         VALUES (?, ?, ?, ?, ?)'
    );
    $consulta->execute([
        $datos['nombre'],
        $datos['correo'],
        $datos['contrasena'],
        $datos['puesto_id'],
        $datos['activo'],
    ]);

    $id = (int) $db->lastInsertId();
    guardarOverrides($db, $id, $overrides);

    return $id;
}

function actualizarEmpleado(PDO $db, array $datos, ?array $overrides): void
{
    if ($datos['contrasena'] !== null) {
        $consulta = $db->prepare(
            'UPDATE empleados
             SET nombre = ?, correo = ?, contrasena = ?, puesto_id = ?, activo = ?
             WHERE id = ?'
        );
        $consulta->execute([
            $datos['nombre'],
            $datos['correo'],
            $datos['contrasena'],
            $datos['puesto_id'],
            $datos['activo'],
            $datos['id'],
        ]);
    } else {
        $consulta = $db->prepare(
            'UPDATE empleados
             SET nombre = ?, correo = ?, puesto_id = ?, activo = ?
             WHERE id = ?'
        );
        $consulta->execute([
            $datos['nombre'],
            $datos['correo'],
            $datos['puesto_id'],
            $datos['activo'],
            $datos['id'],
        ]);
    }

    if ($overrides !== null) {
        guardarOverrides($db, (int) $datos['id'], $overrides);
    }
}

function eliminarEmpleado(PDO $db, int $id): void
{
    $db->prepare('DELETE FROM empleados WHERE id = ?')->execute([$id]);
}

function simularPuesto(PDO $db, int $puestoId, array $clavesNuevas): bool
{
    $mapa = mapaPersonal($db);
    $porPuesto = mapaClavesPorPuesto($db);
    $porPuesto[$puestoId] = array_values($clavesNuevas);

    foreach ($mapa as &$empleado) {
        $base = $porPuesto[$empleado['puesto_id']] ?? [];
        $overrides = overridesDeEmpleado($db, $empleado['id']);
        $empleado['claves'] = clavesEfectivas($base, $overrides);
    }
    unset($empleado);

    return quedaAdministrador($mapa);
}
