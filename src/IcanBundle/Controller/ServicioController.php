<?php

namespace IcanBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use IcanBundle\Entity;

class ServicioController extends BaseController
{

    public function indexAction()
    {

        $servicio = $this->getDoctrine()->getRepository('IcanBundle:Servicio')
            ->DevolverServicio();

        return $this->render('IcanBundle:Servicio:index.html.twig', array(
            'servicio' => $servicio,
        ));
    }

    /**
     * salvarAction Acción que salva los datos de la servicio quienes somos en la BD
     *
     */
    public function salvarAction(Request $request)
    {
        $servicio_id = $request->get('servicio_id');

        $titulo = $request->get('titulo');
        $descripcion = $request->get('descripcion');
        $tags = $request->get('tags');
        $imagenes = $request->get('imagenes');

        $resultadoJson = array();
        $resultado = $this->SalvarServicio($servicio_id, $titulo, $descripcion, $tags, $imagenes);

        if ($resultado['success']) {

            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['servicio_id'] = $resultado['servicio_id'];
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
            $dir = 'uploads/servicios/';
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
        $servicio_id = $request->get('servicio_id');

        $resultado = $this->CargarDatosServicio($servicio_id);
        if ($resultado['success']) {

            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['servicio'] = $resultado['servicio'];
            return new Response(json_encode($resultadoJson));
        } else {
            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['error'] = $resultado['error'];
            return new Response(json_encode($resultadoJson));
        }
    }

    /**
     * CargarDatosServicio: Carga los datos de un usuario
     *
     * @param int $servicio_id Id
     *
     * @author Marcel
     */
    public function CargarDatosServicio($servicio_id)
    {
        $resultado = array();
        $arreglo_resultado = array();

        $entity = $this->getDoctrine()->getRepository('IcanBundle:Servicio')
            ->find($servicio_id);
        if ($entity != null) {
            $arreglo_resultado['titulo'] = $entity->getTitulo();
            $arreglo_resultado['descripcion'] = $entity->getDescripcion();
            $arreglo_resultado['tags'] = $entity->getTags();

            $ruta = $this->ObtenerURL();
            $dir = 'uploads/servicios/';
            $ruta = $ruta . $dir;

            //Imagenes del servicio
            $servicio_imagenes = $this->getDoctrine()->getRepository('IcanBundle:ServicioImagen')
                ->ListarImagenes($servicio_id);
            $imagenes = array();
            $cont = 0;
            foreach ($servicio_imagenes as $servicio_imagen) {
                $imagen = $servicio_imagen->getImagen();
                $size = (is_file($dir . $imagen)) ? filesize($dir . $imagen) : 0;
                $imagenes[$cont]['imagen'] = array($imagen, $size, $ruta);
                $cont++;
            }
            $arreglo_resultado['imagenes'] = $imagenes;


            $resultado['success'] = true;
            $resultado['servicio'] = $arreglo_resultado;
        } else {
            $resultado['success'] = true;
            $resultado['servicio'] = false;
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
            $dir = 'uploads/servicios/';
            if (is_file($dir . $imagen)) {
                unlink($dir . $imagen);
            }
        }

        $entity = $this->getDoctrine()->getRepository('IcanBundle:ServicioImagen')
            ->findOneBy(array('imagen' => $imagen));
        if ($entity != null) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($entity);
            $em->flush();
        }

        $resultado['success'] = true;
        return $resultado;
    }

    /**
     * SalvarServicio: Guarda los datos del usuario en la BD
     *
     * @author Marcel
     */
    public function SalvarServicio($servicio_id, $titulo, $descripcion, $tags, $imagenes)
    {
        $em = $this->getDoctrine()->getManager();

        $resultado = array();

        $entity = $this->getDoctrine()->getRepository('IcanBundle:Servicio')->find($servicio_id);
        $is_new = false;
        if ($entity == null) {
            $entity = new Entity\Servicio();
            $is_new = true;
        }

        $entity->setTitulo($titulo);
        $entity->setDescripcion($descripcion);
        $entity->setTags($tags);

        if ($is_new) {
            $em->persist($entity);
        }
        $em->flush();

        if ($imagenes != "") {
            $imagenes = explode(',', $imagenes);
            $cont = 1;
            foreach ($imagenes as $value) {
                if ($value != "") {

                    $servicio_imagen = $this->getDoctrine()->getRepository('IcanBundle:ServicioImagen')
                        ->findOneBy(
                            array('imagen' => $value)
                        );
                    if ($servicio_imagen == null) {
                        $value = $this->RenombrarImagen($servicio_id, $value, $cont);

                        $servicioimagen = new Entity\ServicioImagen();

                        $servicioimagen->setServicio($entity);
                        $servicioimagen->setImagen($value);

                        $em->persist($servicioimagen);
                    }
                    $cont++;
                }
            }
        }

        $em->flush();

        $resultado['success'] = true;
        $resultado['servicio_id'] = $servicio_id;

        return $resultado;
    }

    /**
     * RenombrarImagen: Renombra la imagen en la BD
     *
     * @author Marcel
     */
    public function RenombrarImagen($id, $imagen, $cont = 0)
    {
        $dir = 'uploads/servicios/';
        $empresa = "ican";
        $tipo = "servicio";
        $imagen_new = "";

        if ($imagen != "") {
            $extension_array = explode('.', $imagen);
            $extension = $extension_array[1];


            if ($cont == 0) {
                //Imagen nueva
                $imagen_new = $empresa . '-' . $tipo . '-' . $id . '.' . $extension;
                if (is_file($dir . $imagen)) {
                    //Renombrar imagen
                    rename($dir . $imagen, $dir . $imagen_new);
                }
            } else {
                //Imagen nueva
                $imagen_new = $empresa . '-' . $tipo . '-' . $id . '-' . $cont . '.' . $extension;
                if (is_file($dir . $imagen)) {
                    //Renombrar imagen
                    rename($dir . $imagen, $dir . $imagen_new);
                }
            }
        }

        return $imagen_new;
    }
}
