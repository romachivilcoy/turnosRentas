<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Boxes
 *
 * @ORM\Table(name="Boxes")
 * @ORM\Entity
 */
class Boxes
{
    /**
     * @var integer
     *
     * @ORM\Column(name="box_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $boxId;

    /**
     * @var integer
     *
     * @ORM\Column(name="box_orden", type="integer", nullable=true)
     */
    private $boxOrden;

    /**
     * @var integer
     *
     * @ORM\Column(name="box_numero", type="integer", nullable=true)
     */
    private $boxNumero;

    /**
     * @var integer
     *
     * @ORM\Column(name="box_idturno", type="integer", nullable=true)
     */
    private $boxIdturno;

    /**
     * @var string
     *
     * @ORM\Column(name="box_estado", type="string", length=1, nullable=true)
     */
    private $boxEstado;

    /**
     * @var string
     *
     * @ORM\Column(name="box_sonido", type="string", length=1, nullable=true)
     */
    private $boxSonido = 'N';

    /**
     * @var integer
     *
     * @ORM\Column(name="box_usuario", type="integer", nullable=true)
     */
    private $boxUsuario;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="box_fecha", type="datetime", nullable=true)
     */
    private $boxFecha;

    /**
     * @var integer
     *
     * @ORM\Column(name="box_tiempo", type="integer", nullable=true)
     */
    private $boxTiempo;

    /**
     * @return int
     */
    public function getBoxId()
    {
        return $this->boxId;
    }

    /**
     * @param int $boxId
     */
    public function setBoxId($boxId)
    {
        $this->boxId = $boxId;
    }

    /**
     * @return int
     */
    public function getBoxOrden()
    {
        return $this->boxOrden;
    }

    /**
     * @param int $boxOrden
     */
    public function setBoxOrden($boxOrden)
    {
        $this->boxOrden = $boxOrden;
    }

    /**
     * @return int
     */
    public function getBoxNumero()
    {
        return $this->boxNumero;
    }

    /**
     * @param int $boxNumero
     */
    public function setBoxNumero($boxNumero)
    {
        $this->boxNumero = $boxNumero;
    }

    /**
     * @return int
     */
    public function getBoxIdturno()
    {
        return $this->boxIdturno;
    }

    /**
     * @param int $boxIdturno
     */
    public function setBoxIdturno($boxIdturno)
    {
        $this->boxIdturno = $boxIdturno;
    }

    /**
     * @return string
     */
    public function getBoxEstado()
    {
        return $this->boxEstado;
    }

    /**
     * @param string $boxEstado
     */
    public function setBoxEstado($boxEstado)
    {
        $this->boxEstado = $boxEstado;
    }

    /**
     * @return string
     */
    public function getBoxSonido()
    {
        return $this->boxSonido;
    }

    /**
     * @param string $boxSonido
     */
    public function setBoxSonido($boxSonido)
    {
        $this->boxSonido = $boxSonido;
    }

    /**
     * @return int
     */
    public function getBoxUsuario()
    {
        return $this->boxUsuario;
    }

    /**
     * @param int $boxUsuario
     */
    public function setBoxUsuario($boxUsuario)
    {
        $this->boxUsuario = $boxUsuario;
    }

    /**
     * @return \DateTime
     */
    public function getBoxFecha()
    {
        return $this->boxFecha;
    }

    /**
     * @param \DateTime $boxFecha
     */
    public function setBoxFecha($boxFecha)
    {
        $this->boxFecha = $boxFecha;
    }

    /**
     * @return int
     */
    public function getBoxTiempo()
    {
        return $this->boxTiempo;
    }

    /**
     * @param int $boxTiempo
     */
    public function setBoxTiempo($boxTiempo)
    {
        $this->boxTiempo = $boxTiempo;
    }


}

