<?php
// app/models/AuditLog.php

require_once __DIR__ . '/../db.php';

class AuditLog
{
    public static function log(int $actor_id, string $action, string $entity, ?int $entity_id, array $payload = []): void
    {
        try {
            $stmt = db()->prepare("
                INSERT INTO audit_logs (actor_id, action, entity, entity_id, payload_json)
                VALUES (:actor_id, :action, :entity, :entity_id, :payload_json)
            ");
            $stmt->execute([
                ':actor_id'    => $actor_id,
                ':action'      => $action,
                ':entity'      => $entity,
                ':entity_id'   => $entity_id,
                ':payload_json'=> json_encode($payload, JSON_UNESCAPED_UNICODE),
            ]);
        } catch (Throwable $e) {
            // ignore log error
        }
    }
}
