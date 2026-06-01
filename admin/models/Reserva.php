<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/conexao.php';

class Reserva
{
    private PDO $pdo;
    private int $capacidadePorHorario;

    public const STATUS = ['pendente', 'confirmada', 'cancelada'];

    public function __construct(int $capacidadePorHorario = 60)
    {
        $this->pdo = getConnection();
        $this->capacidadePorHorario = $capacidadePorHorario;
    }

    public function cadastrar(int $usuarioId, array $dados): int
    {
        $data = (string) ($dados['data'] ?? '');
        $horario = (string) ($dados['horario'] ?? '');
        $numPessoas = max(1, (int) ($dados['num_pessoas'] ?? 1));

        if (!$this->verificarDisponibilidade($data, $horario, $numPessoas)) {
            throw new RuntimeException('Horário indisponível para a quantidade de pessoas informada.');
        }

        $stmt = $this->pdo->prepare(
            'INSERT INTO reservas (usuario_id, data, horario, num_pessoas, status, observacao)
             VALUES (?, ?, ?, ?, "pendente", ?)'
        );
        $stmt->execute([
            $usuarioId,
            $data,
            $horario,
            $numPessoas,
            trim((string) ($dados['observacao'] ?? '')),
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function confirmar(int $id): bool
    {
        $reserva = $this->buscarPorId($id);
        if (!$reserva || !$this->verificarDisponibilidade($reserva['data'], $reserva['horario'], (int) $reserva['num_pessoas'], $id)) {
            return false;
        }

        return $this->atualizarStatus($id, 'confirmada');
    }

    public function cancelar(int $id, ?int $usuarioId = null): bool
    {
        if ($usuarioId !== null) {
            $stmt = $this->pdo->prepare('DELETE FROM reservas WHERE id = ? AND usuario_id = ?');
            $stmt->execute([$id, $usuarioId]);
            return $stmt->rowCount() > 0;
        }

        $sql = 'UPDATE reservas SET status = "cancelada" WHERE id = ?';
        $params = [$id];

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function atualizarStatus(int $id, string $status): bool
    {
        if (!in_array($status, self::STATUS, true)) {
            return false;
        }

        $stmt = $this->pdo->prepare('UPDATE reservas SET status = ? WHERE id = ?');
        return $stmt->execute([$status, $id]);
    }

    private function verificarDisponibilidade(string $data, string $horario, int $numPessoas, ?int $ignorarReservaId = null): bool
    {
        if ($data === '' || $horario === '' || $numPessoas < 1) {
            return false;
        }

        $sql = 'SELECT COALESCE(SUM(num_pessoas), 0) FROM reservas
                WHERE data = ? AND horario = ? AND status = "confirmada"';
        $params = [$data, $horario];

        if ($ignorarReservaId) {
            $sql .= ' AND id <> ?';
            $params[] = $ignorarReservaId;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $ocupado = (int) $stmt->fetchColumn();

        return ($ocupado + $numPessoas) <= $this->capacidadePorHorario;
    }

    public function listar(?string $data = null): array
    {
        $sql = "SELECT r.*, u.nome AS cliente, u.telefone
                FROM reservas r
                JOIN usuarios u ON u.id = r.usuario_id
                WHERE r.data >= CURDATE()";
        $params = [];

        if ($data) {
            $sql .= ' AND r.data = ?';
            $params[] = $data;
        }

        $sql .= ' ORDER BY r.data ASC, r.horario ASC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function listarPorUsuario(int $usuarioId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM reservas WHERE usuario_id = ? ORDER BY data DESC, horario DESC');
        $stmt->execute([$usuarioId]);
        return $stmt->fetchAll();
    }

    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM reservas WHERE id = ?');
        $stmt->execute([$id]);
        $reserva = $stmt->fetch();
        return $reserva ?: null;
    }
}
