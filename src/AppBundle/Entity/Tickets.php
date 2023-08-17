<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tickets
 *
 * @ORM\Table(name="Tickets")
 * @ORM\Entity
 */
class Tickets
{
    /**
     * @var integer
     *
     * @ORM\Column(name="tk_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $tkId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="tk_fecha", type="datetime", nullable=true)
     */
    private $tkFecha;

    /**
     * @var string
     *
     * @ORM\Column(name="tk_escribano", type="string", length=3, nullable=false)
     */
    private $tkEscribano = 'N\'E00';

    /**
     * @var string
     *
     * @ORM\Column(name="tk_inmueble", type="string", length=3, nullable=false)
     */
    private $tkInmueble = 'N\'P00';

    /**
     * @var string
     *
     * @ORM\Column(name="tk_comercio", type="string", length=3, nullable=false)
     */
    private $tkComercio = 'N\'C00';

    /**
     * @var string
     *
     * @ORM\Column(name="tk_moto", type="string", length=3, nullable=false)
     */
    private $tkMoto = 'N\'R00';

    /**
     * @var string
     *
     * @ORM\Column(name="tk_cementerio", type="string", length=3, nullable=true)
     */
    private $tkCementerio = 'N\'M00';

    /**
     * @var string
     *
     * @ORM\Column(name="tk_juicio", type="string", length=3, nullable=true)
     */
    private $tkJuicio = 'N\'J00';

    /**
     * @var string
     *
     * @ORM\Column(name="tk_varios", type="string", length=3, nullable=true)
     */
    private $tkVarios = 'N\'V00';

    /**
     * @var string
     *
     * @ORM\Column(name="tk_arba", type="string", length=3, nullable=true)
     */
    private $tkArba = 'N\'A00';

    /**
     * @return int
     */
    public function getTkId()
    {
        return $this->tkId;
    }

    /**
     * @param int $tkId
     */
    public function setTkId($tkId)
    {
        $this->tkId = $tkId;
    }

    /**
     * @return \DateTime
     */
    public function getTkFecha()
    {
        return $this->tkFecha;
    }

    /**
     * @param \DateTime $tkFecha
     */
    public function setTkFecha($tkFecha)
    {
        $this->tkFecha = $tkFecha;
    }

    /**
     * @return string
     */
    public function getTkEscribano()
    {
        return $this->tkEscribano;
    }

    /**
     * @param string $tkEscribano
     */
    public function setTkEscribano($tkEscribano)
    {
        $this->tkEscribano = $tkEscribano;
    }

    /**
     * @return string
     */
    public function getTkInmueble()
    {
        return $this->tkInmueble;
    }

    /**
     * @param string $tkInmueble
     */
    public function setTkInmueble($tkInmueble)
    {
        $this->tkInmueble = $tkInmueble;
    }

    /**
     * @return string
     */
    public function getTkComercio()
    {
        return $this->tkComercio;
    }

    /**
     * @param string $tkComercio
     */
    public function setTkComercio($tkComercio)
    {
        $this->tkComercio = $tkComercio;
    }

    /**
     * @return string
     */
    public function getTkMoto()
    {
        return $this->tkMoto;
    }

    /**
     * @param string $tkMoto
     */
    public function setTkMoto($tkMoto)
    {
        $this->tkMoto = $tkMoto;
    }

    /**
     * @return string
     */
    public function getTkCementerio()
    {
        return $this->tkCementerio;
    }

    /**
     * @param string $tkCementerio
     */
    public function setTkCementerio($tkCementerio)
    {
        $this->tkCementerio = $tkCementerio;
    }

    /**
     * @return string
     */
    public function getTkJuicio()
    {
        return $this->tkJuicio;
    }

    /**
     * @param string $tkJuicio
     */
    public function setTkJuicio($tkJuicio)
    {
        $this->tkJuicio = $tkJuicio;
    }

    /**
     * @return string
     */
    public function getTkVarios()
    {
        return $this->tkVarios;
    }

    /**
     * @param string $tkVarios
     */
    public function setTkVarios($tkVarios)
    {
        $this->tkVarios = $tkVarios;
    }

    /**
     * @return string
     */
    public function getTkArba()
    {
        return $this->tkArba;
    }

    /**
     * @param string $tkArba
     */
    public function setTkArba($tkArba)
    {
        $this->tkArba = $tkArba;
    }


}

