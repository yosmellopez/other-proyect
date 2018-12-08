<?php

namespace IcanBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use IcanBundle\Entity;

class QuienesSomosController extends BaseController
{
    public function indexAction()
    {

        $pagina = $this->getDoctrine()->getRepository('IcanBundle:QuienesSomos')
            ->DevolverQuienesSomos();

        return $this->render('IcanBundle:QuienesSomos:index.html.twig', array(
            'pagina' => $pagina,
        ));
    }

    /**
     * salvarAction Acción que salva los datos de la pagina quienes somos en la BD
     *
     */
    public function salvarAction(Request $request)
    {
        $pagina_id = $request->get('pagina_id');

        $titulo = $request->get('titulo');
        $descripcion = $request->get('descripcion');
        $tags = $request->get('tags');
        $imagen = $request->get('imagen');

        $resultadoJson = array();
        $resultado = $this->SalvarPagina($pagina_id, $titulo, $descripcion, $tags, $imagen);

        if ($resultado['success']) {

            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['pagina_id'] = $resultado['pagina_id'];
            $resultadoJson['message'] = "La operación se realizó correctamente";

            return new Response(json_encode($resultadoJson));
        } else {
            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['error'] = $resultado['error'];
            return new Response(json_encode($resultadoJson));
        }
    }

    public function salvarImagenAction()
    {
        try {
            $nombre_archivo = $_FILES['foto']['name'];
            $array_nombre_archivo = explode('.', $nombre_archivo);
            $pos = count($array_nombre_archivo) - 1;
            $extension = $array_nombre_archivo[$pos];

            $archivo = $this->generarCadenaAleatoria() . '.' . $extension;

            //Manejar la imagen
            $dir = 'uploads/quienes-somos/';
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
    public function eliminarImagenAction(Request $request)
    {
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
     * cargarDatosAction Acción que carga los datos del usuario en la BD
     *
     */
    public function cargarDatosAction(Request $request)
    {
        $pagina_id = $request->get('pagina_id');

        $resultado = $this->CargarDatosPagina($pagina_id);
        if ($resultado['success']) {

            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['pagina'] = $resultado['pagina'];
            return new Response(json_encode($resultadoJson));
        } else {
            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['error'] = $resultado['error'];
            return new Response(json_encode($resultadoJson));
        }
    }

    /**
     * CargarDatosPagina: Carga los datos de un usuario
     *
     * @param int $pagina_id Id
     *
     * @author Marcel
     */
    public function CargarDatosPagina($pagina_id)
    {
        $resultado = array();
        $arreglo_resultado = array();

        $entity = $this->getDoctrine()->getRepository('IcanBundle:QuienesSomos')
            ->find($pagina_id);
        if ($entity != null) {
            $arreglo_resultado['titulo'] = $entity->getTitulo();
            $arreglo_resultado['descripcion'] = $entity->getDescripcion();
            $arreglo_resultado['tags'] = $entity->getTags();

            $imagen = $entity->getImagen();
            $ruta = $this->ObtenerURL();
            $dir = 'uploads/quienes-somos/';
            $ruta = $ruta . $dir;
            $size = (is_file($dir . $imagen)) ? filesize($dir . $imagen) : 0;
            $arreglo_resultado['imagen'] = array($imagen, $size, $ruta);


            $resultado['success'] = true;
            $resultado['pagina'] = $arreglo_resultado;
        } else {
            $resultado['success'] = true;
            $resultado['pagina'] = false;
        }
        return $resultado;
    }


    /**
     * EliminarImagen: Elimina una imagen de un noticia en la BD
     * @param int $noticia_id Id
     * @author Marcel
     */
    public function EliminarImagen($imagen)
    {
        $resultado = array();
        //Eliminar foto       
        if ($imagen != "") {
            $dir = 'uploads/quienes-somos/';
            if (is_file($dir . $imagen)) {
                unlink($dir . $imagen);
            }
        }

        $entity = $this->getDoctrine()->getRepository('IcanBundle:QuienesSomos')
            ->findOneBy(array('imagen' => $imagen));
        if ($entity != null) {
            $em = $this->getDoctrine()->getManager();
            $entity->setImagen("");
            $em->flush();
        }

        $resultado['success'] = true;
        return $resultado;
    }

    /**
     * SalvarPagina: Guarda los datos del usuario en la BD
     *
     *
     * @author Marcel
     */
    public function SalvarPagina($pagina_id, $titulo, $descripcion, $tags, $imagen)
    {
        $em = $this->getDoctrine()->getManager();

        $resultado = array();

        $entity = $this->getDoctrine()->getRepository('IcanBundle:QuienesSomos')->find($pagina_id);
        $is_new = false;
        if ($entity == null) {
            $entity = new Entity\QuienesSomos();
            $is_new = true;
        }

        $entity->setTitulo($titulo);
        $entity->setDescripcion($descripcion);
        $entity->setTags($tags);

        if ($is_new) {
            $em->persist($entity);
        }
        $em->flush();

        //Salvar imagen
        $pagina_id = $entity->getPaginaId();
        $imagen = $this->RenombrarImagen($pagina_id, $imagen);
        $entity->setImagen($imagen);

        $em->flush();

        $resultado['success'] = true;
        $resultado['pagina_id'] = $pagina_id;

        return $resultado;
    }

    /**
     * RenombrarImagen: Renombra la imagen en la BD
     *
     * @author Marcel
     */
    public function RenombrarImagen($id, $imagen)
    {
        $dir = 'uploads/quienes-somos/';
        $empresa = "ican";
        $tipo = "quienes-somos";
        $imagen_new = "";

        if ($imagen != "") {
            $extension_array = explode('.', $imagen);
            $extension = $extension_array[1];

            //Imagen nueva
            $imagen_new = $empresa . '-' . $tipo . '-' . $id . '.' . $extension;
            if (is_file($dir . $imagen)) {
                //Renombrar imagen
                rename($dir . $imagen, $dir . $imagen_new);
            }
        }

        return $imagen_new;
    }
}
