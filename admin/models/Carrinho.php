<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/Prato.php';

class Carrinho
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = getConnection();
    }

    public function adicionar(int $usuarioId, int $pratoId, int $quantidade, string $obsItem = ''): bool
    {
        $quantidade = max(1, $quantidade);
        $obsItem = trim($obsItem);
        $preco = (new Prato())->precoAtual($pratoId);

        if ($preco === null) {
            return false;
        }

        $existente = $this->pdo->prepare(
            'SELECT id, quantidade FROM carrinho WHERE usuario_id = ? AND prato_id = ? AND COALESCE(obs_item, "") = ?'
        );
        $existente->execute([$usuarioId, $pratoId, $obsItem]);
        $item = $existente->fetch();

        if ($item) {
            $stmt = $this->pdo->prepare('UPDATE carrinho SET quantidade = quantidade + ? WHERE id = ?');
            return $stmt->execute([$quantidade, (int) $item['id']]);
        }

        $stmt = $this->pdo->prepare(
            'INSERT INTO carrinho (usuario_id, prato_id, quantidade, obs_item, preco_unit) VALUES (?, ?, ?, ?, ?)'
        );

        return $stmt->execute([$usuarioId, $pratoId, $quantidade, $obsItem, $preco]);
    }

    public function remover(int $usuarioId, int $itemId): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM carrinho WHERE id = ? AND usuario_id = ?');
        return $stmt->execute([$itemId, $usuarioId]);
    }

    public function atualizarQuantidade(int $usuarioId, int $itemId, int $quantidade): bool
    {
        if ($quantidade <= 0) {
            return $this->remover($usuarioId, $itemId);
        }

        $stmt = $this->pdo->prepare('UPDATE carrinho SET quantidade = ? WHERE id = ? AND usuario_id = ?');
        return $stmt->execute([$quantidade, $itemId, $usuarioId]);
    }

    public function listarPorUsuario(int $usuarioId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT c.*, p.nome, p.imagem, p.tempo_preparo, cat.nome AS categoria,
                    (c.quantidade * c.preco_unit) AS subtotal
             FROM carrinho c
             JOIN pratos p ON p.id = c.prato_id
             JOIN categorias cat ON cat.id = p.categoria_id
             WHERE c.usuario_id = ?
             ORDER BY c.created_at DESC'
        );
        $stmt->execute([$usuarioId]);
        return $stmt->fetchAll();
    }

    public function calcularTotal(int $usuarioId): float
    {
        $stmt = $this->pdo->prepare(
            'SELECT COALESCE(SUM(quantidade * preco_unit), 0) FROM carrinho WHERE usuario_id = ?'
        );
        $stmt->execute([$usuarioId]);

        return (float) $stmt->fetchColumn();
    }

    public function limpar(int $usuarioId): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM carrinho WHERE usuario_id = ?');
        return $stmt->execute([$usuarioId]);
    }

    public function contarItens(int $usuarioId): int
    {
        $stmt = $this->pdo->prepare('SELECT COALESCE(SUM(quantidade), 0) FROM carrinho WHERE usuario_id = ?');
        $stmt->execute([$usuarioId]);

        return (int) $stmt->fetchColumn();
    }

    public function obterEstadoCarrinho(int $usuarioId): array
    {
        $itens = $this->listarPorUsuario($usuarioId);
        $total = 0.0;
        $count = 0;

        foreach ($itens as $item) {
            $total += (float) $item['subtotal'];
            $count += (int) $item['quantidade'];
        }

        return [
            'itens' => $itens,
            'total' => $total,
            'contagem' => $count
        ];
    }
}
