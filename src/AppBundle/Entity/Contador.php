<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Contador
 *
 * @ORM\Table(name="Contador")
 * @ORM\Entity
 */
class Contador
{
    /**
     * @var integer
     *
     * @ORM\Column(name="contador_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $contadorId;

    /**
     * @var integer
     *
     * @ORM\Column(name="contador_numero", type="integer", nullable=true)
     */
    private $contadorNumero;

    /**
     * @var integer
     *
     * @ORM\Column(name="contador_bandera", type="integer", nullable=true)
     */
    private $contadorBandera;

    /**
     * @var integer
     *
     * @ORM\Column(name="contador_aviso", type="integer", nullable=true)
     */
    private $contadorAviso = '0';

    /**
     * @return int
     */
    public function getContadorId()
    {
        return $this->contadorId;
    }

    /**
     * @param int $contadorId
     */
    public function setContadorId($contadorId)
    {
        $this->contadorId = $contadorId;
    }

    /**
     * @return int
     */
    public function getContadorNumero()
    {
        return $this->contadorNumero;
    }

    /**
     * @param int $contadorNumero
     */
    public function setContadorNumero($contadorNumero)
    {
        $this->contadorNumero = $contadorNumero;
    }

    /**
     * @return int
     */
    public function getContadorBandera()
    {
        return $this->contadorBandera;
    }

    /**
     * @param int $contadorBandera
     */
    public function setContadorBandera($contadorBandera)
    {
        $this->contadorBandera = $contadorBandera;
    }

    /**
     * @return int
     */
    public function getContadorAviso()
    {
        return $this->contadorAviso;
    }

    /**
     * @param int $contadorAviso
     */
    public function setContadorAviso($contadorAviso)
    {
        $this->contadorAviso = $contadorAviso;
    }


}

