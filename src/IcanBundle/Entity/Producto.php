<?php

namespace IcanBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Producto
 *
 * @ORM\Table(name="producto")
 * @ORM\Entity(repositoryClass="IcanBundle\Entity\ProductoRepository")
 */
class Producto
{

    /**
     * @var integer
     *
     * @ORM\Column(name="producto_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $productoId;

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
     * @ORM\ManyToOne(targetEntity="Categoria")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="categoria_id", referencedColumnName="categoria_id")
     * })
     */
    private $categoria;

    /**
     * @var \Marca
     *
     * @ORM\ManyToOne(targetEntity="Marca")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="marca_id", referencedColumnName="marca_id")
     * })
     */
    private $marca;

    /**
     * Get productoId
     *
     * @return integer
     */
    public function getProductoId()
    {
        return $this->productoId;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return Producto
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set titulo
     *
     * @param string $titulo
     * @return Producto
     */
    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;

        return $this;
    }

    /**
     * Get titulo
     *
     * @return string
     */
    public function getTitulo()
    {
        return $this->titulo;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return Producto
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set tags
     *
     * @param string $tags
     * @return Producto
     */
    public function setTags($tags)
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * Get tags
     *
     * @return string
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Set stock
     *
     * @param integer $stock
     * @return Producto
     */
    public function setStock($stock)
    {
        $this->stock = $stock;

        return $this;
    }

    /**
     * Get stock
     *
     * @return integer
     */
    public function getStock()
    {
        return $this->stock;
    }

    /**
     * Set precio
     *
     * @param integer $precio
     * @return Producto
     */
    public function setPrecio($precio)
    {
        $this->precio = $precio;

        return $this;
    }

    /**
     * Get precio
     *
     * @return integer
     */
    public function getPrecio()
    {
        return $this->precio;
    }

    /**
     * Set precioOferta
     *
     * @param integer $precioOferta
     * @return Producto
     */
    public function setPrecioOferta($precioOferta)
    {
        $this->precioOferta = $precioOferta;

        return $this;
    }

    /**
     * Get precioOferta
     *
     * @return integer
     */
    public function getPrecioOferta()
    {
        return $this->precioOferta;
    }

    /**
     * Set mostrarPrecio
     *
     * @param boolean $mostrarPrecio
     * @return Producto
     */
    public function setMostrarPrecio($mostrarPrecio)
    {
        $this->mostrarPrecio = $mostrarPrecio;

        return $this;
    }

    /**
     * Get mostrarPrecio
     *
     * @return boolean
     */
    public function getMostrarPrecio()
    {
        return $this->mostrarPrecio;
    }

    /**
     * Set imagen
     *
     * @param string $imagen
     * @return Producto
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
     * Set estado
     *
     * @param boolean $estado
     * @return Producto
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Get estado
     *
     * @return boolean
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return Producto
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set views
     *
     * @param string $views
     * @return Noticia
     */
    public function setViews($views)
    {
        $this->views = $views;

        return $this;
    }

    /**
     * Get views
     *
     * @return string
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * Set fechapublicacion
     *
     * @param string $fechapublicacion
     * @return Producto
     */
    public function setFechapublicacion($fechapublicacion)
    {
        $this->fechapublicacion = $fechapublicacion;

        return $this;
    }

    /**
     * Get fechapublicacion
     *
     * @return string
     */
    public function getFechapublicacion()
    {
        return $this->fechapublicacion;
    }

    /**
     * Set categoria
     *
     * @param \IcanBundle\Entity\Categoria $categoria
     * @return Permisousuario
     */
    public function setCategoria(\IcanBundle\Entity\Categoria $categoria = null)
    {
        $this->categoria = $categoria;

        return $this;
    }

    /**
     * Get categoria
     *
     * @return \IcanBundle\Entity\Categoria
     */
    public function getCategoria()
    {
        return $this->categoria;
    }

    /**
     * Set marca
     *
     * @param \IcanBundle\Entity\Marca $marca
     * @return PermisoRol
     */
    public function setMarca(\IcanBundle\Entity\Marca $marca = null)
    {
        $this->marca = $marca;

        return $this;
    }

    /**
     * Get marca
     *
     * @return \IcanBundle\Entity\Marca
     */
    public function getMarca()
    {
        return $this->marca;
    }
}
