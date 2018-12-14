<?php

namespace IcanBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CategoriaProducto
 *
 * @ORM\Table(name="categoria_producto")
 * @ORM\Entity(repositoryClass="IcanBundle\Entity\CategoriaProductoRepository")
 */
class CategoriaProducto
{
    /**
     * @var integer
     *
     * @ORM\Column(name="categoria_producto_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $categoriaProductoId;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255, nullable=true)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="titulo", type="string", length=255, nullable=true)
     */
    private $titulo;

    /**
     * @var \CategoriaProducto
     *
     * @ORM\ManyToOne(targetEntity="CategoriaProducto")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="categoria_padre_id", referencedColumnName="categoria_producto_id")
     * })
     */
    private $categoriaPadre;

    /**
     * @return int
     */
    public function getCategoriaProductoId() {
        return $this->categoriaProductoId;
    }

    /**
     * @param int $categoriaProductoId
     */
    public function setCategoriaProductoId($categoriaProductoId) {
        $this->categoriaProductoId = $categoriaProductoId;
    }

    /**
     * @return string
     */
    public function getNombre() {
        return $this->nombre;
    }

    /**
     * @param string $nombre
     */
    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    /**
     * @return string
     */
    public function getTitulo() {
        return $this->titulo;
    }

    /**
     * @param string $titulo
     */
    public function setTitulo($titulo) {
        $this->titulo = $titulo;
    }

    /**
     * @return \CategoriaProducto
     */
    public function getCategoriaPadre() {
        return $this->categoriaPadre;
    }

    /**
     * @param \CategoriaProducto $categoriaPadre
     */
    public function setCategoriaPadre(CategoriaProducto $categoriaPadre) {
        $this->categoriaPadre = $categoriaPadre;
    }

}