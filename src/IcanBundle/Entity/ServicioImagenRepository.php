<?php

namespace IcanBundle\Entity;

use Doctrine\ORM\EntityRepository;


class ServicioImagenRepository extends EntityRepository
{

    /**
     * ListarImagenes: Lista las imagenes de un servicio
     * @param int $servicio_id
     *
     * @author Marcel
     */
    public function ListarImagenes($servicio_id)
    {
        $consulta = $this->createQueryBuilder('s_i')
            ->leftJoin('s_i.servicio', 's')
            ->where('s.servicioId = :servicio_id')
            ->setParameter('servicio_id', $servicio_id)
            ->getQuery();

        $lista = $consulta->getResult();
        return $lista;
    }
}