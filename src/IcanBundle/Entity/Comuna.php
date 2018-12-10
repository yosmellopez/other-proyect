<?php

namespace IcanBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Categoria
 *
 * @ORM\Table(name="comuna")
 * @ORM\Entity(repositoryClass="IcanBundle\Entity\ComunaRepository")
 */
class Comuna
{
    /**
     * @var integer
     *
     * @ORM\Column(name="comuna_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $comunaId;

    /**
     * @var string
     *
     * @ORM\Column(name="region", type="string", length=255, nullable=true)
     */
    private $region;

    /**
     * @var string
     *
     * @ORM\Column(name="provincia", type="string", length=255, nullable=true)
     */
    private $provincia;

    /**
     * @return int
     */
    public function getComunaId() {
        return $this->comunaId;
    }

    /**
     * @param int $comunaId
     */
    public function setComunaId($comunaId) {
        $this->comunaId = $comunaId;
    }

    /**
     * @return string
     */
    public function getRegion() {
        return $this->region;
    }

    /**
     * @param string $region
     */
    public function setRegion($region) {
        $this->region = $region;
    }

    /**
     * @return string
     */
    public function getProvincia() {
        return $this->provincia;
    }

    /**
     * @param string $provincia
     */
    public function setProvincia($provincia) {
        $this->provincia = $provincia;
    }

    public function toString() {
        return $this->provincia . " - " . $this->region;
    }

}