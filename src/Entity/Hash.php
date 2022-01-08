<?php

namespace App\Entity;

use App\Repository\HashRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HashRepository::class)]
class Hash
{
    /**
     * @var int|null
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    protected ?int $id;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255)]
    protected ?string $batch;

    /**
     * @var int|null
     */
    #[ORM\Column(type: 'integer')]
    protected ?int $block;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255)]
    protected ?string $string;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255)]
    protected ?string $key_string;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255)]
    protected ?string $hash;

    /**
     * @var int|null
     */
    #[ORM\Column(type: 'integer')]
    protected ?int $attempts;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getbatch(): ?string
    {
        return $this->batch;
    }

    /**
     * @param string $batch
     * @return $this
     */
    public function setBatch(string $batch): self
    {
        $this->batch = $batch;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getBlock(): ?int
    {
        return $this->block;
    }

    /**
     * @param int $block
     * @return $this
     */
    public function setBlock(int $block): self
    {
        $this->block = $block;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getString(): ?string
    {
        return $this->string;
    }

    /**
     * @param string $string
     * @return $this
     */
    public function setString(string $string): self
    {
        $this->string = $string;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getKey_string(): ?string
    {
        return $this->key_string;
    }

    /**
     * @param string $key_string
     * @return $this
     */
    public function setKey(string $key_string): self
    {
        $this->key_string = $key_string;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getHash(): ?string
    {
        return $this->hash;
    }

    /**
     * @param string $hash
     * @return $this
     */
    public function setHash(string $hash): self
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getAttempts(): ?int
    {
        return $this->attempts;
    }

    /**
     * @param int $attempts
     * @return $this
     */
    public function setAttempts(int $attempts): self
    {
        $this->attempts = $attempts;

        return $this;
    }
}
