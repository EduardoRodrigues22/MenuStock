<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/conexao.php';

class Promocao
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = getConnection();
    }

    public function listar(): array
    {
        return $this->pdo
            ->query(
                'SELECT pr.*, p.nome AS prato, p.preco
                 FROM promocoes pr
                 JOIN pratos p ON p.id = pr.prato_id
                 ORDER BY pr.data_fim DESC, pr.nome'
            )
            ->fetchAll();
    }

    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM promocoes WHERE id = ?');
        $stmt->execute([$id]);
        $promocao = $stmt->fetch();
        return $promocao ?: null;
    }

    public function cadastrar(array $dados): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO promocoes (prato_id, nome, descricao, tipo, valor, data_inicio, data_fim, ativa)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute($this->normalizarDados($dados));
        return (int) $this->pdo->lastInsertId();
    }

    public function editar(int $id, array $dados): bool
    {
        $params = $this->normalizarDados($dados);
        $params[] = $id;
        $stmt = $this->pdo->prepare(
            'UPDATE promocoes
             SET prato_id = ?, nome = ?, descricao = ?, tipo = ?, valor = ?, data_inicio = ?, data_fim = ?, ativa = ?
             WHERE id = ?'
        );

        return $stmt->execute($params);
    }

    public function deletar(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM promocoes WHERE id = ?');
        return $stmt->execute([$id]);
    }

    private function normalizarDados(array $dados): array
    {
        $inicio = (string) ($dados['data_inicio'] ?? '');
        $fim = (string) ($dados['data_fim'] ?? '');

        if ($inicio === '' || $fim === '' || strtotime($fim) < strtotime($inicio)) {
            throw new InvalidArgumentException('A data final deve ser maior ou igual à data inicial.');
        }

        return [
            (int) ($dados['prato_id'] ?? 0),
            trim((string) ($dados['nome'] ?? '')),
            trim((string) ($dados['descricao'] ?? '')),
            (string) ($dados['tipo'] ?? 'desconto_percentual'),
            (float) str_replace(',', '.', (string) ($dados['valor'] ?? 0)),
            $inicio,
            $fim,
            isset($dados['ativa']) ? 1 : 0,
        ];
    }
}
