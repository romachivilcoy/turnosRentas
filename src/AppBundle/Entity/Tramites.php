<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tramites
 *
 * @ORM\Table(name="Tramites")
 * @ORM\Entity
 */
class Tramites
{
    /**
     * @var integer
     *
     * @ORM\Column(name="tra_id_tramite", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $traIdTramite;

    /**
     * @var string
     *
     * @ORM\Column(name="tra_tipo_tramite", type="string", length=30, nullable=true)
     */
    private $traTipoTramite;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=50, nullable=true)
     */
    private $descripcion;

    /**
     * @var string
     *
     * @ORM\Column(name="vigente", type="string", length=1, nullable=true)
     */
    private $vigente;

    /**
     * @var integer
     *
     * @ORM\Column(name="tra_prioridad", type="integer", nullable=true)
     */
    private $traPrioridad = '0';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="tra_hora_inicio", type="time", nullable=true)
     */
    private $traHoraInicio;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="tra_hora_fin", type="time", nullable=true)
     */
    private $traHoraFin;

    /**
     * @return int
     */
    public function getTraIdTramite()
    {
        return $this->traIdTramite;
    }

    /**
     * @param int $traIdTramite
     */
    public function setTraIdTramite($traIdTramite)
    {
        $this->traIdTramite = $traIdTramite;
    }

    /**
     * @return string
     */
    public function getTraTipoTramite()
    {
        return $this->traTipoTramite;
    }

    /**
     * @param string $traTipoTramite
     */
    public function setTraTipoTramite($traTipoTramite)
    {
        $this->traTipoTramite = $traTipoTramite;
    }

    /**
     * @return string
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * @param string $descripcion
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
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

    /**
     * @return int
     */
    public function getTraPrioridad()
    {
        return $this->traPrioridad;
    }

    /**
     * @param int $traPrioridad
     */
    public function setTraPrioridad($traPrioridad)
    {
        $this->traPrioridad = $traPrioridad;
    }

    /**
     * @return \DateTime
     */
    public function getTraHoraInicio()
    {
        return $this->traHoraInicio;
    }

    /**
     * @param \DateTime $traHoraInicio
     */
    public function setTraHoraInicio($traHoraInicio)
    {
        $this->traHoraInicio = $traHoraInicio;
    }

    /**
     * @return \DateTime
     */
    public function getTraHoraFin()
    {
        return $this->traHoraFin;
    }

    /**
     * @param \DateTime $traHoraFin
     */
    public function setTraHoraFin($traHoraFin)
    {
        $this->traHoraFin = $traHoraFin;
    }


}

