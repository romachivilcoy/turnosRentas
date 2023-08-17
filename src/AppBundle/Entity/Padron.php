<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Padron
 *
 * @ORM\Table(name="PADRON")
 * @ORM\Entity
 */
class Padron
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_padron", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $idPadron;

    /**
     * @var integer
     *
     * @ORM\Column(name="PDOC", type="integer", nullable=false)
     */
    private $pdoc;

    /**
     * @var string
     *
     * @ORM\Column(name="APELLIDO_Y_NOMBRE", type="string", length=50, nullable=true)
     */
    private $apellidoYNombre;

    /**
     * @var string
     *
     * @ORM\Column(name="PDOM", type="string", length=50, nullable=true)
     */
    private $pdom;

    /**
     * @var string
     *
     * @ORM\Column(name="NombreCalle", type="string", length=50, nullable=true)
     */
    private $nombrecalle;

    /**
     * @var string
     *
     * @ORM\Column(name="NroCalle", type="string", length=6, nullable=true)
     */
    private $nrocalle;

    /**
     * @var string
     *
     * @ORM\Column(name="Aclaracion", type="string", length=40, nullable=true)
     */
    private $aclaracion;

    /**
     * @var string
     *
     * @ORM\Column(name="PSEXO", type="string", length=1, nullable=true)
     */
    private $psexo;

    /**
     * @var string
     *
     * @ORM\Column(name="Nacionalid", type="string", length=30, nullable=true)
     */
    private $nacionalid;

    /**
     * @var string
     *
     * @ORM\Column(name="EstadoCivi", type="string", length=12, nullable=true)
     */
    private $estadocivi;

    /**
     * @var string
     *
     * @ORM\Column(name="PPRO", type="string", length=30, nullable=true)
     */
    private $ppro;

    /**
     * @var string
     *
     * @ORM\Column(name="Telefono", type="string", length=20, nullable=true)
     */
    private $telefono;

    /**
     * @var string
     *
     * @ORM\Column(name="Celular", type="string", length=20, nullable=true)
     */
    private $celular;

    /**
     * @var string
     *
     * @ORM\Column(name="FEC_NAC", type="string", length=10, nullable=true)
     */
    private $fecNac;

    /**
     * @var string
     *
     * @ORM\Column(name="Mail", type="string", length=50, nullable=true)
     */
    private $mail;


    /**
     * @var string
     *
     * @ORM\Column(name="notificacion_email", type="string", length=1, nullable=true)
     */
    private $notificacionEmail;

    /**
     * @var string
     *
     * @ORM\Column(name="notificacion_sms", type="string", length=1, nullable=true)
     */
    private $notificacionSms;


    /**
     * Get idPadron
     *
     * @return integer
     */
    public function getIdPadron()
    {
        return $this->idPadron;
    }

    /**
     * Set pdoc
     *
     * @param integer $pdoc
     *
     * @return Padron
     */
    public function setPdoc($pdoc)
    {
        $this->pdoc = $pdoc;

        return $this;
    }

    /**
     * Get pdoc
     *
     * @return integer
     */
    public function getPdoc()
    {
        return $this->pdoc;
    }

    /**
     * Set apellidoYNombre
     *
     * @param string $apellidoYNombre
     *
     * @return Padron
     */
    public function setApellidoYNombre($apellidoYNombre)
    {
        $this->apellidoYNombre = $apellidoYNombre;

        return $this;
    }

    /**
     * Get apellidoYNombre
     *
     * @return string
     */
    public function getApellidoYNombre()
    {
        return $this->apellidoYNombre;
    }

    /**
     * Set pdom
     *
     * @param string $pdom
     *
     * @return Padron
     */
    public function setPdom($pdom)
    {
        $this->pdom = $pdom;

        return $this;
    }

    /**
     * Get pdom
     *
     * @return string
     */
    public function getPdom()
    {
        return $this->pdom;
    }

    /**
     * Set nombrecalle
     *
     * @param string $nombrecalle
     *
     * @return Padron
     */
    public function setNombrecalle($nombrecalle)
    {
        $this->nombrecalle = $nombrecalle;

        return $this;
    }

    /**
     * Get nombrecalle
     *
     * @return string
     */
    public function getNombrecalle()
    {
        return $this->nombrecalle;
    }

    /**
     * Set nrocalle
     *
     * @param string $nrocalle
     *
     * @return Padron
     */
    public function setNrocalle($nrocalle)
    {
        $this->nrocalle = $nrocalle;

        return $this;
    }

    /**
     * Get nrocalle
     *
     * @return string
     */
    public function getNrocalle()
    {
        return $this->nrocalle;
    }

    /**
     * Set aclaracion
     *
     * @param string $aclaracion
     *
     * @return Padron
     */
    public function setAclaracion($aclaracion)
    {
        $this->aclaracion = $aclaracion;

        return $this;
    }

    /**
     * Get aclaracion
     *
     * @return string
     */
    public function getAclaracion()
    {
        return $this->aclaracion;
    }

    /**
     * Set psexo
     *
     * @param string $psexo
     *
     * @return Padron
     */
    public function setPsexo($psexo)
    {
        $this->psexo = $psexo;

        return $this;
    }

    /**
     * Get psexo
     *
     * @return string
     */
    public function getPsexo()
    {
        return $this->psexo;
    }

    /**
     * Set nacionalid
     *
     * @param string $nacionalid
     *
     * @return Padron
     */
    public function setNacionalid($nacionalid)
    {
        $this->nacionalid = $nacionalid;

        return $this;
    }

    /**
     * Get nacionalid
     *
     * @return string
     */
    public function getNacionalid()
    {
        return $this->nacionalid;
    }

    /**
     * Set estadocivi
     *
     * @param string $estadocivi
     *
     * @return Padron
     */
    public function setEstadocivi($estadocivi)
    {
        $this->estadocivi = $estadocivi;

        return $this;
    }

    /**
     * Get estadocivi
     *
     * @return string
     */
    public function getEstadocivi()
    {
        return $this->estadocivi;
    }

    /**
     * Set ppro
     *
     * @param string $ppro
     *
     * @return Padron
     */
    public function setPpro($ppro)
    {
        $this->ppro = $ppro;

        return $this;
    }

    /**
     * Get ppro
     *
     * @return string
     */
    public function getPpro()
    {
        return $this->ppro;
    }

    /**
     * Set telefono
     *
     * @param string $telefono
     *
     * @return Padron
     */
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;

        return $this;
    }

    /**
     * Get telefono
     *
     * @return string
     */
    public function getTelefono()
    {
        return $this->telefono;
    }

    /**
     * Set celular
     *
     * @param string $celular
     *
     * @return Padron
     */
    public function setCelular($celular)
    {
        $this->celular = $celular;

        return $this;
    }

    /**
     * Get celular
     *
     * @return string
     */
    public function getCelular()
    {
        return $this->celular;
    }

    /**
     * Set fecNac
     *
     * @param string $fecNac
     *
     * @return Padron
     */
    public function setFecNac($fecNac)
    {
        $this->fecNac = $fecNac;

        return $this;
    }

    /**
     * Get fecNac
     *
     * @return string
     */
    public function getFecNac()
    {
        return $this->fecNac;
    }


    /**
     * Set mail
     *
     * @param string $mail
     *
     * @return Padron
     */
    public function setMail($mail)
    {
        $this->mail = $mail;

        return $this;
    }

    /**
     * Get mail
     *
     * @return string
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * Set notificacionEmail
     *
     * @param string $notificacionEmail
     *
     * @return Padron
     */
    public function setNotificacionEmail($notificacionEmail)
    {
        $this->notificacionEmail = $notificacionEmail;

        return $this;
    }

    /**
     * Get notificacionEmail
     *
     * @return string
     */
    public function getNotificacionEmail()
    {
        return $this->notificacionEmail;
    }

    /**
     * Set notificacionSms
     *
     * @param string $notificacionSms
     *
     * @return Padron
     */
    public function setNotificacionSms($notificacionSms)
    {
        $this->notificacionSms = $notificacionSms;

        return $this;
    }

    /**
     * Get notificacionSms
     *
     * @return string
     */
    public function getNotificacionSms()
    {
        return $this->notificacionSms;
    }
}
