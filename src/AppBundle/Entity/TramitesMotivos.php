<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TramitesMotivos
 *
 * @ORM\Table(name="tramites_motivos")
 * @ORM\Entity
 */
class TramitesMotivos
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_motivo", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $idMotivo;

    /**
     * @var string
     *
     * @ORM\Column(name="tipo_tramite", type="string", length=1, nullable=true)
     */
    private $tipoTramite;

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
     * @ORM\Column(name="orden", type="integer", nullable=true)
     */
    private $orden;

    /**
     * @return int
     */
    public function getIdMotivo()
    {
        return $this->idMotivo;
    }

    /**
     * @param int $idMotivo
     */
    public function setIdMotivo($idMotivo)
    {
        $this->idMotivo = $idMotivo;
    }

    /**
     * @return string
     */
    public function getTipoTramite()
    {
        return $this->tipoTramite;
    }

    /**
     * @param string $tipoTramite
     */
    public function setTipoTramite($tipoTramite)
    {
        $this->tipoTramite = $tipoTramite;
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
    public function getOrden()
    {
        return $this->orden;
    }

    /**
     * @param int $orden
     */
    public function setOrden($orden)
    {
        $this->orden = $orden;
    }


}

