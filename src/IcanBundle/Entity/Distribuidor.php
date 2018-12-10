<?php

namespace IcanBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Marca
 *
 * @ORM\Table(name="distribuidor")
 * @ORM\Entity(repositoryClass="IcanBundle\Entity\DistribuidorRepository")
 */
class Distribuidor
{

    /**
     * @var integer
     *
     * @ORM\Column(name="distribuidor_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $distribuidorId;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255, nullable=false)
     */
    private $nombre;


    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=255, nullable=true)
     */
    private $descripcion;

    /**
     * @var string
     *
     * @ORM\Column(name="imagen", type="string", length=255, nullable=true)
     */
    private $direccion;

    /**
     * @var string
     *
     * @ORM\Column(name="telefono", type="string", length=12, nullable=true)
     */
    private $telefono;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @var boolean
     *
     * @ORM\Column(name="estado", type="boolean", nullable=true)
     */
    private $estado;

    /**
     * @var \Comuna
     *
     * @ORM\ManyToOne(targetEntity="Comuna")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="comuna_id", referencedColumnName="comuna_id")
     * })
     */
    private $comuna;

    /**
     * @return int
     */
    public function getDistribuidorId() {
        return $this->distribuidorId;
    }

    /**
     * @param int $distribuidorId
     */
    public function setDistribuidorId($distribuidorId) {
        $this->distribuidorId = $distribuidorId;
    }


    /**
     * Set nombre
     *
     * @param string $nombre
     * @return Marca
     */
    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getNombre() {
        return $this->nombre;
    }

    /**
     * Set titulo
     *
     * @param string $titulo
     * @return Marca
     */
    public function setTitulo($titulo) {
        $this->titulo = $titulo;
    }

    /**
     * Get titulo
     *
     * @return string
     */
    public function getTitulo() {
        return $this->titulo;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return Marca
     */
    public function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;
    }

    /**
     * Get descripcion
     *
     * @return string
     */
    public function getDescripcion() {
        return $this->descripcion;
    }

    /**
     * Set estado
     *
     * @param boolean $estado
     * @return Marca
     */
    public function setEstado($estado) {
        $this->estado = $estado;
    }

    /**
     * Get estado
     *
     * @return boolean
     */
    public function getEstado() {
        return $this->estado;
    }

    /**
     * @return string
     */
    public function getDireccion() {
        return $this->direccion;
    }

    /**
     * @param string $direccion
     */
    public function setDireccion($direccion) {
        $this->direccion = $direccion;
    }

    /**
     * @return string
     */
    public function getTelefono() {
        return $this->telefono;
    }

    /**
     * @param string $telefono
     */
    public function setTelefono($telefono) {
        $this->telefono = $telefono;
    }

    /**
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email) {
        $this->email = $email;
    }

    /**
     * @return \Comuna
     */
    public function getComuna() {
        return $this->comuna;
    }

    /**
     * @param \Comuna $comuna
     */
    public function setComuna($comuna) {
        $this->comuna = $comuna;
    }

}
