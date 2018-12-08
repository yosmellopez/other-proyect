<?php

namespace IcanBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use IcanBundle\Entity;

class FuncionController extends BaseController {

    const FUNCION_ID = 2;

    public function indexAction() {

        $usuario = $this->get('security.context')->getToken()->getUser();
        $autenticado = false;
        if (is_object($usuario))
            $autenticado = true;

        $menu = $this->Menu($usuario->getUsuarioId());
        $permisos = $this->ListarPermisosUsuario($usuario->getUsuarioId(), self::FUNCION_ID);

        $funciones = $this->ListarFunciones();

        $mensajes = $this->ListarMensajesUltimosDias();
        $total_mensajes = count($mensajes);

        if ($permisos[0]['ver'] == 1) {
            return $this->render('IcanBundle:Funcion:index.html.twig', array(
                        'autenticado' => $autenticado,
                        'menu' => $menu,
                        'funcionalidades' => $funciones,
                        'permisos' => $permisos,
                        'mensajes' => $mensajes,
                        'total_mensajes' => $total_mensajes
            ));
        } else {
            return $this->render('IcanBundle:Usuario:denegado.html.twig', array(
                        'autenticado' => $autenticado,
                        'menu' => $menu,
                        'permisos' => $permisos,
                        'mensajes' => $mensajes,
                        'total_mensajes' => $total_mensajes
            ));
        }
    }

    /**
     * salvarMenuAction Acción que inserta un funcion menu en la BD
     * 
     */
    public function salvarMenuAction() {
        $request = $this->getRequest();


        $descripcion = $request->request->get('descripcion');
        try {
            $funcion_id = $this->SalvarMenu($descripcion);

            $resultadoJson['success'] = true;
            $resultadoJson['message'] = "La operación se realizó correctamente";
            $resultadoJson['funcion_id'] = $funcion_id;
            $resultadoJson['descripcion'] = $descripcion;
            return new Response(json_encode($resultadoJson));
        } catch (Exception $e) {
            $resultadoJson['success'] = false;
            $resultadoJson['error'] = $e->getMessage();

            return new Response(json_encode($resultadoJson));
        }
    }

    /**
     * salvarFuncionAction Acción que inserta un funcion en la BD
     * 
     */
    public function salvarFuncionAction() {
        $request = $this->getRequest();

        $menu_id = $request->request->get('menu_id');
        $descripcion = $request->request->get('descripcion');

        try {
            $funcion_id = $this->SalvarFuncion($menu_id, $descripcion);

            $resultadoJson['success'] = true;
            $resultadoJson['message'] = "La operación se realizó correctamente";
            $resultadoJson['funcion_id'] = $funcion_id;
            $resultadoJson['menu_id'] = $menu_id;
            $resultadoJson['descripcion'] = $descripcion;
            return new Response(json_encode($resultadoJson));
        } catch (Exception $e) {
            $resultadoJson['success'] = false;
            $resultadoJson['error'] = $e->getMessage();

            return new Response(json_encode($resultadoJson));
        }
    }

    /**
     * actualizarMenuAction Acción que inserta un funcion en la BD
     * 
     */
    public function actualizarMenuAction() {
        $request = $this->getRequest();

        $funcion_id = $request->request->get('menu_id');
        $descripcion = $request->request->get('descripcion');


        try {
            $this->ActualizarMenu($funcion_id, $descripcion);

            $resultadoJson['success'] = true;
            $resultadoJson['message'] = "La operación se realizó correctamente";
            $resultadoJson['funcion_id'] = $funcion_id;
            $resultadoJson['descripcion'] = $descripcion;

            return new Response(json_encode($resultadoJson));
        } catch (Exception $e) {
            $resultadoJson['success'] = false;
            $resultadoJson['error'] = $e->getMessage();

            return new Response(json_encode($resultadoJson));
        }
    }

    /**
     * actualizarFuncionAction Acción que inserta un funcion en la BD
     * 
     */
    public function actualizarFuncionAction() {
        $request = $this->getRequest();

        $funcion_id = $request->request->get('funcion_id');
        $descripcion = $request->request->get('descripcion');

        try {
            $funcion = $this->ActualizarFuncion($funcion_id, $descripcion);

            $resultadoJson['success'] = true;
            $resultadoJson['message'] = "La operación se realizó correctamente";
            $resultadoJson['funcion_id'] = $funcion_id;
            $resultadoJson['menu_id'] = $funcion->getMenu()->getFuncionId();
            $resultadoJson['descripcion'] = $descripcion;

            return new Response(json_encode($resultadoJson));
        } catch (Exception $e) {
            $resultadoJson['success'] = false;
            $resultadoJson['error'] = $e->getMessage();

            return new Response(json_encode($resultadoJson));
        }
    }

    /**
     * eliminarAction Acción que elimina un funcion en la BD
     * 
     */
    public function eliminarAction() {
        $request = $this->getRequest();

        $funcion_id = $request->request->get('funcion_id');

        try {
            $resultado = $this->EliminarFuncion($funcion_id);

            return new Response(json_encode($resultado));
        } catch (Exception $e) {
            $resultadoJson['success'] = false;
            $resultadoJson['error'] = $e->getMessage();

            return new Response(json_encode($resultadoJson));
        }
    }

    /**
     * EliminarFuncion: Elimina una funcion en la BD
     * @param int $funcion_id Id de la funcion     
     * @author Marcel
     */
    public function EliminarFuncion($funcion_id) {
        $resultado = array();
        $em = $this->getDoctrine()->getManager();

        $funcion = $this->getDoctrine()->getRepository('IcanBundle:Funcion')
                ->find($funcion_id);

        if ($funcion != null) {
            //Subfunciones
            $subfunciones = $this->getDoctrine()->getRepository('IcanBundle:Funcion')
                    ->ListarFuncionesDeMenu($funcion_id);
            if (count($subfunciones) > 0) {
                $resultado['success'] = false;
                $resultado['error'] = "No se pudo eliminar la funcion, porque se encuenta vinculada a otras funciones";
                return $resultado;
            }
            //Permisos Rol
            $permisos_rol = $this->getDoctrine()->getRepository('IcanBundle:PermisoRol')
                    ->ListarPermisosFuncion($funcion_id);
            foreach ($permisos_rol as $permiso)
                $em->remove($permiso);

            //Permisos Usuario
            $permisos_usuario = $this->getDoctrine()->getRepository('IcanBundle:PermisoUsuario')
                    ->ListarPermisosFuncion($funcion_id);
            foreach ($permisos_usuario as $permiso)
                $em->remove($permiso);

            $em->remove($funcion);
            $em->flush();
            $resultado['success'] = true;
            $resultado['message'] = "La operación se realizó correctamente";
            return $resultado;
        }
    }

    /**
     * ActualizarMenu: Actuializa los datos de un menu en la BD
     * @param int $funcion_id Id del funcion          
     * @param string $descripcion Descripcion del funcion         
     * @author Marcel
     */
    public function ActualizarMenu($funcion_id, $descripcion) {
        $em = $this->getDoctrine()->getManager();

        $entity = $this->getDoctrine()->getRepository('IcanBundle:Funcion')
                ->find($funcion_id);

        if ($entity != null) {
            $entity->setDescripcion($descripcion);
            $em->flush();
        }
    }

    /**
     * ActualizarFuncion: Actuializa los datos de la funcion en la BD
     * @param int $funcion_id Id del funcion       
     * @param string $descripcion Descripcion del funcion      
     * @author Marcel
     */
    public function ActualizarFuncion($funcion_id, $descripcion) {
        $em = $this->getDoctrine()->getManager();

        $entity = $this->getDoctrine()->getRepository('IcanBundle:Funcion')
                ->find($funcion_id);

        if ($entity != null) {
            $entity->setDescripcion($descripcion);
            $em->flush();
        }
        return $entity;
    }

    /**
     * SalvarMenu: Guarda los datos del menu en la BD       
     * @param string $descripcion Descripcion del funcion       
     * @author Marcel
     */
    public function SalvarMenu($descripcion) {
        $em = $this->getDoctrine()->getManager();

        $entity = new Entity\Funcion();
        $entity->setDescripcion($descripcion);

        $em->persist($entity);
        $em->flush();
        return $entity->getFuncionId();
    }

    /**
     * SalvarFuncion: Guarda los datos de la funcion en la BD   
     * @param int $menu_id Menu   
     * @param string $descripcion Descripcion del funcion       
     * @author Marcel
     */
    public function SalvarFuncion($menu_id, $descripcion) {
        $em = $this->getDoctrine()->getManager();


        $entity = new Entity\Funcion();
        $entity->setDescripcion($descripcion);

        $menu = $em->find('IcanBundle:Funcion', $menu_id);
        if ($menu != null)
            $entity->setMenu($menu);

        $em->persist($entity);
        $em->flush();
        return $entity->getFuncionId();
    }

    /**
     * ListarFunciones: Lista las funciones de la BD         
     * @author Marcel
     */
    public function ListarFunciones() {
        $arreglo_resultado = array();
        $cont = 0;
        $lista = $this->getDoctrine()->getRepository('IcanBundle:Funcion')
                ->findAll();
        foreach ($lista as $value) {
            $funcion_id = $value->getFuncionId();
            $descripcion = $value->getDescripcion();
            $menu = $value->getMenu();
            if ($menu == null) {
                $funciones = $this->getDoctrine()->getRepository('IcanBundle:Funcion')
                        ->ListarFuncionesDeMenu($funcion_id);

                $arreglo_resultado[$cont]['funcion_id'] = $funcion_id;
                $arreglo_resultado[$cont]['descripcion'] = $descripcion;

                $funciones_hijas_array = array();
                $cont_hijas = 0;
                foreach ($funciones as $hija) {
                    $funciones_hijas_array[$cont_hijas]['funcion_id'] = $hija->getFuncionId();
                    $funciones_hijas_array[$cont_hijas]['descripcion'] = $hija->getDescripcion();

                    $cont_hijas++;
                }
                $arreglo_resultado[$cont]['funciones_hijas'] = $funciones_hijas_array;

                $cont++;
            }
        }

        return $arreglo_resultado;
    }    

}
