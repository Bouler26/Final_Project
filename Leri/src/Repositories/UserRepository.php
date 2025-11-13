<?php
namespace Src\Repositories;

use PDO;
use Src\Config\Database;

class UserRepository {
    private PDO $db;

    public function __construct(array $cfg) {
        $this->db = Database::conn($cfg);
    }

    public function paginate($page, $per, $search = null, $sort = 'id', $direction = 'DESC') {
        $allowedSorts = ['id', 'name', 'email', 'role', 'created_at', 'updated_at'];
        $sortColumn = in_array($sort, $allowedSorts, true) ? $sort : 'id';
        $dir = strtoupper($direction) === 'ASC' ? 'ASC' : 'DESC';

        $where = '';
        $params = [];
        if ($search !== null && $search !== '') {
            $where = ' WHERE name LIKE :search OR email LIKE :search';
            $params[':search'] = '%' . $search . '%';
        }

        $totalStmt = $this->db->prepare('SELECT COUNT(*) FROM users' . $where);
        foreach ($params as $key => $value) {
            $totalStmt->bindValue($key, $value, PDO::PARAM_STR);
        }
        $totalStmt->execute();
        $total = (int)$totalStmt->fetchColumn();

        $off = ($page - 1) * $per;
        $sql = 'SELECT id, name, email, role, created_at, updated_at FROM users' . $where . ' ORDER BY ' . $sortColumn . ' ' . $dir . ' LIMIT :per OFFSET :off';
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_STR);
        }
        $stmt->bindValue(':per', (int)$per, PDO::PARAM_INT);
        $stmt->bindValue(':off', (int)$off, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'data' => $stmt->fetchAll(),
            'meta' => [
                'total' => $total,
                'page' => $page,
                'per_page' => $per,
                'last_page' => max(1, (int)ceil($total / $per))
            ]
        ];
    }

    public function find($id) {
        $s = $this->db->prepare('SELECT id, name, email, role, created_at, updated_at 
                                 FROM users WHERE id = ?');
        $s->execute([$id]);
        return $s->fetch();
    }

    public function create($name, $email, $hash, $role = 'user') {
        $this->db->beginTransaction();
        try {
            $s = $this->db->prepare('INSERT INTO users (name, email, password_hash, role)
                                     VALUES (?, ?, ?, ?)');
            $s->execute([$name, $email, $hash, $role]);
            $id = $this->db->lastInsertId();
            $this->db->commit();
            return $this->find($id);
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function update($id, $name, $email, $role) {
        $s = $this->db->prepare('UPDATE users SET name = ?, email = ?, role = ? WHERE id = ?');
        return $s->execute([$name, $email, $role, $id]);
    }

    public function delete($id) {
        $s = $this->db->prepare('DELETE FROM users WHERE id = ?');
        return $s->execute([$id]);
    }
}
