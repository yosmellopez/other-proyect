<?php
/**
 * Created by PhpStorm.
 * User: yosme
 * Date: 08/12/2018
 * Time: 2:28 PM
 */

namespace IcanBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tipo Producto
 *
 * @ORM\Table(name="tipo_producto")
 * @ORM\Entity(repositoryClass="IcanBundle\Entity\TipoProductoRepository")
 */
class TipoProducto
{
    /**
     * @var integer
     *
     * @ORM\Column(name="tipo_producto_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $tipoProductoId;

    /**
     * @var string
     *
     * @ORM\Column(name="tipo_producto", type="string", nullable=false)
     */
    private $tipoProducto;

    /**
     * @return int
     */
    public function getTipoProductoId() {
        return $this->tipoProductoId;
    }

    /**
     * @param int $tipoProductoId
     */
    public function setTipoProductoId($tipoProductoId) {
        $this->tipoProductoId = $tipoProductoId;
    }

    /**
     * @return string
     */
    public function getTipoProducto() {
        return $this->tipoProducto;
    }

    /**
     * @param string $tipoProducto
     */
    public function setTipoProducto($tipoProducto) {
        $this->tipoProducto = $tipoProducto;
    }
}