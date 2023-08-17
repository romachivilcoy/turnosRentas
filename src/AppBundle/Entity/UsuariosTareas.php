<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UsuariosTareas
 *
 * @ORM\Table(name="Usuarios_tareas")
 * @ORM\Entity
 */
class UsuariosTareas
{
    /**
     * @var integer
     *
     * @ORM\Column(name="ut_id_usuario", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $utIdUsuario;

    /**
     * @var integer
     *
     * @ORM\Column(name="ut_id_tarea", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $utIdTarea;

    /**
     * @var string
     *
     * @ORM\Column(name="ut_acceso", type="string", length=1, nullable=true)
     */
    private $utAcceso;

    /**
     * @var string
     *
     * @ORM\Column(name="ut_alta", type="string", length=1, nullable=true)
     */
    private $utAlta;

    /**
     * @var string
     *
     * @ORM\Column(name="ut_baja", type="string", length=1, nullable=true)
     */
    private $utBaja;

    /**
     * @var string
     *
     * @ORM\Column(name="ut_modificacion", type="string", length=1, nullable=true)
     */
    private $utModificacion;

    /**
     * @var string
     *
     * @ORM\Column(name="ut_vigente", type="string", length=1, nullable=true)
     */
    private $utVigente;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="ut_fecha_ult_modi", type="datetime", nullable=true)
     */
    private $utFechaUltModi;

    /**
     * @var string
     *
     * @ORM\Column(name="ut_usuario_modi", type="string", length=20, nullable=true)
     */
    private $utUsuarioModi;

    /**
     * @return int
     */
    public function getUtIdUsuario()
    {
        return $this->utIdUsuario;
    }

    /**
     * @param int $utIdUsuario
     */
    public function setUtIdUsuario($utIdUsuario)
    {
        $this->utIdUsuario = $utIdUsuario;
    }

    /**
     * @return int
     */
    public function getUtIdTarea()
    {
        return $this->utIdTarea;
    }

    /**
     * @param int $utIdTarea
     */
    public function setUtIdTarea($utIdTarea)
    {
        $this->utIdTarea = $utIdTarea;
    }

    /**
     * @return string
     */
    public function getUtAcceso()
    {
        return $this->utAcceso;
    }

    /**
     * @param string $utAcceso
     */
    public function setUtAcceso($utAcceso)
    {
        $this->utAcceso = $utAcceso;
    }

    /**
     * @return string
     */
    public function getUtAlta()
    {
        return $this->utAlta;
    }

    /**
     * @param string $utAlta
     */
    public function setUtAlta($utAlta)
    {
        $this->utAlta = $utAlta;
    }

    /**
     * @return string
     */
    public function getUtBaja()
    {
        return $this->utBaja;
    }

    /**
     * @param string $utBaja
     */
    public function setUtBaja($utBaja)
    {
        $this->utBaja = $utBaja;
    }

    /**
     * @return string
     */
    public function getUtModificacion()
    {
        return $this->utModificacion;
    }

    /**
     * @param string $utModificacion
     */
    public function setUtModificacion($utModificacion)
    {
        $this->utModificacion = $utModificacion;
    }

    /**
     * @return string
     */
    public function getUtVigente()
    {
        return $this->utVigente;
    }

    /**
     * @param string $utVigente
     */
    public function setUtVigente($utVigente)
    {
        $this->utVigente = $utVigente;
    }

    /**
     * @return \DateTime
     */
    public function getUtFechaUltModi()
    {
        return $this->utFechaUltModi;
    }

    /**
     * @param \DateTime $utFechaUltModi
     */
    public function setUtFechaUltModi($utFechaUltModi)
    {
        $this->utFechaUltModi = $utFechaUltModi;
    }

    /**
     * @return string
     */
    public function getUtUsuarioModi()
    {
        return $this->utUsuarioModi;
    }

    /**
     * @param string $utUsuarioModi
     */
    public function setUtUsuarioModi($utUsuarioModi)
    {
        $this->utUsuarioModi = $utUsuarioModi;
    }


}

