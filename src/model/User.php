<?php

declare(strict_types=1);

namespace Vosouza\Sonomais\model;

class User
{
    private ?string $id;
    private string $name;
    private string $email;
    private string $password;

    public function __construct(?string $id, string $name, string $email, string $password)
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public static function fromLoginPost(): ?User
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            echo 'aqui';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            if (!empty($email) && !empty($password)) {
                return new self(null, '', $email, $password);
            }
        }
        return null;
    }
}