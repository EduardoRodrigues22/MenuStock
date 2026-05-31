<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/conexao.php';

class Ingrediente
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = getConnection();
    }

    public function listar(): array
    {
        return $this->pdo
            ->query('SELECT * FROM ingredientes ORDER BY nome')
            ->fetchAll();
    }

    public function listarPorPrato(int $pratoId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT i.*, pi.quantidade
             FROM ingredientes i
             JOIN prato_ingrediente pi ON pi.ingrediente_id = i.id
             WHERE pi.prato_id = ?
             ORDER BY i.nome'
        );
        $stmt->execute([$pratoId]);
        return $stmt->fetchAll();
    }

    public function cadastrar(array $dados): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO ingredientes (nome, unidade) VALUES (?, ?)'
        );
        $stmt->execute([
            trim((string) ($dados['nome'] ?? '')),
            trim((string) ($dados['unidade'] ?? 'g')),
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function editar(int $id, array $dados): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE ingredientes SET nome = ?, unidade = ? WHERE id = ?'
        );

        return $stmt->execute([
            trim((string) ($dados['nome'] ?? '')),
            trim((string) ($dados['unidade'] ?? 'g')),
            $id,
        ]);
    }

    public function deletar(int $id): bool
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM prato_ingrediente WHERE ingrediente_id = ?');
        $stmt->execute([$id]);

        if ((int) $stmt->fetchColumn() > 0) {
            return false;
        }

        $delete = $this->pdo->prepare('DELETE FROM ingredientes WHERE id = ?');
        return $delete->execute([$id]);
    }
}
