<?php

namespace IcanBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use IcanBundle\Entity;

class VideoController extends BaseController
{
    private $extensiones = array(".mp4", ".avi");

    public function indexAction() {
        return $this->render('IcanBundle:Video:index.html.twig', array());
    }

    /**
     * listarAction Acción que lista los videos
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
            $total = $this->TotalVideos($sSearch);
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

            $data = $this->ListarVideos($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0);

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
        $video_id = $request->get('video_id');

        $nombre = $request->get('nombre');
        $titulo = $request->get('titulo');
        $descripcion = $request->get('descripcion');
        $tags = $request->get('tags');
        $estado = $request->get('estado');

        $imagen = $request->get('imagen');

        if ($video_id == "") {
            $resultado = $this->SalvarVideo($nombre, $titulo, $descripcion, $tags, $estado, $imagen);
        } else {
            $resultado = $this->ActualizarVideo($video_id, $nombre, $titulo, $descripcion, $tags, $estado, $imagen);
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
     * eliminarAction Acción que elimina un video en la BD
     *
     */
    public function eliminarAction(Request $request) {
        $video_id = $request->get('video_id');

        $resultado = $this->EliminarVideo($video_id);
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
     * eliminarVideosAction Acción que elimina los videos seleccionados en la BD
     *
     */
    public function eliminarVideosAction(Request $request) {
        $ids = $request->get('ids');

        $resultado = $this->EliminarVideos($ids);
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
     * cargarDatosAction Acción que carga los datos del video en la BD
     *
     */
    public function cargarDatosAction(Request $request) {
        $video_id = $request->get('video_id');

        $resultado = $this->CargarDatosVideo($video_id);
        if ($resultado['success']) {

            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['video'] = $resultado['video'];

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
            $dir = 'uploads/videos/';
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

    public function salvarVideoAction(Request $request) {
        $video = $request->get("video");
        if ($this->existeExtension($video)) {
            $resultadoJson = array("success" => true, "message" => "La operación se realizó correctamente");
            return new Response(json_encode($resultadoJson));
        } else {
            $resultadoJson = array("success" => false, "message" => "No es una extension válida de video");
            return new Response(json_encode($resultadoJson));
        }
    }

    private function existeExtension($fileExtension) {
        foreach ($this->extensiones as $extension) {
            if ($extension == $fileExtension) {
                return true;
            }
        }
        return false;
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
            $dir = 'uploads/videos/';
            if (is_file($dir . $imagen)) {
                unlink($dir . $imagen);
            }
        }

        $em = $this->getDoctrine()->getManager();

        $entity = $this->getDoctrine()->getRepository('IcanBundle:Video')->findOneBy(array('imagen' => $imagen));
        if ($entity != null) {
            $entity->setImagen("");
        }

        $em->flush();

        $resultado['success'] = true;
        return $resultado;
    }

    /**
     * CargarDatosVideo: Carga los datos de un video
     *
     * @param int $video_id Id
     *
     * @author Marcel
     */
    public function CargarDatosVideo($video_id) {
        $resultado = array();
        $arreglo_resultado = array();

        $entity = $this->getDoctrine()->getRepository('IcanBundle:Video')->find($video_id);
        if ($entity != null) {

            $arreglo_resultado['nombre'] = $entity->getNombre();
            $arreglo_resultado['titulo'] = $entity->getTitulo();
            $arreglo_resultado['descripcion'] = $entity->getDescripcion();
            $arreglo_resultado['estado'] = ($entity->getEstado() == 1) ? true : false;
            $arreglo_resultado['tags'] = $entity->getTags();

            $ruta = $this->ObtenerURL();
            $dir = 'uploads/videos/';
            $ruta = $ruta . $dir;
            $imagen = $entity->getImagen();
            $size = (is_file($dir . $imagen)) ? filesize($dir . $imagen) : 0;
            $arreglo_resultado['imagen'] = array($imagen, $size, $ruta);

            $resultado['success'] = true;
            $resultado['video'] = $arreglo_resultado;
        }

        return $resultado;
    }

    /**
     * EliminarVideo: Elimina un rol en la BD
     * @param int $video_id Id
     * @author Marcel
     */
    public function EliminarVideo($video_id) {
        $em = $this->getDoctrine()->getManager();
        $entity = $this->getDoctrine()->getRepository('IcanBundle:Video')->find($video_id);

        if ($entity != null) {

            //Productos
            $productos = $this->getDoctrine()->getRepository('IcanBundle:Producto')->ListarProductosDeVideo($video_id);
            if (count($productos) > 0) {
                $resultado['success'] = false;
                $resultado['error'] = "No se pudo eliminar la video, porque tiene productos asociados";
                return $resultado;
            }

            //Eliminar foto
            $foto_eliminar = $entity->getImagen();
            if ($foto_eliminar != "") {
                $dir = 'uploads/videos/';
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
     * EliminarVideos: Elimina los videos seleccionados en la BD
     * @param int $ids Ids
     * @author Marcel
     */
    public function EliminarVideos($ids) {
        $em = $this->getDoctrine()->getManager();

        if ($ids != "") {
            $ids = explode(',', $ids);
            $cant_eliminada = 0;
            foreach ($ids as $video_id) {
                if ($video_id != "") {
                    $entity = $this->getDoctrine()->getRepository('IcanBundle:Video')->find($video_id);
                    if ($entity != null) {

                        //Productos
                        $productos = $this->getDoctrine()->getRepository('IcanBundle:Producto')->ListarProductosDeVideo($video_id);
                        if (count($productos) == 0) {

                            //Eliminar foto
                            $foto_eliminar = $entity->getImagen();
                            if ($foto_eliminar != "") {
                                $dir = 'uploads/videos/';
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
        }
        $em->flush();

        if ($cant_eliminada == 0) {
            $resultado['success'] = false;
            $resultado['error'] = "No se pudo eliminar las videos, porque están asociados a un producto";
        } else {
            $resultado['success'] = true;

            $mensaje = ($cant_eliminada == count($ids)) ? "La operación se ha realizado correctamente" : "La operación se ha realizado correctamente. Pero atención no se pudo eliminar todas las videos seleccionadas porque están asociadas a un producto";
            $resultado['message'] = $mensaje;
        }

        return $resultado;
    }

    /**
     * ActualizarVideo: Actualiza los datos del video en la BD
     *
     * @param string $video_id Id
     *
     * @author Marcel
     */
    public function ActualizarVideo($video_id, $nombre, $titulo, $descripcion, $tags, $estado, $imagen) {
        $em = $this->getDoctrine()->getManager();

        $resultado = array();
        $entity = $this->getDoctrine()->getRepository('IcanBundle:Video')->find($video_id);
        if ($entity != null) {
            //Verificar nombre
            $video = $this->getDoctrine()->getRepository('IcanBundle:Video')->findOneBy(array('nombre' => $nombre));
            if ($video != null) {
                if ($entity->getVideoId() != $video->getVideoId()) {
                    $resultado['success'] = false;
                    $resultado['error'] = "El nombre del video está en uso, por favor intente ingrese otro.";
                    return $resultado;
                }
            }

            $entity->setNombre($nombre);
            $entity->setTitulo($titulo);
            $entity->setDescripcion($descripcion);
            $entity->setEstado($estado);
            $entity->setTags($tags);

            //Hacer Url
            $url = $this->HacerUrl($nombre);
            $i = 1;
            $paux = $this->getDoctrine()->getRepository('IcanBundle:Video')->BuscarPorUrl($url);
            while (!empty($paux) && $paux->getVideoId() != $video_id) {
                $url = $this->HacerUrl($nombre) . "-" . $i;
                $paux = $this->getDoctrine()->getRepository('IcanBundle:Video')->BuscarPorUrl($url);
                $i++;
            }

            $entity->setUrl($url);

            if ($imagen != "") {
                $foto_eliminar = $entity->getImagen();
                if ($imagen != $foto_eliminar) {
                    //Eliminar foto
                    if ($foto_eliminar != "") {
                        $dir = 'uploads/videos/';
                        if (is_file($dir . $foto_eliminar)) {
                            unlink($dir . $foto_eliminar);
                        }
                    }
                    $imagen = $this->RenombrarImagen($url, $imagen);
                    $entity->setImagen($imagen);
                }
            }

            $em->flush();
            $resultado['success'] = true;
        } else {
            $resultado['success'] = false;
            $resultado['error'] = "No existe un video que se corresponda con ese identificador";
        }
        return $resultado;
    }

    /**
     * SalvarVideo: Guarda los datos del usuario en la BD
     *
     *
     * @author Marcel
     */
    public function SalvarVideo($nombre, $titulo, $descripcion, $tags, $estado, $imagen) {
        $resultado = array();
        $em = $this->getDoctrine()->getManager();

        //Verificar nombre
        $video = $this->getDoctrine()->getRepository('IcanBundle:Video')->findOneBy(array('nombre' => $nombre));
        if ($video != null) {
            $resultado['success'] = false;
            $resultado['error'] = "El nombre del video está en uso, por favor intente ingrese otro.";
            return $resultado;
        }

        $entity = new Entity\Video();

        $entity->setNombre($nombre);
        $entity->setTitulo($titulo);
        $entity->setDescripcion($descripcion);
        $entity->setEstado($estado);
        $entity->setTags($tags);

        //Hacer Url
        $url = $this->HacerUrl($nombre);
        $i = 1;
        $paux = $this->getDoctrine()->getRepository('IcanBundle:Video')->BuscarPorUrl($url);
        while (!empty($paux)) {
            $url = $this->HacerUrl($nombre) . "-" . $i;
            $paux = $this->getDoctrine()->getRepository('IcanBundle:Video')->BuscarPorUrl($url);
            $i++;
        }

        $entity->setUrl($url);

        $em->persist($entity);
        $em->flush();

        //Salvar imagen
        $imagen = $this->RenombrarImagen($url, $imagen);
        $entity->setImagen($imagen);

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
        $dir = 'uploads/videos/';
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
     * ListarVideos: Listar los videos
     *
     * @param int $start Inicio
     * @param int $limit Limite
     * @param string $sSearch Para buscar
     *
     * @author Marcel
     */
    public function ListarVideos($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0) {
        $arreglo_resultado = array();
        $cont = 0;

        $lista = $this->getDoctrine()->getRepository('IcanBundle:Video')->ListarVideos($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0);

        foreach ($lista as $value) {
            $video_id = $value->getVideoId();

            $acciones = $this->ListarAcciones($video_id);

            $ruta = $this->ObtenerURL();
            $dir = 'uploads/videos/';
            $imagen = $ruta . $dir . $value->getImagen();

            $arreglo_resultado[$cont] = array("id" => $video_id, "nombre" => $value->getNombre(), "descripcion" => $value->getDescripcion(), "estado" => ($value->getEstado()) ? 1 : 0, "imagen" => $imagen, "acciones" => $acciones);

            $cont++;
        }

        return $arreglo_resultado;
    }

    /**
     * TotalVideos: Total de videos
     * @param string $sSearch Para buscar
     * @author Marcel
     */
    public function TotalVideos($sSearch) {
        $total = $this->getDoctrine()->getRepository('IcanBundle:Video')->TotalVideos($sSearch);

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
