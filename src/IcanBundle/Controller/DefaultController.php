<?php

namespace IcanBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use IcanBundle\Entity;
use IcanBundle\Util;

class DefaultController extends BaseController
{

    public function indexAction()
    {

        $ultimos = $this->getDoctrine()->getRepository('IcanBundle:Producto')
            ->ListarProductosOrdenados(0, 3);

        $masvisitados = $this->getDoctrine()->getRepository('IcanBundle:Producto')
            ->ListarProductosMasVistas();

        return $this->render('IcanBundle:Default:index.html.twig', array(
            'ultimos' => $ultimos,
            'masvisitados' => $masvisitados,
        ));
    }

    public function renderHeaderAction()
    {
        $usuario = $this->getUser();
        $mensajes = $this->ListarMensajesUltimosDias();


        return $this->render('@Ican/Layout/header.html.twig', array(
            'usuario' => $usuario,
            'mensajes' => $mensajes
        ));
    }
    public function renderMenuAction()
    {
        $usuario = $this->getUser();

        return $this->render('@Ican/Layout/menu.html.twig', array(
            'usuario' => $usuario
        ));
    }
}
