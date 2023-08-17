<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Estados
 *
 * @ORM\Table(name="Estados")
 * @ORM\Entity
 */
class Estados
{
    /**
     * @var integer
     *
     * @ORM\Column(name="et_id_estado", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $etIdEstado;

    /**
     * @var string
     *
     * @ORM\Column(name="et_tipo_estado", type="string", length=20, nullable=true)
     */
    private $etTipoEstado;

    /**
     * @return int
     */
    public function getEtIdEstado()
    {
        return $this->etIdEstado;
    }

    /**
     * @param int $etIdEstado
     */
    public function setEtIdEstado($etIdEstado)
    {
        $this->etIdEstado = $etIdEstado;
    }

    /**
     * @return string
     */
    public function getEtTipoEstado()
    {
        return $this->etTipoEstado;
    }

    /**
     * @param string $etTipoEstado
     */
    public function setEtTipoEstado($etTipoEstado)
    {
        $this->etTipoEstado = $etTipoEstado;
    }


}

