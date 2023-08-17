<?php

namespace AppBundle\Controller;
use AppBundle\AppBundle;
use AppBundle\Entity\Tramites;
use AppBundle\Entity\TramitesMotivos;
use AppBundle\Entity\Tareas;
use AppBundle\Entity\Menues;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class TurnosController extends Controller
{
    /**
     * @Route("/iniciarBox", name="iniciarBox")
     */
    public function iniciarBoxAction(Request $request)
    {

        $idUser  = $request->getSession()->get("usuarioLogeado");
        $modulos = $this->recuperarModulosUsuario($idUser);


        $query   = $this->getDoctrine()->getEntityManager()->createQuery("select ub from AppBundle:UsuariosBox ub where ub.usbxIdus= $idUser and ub.usbxNrobox not in (select bo.boxNumero from AppBundle:Boxes bo where bo.boxEstado='S') and ub.usbxVigente='S' and  ub.usbxDefault='N' and ub.usbxIdus not in (select b.boxUsuario from AppBundle:Boxes b where b.boxEstado='S') order by ub.usbxNrobox" );
        $rg_ub   = $query->getResult();
        if (count($rg_ub)>0){

            return $this->render('Turnos/reemplazoBox.html.twig', array('modulos' => $modulos,"boxes" => $rg_ub,"mensaje"=> ""));

        }else{
            //die('IniciarBox_noEntro');
            return $this->render('Turnos/reemplazoBox.html.twig', array('modulos' => $modulos,"boxes" => $rg_ub,"mensaje"=> "No hay Box disponibles o su usuario no tiene permisos. Por favor, comuníquese con su administrador.-"));
        }
    }

}
