<?php

namespace IcanBundle\Entity;

use Doctrine\ORM\EntityRepository;

class QuienesSomosRepository extends EntityRepository
{

    /**
     * DevolverQuienesSomos: Devuelve la pagina de quienes somos
     *
     * @author Marcel
     */
    public function DevolverQuienesSomos()
    {
        $criteria = array();
        return $this->findOneBy($criteria);
    }
}
