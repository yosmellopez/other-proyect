<?php

namespace IcanBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use IcanBundle\Entity\Producto;

/**
 * Producto
 *
 * @ORM\Table(name="seccion_producto")
 * @ORM\Entity(repositoryClass="IcanBundle\Entity\SeccionProductoRepository")
 */
class SeccionProducto
{

    /**
     * @var integer
     *
     * @ORM\Column(name="seccion_producto_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $seccionProductoId;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255, nullable=false)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="titulo", type="string", length=255, nullable=true)
     */
    private $titulo;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="text", nullable=false)
     */
    private $descripcion;

    /**
     * @var string
     *
     * @ORM\Column(name="tags", type="text", nullable=false)
     */
    private $tags;

    /**
     * @var string
     *
     * @ORM\Column(name="imagen", type="string", length=255, nullable=true)
     */
    private $imagen;

    /**
     * @var integer
     *
     * @ORM\Column(name="stock", type="integer", nullable=false)
     */
    private $stock;

    /**
     * @var integer
     *
     * @ORM\Column(name="precio", type="integer", nullable=false)
     */
    private $precio;

    /**
     * @var integer
     *
     * @ORM\Column(name="precio_oferta", type="integer", nullable=false)
     */
    private $precioOferta;

    /**
     * @var boolean
     *
     * @ORM\Column(name="mostrar_precio", type="boolean", nullable=true)
     */
    private $mostrarPrecio;

    /**
     * @var boolean
     *
     * @ORM\Column(name="estado", type="boolean", nullable=true)
     */
    private $estado;

    /**
     * @var string
     *
     * @ORM\Column(name="fechapublicacion", type="datetime", nullable=true)
     */
    private $fechapublicacion;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * @var integer
     *
     * @ORM\Column(name="views", type="integer", nullable=true)
     */
    private $views;

    /**
     * @var \Categoria
     *
     * @ORM\ManyToOne(targetEntity="Producto")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="producto_id", referencedColumnName="producto_id")
     * })
     */
    private $producto;

    /**
     * @return int
     */
    public function getSeccionProductoId() {
        return $this->seccionProductoId;
    }

    /**
     * @param int $seccionProductoId
     */
    public function setSeccionProductoId($seccionProductoId) {
        $this->seccionProductoId = $seccionProductoId;
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
     * @return string
     */
    public function getDescripcion() {
        return $this->descripcion;
    }

    /**
     * @param string $descripcion
     */
    public function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;
    }

    /**
     * @return string
     */
    public function getTags() {
        return $this->tags;
    }

    /**
     * @param string $tags
     */
    public function setTags($tags) {
        $this->tags = $tags;
    }

    /**
     * @return string
     */
    public function getImagen() {
        return $this->imagen;
    }

    /**
     * @param string $imagen
     */
    public function setImagen($imagen) {
        $this->imagen = $imagen;
    }

    /**
     * @return int
     */
    public function getStock() {
        return $this->stock;
    }

    /**
     * @param int $stock
     */
    public function setStock($stock) {
        $this->stock = $stock;
    }

    /**
     * @return int
     */
    public function getPrecio() {
        return $this->precio;
    }

    /**
     * @param int $precio
     */
    public function setPrecio($precio) {
        $this->precio = $precio;
    }

    /**
     * @return int
     */
    public function getPrecioOferta() {
        return $this->precioOferta;
    }

    /**
     * @param int $precioOferta
     */
    public function setPrecioOferta($precioOferta) {
        $this->precioOferta = $precioOferta;
    }

    /**
     * @return bool
     */
    public function isMostrarPrecio() {
        return $this->mostrarPrecio;
    }

    /**
     * @param bool $mostrarPrecio
     */
    public function setMostrarPrecio($mostrarPrecio) {
        $this->mostrarPrecio = $mostrarPrecio;
    }

    /**
     * @return bool
     */
    public function isEstado() {
        return $this->estado;
    }

    /**
     * @param bool $estado
     */
    public function setEstado($estado) {
        $this->estado = $estado;
    }

    /**
     * @return string
     */
    public function getFechapublicacion() {
        return $this->fechapublicacion;
    }

    /**
     * @param string $fechapublicacion
     */
    public function setFechapublicacion($fechapublicacion) {
        $this->fechapublicacion = $fechapublicacion;
    }

    /**
     * @return string
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url) {
        $this->url = $url;
    }

    /**
     * @return int
     */
    public function getViews() {
        return $this->views;
    }

    /**
     * @param int $views
     */
    public function setViews($views) {
        $this->views = $views;
    }

    /**
     * @return \Categoria
     */
    public function getProducto() {
        return $this->producto;
    }

    /**
     * @param \Categoria $producto
     */
    public function setProducto(Producto $producto) {
        $this->producto = $producto;
    }

}
