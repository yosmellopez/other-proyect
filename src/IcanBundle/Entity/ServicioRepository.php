<?php

namespace IcanBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ServicioRepository extends EntityRepository
{

    /**
     * DevolverServicio: Devuelve la pagina de servicios
     *
     * @author Marcel
     */
    public function DevolverServicio()
    {
        $criteria = array();
        return $this->findOneBy($criteria);
    }
}
