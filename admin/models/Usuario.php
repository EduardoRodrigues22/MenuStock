<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/conexao.php';

class Usuario
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = getConnection();
    }

    public function autenticar(string $email, string $senha): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM usuarios WHERE email = ? LIMIT 1');
        $stmt->execute([strtolower(trim($email))]);
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($senha, $usuario['senha'])) {
            return $usuario;
        }

        return null;
    }

    public function cadastrar(array $dados): int
    {
        $nome = trim((string) ($dados['nome'] ?? ''));
        $email = strtolower(trim((string) ($dados['email'] ?? '')));
        $senha = (string) ($dados['senha'] ?? '');
        $telefone = trim((string) ($dados['telefone'] ?? ''));

        if ($nome === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($senha) < 6) {
            throw new InvalidArgumentException('Informe nome, e-mail válido e senha com no mínimo 6 caracteres.');
        }

        $stmt = $this->pdo->prepare(
            'INSERT INTO usuarios (nome, email, senha, telefone, tipo) VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $nome,
            $email,
            password_hash($senha, PASSWORD_DEFAULT),
            $telefone,
            self::tipoPorEmail($email),
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function editar(int $id, array $dados): bool
    {
        $nome = trim((string) ($dados['nome'] ?? ''));
        $email = strtolower(trim((string) ($dados['email'] ?? '')));
        $telefone = trim((string) ($dados['telefone'] ?? ''));

        if ($id <= 0 || $nome === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Dados de usuário inválidos.');
        }

        $stmt = $this->pdo->prepare(
            'UPDATE usuarios SET nome = ?, email = ?, telefone = ?, tipo = ? WHERE id = ?'
        );

        return $stmt->execute([$nome, $email, $telefone, self::tipoPorEmail($email), $id]);
    }

    public function alterarSenha(int $id, string $senha): bool
    {
        if ($id <= 0 || strlen($senha) < 6) {
            throw new InvalidArgumentException('A senha deve ter no mínimo 6 caracteres.');
        }

        $stmt = $this->pdo->prepare('UPDATE usuarios SET senha = ? WHERE id = ?');
        return $stmt->execute([password_hash($senha, PASSWORD_DEFAULT), $id]);
    }

    public function deletar(int $id, ?int $usuarioLogadoId = null): bool
    {
        if ($id <= 0 || ($usuarioLogadoId !== null && $id === $usuarioLogadoId)) {
            return false;
        }

        $stmt = $this->pdo->prepare('DELETE FROM usuarios WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public function listar(?string $busca = null): array
    {
        $sql = 'SELECT id, nome, email, telefone, tipo, created_at FROM usuarios';
        $params = [];

        if ($busca) {
            $sql .= ' WHERE nome LIKE ? OR email LIKE ?';
            $term = '%' . $busca . '%';
            $params = [$term, $term];
        }

        $sql .= ' ORDER BY nome';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function listarClientes(): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, nome, email, telefone, tipo, created_at
             FROM usuarios
             WHERE tipo = "user"
             ORDER BY nome'
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT id, nome, email, telefone, tipo, created_at FROM usuarios WHERE id = ?');
        $stmt->execute([$id]);
        $usuario = $stmt->fetch();

        return $usuario ?: null;
    }

    public static function tipoPorEmail(string $email): string
    {
        $email = strtolower(trim($email));

        if (str_ends_with($email, '@garcom.menustock.com.br')) {
            return 'garcom';
        }

        if (str_ends_with($email, '@menustock.com.br')) {
            return 'admin';
        }

        return 'user';
    }
}
