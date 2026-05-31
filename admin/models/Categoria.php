<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/conexao.php';

class Categoria
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = getConnection();
    }

    public function listar(): array
    {
        return $this->pdo
            ->query('SELECT * FROM categorias ORDER BY ordem_exibicao, nome')
            ->fetchAll();
    }

    public function cadastrar(array $dados): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO categorias (nome, descricao, ordem_exibicao) VALUES (?, ?, ?)'
        );
        $stmt->execute([
            trim((string) ($dados['nome'] ?? '')),
            trim((string) ($dados['descricao'] ?? '')),
            (int) ($dados['ordem_exibicao'] ?? 0),
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function editar(int $id, array $dados): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE categorias SET nome = ?, descricao = ?, ordem_exibicao = ? WHERE id = ?'
        );

        return $stmt->execute([
            trim((string) ($dados['nome'] ?? '')),
            trim((string) ($dados['descricao'] ?? '')),
            (int) ($dados['ordem_exibicao'] ?? 0),
            $id,
        ]);
    }

    public function deletar(int $id): bool
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM pratos WHERE categoria_id = ?');
        $stmt->execute([$id]);

        if ((int) $stmt->fetchColumn() > 0) {
            return false;
        }

        $delete = $this->pdo->prepare('DELETE FROM categorias WHERE id = ?');
        return $delete->execute([$id]);
    }
}
