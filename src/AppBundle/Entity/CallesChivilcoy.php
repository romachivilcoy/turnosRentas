<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CallesChivilcoy
 *
 * @ORM\Table(name="Calles_chivilcoy")
 * @ORM\Entity
 */
class CallesChivilcoy
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_calle", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $idCalle;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255, nullable=true)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="cod_rafam", type="string", length=5, nullable=true)
     */
    private $codRafam;

    /**
     * @var string
     *
     * @ORM\Column(name="vigente", type="string", length=1, nullable=true)
     */
    private $vigente;

    /**
     * @return int
     */
    public function getIdCalle()
    {
        return $this->idCalle;
    }

    /**
     * @param int $idCalle
     */
    public function setIdCalle($idCalle)
    {
        $this->idCalle = $idCalle;
    }

    /**
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * @param string $nombre
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    /**
     * @return string
     */
    public function getCodRafam()
    {
        return $this->codRafam;
    }

    /**
     * @param string $codRafam
     */
    public function setCodRafam($codRafam)
    {
        $this->codRafam = $codRafam;
    }

    /**
     * @return string
     */
    public function getVigente()
    {
        return $this->vigente;
    }

    /**
     * @param string $vigente
     */
    public function setVigente($vigente)
    {
        $this->vigente = $vigente;
    }


}

