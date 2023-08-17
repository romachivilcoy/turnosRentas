<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tareas
 *
 * @ORM\Table(name="Tareas")
 * @ORM\Entity
 */
class Tareas
{
    /**
     * @var integer
     *
     * @ORM\Column(name="ta_id_tarea", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $taIdTarea;

    /**
     * @var integer
     *
     * @ORM\Column(name="ta_id_menue", type="integer", nullable=false)
     */
    private $taIdMenue;

    /**
     * @var integer
     *
     * @ORM\Column(name="ta_orden", type="integer", nullable=true)
     */
    private $taOrden;

    /**
     * @var string
     *
     * @ORM\Column(name="ta_descripcion", type="string", length=50, nullable=false)
     */
    private $taDescripcion;

    /**
     * @var string
     *
     * @ORM\Column(name="ta_vigente", type="string", length=1, nullable=false)
     */
    private $taVigente;

    /**
     * @var string
     *
     * @ORM\Column(name="ta_url", type="string", length=150, nullable=true)
     */
    private $taUrl;

    /**
     * @return int
     */
    public function getTaIdTarea()
    {
        return $this->taIdTarea;
    }

    /**
     * @param int $taIdTarea
     */
    public function setTaIdTarea($taIdTarea)
    {
        $this->taIdTarea = $taIdTarea;
    }

    /**
     * @return int
     */
    public function getTaIdMenue()
    {
        return $this->taIdMenue;
    }

    /**
     * @param int $taIdMenue
     */
    public function setTaIdMenue($taIdMenue)
    {
        $this->taIdMenue = $taIdMenue;
    }

    /**
     * @return int
     */
    public function getTaOrden()
    {
        return $this->taOrden;
    }

    /**
     * @param int $taOrden
     */
    public function setTaOrden($taOrden)
    {
        $this->taOrden = $taOrden;
    }

    /**
     * @return string
     */
    public function getTaDescripcion()
    {
        return $this->taDescripcion;
    }

    /**
     * @param string $taDescripcion
     */
    public function setTaDescripcion($taDescripcion)
    {
        $this->taDescripcion = $taDescripcion;
    }

    /**
     * @return string
     */
    public function getTaVigente()
    {
        return $this->taVigente;
    }

    /**
     * @param string $taVigente
     */
    public function setTaVigente($taVigente)
    {
        $this->taVigente = $taVigente;
    }

    /**
     * @return string
     */
    public function getTaUrl()
    {
        return $this->taUrl;
    }

    /**
     * @param string $taUrl
     */
    public function setTaUrl($taUrl)
    {
        $this->taUrl = $taUrl;
    }


}

