<?php

namespace IcanBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use IcanBundle\Entity;

class ComunaController extends BaseController
{

    public function indexAction() {
        return $this->render('IcanBundle:Comuna:index.html.twig', array());
    }

    /**
     * listarAction Acción que lista los comunas
     *
     */
    public function listarAction(Request $request) {
        // search filter by keywords
        $query = !empty($request->get('query')) ? $request->get('query') : array();
        $sSearch = isset($query['generalSearch']) && is_string($query['generalSearch']) ? $query['generalSearch'] : '';
        //Sort
        $sort = !empty($request->get('sort')) ? $request->get('sort') : array();
        $sSortDir_0 = !empty($sort['sort']) ? $sort['sort'] : 'asc';
        $iSortCol_0 = !empty($sort['field']) ? $sort['field'] : 'provincia';
        //$start and $limit
        $pagination = !empty($request->get('pagination')) ? $request->get('pagination') : array();
        $page = !empty($pagination['page']) ? (int)$pagination['page'] : 1;
        $limit = !empty($pagination['perpage']) ? (int)$pagination['perpage'] : -1;
        $start = 0;

        try {
            $pages = 1;
            $total = $this->TotalComunas($sSearch);
            if ($limit > 0) {
                $pages = ceil($total / $limit); // calculate total pages
                $page = max($page, 1); // get 1 page when $_REQUEST['page'] <= 0
                $page = min($page, $pages); // get last page when $_REQUEST['page'] > $totalPages
                $start = ($page - 1) * $limit;
                if ($start < 0) {
                    $start = 0;
                }
            }

            $meta = array('page' => $page, 'pages' => $pages, 'perpage' => $limit, 'total' => $total, 'field' => $iSortCol_0, 'sort' => $sSortDir_0);

            $data = $this->ListarComunas($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0);

            $resultadoJson = array('meta' => $meta, 'data' => $data);

            return new Response(json_encode($resultadoJson));

        } catch (Exception $e) {
            $resultadoJson['success'] = false;
            $resultadoJson['error'] = $e->getMessage();

            return new Response(json_encode($resultadoJson));
        }
    }

    /**
     * salvarAction Acción que inserta un menu en la BD
     *
     */
    public function salvarAction(Request $request) {
        $comuna_id = $request->get('comuna_id');
        $provincia = $request->get('provincia');
        $region = $request->get('region');
        if ($comuna_id == "") {
            $resultado = $this->SalvarComuna($provincia, $region);
        } else {
            $resultado = $this->ActualizarComuna($comuna_id, $provincia, $region);
        }
        if ($resultado['success']) {
            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['message'] = "La operación se realizó correctamente";
            return new Response(json_encode($resultadoJson));
        } else {
            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['error'] = $resultado['error'];
            return new Response(json_encode($resultadoJson));
        }
    }

    /**
     * eliminarAction Acción que elimina un comuna en la BD
     *
     */
    public function eliminarAction(Request $request) {
        $comuna_id = $request->get('comuna_id');

        $resultado = $this->EliminarComuna($comuna_id);
        if ($resultado['success']) {
            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['message'] = "La operación se realizó correctamente";
            return new Response(json_encode($resultadoJson));
        } else {
            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['error'] = $resultado['error'];
            return new Response(json_encode($resultadoJson));
        }
    }

    /**
     * eliminarComunasAction Acción que elimina los comunas seleccionados en la BD
     *
     */
    public function eliminarComunasAction(Request $request) {
        $ids = $request->get('ids');

        $resultado = $this->EliminarComunas($ids);
        if ($resultado['success']) {
            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['message'] = "La operación se realizó correctamente";
            return new Response(json_encode($resultadoJson));
        } else {
            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['error'] = $resultado['error'];
            return new Response(json_encode($resultadoJson));
        }
    }

    /**
     * cargarDatosAction Acción que carga los datos del comuna en la BD
     *
     */
    public function cargarDatosAction(Request $request) {
        $comuna_id = $request->get('comuna_id');
        $resultado = $this->CargarDatosComuna($comuna_id);
        if ($resultado['success']) {
            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['comuna'] = $resultado['comuna'];
            return new Response(json_encode($resultadoJson));
        } else {
            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['error'] = $resultado['error'];
            return new Response(json_encode($resultadoJson));
        }
    }

    /**
     * salvarImagenAction Acción para subir una imagen al servidor
     *
     */
    public function salvarImagenAction() {
        try {
            $nombre_archivo = $_FILES['foto']['name'];
            $array_nombre_archivo = explode('.', $nombre_archivo);
            $pos = count($array_nombre_archivo) - 1;
            $extension = $array_nombre_archivo[$pos];

            $archivo = $this->generarCadenaAleatoria() . '.' . $extension;

            //Manejar la imagen
            $dir = 'uploads/comunaes/';
            $archivo_tmp = $_FILES['foto']['tmp_name'];
            move_uploaded_file($archivo_tmp, $dir . $archivo);


            $resultadoJson['success'] = true;
            $resultadoJson['name'] = $archivo;
            return new Response(json_encode($resultadoJson));
        } catch (Exception $e) {
            $resultadoJson['success'] = false;
            $resultadoJson['error'] = $e->getMessage();
            return new Response(json_encode($resultadoJson));
        }
    }

    /**
     * eliminarImagenAction Acción que elimina una imagen en la BD
     *
     */
    public function eliminarImagenAction(Request $request) {
        $imagen = $request->get('imagen');

        $resultado = $this->EliminarImagen($imagen);
        if ($resultado['success']) {

            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['message'] = "La operación se realizó correctamente";
            return new Response(json_encode($resultadoJson));
        } else {
            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['error'] = $resultado['error'];
            return new Response(json_encode($resultadoJson));
        }
    }

    /**
     * EliminarImagen: Elimina una imagen en la BD
     *
     * @author Marcel
     */
    public function EliminarImagen($imagen) {
        $resultado = array();
        //Eliminar foto
        if ($imagen != "") {
            $dir = 'uploads/comunaes/';
            if (is_file($dir . $imagen)) {
                unlink($dir . $imagen);
            }
        }

        $em = $this->getDoctrine()->getManager();

        $entity = $this->getDoctrine()->getRepository('IcanBundle:Comuna')->findOneBy(array('imagen' => $imagen));
        if ($entity != null) {
            $entity->setImagen("");
        }

        $em->flush();

        $resultado['success'] = true;
        return $resultado;
    }

    /**
     * CargarDatosComuna: Carga los datos de un comuna
     *
     * @param int $comuna_id Id
     *
     * @author Marcel
     */
    public function CargarDatosComuna($comuna_id) {
        $resultado = array();
        $arreglo_resultado = array();
        $entity = $this->getDoctrine()->getRepository('IcanBundle:Comuna')->find($comuna_id);
        if ($entity != null) {
            $arreglo_resultado['provincia'] = $entity->getProvincia();
            $arreglo_resultado['region'] = $entity->getRegion();
            $resultado['success'] = true;
            $resultado['comuna'] = $arreglo_resultado;
        }

        return $resultado;
    }

    /**
     * EliminarComuna: Elimina un rol en la BD
     * @param int $comuna_id Id
     * @author Marcel
     */
    public function EliminarComuna($comuna_id) {
        $em = $this->getDoctrine()->getManager();
        $entity = $this->getDoctrine()->getRepository('IcanBundle:Comuna')->find($comuna_id);
        if ($entity != null) {
            $lista = $this->getDoctrine()->getRepository('IcanBundle:Distribuidor')->BuscarPorComuna($entity);
            $tieneDistribuidor = false;
            foreach ($lista as $distribuidor) {
                $tieneDistribuidor = true;
                break;
            }
            if (!$tieneDistribuidor) {
                $em->remove($entity);
                $em->flush();
                $resultado['success'] = true;
            } else {
                $resultado['success'] = false;
                $resultado['error'] = "No se puede eliminar la comuna porque contiene distribuidores";
            }
        } else {
            $resultado['success'] = false;
            $resultado['error'] = "No existe el registro solicitado";
        }
        return $resultado;
    }

    /**
     * EliminarComunas: Elimina los comunas seleccionados en la BD
     * @param int $ids Ids
     * @author Marcel
     */
    public function EliminarComunas($ids) {
        $em = $this->getDoctrine()->getManager();
        if ($ids != "") {
            $ids = explode(',', $ids);
            $cant_eliminada = 0;
            foreach ($ids as $comuna_id) {
                if ($comuna_id != "") {
                    $entity = $this->getDoctrine()->getRepository('IcanBundle:Comuna')->find($comuna_id);
                    if ($entity != null) {
                        $lista = $this->getDoctrine()->getRepository('IcanBundle:Distribuidor')->BuscarPorComuna($entity);
                        $tieneDistribuidor = false;
                        foreach ($lista as $distribuidor) {
                            $tieneDistribuidor = true;
                            break;
                        }
                        if (!$tieneDistribuidor) {
                            $em->remove($entity);
                            $cant_eliminada++;
                        }
                    }
                }
            }
        }
        $em->flush();
        if ($cant_eliminada == 0) {
            $resultado['success'] = false;
            $resultado['error'] = "No se pudo eliminar las comunas, porque están asociados a un distribuidor";
        } else {
            $resultado['success'] = true;
            $mensaje = ($cant_eliminada == count($ids)) ? "La operación se ha realizado correctamente" : "La operación se ha realizado correctamente. Pero atención no se pudo eliminar todas las comunaes seleccionadas porque están asociadas a un producto";
            $resultado['message'] = $mensaje;
        }
        return $resultado;
    }

    /**
     * ActualizarComuna: Actualiza los datos del comuna en la BD
     *
     * @param string $comuna_id Id
     *
     * @author Marcel
     */
    public function ActualizarComuna($comuna_id, $provincia, $region) {
        $em = $this->getDoctrine()->getManager();

        $resultado = array();
        $entity = $this->getDoctrine()->getRepository('IcanBundle:Comuna')->find($comuna_id);
        if ($entity != null) {
            //Verificar nombre
            $comuna = $this->getDoctrine()->getRepository('IcanBundle:Comuna')->findOneBy(array('provincia' => $provincia));
            if ($comuna != null) {
                if ($entity->getComunaId() != $comuna->getComunaId()) {
                    $resultado['success'] = false;
                    $resultado['error'] = "El nombre del comuna está en uso, por favor intente ingrese otro.";
                    return $resultado;
                }
            }
            $entity->setProvincia($provincia);
            $entity->setRegion($region);
            $em->flush();
            $resultado['success'] = true;
        } else {
            $resultado['success'] = false;
            $resultado['error'] = "No existe un comuna que se corresponda con ese identificador";
        }
        return $resultado;
    }

    /**
     * SalvarComuna: Guarda los datos del usuario en la BD
     *
     *
     * @author Marcel
     */
    public function SalvarComuna($provincia, $region) {
        $resultado = array();
        $em = $this->getDoctrine()->getManager();

        //Verificar nombre
        $comuna = $this->getDoctrine()->getRepository('IcanBundle:Comuna')->findOneBy(array('provincia' => $provincia));
        if ($comuna != null) {
            $resultado['success'] = false;
            $resultado['error'] = "El nombre del comuna está en uso, por favor intente ingrese otro.";
            return $resultado;
        }
        $entity = new Entity\Comuna();
        $entity->setProvincia($provincia);
        $entity->setRegion($region);
        $em->persist($entity);
        $em->flush();
        $resultado['success'] = true;

        return $resultado;
    }

    /**
     * ListarComunas: Listar los comunas
     *
     * @param int $start Inicio
     * @param int $limit Limite
     * @param string $sSearch Para buscar
     *
     * @author Marcel
     */
    public function ListarComunas($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0) {
        $arreglo_resultado = array();
        $cont = 0;
        $lista = $this->getDoctrine()->getRepository('IcanBundle:Comuna')->ListarComunas($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0);
        foreach ($lista as $value) {
            $comuna_id = $value->getComunaId();
            $acciones = $this->ListarAcciones($comuna_id);
            $arreglo_resultado[$cont] = array("id" => $comuna_id, "provincia" => $value->getProvincia(), "region" => $value->getRegion(), "acciones" => $acciones);
            $cont++;
        }
        return $arreglo_resultado;
    }

    /**
     * TotalComunas: Total de comunas
     * @param string $sSearch Para buscar
     * @author Marcel
     */
    public function TotalComunas($sSearch) {
        $total = $this->getDoctrine()->getRepository('IcanBundle:Comuna')->TotalComunas($sSearch);
        return $total;
    }

    /**
     * ListarAcciones: Lista los permisos de un usuario de la BD
     *
     * @author Marcel
     */
    public function ListarAcciones($id) {

        $acciones = "";

        $acciones .= '<a href="javascript:;" class="edit m-portlet__nav-link btn m-btn m-btn--hover-success m-btn--icon m-btn--icon-only m-btn--pill" title="Editar registro" data-id="' . $id . '"> <i class="la la-edit"></i> </a> ';
        $acciones .= ' <a href="javascript:;" class="delete m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill" title="Eliminar registro" data-id="' . $id . '"><i class="la la-trash"></i></a>';

        return $acciones;
    }

}
