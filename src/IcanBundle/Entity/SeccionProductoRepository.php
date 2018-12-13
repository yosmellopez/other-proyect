<?php

namespace IcanBundle\Entity;

use Doctrine\ORM\EntityRepository;

class SeccionProductoRepository extends EntityRepository
{

    /**
     * ListarOrdenados: Lista los seccionProductos ordenados por fecha
     *
     * @author Marcel
     */
    public function ListarOrdenados() {
        $consulta = $this->createQueryBuilder('p')
            ->where('p.estado = 1')
            ->orderBy('p.fechapublicacion', 'DESC');

        $lista = $consulta->getQuery()->getResult();
        return $lista;
    }

    /**
     * ListarUltimosSeccionProductos: Lista los ultimos 6 seccionProductos ordenados por fecha
     *
     * @author Marcel
     */
    public function ListarUltimosSeccionProductos($fecha_actual, $limit) {
        $consulta = $this->createQueryBuilder('p')
            ->andWhere('p.estado = 1');

        if ($fecha_actual != "") {
            $consulta->andWhere("p.fechapublicacion <= :fecha")
                ->setParameter('fecha', $fecha_actual);
        }

        $lista = $consulta->orderBy('p.fechapublicacion', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
        return $lista;
    }

    /**
     * BuscarPorUrl: Devuelve el seccionProducto de la url
     * @param string $url url
     *
     * @author Marcel
     */
    public function BuscarPorUrl($url) {
        $criteria = array('url' => $url);
        return $this->findOneBy($criteria);
    }

    /**
     * ListarSeccionProductosDeCategoria: Lista los seccionProductos de una categoria
     * @param int $categoria_id
     *
     * @author Marcel
     */
    public function ListarSeccionProductosDeCategoria($categoria_id) {
        $consulta = $this->createQueryBuilder('p')
            ->leftJoin('p.categoria', 'c')
            ->where('c.categoriaId = :categoria_id')
            ->setParameter('categoria_id', $categoria_id)
            ->getQuery();

        $lista = $consulta->getResult();
        return $lista;
    }

    /**
     * ListarSeccionProductosDeMarca: Lista los seccionProductos de una marca
     * @param int $marca_id
     *
     * @author Marcel
     */
    public function ListarSeccionProductosDeMarca($marca_id) {
        $consulta = $this->createQueryBuilder('p')
            ->leftJoin('p.marca', 'm')
            ->where('m.marcaId = :marca_id')
            ->setParameter('marca_id', $marca_id)
            ->getQuery();

        $lista = $consulta->getResult();
        return $lista;
    }

    /**
     * ListarSeccionProductosOrdenados: Lista los seccionProductos ordenados por fecha
     * @param int $start Inicio
     * @param int $limit Limite
     * @param string $sSearch Para buscar
     *
     * @author Marcel
     */
    public function ListarSeccionProductosOrdenados($start, $limit) {
        $consulta = $this->createQueryBuilder('p')->orderBy('p.fechapublicacion', 'DESC');

        $lista = $consulta->setFirstResult($start)
            ->setMaxResults($limit)
            ->getQuery()->getResult();
        return $lista;
    }

    /**
     * ListarSeccionProductos: Lista los seccionProducto
     * @param int $start Inicio
     * @param int $limit Limite
     * @param string $sSearch Para buscar
     *
     * @author Marcel
     */
    public function ListarSeccionProductos($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0, $categoria_id = "", $marca_id = "") {
        $consulta = $this->createQueryBuilder('p')
            ->leftJoin('p.categoria', 'c')
            ->leftJoin('p.marca', 'm');

        if ($sSearch != "") {
            $consulta
                ->andWhere('p.nombre LIKE :nombre OR p.descripcion LIKE :descripcion')
                ->setParameter('nombre', "%${sSearch}%")
                ->setParameter('descripcion', "%${sSearch}%");
        }

        if ($categoria_id != "") {
            $consulta->andWhere('c.categoriaId = :categoria_id')
                ->setParameter('categoria_id', $categoria_id);
        }
        if ($marca_id != "") {
            $consulta->andWhere('m.marcaId = :marca_id')
                ->setParameter('marca_id', $marca_id);
        }

        if ($iSortCol_0 == "categoria") {
            $consulta->orderBy("c.nombre", $sSortDir_0);
        } else {
            if ($iSortCol_0 == "marca") {
                $consulta->orderBy("m.descripcion", $sSortDir_0);
            } else {
                $consulta->orderBy("p.$iSortCol_0", $sSortDir_0);
            }
        }

        if ($limit > 0) {
            $consulta->setMaxResults($limit);
        }

        $lista = $consulta->setFirstResult($start)
            ->getQuery()->getResult();
        return $lista;
    }

    /**
     * TotalSeccionProductos: Total de seccionProducto de la BD
     * @param string $sSearch Para buscar
     *
     * @author Marcel
     */
    public function TotalSeccionProductos($sSearch, $categoria_id = "", $marca_id = "") {
        $em = $this->getEntityManager();
        $consulta = 'SELECT COUNT(p.seccionProductoId) FROM IcanBundle\Entity\SeccionProducto p ';
        $join = ' LEFT JOIN p.categoria c LEFT JOIN p.marca m ';
        $where = '';

        if ($sSearch != "") {
            $esta_query = explode("WHERE", $where);
            if (count($esta_query) == 1)
                $where .= ' WHERE p.nombre LIKE :nombre OR p.descripcion LIKE :descripcion ';
            else
                $where .= ' AND p.nombre LIKE :nombre OR p.descripcion LIKE :descripcion  ';
        }

        if ($categoria_id != "") {
            $esta_query = explode("WHERE", $where);
            if (count($esta_query) == 1) {
                $where .= 'WHERE c.categoriaId = :categoria_id ';
            } else {
                $where .= 'AND c.categoriaId = :categoria_id ';
            }
        }
        if ($marca_id != "") {
            $esta_query = explode("WHERE", $where);
            if (count($esta_query) == 1) {
                $where .= 'WHERE m.marcaId = :marca_id ';
            } else {
                $where .= 'AND m.marcaId = :marca_id ';
            }
        }

        $consulta .= $join;
        $consulta .= $where;
        $query = $em->createQuery($consulta);
        //Adicionar parametros        
        //$sSearch    
        $esta_query_categoria_id = substr_count($consulta, ':categoria_id');
        if ($esta_query_categoria_id == 1)
            $query->setParameter('categoria_id', $categoria_id);

        $esta_query_marca_id = substr_count($consulta, ':marca_id');
        if ($esta_query_marca_id == 1)
            $query->setParameter('marca_id', $marca_id);

        $esta_query_nombre = substr_count($consulta, ':nombre');
        if ($esta_query_nombre == 1)
            $query->setParameter('nombre', "%${sSearch}%");

        $esta_query_descripcion = substr_count($consulta, ':descripcion');
        if ($esta_query_descripcion == 1)
            $query->setParameter('descripcion', "%${sSearch}%");

        $total = $query->getSingleScalarResult();
        return $total;
    }

    /**
     * ListarSeccionProductosParaRelacionados: Lista los seccionProductos para seleccionar relacionados
     * @param int $start Inicio
     * @param int $limit Limite
     * @param string $sSearch Para buscar
     *
     * @author Marcel
     */
    public function ListarSeccionProductosParaRelacionados($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0, $categoria_id = "", $marca_id = "", $seccionProductos_id = array()) {
        $consulta = $this->createQueryBuilder('p')
            ->leftJoin('p.categoria', 'c')
            ->leftJoin('p.marca', 'm');

        if ($sSearch != "") {
            $consulta
                ->andWhere('p.nombre LIKE :nombre OR p.descripcion LIKE :descripcion')
                ->setParameter('nombre', "%${sSearch}%")
                ->setParameter('descripcion', "%${sSearch}%");
        }

        if ($categoria_id != "") {
            $consulta->andWhere('c.categoriaId = :categoria_id')
                ->setParameter('categoria_id', $categoria_id);
        }
        if ($marca_id != "") {
            $consulta->andWhere('m.marcaId = :marca_id')
                ->setParameter('marca_id', $marca_id);
        }

        if (count($seccionProductos_id) > 0) {
            foreach ($seccionProductos_id as $key => $seccionProducto_id) {
                $consulta->andWhere("p.seccionProductoId <> :seccionProducto$key")
                    ->setParameter("seccionProducto$key", $seccionProducto_id);
            }
        }

        if ($iSortCol_0 == "categoria") {
            $consulta->orderBy("c.nombre", $sSortDir_0);
        } else {
            if ($iSortCol_0 == "marca") {
                $consulta->orderBy("m.descripcion", $sSortDir_0);
            } else {
                $consulta->orderBy("p.$iSortCol_0", $sSortDir_0);
            }
        }

        if ($limit > 0) {
            $consulta->setMaxResults($limit);
        }

        $lista = $consulta->setFirstResult($start)
            ->getQuery()->getResult();
        return $lista;
    }

    /**
     * TotalSeccionProductosParaRelacionados: Total de seccionProductos para seleccionar relacionados de la BD
     * @param string $sSearch Para buscar
     *
     * @author Marcel
     */
    public function TotalSeccionProductosParaRelacionados($sSearch, $categoria_id = "", $marca_id = "", $seccionProductos_id = array()) {
        $em = $this->getEntityManager();
        $consulta = 'SELECT COUNT(p.seccionProductoId) FROM IcanBundle\Entity\SeccionProducto p ';
        $join = ' LEFT JOIN p.categoria c LEFT JOIN p.marca m ';
        $where = '';

        if ($sSearch != "") {
            $esta_query = explode("WHERE", $where);
            if (count($esta_query) == 1) {
                $where .= ' WHERE p.nombre LIKE :nombre OR p.descripcion LIKE :descripcion ';
            } else {
                $where .= ' AND p.nombre LIKE :nombre OR p.descripcion LIKE :descripcion  ';
            }
        }

        if ($categoria_id != "") {
            $esta_query = explode("WHERE", $where);
            if (count($esta_query) == 1) {
                $where .= 'WHERE c.categoriaId = :categoria_id ';
            } else {
                $where .= 'AND c.categoriaId = :categoria_id ';
            }
        }
        if ($marca_id != "") {
            $esta_query = explode("WHERE", $where);
            if (count($esta_query) == 1) {
                $where .= 'WHERE m.marcaId = :marca_id ';
            } else {
                $where .= 'AND m.marcaId = :marca_id ';
            }
        }

        $params_seccionProductos = array();
        if (count($seccionProductos_id) > 0) {
            foreach ($seccionProductos_id as $key => $seccionProducto_id) {

                $param = $this->generarParamAleatorio();
                array_push($params_seccionProductos, $param);

                $esta_query = explode("WHERE", $where);
                if (count($esta_query) == 1) {
                    $where .= " WHERE (p.seccionProductoId <> :$param)";
                } else {
                    $where .= "AND (p.seccionProductoId <> :$param) ";
                }
            }
        }

        $consulta .= $join;
        $consulta .= $where;
        $query = $em->createQuery($consulta);
        //Adicionar parametros        
        //$sSearch
        $esta_query_categoria_id = substr_count($consulta, ':categoria_id');
        if ($esta_query_categoria_id == 1)
            $query->setParameter('categoria_id', $categoria_id);

        $esta_query_marca_id = substr_count($consulta, ':marca_id');
        if ($esta_query_marca_id == 1)
            $query->setParameter('marca_id', $marca_id);

        $esta_query_nombre = substr_count($consulta, ':nombre');
        if ($esta_query_nombre == 1) {
            $query->setParameter('nombre', "%${sSearch}%");
        }

        $esta_query_descripcion = substr_count($consulta, ':descripcion');
        if ($esta_query_descripcion == 1) {
            $query->setParameter('descripcion', "%${sSearch}%");
        }

        if (count($seccionProductos_id) > 0) {
            foreach ($seccionProductos_id as $key => $seccionProducto_id) {
                $param = $params_seccionProductos[$key];
                $esta_query_pregunta = substr_count($consulta, ":$param");
                if ($esta_query_pregunta == 1) {
                    $query->setParameter(":$param", $seccionProducto_id);
                }
            }
        }

        $total = $query->getSingleScalarResult();
        return $total;
    }

    /**
     * ListarSeccionProductosMasVistas: Lista los seccionProductos mas vistas
     * @param int $start Inicio
     * @param int $limit Limite
     * @param string $sSearch Para buscar
     *
     * @author Marcel
     */
    public function ListarSeccionProductosMasVistas() {
        $consulta = $this->createQueryBuilder('p')
            ->where('p.views > 0')
            ->orderBy('p.views', 'DESC');

        $lista = $consulta->setMaxResults(3)
            ->getQuery()->getResult();
        return $lista;
    }


    /**
     * ListarSeccionProductosPortada: Lista los seccionProductos para la portada
     *
     * @author Marcel
     */
    public function ListarSeccionProductosPortada($fecha_actual) {
        $consulta = $this->createQueryBuilder('p')
            ->where('p.estado = 1');

        if ($fecha_actual != "") {
            $consulta->andWhere("p.fechapublicacion <= :fecha")
                ->setParameter('fecha', $fecha_actual);
        }

        $lista = $consulta->orderBy('p.fechapublicacion', 'DESC')
            ->getQuery()
            ->getResult();

        return $lista;
    }

    /**
     * ListarSeccionProductosCategoriaPortada: Lista los seccionProductos de una categoria para el frontend
     *
     * @author Marcel
     */
    public function ListarSeccionProductosCategoriaPortada($categoria, $fecha_actual) {
        $consulta = $this->createQueryBuilder('p')
            ->where('p.estado = 1');

        if ($categoria != "") {
            $consulta->leftJoin('p.categoria', 'c')
                ->leftJoin('c.categoriaPadre', 'c_p')
                ->andWhere("c.url = :url OR c_p.url = :url_padre")
                ->setParameter('url', $categoria)
                ->setParameter('url_padre', $categoria);
        }

        if ($fecha_actual != "") {
            $consulta->andWhere("p.fechapublicacion <= :fecha")
                ->setParameter('fecha', $fecha_actual);
        }

        $lista = $consulta->orderBy('p.fechapublicacion', 'DESC')
            ->getQuery()
            ->getResult();
        return $lista;
    }

    /**
     * ListarSeccionProductosMarcaPortada: Lista los seccionProductos de una marca para el frontend
     *
     * @author Marcel
     */
    public function ListarSeccionProductosMarcaPortada($marca, $fecha_actual) {
        $consulta = $this->createQueryBuilder('p')
            ->where('p.estado = 1');

        if ($marca != "") {
            $consulta->leftJoin('p.marca', 'm')
                ->andWhere("m.url = :url")
                ->setParameter('url', $marca);
        }

        if ($fecha_actual != "") {
            $consulta->andWhere("p.fechapublicacion <= :fecha")
                ->setParameter('fecha', $fecha_actual);
        }

        $lista = $consulta->orderBy('p.fechapublicacion', 'DESC')
            ->getQuery()
            ->getResult();
        return $lista;
    }

    /**
     * ListarBusquedaSeccionProductosPortada: Realiza la busqueda del frontend
     *
     * @author Marcel
     */
    public function ListarBusquedaSeccionProductosPortada($sSearch, $fecha_actual) {
        $consulta = $this->createQueryBuilder('p')
            ->where('p.estado = 1');

        if ($sSearch != "") {
            $consulta->leftJoin('p.categoria', 'c')
                ->leftJoin('c.categoriaPadre', 'c_p')
                ->andWhere("c.nombre LIKE :categoria OR c_p.nombre LIKE :categoria_padre OR p.nombre LIKE :nombre OR p.descripcion LIKE :descripcion")
                ->setParameter('categoria', "%${sSearch}%")
                ->setParameter('categoria_padre', "%${sSearch}%")
                ->setParameter('nombre', "%${sSearch}%")
                ->setParameter('descripcion', "%${sSearch}%");
        }

        if ($fecha_actual != "") {
            $consulta->andWhere("p.fechapublicacion <= :fecha")
                ->setParameter('fecha', $fecha_actual);
        }

        $lista = $consulta->orderBy('p.fechapublicacion', 'DESC')
            ->getQuery()
            ->getResult();
        return $lista;
    }

    function generarParamAleatorio() {
        $codigo = "";
        //Dos letras
        $codigo .= substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz"), 0, 10);

        return $codigo;
    }
}
