<?php
/**
 * Created by PhpStorm.
 * User: yosme
 * Date: 08/12/2018
 * Time: 6:33 PM
 */

namespace IcanBundle\Entity;

/**
 * Video
 *
 * @ORM\Table(name="video")
 * @ORM\Entity(repositoryClass=IcanBundle\Entity\VideoRepository")
 */
class Video
{

    /**
     * @var integer
     *
     * @ORM\Column(name="video_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $videoId;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", nullable=false)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string")
     */
    private $descripcion;

    /**
     * @var string
     *
     * @ORM\Column(name="imagen", type="string")
     */
    private $imagen;

    /**
     * @var string
     *
     * @ORM\Column(name="estado", type="boolean")
     */
    private $estado;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", nullable=false)
     */
    private $url;

    /**
     * @return int
     */
    public function getVideoId() {
        return $this->videoId;
    }

    /**
     * @param int $videoId
     */
    public function setVideoId($videoId) {
        $this->videoId = $videoId;
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
     * @return string
     */
    public function getEstado() {
        return $this->estado;
    }

    /**
     * @param string $estado
     */
    public function setEstado($estado) {
        $this->estado = $estado;
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

}