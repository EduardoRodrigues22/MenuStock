<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/Carrinho.php';

class Pedido
{
    private PDO $pdo;

    public const STATUS = ['recebido', 'preparo', 'pronto', 'entregue', 'cancelado'];

    public function __construct()
    {
        $this->pdo = getConnection();
    }

    public function criarDoCarrinho(int $usuarioId, ?int $mesa = null, string $obsGeral = ''): ?int
    {
        return $this->criarDoCarrinhoParaUsuario($usuarioId, $usuarioId, $mesa, $obsGeral);
    }

    public function criarDoCarrinhoParaUsuario(int $carrinhoUsuarioId, ?int $pedidoUsuarioId, ?int $mesa = null, string $obsGeral = ''): ?int
    {
        $carrinho = new Carrinho();
        $itens = $carrinho->listarPorUsuario($carrinhoUsuarioId);

        if (!$itens) {
            return null;
        }

        $total = $carrinho->calcularTotal($carrinhoUsuarioId);
        $this->pdo->beginTransaction();

        try {
            $pedidoStmt = $this->pdo->prepare(
                'INSERT INTO pedidos (usuario_id, status, obs_geral, total, mesa) VALUES (?, "recebido", ?, ?, ?)'
            );
            $pedidoStmt->execute([$pedidoUsuarioId, trim($obsGeral), $total, $mesa]);
            $pedidoId = (int) $this->pdo->lastInsertId();

            $itemStmt = $this->pdo->prepare(
                'INSERT INTO itens_pedido (pedido_id, prato_id, quantidade, preco_unit, obs_item)
                 VALUES (?, ?, ?, ?, ?)'
            );

            foreach ($itens as $item) {
                $itemStmt->execute([
                    $pedidoId,
                    (int) $item['prato_id'],
                    (int) $item['quantidade'],
                    (float) $item['preco_unit'],
                    (string) $item['obs_item'],
                ]);
            }

            $carrinho->limpar($carrinhoUsuarioId);
            $this->pdo->commit();
            return $pedidoId;
        } catch (Throwable $exception) {
            $this->pdo->rollBack();
            throw $exception;
        }
    }

    public function atualizarStatus(int $id, string $status): bool
    {
        if (!in_array($status, self::STATUS, true)) {
            return false;
        }

        $atual = $this->buscarPorId($id);
        if (!$atual || in_array($atual['status'], ['cancelado', 'entregue'], true)) {
            return false;
        }

        $stmt = $this->pdo->prepare('UPDATE pedidos SET status = ? WHERE id = ?');
        return $stmt->execute([$status, $id]);
    }

    public function listar(?string $status = null, bool $somenteHoje = false): array
    {
        $sql = "SELECT p.id, p.status, p.total, p.obs_geral, p.created_at, p.updated_at, p.mesa,
                       u.nome AS cliente, u.telefone, COUNT(ip.id) AS total_itens
                FROM pedidos p
                LEFT JOIN usuarios u ON u.id = p.usuario_id
                LEFT JOIN itens_pedido ip ON ip.pedido_id = p.id";
        $params = [];
        $where = [];

        if ($status) {
            $where[] = 'p.status = ?';
            $params[] = $status;
        }

        if ($somenteHoje) {
            $where[] = 'p.created_at >= CURDATE() AND p.created_at < DATE_ADD(CURDATE(), INTERVAL 1 DAY)';
        }

        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $sql .= ' GROUP BY p.id, p.status, p.total, p.obs_geral, p.created_at, p.updated_at, p.mesa, u.nome, u.telefone
                  ORDER BY p.created_at DESC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function listarPorUsuario(int $usuarioId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM pedidos WHERE usuario_id = ? ORDER BY created_at DESC'
        );
        $stmt->execute([$usuarioId]);
        return $stmt->fetchAll();
    }

    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT p.*, u.nome AS cliente, u.email, u.telefone
             FROM pedidos p
             LEFT JOIN usuarios u ON u.id = p.usuario_id
             WHERE p.id = ?'
        );
        $stmt->execute([$id]);
        $pedido = $stmt->fetch();
        return $pedido ?: null;
    }

    public function itens(int $pedidoId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT ip.*, p.nome, p.imagem
             FROM itens_pedido ip
             JOIN pratos p ON p.id = ip.prato_id
             WHERE ip.pedido_id = ?
             ORDER BY ip.id'
        );
        $stmt->execute([$pedidoId]);
        return $stmt->fetchAll();
    }
    public function contar(?string $status = null, bool $somenteHoje = false): int
    {
        $sql    = 'SELECT COUNT(*) FROM pedidos p WHERE 1=1';
        $params = [];

        if ($status !== null && $status !== '') {
            $sql      .= ' AND p.status = ?';
            $params[]  = $status;
        }

        if ($somenteHoje) {
            $sql .= ' AND p.created_at >= CURDATE()';
            $sql .= ' AND p.created_at < DATE_ADD(CURDATE(), INTERVAL 1 DAY)';
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }
}
