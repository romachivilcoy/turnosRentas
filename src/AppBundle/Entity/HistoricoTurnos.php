<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HistoricoTurnos
 *
 * @ORM\Table(name="Historico_turnos")
 * @ORM\Entity
 */
class HistoricoTurnos
{
    /**
     * @var integer
     *
     * @ORM\Column(name="ht_id_historico", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $htIdHistorico;

    /**
     * @var integer
     *
     * @ORM\Column(name="ht_id_turno", type="integer", nullable=false)
     */
    private $htIdTurno;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="ht_fecha_turno", type="datetime", nullable=true)
     */
    private $htFechaTurno;

    /**
     * @var integer
     *
     * @ORM\Column(name="ht_nro_doc", type="integer", nullable=true)
     */
    private $htNroDoc;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="ht_fecha_origen", type="datetime", nullable=true)
     */
    private $htFechaOrigen;

    /**
     * @var integer
     *
     * @ORM\Column(name="ht_estado_turno", type="integer", nullable=true)
     */
    private $htEstadoTurno;

    /**
     * @var integer
     *
     * @ORM\Column(name="ht_box_atencion", type="integer", nullable=true)
     */
    private $htBoxAtencion;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="ht_box_fechahora", type="datetime", nullable=true)
     */
    private $htBoxFechahora;

    /**
     * @var integer
     *
     * @ORM\Column(name="ht_tipo_tramite", type="integer", nullable=true)
     */
    private $htTipoTramite;

    /**
     * @var integer
     *
     * @ORM\Column(name="ht_usuario_box", type="integer", nullable=true)
     */
    private $htUsuarioBox;

    /**
     * @var string
     *
     * @ORM\Column(name="ht_ticket", type="string", length=3, nullable=true)
     */
    private $htTicket;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="ht_fecha_presente", type="datetime", nullable=true)
     */
    private $htFechaPresente;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="ht_fecha_cancelado", type="datetime", nullable=true)
     */
    private $htFechaCancelado;

    /**
     * @var integer
     *
     * @ORM\Column(name="ht_usuario_cancela", type="integer", nullable=true)
     */
    private $htUsuarioCancela;

    /**
     * @var integer
     *
     * @ORM\Column(name="ht_motivo", type="integer", nullable=true)
     */
    private $htMotivo;

    /**
     * @return int
     */
    public function getHtIdHistorico()
    {
        return $this->htIdHistorico;
    }

    /**
     * @param int $htIdHistorico
     */
    public function setHtIdHistorico($htIdHistorico)
    {
        $this->htIdHistorico = $htIdHistorico;
    }

    /**
     * @return int
     */
    public function getHtIdTurno()
    {
        return $this->htIdTurno;
    }

    /**
     * @param int $htIdTurno
     */
    public function setHtIdTurno($htIdTurno)
    {
        $this->htIdTurno = $htIdTurno;
    }

    /**
     * @return \DateTime
     */
    public function getHtFechaTurno()
    {
        return $this->htFechaTurno;
    }

    /**
     * @param \DateTime $htFechaTurno
     */
    public function setHtFechaTurno($htFechaTurno)
    {
        $this->htFechaTurno = $htFechaTurno;
    }

    /**
     * @return int
     */
    public function getHtNroDoc()
    {
        return $this->htNroDoc;
    }

    /**
     * @param int $htNroDoc
     */
    public function setHtNroDoc($htNroDoc)
    {
        $this->htNroDoc = $htNroDoc;
    }

    /**
     * @return \DateTime
     */
    public function getHtFechaOrigen()
    {
        return $this->htFechaOrigen;
    }

    /**
     * @param \DateTime $htFechaOrigen
     */
    public function setHtFechaOrigen($htFechaOrigen)
    {
        $this->htFechaOrigen = $htFechaOrigen;
    }

    /**
     * @return int
     */
    public function getHtEstadoTurno()
    {
        return $this->htEstadoTurno;
    }

    /**
     * @param int $htEstadoTurno
     */
    public function setHtEstadoTurno($htEstadoTurno)
    {
        $this->htEstadoTurno = $htEstadoTurno;
    }

    /**
     * @return int
     */
    public function getHtBoxAtencion()
    {
        return $this->htBoxAtencion;
    }

    /**
     * @param int $htBoxAtencion
     */
    public function setHtBoxAtencion($htBoxAtencion)
    {
        $this->htBoxAtencion = $htBoxAtencion;
    }

    /**
     * @return \DateTime
     */
    public function getHtBoxFechahora()
    {
        return $this->htBoxFechahora;
    }

    /**
     * @param \DateTime $htBoxFechahora
     */
    public function setHtBoxFechahora($htBoxFechahora)
    {
        $this->htBoxFechahora = $htBoxFechahora;
    }

    /**
     * @return int
     */
    public function getHtTipoTramite()
    {
        return $this->htTipoTramite;
    }

    /**
     * @param int $htTipoTramite
     */
    public function setHtTipoTramite($htTipoTramite)
    {
        $this->htTipoTramite = $htTipoTramite;
    }

    /**
     * @return int
     */
    public function getHtUsuarioBox()
    {
        return $this->htUsuarioBox;
    }

    /**
     * @param int $htUsuarioBox
     */
    public function setHtUsuarioBox($htUsuarioBox)
    {
        $this->htUsuarioBox = $htUsuarioBox;
    }

    /**
     * @return string
     */
    public function getHtTicket()
    {
        return $this->htTicket;
    }

    /**
     * @param string $htTicket
     */
    public function setHtTicket($htTicket)
    {
        $this->htTicket = $htTicket;
    }

    /**
     * @return \DateTime
     */
    public function getHtFechaPresente()
    {
        return $this->htFechaPresente;
    }

    /**
     * @param \DateTime $htFechaPresente
     */
    public function setHtFechaPresente($htFechaPresente)
    {
        $this->htFechaPresente = $htFechaPresente;
    }

    /**
     * @return \DateTime
     */
    public function getHtFechaCancelado()
    {
        return $this->htFechaCancelado;
    }

    /**
     * @param \DateTime $htFechaCancelado
     */
    public function setHtFechaCancelado($htFechaCancelado)
    {
        $this->htFechaCancelado = $htFechaCancelado;
    }

    /**
     * @return int
     */
    public function getHtUsuarioCancela()
    {
        return $this->htUsuarioCancela;
    }

    /**
     * @param int $htUsuarioCancela
     */
    public function setHtUsuarioCancela($htUsuarioCancela)
    {
        $this->htUsuarioCancela = $htUsuarioCancela;
    }

    /**
     * @return int
     */
    public function getHtMotivo()
    {
        return $this->htMotivo;
    }

    /**
     * @param int $htMotivo
     */
    public function setHtMotivo($htMotivo)
    {
        $this->htMotivo = $htMotivo;
    }


}

