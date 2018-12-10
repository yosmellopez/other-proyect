<?php

namespace IcanBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ComunaRepository extends EntityRepository
{
    /**
     * ListarOrdenadas: Lista las marcas ordenadas
     *
     * @author Marcel
     */
    public function ListarOrdenadas() {
        $consulta = $this->createQueryBuilder('m')->orderBy('m.provincia', 'ASC');
        $lista = $consulta->getQuery()->getResult();
        return $lista;
    }

    /**
     * ListarComunas: Lista los comuna
     * @param int $start Inicio
     * @param int $limit Limite
     * @param string $sSearch Para buscar
     *
     * @author Marcel
     */
    public function ListarComunas($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0) {
        $consulta = $this->createQueryBuilder('c');

        if ($sSearch != "")
            $consulta->andWhere('c.region LIKE :region OR c.provincia LIKE :provincia')
                ->setParameter('region', "%${sSearch}%")
                ->setParameter('provincia', "%${sSearch}%");

        $consulta->orderBy("c.$iSortCol_0", $sSortDir_0);
        if ($limit > 0) {
            $consulta->setMaxResults($limit);
        }

        $lista = $consulta->setFirstResult($start)
            ->getQuery()->getResult();
        return $lista;
    }

    /**
     * TotalComunas: Total de comuna de la BD
     * @param string $sSearch Para buscar
     *
     * @author Marcel
     */
    public function TotalComunas($sSearch) {
        $em = $this->getEntityManager();
        $consulta = 'SELECT COUNT(c.comunaId) FROM IcanBundle\Entity\Comuna c ';
        $join = '';
        $where = '';

        if ($sSearch != "") {
            $esta_query = explode("WHERE", $where);
            if (count($esta_query) == 1)
                $where .= 'WHERE c.region LIKE :region OR c.provincia LIKE :provincia ';
            else
                $where .= 'AND c.region LIKE :region OR c.provincia LIKE :provincia  ';
        }

        $consulta .= $join;
        $consulta .= $where;
        $query = $em->createQuery($consulta);
        //Adicionar parametros        
        //$sSearch
        $esta_query_nombre = substr_count($consulta, ':region');
        if ($esta_query_nombre == 1)
            $query->setParameter('region', "%${sSearch}%");
        $esta_query_descripcion = substr_count($consulta, ':provincia');
        if ($esta_query_descripcion == 1)
            $query->setParameter('provincia', "%${sSearch}%");

        $total = $query->getSingleScalarResult();
        return $total;
    }

}
