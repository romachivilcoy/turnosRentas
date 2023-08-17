<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UsuariosTramites
 *
 * @ORM\Table(name="Usuarios_tramites", indexes={@ORM\Index(name="IDX_8CAC57B5BF922C73", columns={"us_tr_tramite"})})
 * @ORM\Entity
 */
class UsuariosTramites
{
    /**
     * @var integer
     *
     * @ORM\Column(name="us_tr_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $usTrId;

    /**
     * @var integer
     *
     * @ORM\Column(name="us_tr_usuario", type="integer", nullable=true)
     */
    private $usTrUsuario;

    /**
     * @var \AppBundle\Entity\Tramites
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Tramites")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="us_tr_tramite", referencedColumnName="tra_id_tramite")
     * })
     */
    private $usTrTramite;

    /**
     * @return int
     */
    public function getUsTrId()
    {
        return $this->usTrId;
    }

    /**
     * @param int $usTrId
     */
    public function setUsTrId($usTrId)
    {
        $this->usTrId = $usTrId;
    }

    /**
     * @return int
     */
    public function getUsTrUsuario()
    {
        return $this->usTrUsuario;
    }

    /**
     * @param int $usTrUsuario
     */
    public function setUsTrUsuario($usTrUsuario)
    {
        $this->usTrUsuario = $usTrUsuario;
    }

    /**
     * @return Tramites
     */
    public function getUsTrTramite()
    {
        return $this->usTrTramite;
    }

    /**
     * @param Tramites $usTrTramite
     */
    public function setUsTrTramite($usTrTramite)
    {
        $this->usTrTramite = $usTrTramite;
    }


}

