<?php

namespace FrontendBundle\Controller;

use MakerLabs\PagerBundle\Adapter\ArrayAdapter;
use MakerLabs\PagerBundle\Pager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use IcanBundle\Entity;

class DefaultController extends BaseController
{

    public function indexAction()
    {
        //Metaetiquetas para la pagina principal
        $url = 'homefrontend';
        $seo_on_page = $this->getDoctrine()->getRepository('IcanBundle:SeoOnPage')->findOneBy(array('url' => $url));

        //Sliders
        $sliders = $this->ListarSliders();
        //Redes Sociales
        $redsocial = $this->ListarRedesSociales();
        //Ultimos Productos
        $ultimos = $this->ListarUltimosProductos();
        //Categorias
        $categorias = $this->ListarCategorias();
        //Marcas
        $marcas = $this->ListarMarcasRandom();

        return $this->render('FrontendBundle:Default:index.html.twig', array(
            'seo_on_page' => $seo_on_page,
            'categorias' => $categorias,
            'categoria_activa' => 0,
            'marcas' => $marcas,
            'redsocial' => $redsocial,
            'sliders' => $sliders,
            'ultimos' => $ultimos
        ));
    }

    public function error404Action()
    {
        //Metaetiquetas para la pagina principal
        $url = 'error404';
        $seo_on_page = $this->getDoctrine()->getRepository('IcanBundle:SeoOnPage')->findOneBy(array('url' => $url));

        //Redes Sociales
        $redsocial = $this->ListarRedesSociales();
        //Categorias
        $categorias = $this->ListarCategorias();

        return $this->render('TwigBundle:Exception:error404.html.twig', array(
                'seo_on_page' => $seo_on_page,
                'categorias' => $categorias,
                'categoria_activa' => 0,
                'redsocial' => $redsocial,
            )
        );
    }

    public function quienessomosAction()
    {
        //Redes Sociales
        $redsocial = $this->ListarRedesSociales();
        //Categorias
        $categorias = $this->ListarCategorias();

        $pagina = $this->getDoctrine()->getRepository('IcanBundle:QuienesSomos')
            ->DevolverQuienesSomos();

        return $this->render('FrontendBundle:Default:quienessomos.html.twig', array(
                'categorias' => $categorias,
                'categoria_activa' => 0,
                'redsocial' => $redsocial,
                'pagina' => $pagina
            )
        );
    }

    public function serviciosAction()
    {
        //Redes Sociales
        $redsocial = $this->ListarRedesSociales();
        //Categorias
        $categorias = $this->ListarCategorias();

        $pagina = $this->ListarPaginaServicios();

        return $this->render('FrontendBundle:Default:servicios.html.twig', array(
                'categorias' => $categorias,
                'categoria_activa' => 0,
                'redsocial' => $redsocial,
                'pagina' => $pagina
            )
        );
    }


    public function contactoAction()
    {
        //Metaetiquetas para la pagina principal
        $url = 'contacto';
        $seo_on_page = $this->getDoctrine()->getRepository('IcanBundle:SeoOnPage')->findOneBy(array('url' => $url));

        //Redes Sociales
        $redsocial = $this->ListarRedesSociales();
        //Categorias
        $categorias = $this->ListarCategorias();

        return $this->render('FrontendBundle:Default:contacto.html.twig', array(
                'categorias' => $categorias,
                'categoria_activa' => 0,
                'redsocial' => $redsocial,
                'seo_on_page' => $seo_on_page,
            )
        );
    }

    public function procesarcontactoAction(Request $request)
    {
        $nombre = $request->get('nombre');
        $email = $request->get('email');
        $telefono = $request->get('telefono');
        $asunto = $request->get('asunto');
        $mensaje = $request->get('mensaje');

        $this->ProcesarContacto($nombre, $email, $telefono, $asunto, $mensaje);

        return new Response(1);
    }


    public function ListarPaginaServicios()
    {
        $arreglo_resultado = array();

        $pagina = $this->getDoctrine()->getRepository('IcanBundle:Servicio')
            ->DevolverServicio();
        if ($pagina != null) {
            $servicio_id = $pagina->getServicioId();

            $arreglo_resultado['titulo'] = $pagina->getTitulo();
            $arreglo_resultado['descripcion'] = $pagina->getDescripcion();
            $arreglo_resultado['tags'] = $pagina->getTags();

            //Imagenes del servicio
            $servicio_imagenes = $this->getDoctrine()->getRepository('IcanBundle:ServicioImagen')
                ->ListarImagenes($servicio_id);
            $imagenes = array();
            $cont = 0;
            foreach ($servicio_imagenes as $servicio_imagen) {
                $imagen = $servicio_imagen->getImagen();

                $imagenes[$cont]['imagen'] = $imagen;
                $cont++;
            }
            $arreglo_resultado['imagenes'] = $imagenes;
        }

        return $arreglo_resultado;
    }


    public function ProcesarContacto($nombre, $email, $telefono, $subject, $comentario)
    {
        //Notificar por email al usuario
        $direccion_url = $this->ObtenerURL();

        $direccion_from = $this->container->getParameter('mailer_sender_address');

        $asunto = "Contacto, Ican";

        $contenido = "Su mensaje ha sido enviado exitosamente. Nos pondremos en contacto con usted a la brevedad.
                    Este mensaje ha sido generado automáticamente por nuestro sistema. Por favor no contestar.";

        $mensaje = new \Swift_Message();
        $mensaje->setSubject($asunto)
            ->setFrom($direccion_from)
            ->setTo($email)
            ->setBody($this->renderView('FrontendBundle:Mailing:mail.html.twig', array(
                'direccion_url' => $direccion_url,
                'asunto' => $asunto,
                'receptor' => $nombre,
                'contenido' => $contenido,
            )), 'text/html');

        $this->get('mailer')->send($mensaje);


        //Notificar por email a los admin
        $contenido = 'Ha recibido un mensaje de contacto de: <br>
                      Nombre: ' . $nombre . '<br>
                      Email: ' . $email . '  <br>
                      Telefono: ' . $telefono . '  <br>
                      Asunto: ' . $subject . '<br>                      
                      Mensaje: ' . $comentario . '<br>';

        $administradores = $this->getDoctrine()->getRepository('IcanBundle:Usuario')
            ->ListarUsuariosRol(1);
        foreach ($administradores as $value) {
            $mensaje_admin = new \Swift_Message();
            $mensaje_admin->setSubject($asunto)
                ->setFrom($direccion_from)
                ->setTo($value->getEmail())
                ->setBody($this->renderView('FrontendBundle:Mailing:mail.html.twig', array(
                    'direccion_url' => $direccion_url,
                    'asunto' => $asunto,
                    'receptor' => $value->getNombreCompleto(),
                    'contenido' => $contenido,
                )), 'text/html');

            $this->get('mailer')->send($mensaje_admin);
        }

        //Salvar en bd
        $em = $this->getDoctrine()->getManager();

        $entity = new Entity\Mensaje();
        $entity->setNombre($nombre);
        $entity->setTelefono($telefono);
        $entity->setAsunto($subject);
        $entity->setDescripcion($comentario);
        $entity->setEmail($email);
        $entity->setFecha(new \DateTime());

        $em->persist($entity);
        $em->flush();
    }

}
