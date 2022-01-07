<?php

namespace App\Entity;

use App\Repository\HashRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HashRepository::class)]
class Hash
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $Batch;

    #[ORM\Column(type: 'integer')]
    private ?int $block;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $string;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $key_string;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $hash;

    #[ORM\Column(type: 'integer')]
    private ?int $attempts;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBatch(): ?\DateTimeInterface
    {
        return $this->Batch;
    }

    public function setBatch(\DateTimeInterface $Batch): self
    {
        $this->Batch = $Batch;

        return $this;
    }

    public function getBlock(): ?int
    {
        return $this->block;
    }

    public function setBlock(int $block): self
    {
        $this->block = $block;

        return $this;
    }

    public function getString(): ?string
    {
        return $this->string;
    }

    public function setString(string $string): self
    {
        $this->string = $string;

        return $this;
    }

    public function getKey(): ?string
    {
        return $this->key_string;
    }

    public function setKey(string $key_string): self
    {
        $this->key_string = $key_string;

        return $this;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(string $hash): self
    {
        $this->hash = $hash;

        return $this;
    }

    public function getAttempts(): ?int
    {
        return $this->attempts;
    }

    public function setAttempts(int $attempts): self
    {
        $this->attempts = $attempts;

        return $this;
    }
}
