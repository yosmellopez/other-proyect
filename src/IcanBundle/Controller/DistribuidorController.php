<?php

namespace IcanBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use IcanBundle\Entity;

class DistribuidorController extends BaseController
{

    public function indexAction() {
        $comunas = $this->getDoctrine()->getRepository('IcanBundle:Comuna')->ListarOrdenadas();
        return $this->render('IcanBundle:Distribuidor:index.html.twig', array("comunas" => $comunas));
    }

    /**
     * listarAction Acción que lista los distribuidors
     *
     */
    public function listarAction(Request $request) {
        // search filter by keywords
        $query = !empty($request->get('query')) ? $request->get('query') : array();
        $sSearch = isset($query['generalSearch']) && is_string($query['generalSearch']) ? $query['generalSearch'] : '';
        //Sort
        $sort = !empty($request->get('sort')) ? $request->get('sort') : array();
        $sSortDir_0 = !empty($sort['sort']) ? $sort['sort'] : 'asc';
        $iSortCol_0 = !empty($sort['field']) ? $sort['field'] : 'nombre';
        //$start and $limit
        $pagination = !empty($request->get('pagination')) ? $request->get('pagination') : array();
        $page = !empty($pagination['page']) ? (int)$pagination['page'] : 1;
        $limit = !empty($pagination['perpage']) ? (int)$pagination['perpage'] : -1;
        $start = 0;

        try {
            $pages = 1;
            $total = $this->TotalDistribuidors($sSearch);
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

            $data = $this->ListarDistribuidors($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0);

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
        $distribuidor_id = $request->get('distribuidor_id');

        $nombre = $request->get('nombre');
        $descripcion = $request->get('descripcion');
        $estado = $request->get('estado');
        $comunaId = $request->get('comuna');
        $direccion = $request->get('direccion');
        $telefono = $request->get('telefono');
        $email = $request->get('email');

        if ($distribuidor_id == "") {
            $resultado = $this->SalvarDistribuidor($nombre, $descripcion, $estado, $comunaId, $direccion, $telefono, $email);
        } else {
            $resultado = $this->ActualizarDistribuidor($distribuidor_id, $nombre, $descripcion, $estado, $comunaId, $direccion, $telefono, $email);
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
     * eliminarAction Acción que elimina un distribuidor en la BD
     *
     */
    public function eliminarAction(Request $request) {
        $distribuidor_id = $request->get('distribuidor_id');

        $resultado = $this->EliminarDistribuidor($distribuidor_id);
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
     * eliminarDistribuidorsAction Acción que elimina los distribuidors seleccionados en la BD
     *
     */
    public function eliminarDistribuidorsAction(Request $request) {
        $ids = $request->get('ids');

        $resultado = $this->EliminarDistribuidors($ids);
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
     * cargarDatosAction Acción que carga los datos del distribuidor en la BD
     *
     */
    public function cargarDatosAction(Request $request) {
        $distribuidor_id = $request->get('distribuidor_id');

        $resultado = $this->CargarDatosDistribuidor($distribuidor_id);
        if ($resultado['success']) {

            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['distribuidor'] = $resultado['distribuidor'];

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
            $dir = 'uploads/distribuidores/';
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
            $dir = 'uploads/distribuidores/';
            if (is_file($dir . $imagen)) {
                unlink($dir . $imagen);
            }
        }

        $em = $this->getDoctrine()->getManager();

        $entity = $this->getDoctrine()->getRepository('IcanBundle:Distribuidor')->findOneBy(array('imagen' => $imagen));
        if ($entity != null) {
            $entity->setImagen("");
        }

        $em->flush();

        $resultado['success'] = true;
        return $resultado;
    }

    /**
     * CargarDatosDistribuidor: Carga los datos de un distribuidor
     *
     * @param int $distribuidor_id Id
     *
     * @author Marcel
     */
    public function CargarDatosDistribuidor($distribuidor_id) {
        $resultado = array();
        $arreglo_resultado = array();

        $entity = $this->getDoctrine()->getRepository('IcanBundle:Distribuidor')->find($distribuidor_id);
        if ($entity != null) {

            $arreglo_resultado['nombre'] = $entity->getNombre();
            $arreglo_resultado['descripcion'] = $entity->getDescripcion();
            $arreglo_resultado['direccion'] = $entity->getDireccion();
            $arreglo_resultado['telefono'] = $entity->getTelefono();
            $arreglo_resultado['email'] = $entity->getEmail();
            $arreglo_resultado['comuna'] = $entity->getComuna()->getComunaId();
            $arreglo_resultado['estado'] = ($entity->getEstado() == 1) ? true : false;
            $resultado['success'] = true;
            $resultado['distribuidor'] = $arreglo_resultado;
        }

        return $resultado;
    }

    /**
     * EliminarDistribuidor: Elimina un rol en la BD
     * @param int $distribuidor_id Id
     * @author Marcel
     */
    public function EliminarDistribuidor($distribuidor_id) {
        $em = $this->getDoctrine()->getManager();
        $entity = $this->getDoctrine()->getRepository('IcanBundle:Distribuidor')->find($distribuidor_id);
        if ($entity != null) {
            //Eliminar foto
            $foto_eliminar = $entity->getImagen();
            if ($foto_eliminar != "") {
                $dir = 'uploads/distribuidores/';
                if (is_file($dir . $foto_eliminar)) {
                    unlink($dir . $foto_eliminar);
                }
            }
            $em->remove($entity);
            $em->flush();
            $resultado['success'] = true;
        } else {
            $resultado['success'] = false;
            $resultado['error'] = "No existe el registro solicitado";
        }

        return $resultado;
    }

    /**
     * EliminarDistribuidors: Elimina los distribuidors seleccionados en la BD
     * @param int $ids Ids
     * @author Marcel
     */
    public function EliminarDistribuidors($ids) {
        $em = $this->getDoctrine()->getManager();

        if ($ids != "") {
            $ids = explode(',', $ids);
            $cant_eliminada = 0;
            foreach ($ids as $distribuidor_id) {
                if ($distribuidor_id != "") {
                    $entity = $this->getDoctrine()->getRepository('IcanBundle:Distribuidor')->find($distribuidor_id);
                    if ($entity != null) {
                        //Eliminar foto
                        $foto_eliminar = $entity->getImagen();
                        if ($foto_eliminar != "") {
                            $dir = 'uploads/distribuidores/';
                            if (is_file($dir . $foto_eliminar)) {
                                unlink($dir . $foto_eliminar);
                            }
                        }
                        $em->remove($entity);
                        $cant_eliminada++;
                    }
                }
            }
        }
        $em->flush();

        if ($cant_eliminada == 0) {
            $resultado['success'] = false;
            $resultado['error'] = "No se pudo eliminar las distribuidores, porque están asociados a un producto";
        } else {
            $resultado['success'] = true;

            $mensaje = ($cant_eliminada == count($ids)) ? "La operación se ha realizado correctamente" : "La operación se ha realizado correctamente. Pero atención no se pudo eliminar todas las distribuidores seleccionadas porque están asociadas a un producto";
            $resultado['message'] = $mensaje;
        }

        return $resultado;
    }

    /**
     * ActualizarDistribuidor: Actualiza los datos del distribuidor en la BD
     *
     * @param string $distribuidor_id Id
     *
     * @author Marcel
     */
    public function ActualizarDistribuidor($distribuidor_id, $nombre, $descripcion, $estado, $comunaId, $direccion, $telefono, $email) {
        $em = $this->getDoctrine()->getManager();

        $resultado = array();
        $entity = $this->getDoctrine()->getRepository('IcanBundle:Distribuidor')->find($distribuidor_id);
        $comuna = $this->getDoctrine()->getManager()->find("IcanBundle:Comuna", $comunaId);
        if ($entity != null) {
            //Verificar nombre
            $distribuidor = $this->getDoctrine()->getRepository('IcanBundle:Distribuidor')->findOneBy(array('nombre' => $nombre));
            if ($distribuidor != null) {
                if ($entity->getDistribuidorId() != $distribuidor->getDistribuidorId()) {
                    $resultado['success'] = false;
                    $resultado['error'] = "El nombre del distribuidor está en uso, por favor intente ingrese otro.";
                    return $resultado;
                }
            }

            $entity->setNombre($nombre);
            $entity->setDescripcion($descripcion);
            $entity->setEstado($estado);
            $entity->setComuna($comuna);
            $entity->setDireccion($direccion);
            $entity->setTelefono($telefono);
            $entity->setEmail($email);

            $em->flush();
            $resultado['success'] = true;
        } else {
            $resultado['success'] = false;
            $resultado['error'] = "No existe un distribuidor que se corresponda con ese identificador";
        }
        return $resultado;
    }

    /**
     * SalvarDistribuidor: Guarda los datos del usuario en la BD
     *
     *
     * @author Marcel
     */
    public function SalvarDistribuidor($nombre, $descripcion, $estado, $comunaId, $direccion, $telefono, $email) {
        $resultado = array();
        $em = $this->getDoctrine()->getManager();

        //Verificar nombre
        $distribuidor = $this->getDoctrine()->getRepository('IcanBundle:Distribuidor')->findOneBy(array('nombre' => $nombre));
        $comuna = $this->getDoctrine()->getEntityManager()->find("IcanBundle:Comuna", $comunaId);
        if ($distribuidor != null) {
            $resultado['success'] = false;
            $resultado['error'] = "El nombre del distribuidor está en uso, por favor intente ingrese otro.";
            return $resultado;
        }

        $entity = new Entity\Distribuidor();

        $entity->setNombre($nombre);
        $entity->setDescripcion($descripcion);
        $entity->setEstado($estado);
        $entity->setComuna($comuna);
        $entity->setDireccion($direccion);
        $entity->setTelefono($telefono);
        $entity->setEmail($email);
        $em->persist($entity);
        $em->flush();
        $em->flush();
        $resultado['success'] = true;

        return $resultado;
    }

    /**
     * RenombrarImagen: Renombra la imagen en la BD
     *
     * @author Marcel
     */
    public function RenombrarImagen($id, $imagen) {
        $dir = 'uploads/distribuidores/';
        $imagen_new = "";

        if ($imagen != "") {
            $extension_array = explode('.', $imagen);
            $extension = $extension_array[1];

            //Imagen nueva
            $imagen_new = $id . '.' . $extension;
            if (is_file($dir . $imagen)) {
                //Renombrar imagen
                rename($dir . $imagen, $dir . $imagen_new);
            }
        }

        return $imagen_new;
    }

    /**
     * ListarDistribuidors: Listar los distribuidors
     *
     * @param int $start Inicio
     * @param int $limit Limite
     * @param string $sSearch Para buscar
     *
     * @author Marcel
     */
    public function ListarDistribuidors($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0) {
        $arreglo_resultado = array();
        $cont = 0;

        $lista = $this->getDoctrine()->getRepository('IcanBundle:Distribuidor')->ListarDistribuidors($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0);

        foreach ($lista as $value) {
            $distribuidor_id = $value->getDistribuidorId();
            $acciones = $this->ListarAcciones($distribuidor_id);
            $arreglo_resultado[$cont] = array("id" => $distribuidor_id, "nombre" => $value->getNombre(), "descripcion" => $value->getDescripcion(), "estado" => ($value->getEstado()) ? 1 : 0, "acciones" => $acciones,
                "direccion" => $value->getDireccion(), "email" => $value->getEmail(), "telefono" => $value->getTelefono(), "comuna" => $value->getComuna()->toString());

            $cont++;
        }

        return $arreglo_resultado;
    }

    /**
     * TotalDistribuidors: Total de distribuidors
     * @param string $sSearch Para buscar
     * @author Marcel
     */
    public function TotalDistribuidors($sSearch) {
        $total = $this->getDoctrine()->getRepository('IcanBundle:Distribuidor')->TotalDistribuidors($sSearch);
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
