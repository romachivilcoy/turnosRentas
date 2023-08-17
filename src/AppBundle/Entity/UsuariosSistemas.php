<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UsuariosSistemas
 *
 * @ORM\Table(name="Usuarios_Sistemas")
 * @ORM\Entity
 */
class UsuariosSistemas
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_clave", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $idClave;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_usuario", type="integer", nullable=true)
     */
    private $idUsuario;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_sistema", type="integer", nullable=true)
     */
    private $idSistema;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_ult_modif", type="datetime", nullable=true)
     */
    private $fechaUltModif;

    /**
     * @var integer
     *
     * @ORM\Column(name="orden", type="integer", nullable=true)
     */
    private $orden = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="vigente", type="string", length=1, nullable=true)
     */
    private $vigente;

    /**
     * @return int
     */
    public function getIdClave()
    {
        return $this->idClave;
    }

    /**
     * @param int $idClave
     */
    public function setIdClave($idClave)
    {
        $this->idClave = $idClave;
    }

    /**
     * @return int
     */
    public function getIdUsuario()
    {
        return $this->idUsuario;
    }

    /**
     * @param int $idUsuario
     */
    public function setIdUsuario($idUsuario)
    {
        $this->idUsuario = $idUsuario;
    }

    /**
     * @return int
     */
    public function getIdSistema()
    {
        return $this->idSistema;
    }

    /**
     * @param int $idSistema
     */
    public function setIdSistema($idSistema)
    {
        $this->idSistema = $idSistema;
    }

    /**
     * @return \DateTime
     */
    public function getFechaUltModif()
    {
        return $this->fechaUltModif;
    }

    /**
     * @param \DateTime $fechaUltModif
     */
    public function setFechaUltModif($fechaUltModif)
    {
        $this->fechaUltModif = $fechaUltModif;
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

