<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Turnos
 *
 * @ORM\Table(name="Turnos")
 * @ORM\Entity
 */
class Turnos
{
    /**
     * @var integer
     *
     * @ORM\Column(name="tr_id_turno", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $trIdTurno;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="tr_fechahora_turno", type="datetime", nullable=true)
     */
    private $trFechahoraTurno;

    /**
     * @var integer
     *
     * @ORM\Column(name="tr_nro_doc", type="integer", nullable=true)
     */
    private $trNroDoc;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="tr_fechahora_origen", type="datetime", nullable=true)
     */
    private $trFechahoraOrigen;

    /**
     * @var integer
     *
     * @ORM\Column(name="tr_estado_turno", type="integer", nullable=true)
     */
    private $trEstadoTurno;

    /**
     * @var integer
     *
     * @ORM\Column(name="tr_box_atencion", type="integer", nullable=true)
     */
    private $trBoxAtencion;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="tr_fechahora_box", type="datetime", nullable=true)
     */
    private $trFechahoraBox;

    /**
     * @var integer
     *
     * @ORM\Column(name="tr_tipo_tramite", type="integer", nullable=true)
     */
    private $trTipoTramite;

    /**
     * @var integer
     *
     * @ORM\Column(name="tr_usuario_box", type="integer", nullable=true)
     */
    private $trUsuarioBox;

    /**
     * @var string
     *
     * @ORM\Column(name="tr_ticket", type="string", length=3, nullable=true)
     */
    private $trTicket;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="tr_fechahora_presente", type="datetime", nullable=true)
     */
    private $trFechahoraPresente;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="tr_fechahora_cancelado", type="datetime", nullable=true)
     */
    private $trFechahoraCancelado;

    /**
     * @var integer
     *
     * @ORM\Column(name="tr_usuario_cancela", type="integer", nullable=true)
     */
    private $trUsuarioCancela;

    /**
     * @var integer
     *
     * @ORM\Column(name="tr_motivo", type="integer", nullable=true)
     */
    private $trMotivo;

    /**
     * @var integer
     *
     * @ORM\Column(name="tr_box_retorno", type="integer", nullable=true)
     */
    private $trBoxRetorno;

    /**
     * @return int
     */
    public function getTrIdTurno()
    {
        return $this->trIdTurno;
    }

    /**
     * @param int $trIdTurno
     */
    public function setTrIdTurno($trIdTurno)
    {
        $this->trIdTurno = $trIdTurno;
    }

    /**
     * @return \DateTime
     */
    public function getTrFechahoraTurno()
    {
        return $this->trFechahoraTurno;
    }

    /**
     * @param \DateTime $trFechahoraTurno
     */
    public function setTrFechahoraTurno($trFechahoraTurno)
    {
        $this->trFechahoraTurno = $trFechahoraTurno;
    }

    /**
     * @return int
     */
    public function getTrNroDoc()
    {
        return $this->trNroDoc;
    }

    /**
     * @param int $trNroDoc
     */
    public function setTrNroDoc($trNroDoc)
    {
        $this->trNroDoc = $trNroDoc;
    }

    /**
     * @return \DateTime
     */
    public function getTrFechahoraOrigen()
    {
        return $this->trFechahoraOrigen;
    }

    /**
     * @param \DateTime $trFechahoraOrigen
     */
    public function setTrFechahoraOrigen($trFechahoraOrigen)
    {
        $this->trFechahoraOrigen = $trFechahoraOrigen;
    }

    /**
     * @return int
     */
    public function getTrEstadoTurno()
    {
        return $this->trEstadoTurno;
    }

    /**
     * @param int $trEstadoTurno
     */
    public function setTrEstadoTurno($trEstadoTurno)
    {
        $this->trEstadoTurno = $trEstadoTurno;
    }

    /**
     * @return int
     */
    public function getTrBoxAtencion()
    {
        return $this->trBoxAtencion;
    }

    /**
     * @param int $trBoxAtencion
     */
    public function setTrBoxAtencion($trBoxAtencion)
    {
        $this->trBoxAtencion = $trBoxAtencion;
    }

    /**
     * @return \DateTime
     */
    public function getTrFechahoraBox()
    {
        return $this->trFechahoraBox;
    }

    /**
     * @param \DateTime $trFechahoraBox
     */
    public function setTrFechahoraBox($trFechahoraBox)
    {
        $this->trFechahoraBox = $trFechahoraBox;
    }

    /**
     * @return int
     */
    public function getTrTipoTramite()
    {
        return $this->trTipoTramite;
    }

    /**
     * @param int $trTipoTramite
     */
    public function setTrTipoTramite($trTipoTramite)
    {
        $this->trTipoTramite = $trTipoTramite;
    }

    /**
     * @return int
     */
    public function getTrUsuarioBox()
    {
        return $this->trUsuarioBox;
    }

    /**
     * @param int $trUsuarioBox
     */
    public function setTrUsuarioBox($trUsuarioBox)
    {
        $this->trUsuarioBox = $trUsuarioBox;
    }

    /**
     * @return string
     */
    public function getTrTicket()
    {
        return $this->trTicket;
    }

    /**
     * @param string $trTicket
     */
    public function setTrTicket($trTicket)
    {
        $this->trTicket = $trTicket;
    }

    /**
     * @return \DateTime
     */
    public function getTrFechahoraPresente()
    {
        return $this->trFechahoraPresente;
    }

    /**
     * @param \DateTime $trFechahoraPresente
     */
    public function setTrFechahoraPresente($trFechahoraPresente)
    {
        $this->trFechahoraPresente = $trFechahoraPresente;
    }

    /**
     * @return \DateTime
     */
    public function getTrFechahoraCancelado()
    {
        return $this->trFechahoraCancelado;
    }

    /**
     * @param \DateTime $trFechahoraCancelado
     */
    public function setTrFechahoraCancelado($trFechahoraCancelado)
    {
        $this->trFechahoraCancelado = $trFechahoraCancelado;
    }

    /**
     * @return int
     */
    public function getTrUsuarioCancela()
    {
        return $this->trUsuarioCancela;
    }

    /**
     * @param int $trUsuarioCancela
     */
    public function setTrUsuarioCancela($trUsuarioCancela)
    {
        $this->trUsuarioCancela = $trUsuarioCancela;
    }

    /**
     * @return int
     */
    public function getTrMotivo()
    {
        return $this->trMotivo;
    }

    /**
     * @param int $trMotivo
     */
    public function setTrMotivo($trMotivo)
    {
        $this->trMotivo = $trMotivo;
    }

    /**
     * @return int
     */
    public function getTrBoxRetorno()
    {
        return $this->trBoxRetorno;
    }

    /**
     * @param int $trBoxRetorno
     */
    public function setTrBoxRetorno($trBoxRetorno)
    {
        $this->trBoxRetorno = $trBoxRetorno;
    }


}

