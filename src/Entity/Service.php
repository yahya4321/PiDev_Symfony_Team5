<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Service
 *
 * @ORM\Table(name="service", uniqueConstraints={@ORM\UniqueConstraint(name="ServLib", columns={"ServLib"})})
 * @ORM\Entity
 */
class Service
{
    /**
     * @var int
     *
     * @ORM\Column(name="ServId", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $servid;

    /**
     * @var string
     *
     * @ORM\Column(name="ServLib", type="string", length=20, nullable=false)
     * @Assert\NotBlank(message="Le champ ne doit pas être vide.")
     * @Assert\Length(max=20, maxMessage="Le champ ne doit pas dépasser {{ limit }} caractères.")
     * @Assert\Regex(pattern="/^[^0-9]*$/", message="Le champ ne doit pas contenir de chiffres.")
     */
    private $servlib;

    /**
     * @var string
     *
     * @ORM\Column(name="ServDesc", type="string", length=200, nullable=false)
     * @Assert\NotBlank(message="Le champ ne doit pas être vide.")
     * @Assert\Length(max=200, maxMessage="Le champ ne doit pas dépasser {{ limit }} caractères.")
     */
    private $servdesc;

    /**
     * @var int
     *
     * @ORM\Column(name="ServDispo", type="integer", nullable=false)
     * * @Assert\NotBlank(message="Le champ ne doit pas être vide.")
     */
    private $servdispo;

    /**
     * @var string
     *
     * @ORM\Column(name="ServImg", type="string", length=255, nullable=false)
     * @Assert\NotBlank(message="Le champ ne doit pas être vide.")
     * @Assert\Length(max=255, maxMessage="Le champ ne doit pas dépasser {{ limit }} caractères.")
     */
    private $servimg;


    /**
     * @var string
     *
     * @ORM\Column(name="QrCode", type="string", length=255, nullable=false)
     */
    private $qrcode;

    /**
     * @var float
     *
     * @ORM\Column(name="ServPrix", type="float", precision=10, scale=0, nullable=false)
     * @Assert\Positive(message="La valeur doit être positive.")
     */
    private $servprix;

    /**
     * @var string
     *
     * @ORM\Column(name="ServVille", type="string", length=20, nullable=false)
     * @Assert\NotBlank(message="Le champ ne doit pas être vide.")
     * @Assert\Length(max=20, maxMessage="Le champ ne doit pas dépasser {{ limit }} caractères.")
     * @Assert\Regex(pattern="/^[^0-9]*$/", message="Le champ ne doit pas contenir de chiffres.")
     */
    private $servville;

    /**
     * @var string
     *

     * @ORM\Column(name="CatLib", type="string", length=20, nullable=false)
     *
     * @ORM\ManyToOne(targetEntity="Categorie")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="catlib", referencedColumnName="catlib")
     * })
     */


    private $catlib;


    #[ORM\OneToMany(targetEntity: yyyyy::class, mappedBy: 'service')]

    private $ratings;

    /**
     * @ORM\Column(type="integer",nullable=true)
     */
    private $note;

    /**
     * @return int
     */
    public function getServid(): int
    {
        return $this->servid;
    }

    /**
     * @param int $servid
     */
    public function setServid(int $servid): void
    {
        $this->servid = $servid;
    }

    /**
     * @return string
     */
    public function getServlib(): string
    {
        return $this->servlib;
    }

    /**
     * @param string $servlib
     */
    public function setServlib(string $servlib): void
    {
        $this->servlib = $servlib;
    }

    /**
     * @return string
     */
    public function getServdesc(): string
    {
        return $this->servdesc;
    }

    /**
     * @param string $servdesc
     */
    public function setServdesc(string $servdesc): void
    {
        $this->servdesc = $servdesc;
    }

    /**
     * @return int
     */
    public function getServdispo(): int
    {
        return $this->servdispo;
    }

    /**
     * @param int $servdispo
     */
    public function setServdispo(int $servdispo): void
    {
        $this->servdispo = $servdispo;
    }

    /**
     * @return string
     */
    public function getServimg(): string
    {
        return $this->servimg;
    }

    /**
     * @param string $servimg
     */
    public function setServimg(string $servimg): void
    {
        $this->servimg = $servimg;
    }

    /**
     * @return float
     */
    public function getServprix(): float
    {
        return $this->servprix;
    }

    /**
     * @param float $servprix
     */
    public function setServprix(float $servprix): void
    {
        $this->servprix = $servprix;
    }

    /**
     * @return string
     */
    public function getServville(): string
    {
        return $this->servville;
    }

    /**
     * @param string $servville
     */
    public function setServville(string $servville): void
    {
        $this->servville = $servville;
    }

    /**
     * @return string
     */
    public function getCatlib(): string
    {
        return $this->catlib;
    }

    /**
     * @param string $catlib
     */
    public function setCatlib(Categorie $catlib): void
    {
        $this->catlib = $catlib;
    }


    /**
     * @return string
     */
    public function getQrcode(): string
    {
        return $this->qrcode;
    }

    /**
     * @param string $qrcode
     */
    public function setQrcode(string $qrcode): void
    {
        $this->qrcode = $qrcode;
    }

    /**
     * @return mixed
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param mixed $note
     */
    public function setNote($note): void
    {
        $this->note = $note;
    }

}
