<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/conexao.php';

class Prato
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = getConnection();
    }

    public function listar(?string $busca = null, ?int $categoriaId = null): array
    {
        return $this->consultarPratos(false, $busca, $categoriaId);
    }

    public function listarDisponiveis(?string $busca = null, ?int $categoriaId = null): array
    {
        return $this->consultarPratos(true, $busca, $categoriaId);
    }

    private function consultarPratos(bool $somenteDisponiveis, ?string $busca, ?int $categoriaId): array
    {
        $sql = "SELECT p.*, c.nome AS categoria,
                       promo.preco_promocional
                FROM pratos p
                JOIN categorias c ON c.id = p.categoria_id
                LEFT JOIN (
                    SELECT prato_id, MIN(preco_promocional) AS preco_promocional
                    FROM vw_promocoes_ativas
                    GROUP BY prato_id
                ) promo ON promo.prato_id = p.id
                WHERE 1 = 1";
        $params = [];

        if ($somenteDisponiveis) {
            $sql .= ' AND p.disponivel = 1';
        }

        if ($categoriaId) {
            $sql .= ' AND p.categoria_id = ?';
            $params[] = $categoriaId;
        }

        if ($busca) {
            $sql .= ' AND (p.nome LIKE ? OR p.descricao LIKE ? OR c.nome LIKE ?)';
            $term = '%' . $busca . '%';
            array_push($params, $term, $term, $term);
        }

        $sql .= ' ORDER BY c.ordem_exibicao, p.nome';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function buscarPorId(int $id, bool $somenteDisponivel = false): ?array
    {
        $sql = "SELECT p.*, c.nome AS categoria,
                       promo.preco_promocional
                FROM pratos p
                JOIN categorias c ON c.id = p.categoria_id
                LEFT JOIN (
                    SELECT prato_id, MIN(preco_promocional) AS preco_promocional
                    FROM vw_promocoes_ativas
                    GROUP BY prato_id
                ) promo ON promo.prato_id = p.id
                WHERE p.id = ?";
        $params = [$id];

        if ($somenteDisponivel) {
            $sql .= ' AND p.disponivel = 1';
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $prato = $stmt->fetch();

        return $prato ?: null;
    }

    public function precoAtual(int $id): ?float
    {
        $prato = $this->buscarPorId($id, true);
        if (!$prato) {
            return null;
        }

        return (float) ($prato['preco_promocional'] ?? $prato['preco']);
    }

    public function cadastrar(array $dados, ?array $arquivo = null): int
    {
        $imagem = $this->salvarImagem($arquivo);
        $stmt = $this->pdo->prepare(
            'INSERT INTO pratos (categoria_id, nome, descricao, preco, tempo_preparo, disponivel, imagem)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            (int) ($dados['categoria_id'] ?? 0),
            trim((string) ($dados['nome'] ?? '')),
            trim((string) ($dados['descricao'] ?? '')),
            (float) str_replace(',', '.', (string) ($dados['preco'] ?? 0)),
            (int) ($dados['tempo_preparo'] ?? 0),
            isset($dados['disponivel']) ? 1 : 0,
            $imagem,
        ]);

        $id = (int) $this->pdo->lastInsertId();
        $this->salvarIngredientes($id, $dados['ingredientes'] ?? [], $dados['quantidades'] ?? []);

        return $id;
    }

    public function editar(int $id, array $dados, ?array $arquivo = null): bool
    {
        $atual = $this->buscarPorId($id);
        if (!$atual) {
            return false;
        }

        $imagem = $this->salvarImagem($arquivo, $atual['imagem'] ?? null);
        $stmt = $this->pdo->prepare(
            'UPDATE pratos
             SET categoria_id = ?, nome = ?, descricao = ?, preco = ?, tempo_preparo = ?, disponivel = ?, imagem = ?
             WHERE id = ?'
        );
        $ok = $stmt->execute([
            (int) ($dados['categoria_id'] ?? 0),
            trim((string) ($dados['nome'] ?? '')),
            trim((string) ($dados['descricao'] ?? '')),
            (float) str_replace(',', '.', (string) ($dados['preco'] ?? 0)),
            (int) ($dados['tempo_preparo'] ?? 0),
            isset($dados['disponivel']) ? 1 : 0,
            $imagem,
            $id,
        ]);

        $this->salvarIngredientes($id, $dados['ingredientes'] ?? [], $dados['quantidades'] ?? []);
        return $ok;
    }

    public function deletar(int $id): bool
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM itens_pedido WHERE prato_id = ?');
        $stmt->execute([$id]);

        if ((int) $stmt->fetchColumn() > 0) {
            return false;
        }

        $delete = $this->pdo->prepare('DELETE FROM pratos WHERE id = ?');
        return $delete->execute([$id]);
    }

    public function alterarDisponibilidade(int $id, bool $disponivel): bool
    {
        $stmt = $this->pdo->prepare('UPDATE pratos SET disponivel = ? WHERE id = ?');
        return $stmt->execute([$disponivel ? 1 : 0, $id]);
    }

    private function salvarIngredientes(int $pratoId, array $ingredientes, array $quantidades): void
    {
        $this->pdo->prepare('DELETE FROM prato_ingrediente WHERE prato_id = ?')->execute([$pratoId]);
        $stmt = $this->pdo->prepare(
            'INSERT INTO prato_ingrediente (prato_id, ingrediente_id, quantidade) VALUES (?, ?, ?)'
        );

        foreach ($ingredientes as $ingredienteId) {
            $ingredienteId = (int) $ingredienteId;
            if ($ingredienteId <= 0) {
                continue;
            }

            $quantidade = (float) str_replace(',', '.', (string) ($quantidades[$ingredienteId] ?? 1));
            $stmt->execute([$pratoId, $ingredienteId, $quantidade > 0 ? $quantidade : 1]);
        }
    }

    private function salvarImagem(?array $arquivo, ?string $imagemAtual = null): ?string
    {
        if (!$arquivo || empty($arquivo['name']) || ($arquivo['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return $imagemAtual;
        }

        if (($arquivo['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            return $imagemAtual;
        }

        if (($arquivo['size'] ?? 0) > 3 * 1024 * 1024) {
            throw new RuntimeException('A imagem deve ter até 3 MB.');
        }

        $extensao = strtolower(pathinfo((string) $arquivo['name'], PATHINFO_EXTENSION));
        $permitidas = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

        if (!in_array($extensao, $permitidas, true)) {
            throw new RuntimeException('Formato de imagem não permitido.');
        }

        // Validação real de MIME via conteúdo do arquivo
        $mime = mime_content_type((string) $arquivo['tmp_name']);
        $mimesPermitidos = [
            'image/jpeg',
            'image/png',
            'image/webp',
            'image/gif',
        ];

        if (!in_array($mime, $mimesPermitidos, true)) {
            throw new RuntimeException('O conteúdo do arquivo não corresponde a uma imagem válida.');
        }

        $nome = bin2hex(random_bytes(12)) . '.' . $extensao;
        $destinoDir = __DIR__ . '/../public/imagens';

        if (!is_dir($destinoDir)) {
            mkdir($destinoDir, 0775, true);
        }

        $destino = $destinoDir . DIRECTORY_SEPARATOR . $nome;

        if (!move_uploaded_file((string) $arquivo['tmp_name'], $destino)) {
            throw new RuntimeException('Não foi possível salvar a imagem.');
        }

        return $nome;
    }
}
