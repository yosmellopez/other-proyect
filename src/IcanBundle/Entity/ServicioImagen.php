<?php

namespace IcanBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ServicioImagen
 *
 * @ORM\Table(name="servicio_imagen")
 * @ORM\Entity(repositoryClass="IcanBundle\Entity\ServicioImagenRepository")
 */
class ServicioImagen
{

    /**
     * @var integer
     *
     * @ORM\Column(name="imagen_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $imagenId;

    /**
     * @var string
     *
     * @ORM\Column(name="imagen", type="string", length=255, nullable=false)
     */
    private $imagen;

    /**
     * @var \Servicio
     *
     * @ORM\ManyToOne(targetEntity="Servicio")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="servicio_id", referencedColumnName="servicio_id")
     * })
     */
    private $servicio;


    /**
     * Get imagenId
     *
     * @return integer
     */
    public function getImagenId()
    {
        return $this->imagenId;
    }

    /**
     * Set imagen
     *
     * @param string $imagen
     * @return Servicio
     */
    public function setImagen($imagen)
    {
        $this->imagen = $imagen;

        return $this;
    }

    /**
     * Get imagen
     *
     * @return string
     */
    public function getImagen()
    {
        return $this->imagen;
    }

    /**
     * Set servicio
     *
     * @param \IcanBundle\Entity\Servicio $servicio
     * @return Permisousuario
     */
    public function setServicio(\IcanBundle\Entity\Servicio $servicio = null)
    {
        $this->servicio = $servicio;

        return $this;
    }

    /**
     * Get servicio
     *
     * @return \IcanBundle\Entity\Servicio
     */
    public function getServicio()
    {
        return $this->servicio;
    }

}