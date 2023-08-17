<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ColaTurnos
 *
 * @ORM\Table(name="Cola_Turnos")
 * @ORM\Entity
 */
class ColaTurnos
{
    /**
     * @var integer
     *
     * @ORM\Column(name="cl_id_cola", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $clIdCola;

    /**
     * @var integer
     *
     * @ORM\Column(name="cl_id_turno", type="integer", nullable=true)
     */
    private $clIdTurno;

    /**
     * @var string
     *
     * @ORM\Column(name="cl_estado", type="string", length=1, nullable=true)
     */
    private $clEstado;

    /**
     * @var string
     *
     * @ORM\Column(name="cl_color", type="string", length=3, nullable=true)
     */
    private $clColor;

    /**
     * @var integer
     *
     * @ORM\Column(name="cl_ubicacion", type="integer", nullable=true)
     */
    private $clUbicacion;

    /**
     * @return int
     */
    public function getClIdCola()
    {
        return $this->clIdCola;
    }

    /**
     * @param int $clIdCola
     */
    public function setClIdCola($clIdCola)
    {
        $this->clIdCola = $clIdCola;
    }

    /**
     * @return int
     */
    public function getClIdTurno()
    {
        return $this->clIdTurno;
    }

    /**
     * @param int $clIdTurno
     */
    public function setClIdTurno($clIdTurno)
    {
        $this->clIdTurno = $clIdTurno;
    }

    /**
     * @return string
     */
    public function getClEstado()
    {
        return $this->clEstado;
    }

    /**
     * @param string $clEstado
     */
    public function setClEstado($clEstado)
    {
        $this->clEstado = $clEstado;
    }

    /**
     * @return string
     */
    public function getClColor()
    {
        return $this->clColor;
    }

    /**
     * @param string $clColor
     */
    public function setClColor($clColor)
    {
        $this->clColor = $clColor;
    }

    /**
     * @return int
     */
    public function getClUbicacion()
    {
        return $this->clUbicacion;
    }

    /**
     * @param int $clUbicacion
     */
    public function setClUbicacion($clUbicacion)
    {
        $this->clUbicacion = $clUbicacion;
    }


}

