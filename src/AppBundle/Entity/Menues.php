<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Menues
 *
 * @ORM\Table(name="Menues")
 * @ORM\Entity
 */
class Menues
{
    /**
     * @var integer
     *
     * @ORM\Column(name="mn_id_menu", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $mnIdMenu;

    /**
     * @var string
     *
     * @ORM\Column(name="mn_descripcion", type="string", length=50, nullable=false)
     */
    private $mnDescripcion;

    /**
     * @var string
     *
     * @ORM\Column(name="mn_vigente", type="string", length=1, nullable=false)
     */
    private $mnVigente;

    /**
     * @var integer
     *
     * @ORM\Column(name="mn_orden", type="integer", nullable=true)
     */
    private $mnOrden;

    /**
     * @return int
     */
    public function getMnIdMenu()
    {
        return $this->mnIdMenu;
    }

    /**
     * @param int $mnIdMenu
     */
    public function setMnIdMenu($mnIdMenu)
    {
        $this->mnIdMenu = $mnIdMenu;
    }

    /**
     * @return string
     */
    public function getMnDescripcion()
    {
        return $this->mnDescripcion;
    }

    /**
     * @param string $mnDescripcion
     */
    public function setMnDescripcion($mnDescripcion)
    {
        $this->mnDescripcion = $mnDescripcion;
    }

    /**
     * @return string
     */
    public function getMnVigente()
    {
        return $this->mnVigente;
    }

    /**
     * @param string $mnVigente
     */
    public function setMnVigente($mnVigente)
    {
        $this->mnVigente = $mnVigente;
    }

    /**
     * @return int
     */
    public function getMnOrden()
    {
        return $this->mnOrden;
    }

    /**
     * @param int $mnOrden
     */
    public function setMnOrden($mnOrden)
    {
        $this->mnOrden = $mnOrden;
    }


}

