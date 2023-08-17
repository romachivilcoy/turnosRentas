<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Usuarios
 *
 * @ORM\Table(name="Usuarios")
 * @ORM\Entity
 */
class Usuarios
{
    /**
     * @var integer
     *
     * @ORM\Column(name="us_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $usId;

    /**
     * @var string
     *
     * @ORM\Column(name="us_nombre", type="string", length=255, nullable=true)
     */
    private $usNombre;

    /**
     * @var string
     *
     * @ORM\Column(name="us_usuario", type="string", length=21, nullable=true)
     */
    private $usUsuario;

    /**
     * @var string
     *
     * @ORM\Column(name="us_clave", type="string", length=20, nullable=true)
     */
    private $usClave;

    /**
     * @var integer
     *
     * @ORM\Column(name="us_nivel", type="integer", nullable=true)
     */
    private $usNivel;

    /**
     * @var string
     *
     * @ORM\Column(name="us_mail", type="string", length=200, nullable=true)
     */
    private $usMail;

    /**
     * @var string
     *
     * @ORM\Column(name="us_mail2", type="string", length=200, nullable=true)
     */
    private $usMail2;

    /**
     * @var integer
     *
     * @ORM\Column(name="us_bloqueo", type="integer", nullable=false)
     */
    private $usBloqueo = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="us_intentos", type="integer", nullable=true)
     */
    private $usIntentos;

    /**
     * @var string
     *
     * @ORM\Column(name="us_agenda", type="string", length=1, nullable=true)
     */
    private $usAgenda;

    /**
     * @var string
     *
     * @ORM\Column(name="us_celular", type="string", length=25, nullable=true)
     */
    private $usCelular;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="us_ultimo_login", type="datetime", nullable=true)
     */
    private $usUltimoLogin;

    /**
     * @var string
     *
     * @ORM\Column(name="us_tipo", type="string", length=1, nullable=true)
     */
    private $usTipo;

    /**
     * @var integer
     *
     * @ORM\Column(name="us_acceso", type="integer", nullable=true)
     */
    private $usAcceso;

    /**
     * @var integer
     *
     * @ORM\Column(name="us_alta", type="integer", nullable=true)
     */
    private $usAlta;

    /**
     * @var integer
     *
     * @ORM\Column(name="us_baja", type="integer", nullable=true)
     */
    private $usBaja;

    /**
     * @var integer
     *
     * @ORM\Column(name="us_modificacion", type="integer", nullable=true)
     */
    private $usModificacion;

    /**
     * @var string
     *
     * @ORM\Column(name="us_rafam", type="string", length=50, nullable=true)
     */
    private $usRafam;

    /**
     * @var integer
     *
     * @ORM\Column(name="us_administrador", type="integer", nullable=true)
     */
    private $usAdministrador;

    /**
     * @return int
     */
    public function getUsId()
    {
        return $this->usId;
    }

    /**
     * @param int $usId
     */
    public function setUsId($usId)
    {
        $this->usId = $usId;
    }

    /**
     * @return string
     */
    public function getUsNombre()
    {
        return $this->usNombre;
    }

    /**
     * @param string $usNombre
     */
    public function setUsNombre($usNombre)
    {
        $this->usNombre = $usNombre;
    }

    /**
     * @return string
     */
    public function getUsUsuario()
    {
        return $this->usUsuario;
    }

    /**
     * @param string $usUsuario
     */
    public function setUsUsuario($usUsuario)
    {
        $this->usUsuario = $usUsuario;
    }

    /**
     * @return string
     */
    public function getUsClave()
    {
        return $this->usClave;
    }

    /**
     * @param string $usClave
     */
    public function setUsClave($usClave)
    {
        $this->usClave = $usClave;
    }

    /**
     * @return int
     */
    public function getUsNivel()
    {
        return $this->usNivel;
    }

    /**
     * @param int $usNivel
     */
    public function setUsNivel($usNivel)
    {
        $this->usNivel = $usNivel;
    }

    /**
     * @return string
     */
    public function getUsMail()
    {
        return $this->usMail;
    }

    /**
     * @param string $usMail
     */
    public function setUsMail($usMail)
    {
        $this->usMail = $usMail;
    }

    /**
     * @return string
     */
    public function getUsMail2()
    {
        return $this->usMail2;
    }

    /**
     * @param string $usMail2
     */
    public function setUsMail2($usMail2)
    {
        $this->usMail2 = $usMail2;
    }

    /**
     * @return int
     */
    public function getUsBloqueo()
    {
        return $this->usBloqueo;
    }

    /**
     * @param int $usBloqueo
     */
    public function setUsBloqueo($usBloqueo)
    {
        $this->usBloqueo = $usBloqueo;
    }

    /**
     * @return int
     */
    public function getUsIntentos()
    {
        return $this->usIntentos;
    }

    /**
     * @param int $usIntentos
     */
    public function setUsIntentos($usIntentos)
    {
        $this->usIntentos = $usIntentos;
    }

    /**
     * @return string
     */
    public function getUsAgenda()
    {
        return $this->usAgenda;
    }

    /**
     * @param string $usAgenda
     */
    public function setUsAgenda($usAgenda)
    {
        $this->usAgenda = $usAgenda;
    }

    /**
     * @return string
     */
    public function getUsCelular()
    {
        return $this->usCelular;
    }

    /**
     * @param string $usCelular
     */
    public function setUsCelular($usCelular)
    {
        $this->usCelular = $usCelular;
    }

    /**
     * @return \DateTime
     */
    public function getUsUltimoLogin()
    {
        return $this->usUltimoLogin;
    }

    /**
     * @param \DateTime $usUltimoLogin
     */
    public function setUsUltimoLogin($usUltimoLogin)
    {
        $this->usUltimoLogin = $usUltimoLogin;
    }

    /**
     * @return string
     */
    public function getUsTipo()
    {
        return $this->usTipo;
    }

    /**
     * @param string $usTipo
     */
    public function setUsTipo($usTipo)
    {
        $this->usTipo = $usTipo;
    }

    /**
     * @return int
     */
    public function getUsAcceso()
    {
        return $this->usAcceso;
    }

    /**
     * @param int $usAcceso
     */
    public function setUsAcceso($usAcceso)
    {
        $this->usAcceso = $usAcceso;
    }

    /**
     * @return int
     */
    public function getUsAlta()
    {
        return $this->usAlta;
    }

    /**
     * @param int $usAlta
     */
    public function setUsAlta($usAlta)
    {
        $this->usAlta = $usAlta;
    }

    /**
     * @return int
     */
    public function getUsBaja()
    {
        return $this->usBaja;
    }

    /**
     * @param int $usBaja
     */
    public function setUsBaja($usBaja)
    {
        $this->usBaja = $usBaja;
    }

    /**
     * @return int
     */
    public function getUsModificacion()
    {
        return $this->usModificacion;
    }

    /**
     * @param int $usModificacion
     */
    public function setUsModificacion($usModificacion)
    {
        $this->usModificacion = $usModificacion;
    }

    /**
     * @return string
     */
    public function getUsRafam()
    {
        return $this->usRafam;
    }

    /**
     * @param string $usRafam
     */
    public function setUsRafam($usRafam)
    {
        $this->usRafam = $usRafam;
    }

    /**
     * @return int
     */
    public function getUsAdministrador()
    {
        return $this->usAdministrador;
    }

    /**
     * @param int $usAdministrador
     */
    public function setUsAdministrador($usAdministrador)
    {
        $this->usAdministrador = $usAdministrador;
    }

}

