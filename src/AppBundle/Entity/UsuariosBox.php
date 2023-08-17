<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Serializable;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * UsuariosBox
 *
 * @ORM\Table(name="Usuarios_box")
 * @ORM\Entity
 */
class UsuariosBox implements UserInterface, Serializable
{
    /**
     * @var integer
     *
     * @ORM\Column(name="usbx_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $usbxId;

    /**
     * @var integer
     *
     * @ORM\Column(name="usbx_idus", type="integer", nullable=true)
     */
    private $usbxIdus;

    /**
     * @var integer
     *
     * @ORM\Column(name="usbx_nrobox", type="integer", nullable=true)
     */
    private $usbxNrobox;

    /**
     * @var string
     *
     * @ORM\Column(name="usbx_vigente", type="string", length=1, nullable=true)
     */
    private $usbxVigente;

    /**
     * @var string
     *
     * @ORM\Column(name="usbx_default", type="string", length=1, nullable=true)
     */
    private $usbxDefault;

    /**
     * @var integer
     *
     * @ORM\Column(name="usbx_prioridad", type="integer", nullable=true)
     */
    private $usbxPrioridad;

    /**
     * @return int
     */
    public function getUsbxId()
    {
        return $this->usbxId;
    }

    /**
     * @param int $usbxId
     */
    public function setUsbxId($usbxId)
    {
        $this->usbxId = $usbxId;
    }

    /**
     * @return int
     */
    public function getUsbxIdus()
    {
        return $this->usbxIdus;
    }

    /**
     * @param int $usbxIdus
     */
    public function setUsbxIdus($usbxIdus)
    {
        $this->usbxIdus = $usbxIdus;
    }

    /**
     * @return int
     */
    public function getUsbxNrobox()
    {
        return $this->usbxNrobox;
    }

    /**
     * @param int $usbxNrobox
     */
    public function setUsbxNrobox($usbxNrobox)
    {
        $this->usbxNrobox = $usbxNrobox;
    }

    /**
     * @return string
     */
    public function getUsbxVigente()
    {
        return $this->usbxVigente;
    }

    /**
     * @param string $usbxVigente
     */
    public function setUsbxVigente($usbxVigente)
    {
        $this->usbxVigente = $usbxVigente;
    }

    /**
     * @return string
     */
    public function getUsbxDefault()
    {
        return $this->usbxDefault;
    }

    /**
     * @param string $usbxDefault
     */
    public function setUsbxDefault($usbxDefault)
    {
        $this->usbxDefault = $usbxDefault;
    }

    /**
     * @return int
     */
    public function getUsbxPrioridad()
    {
        return $this->usbxPrioridad;
    }

    /**
     * @param int $usbxPrioridad
     */
    public function setUsbxPrioridad($usbxPrioridad)
    {
        $this->usbxPrioridad = $usbxPrioridad;
    }



    /**
     * Returns the roles granted to the user.
     *
     * <code>
     * public function getRoles()
     * {
     *     return array('ROLE_USER');
     * }
     * </code>
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return (Role|string)[] The user roles
     */
    public function getRoles()
    {
        return array("ROLE_USER");
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->getUsbxIdus();
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    /**
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize()
    {
        return serialize(array(
            $this->usbxId,
            $this->usbxIdus,
        ));
    }

    /**
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     * @since 5.1.0
     */
    public function unserialize($serialized)
    {
        list (
            $this->usbxId,
            $this->usbxIdus,
            ) = unserialize($serialized);
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string The password
     */
    public function getPassword()
    {
        return $this->usbxIdus;
    }
}

