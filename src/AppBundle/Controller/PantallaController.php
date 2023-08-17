<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Turnos;
use Proxies\__CG__\AppBundle\Entity\ColaTurnos;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Boxes;
use AppBundle\Entity\Tramites;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class PantallaController extends Controller
{

    /**
     * @Route("/grid_pantalla", name="grid_pantalla")
     */
    public function grid_pantallaAction(Request $request)
    {
        return $this->render('pantalla/grid_pantalla.html.twig');
    }

    /**
     * @Route("/BoxConsulta", name="BoxConsulta", )
     */
    public function BoxConsultaAction(Request $request){
        //=================================================================================
        //BUSCA LOS ULTIMOS TICKETS ATENDIDOS
        //=================================================================================
        $fecha_hoy   = new \DateTime();
        $f_hoy       = $fecha_hoy->format('d/m/Y H:i');
        $f_hoyd      = $fecha_hoy->format('d/m/Y');
        $f_hoyh      = $fecha_hoy->format('H:i');

        //AND  rgturno.trFechahoraTurno >= '$today'
        $query_box  = $this->getDoctrine()->getEntityManager()->createQuery("SELECT rpd.boxNumero, rpd.boxEstado, rpd.boxSonido, rpd.boxId, rgturno.trTicket, rgturno.trTipoTramite, rgturno.trMotivo, rgturno.trEstadoTurno FROM AppBundle:Turnos rgturno JOIN AppBundle:Boxes rpd WHERE rpd.boxIdturno = rgturno.trIdTurno AND (rpd.boxEstado='S' or rpd.boxEstado='A' ) order by rgturno.trFechahoraTurno DESC"); //or rpd.boxEstado='A'
        $query_box->setMaxResults(8);
        $rpbox  = $query_box->getResult();

        $output[]=array();
        foreach ($rpbox as $datos_p){
            //------------------------------
            //crea el array con los datos

            $b_numero   = $datos_p['boxNumero'];//->getBoxNumero();
            $b_estado   = $datos_p['boxEstado'];//->getBoxEstado();
            $b_sonido   = $datos_p['boxSonido'];//->getBoxSonido();
            $b_id       = $datos_p['boxId'];//->getBoxId();
            $b_tk       = $datos_p['trTicket'];//>getTrTicket();
            $b_letra    = substr($b_tk,0,1);
            $b_nro      = substr($b_tk,1);
            $b_tipo     = $datos_p['trTipoTramite'];
            $b_estadot  = $datos_p['trEstadoTurno'];

            //BUSCA TIPO TRAMITE
            $tramite = $this->getDoctrine()->getRepository(Tramites::class)->findByTraIdTramite($b_tipo);
            if (count($tramite)>0){
                $tipo_n    = trim($tramite[0]->getDescripcion());
            }

            /*$b_tk = strtolower($b_tk);

            $letra_ticket   = substr($b_tk,0,1);
            $nro_ticket     = substr($b_tk,1,2);

            if ($nro_ticket == '01'){
                $n_ticket       = $letra_ticket . ' ' . $nro_ticket;
            }else{
                $n_ticket       = strtolower($letra_ticket ). $nro_ticket;
            }*/




            $output[]   = array("b_numero" => $b_numero, "b_estado" => $b_estado,  "b_sonido" => $b_sonido, "b_id" => $b_id , "b_letra" => $b_letra, "b_nro" => $b_nro, "tipo_n" => $tipo_n, "f_fecha" => $f_hoyd, "f_fechah" => $f_hoyh, "estado_turno" => $b_estadot);//, "f_hora" => $f_hora
        }//end foreach


        return new JsonResponse($output);
    }//end boxConsulta

    /**
     * @Route("/BoxActualizaSonido", name="BoxActualizaSonido", )
     */
    public function BoxActualizaSonidoAction(Request $request){
        //=================================================================================
        //ACTULIZA SONIDO DEL BOX
        //=================================================================================
        $p_id = $request->get("box_id");

        $output[] =array();
        $b_sonido ="N";
        $rg_box = $this->getDoctrine()->getRepository(Boxes::class)->findOneByboxId($p_id);

        if ($rg_box != null) {
            $b_sonido="S";
            $rg_box->setBoxSonido('N');
            // Persisto el objeto en la base de datos
            $this->getDoctrine()->getManager()->flush();
        }//end if

        $output[]= array("b_sonido" => $b_sonido);
        return new JsonResponse($output);
    }//end BoxActualizaSonido


    /**
     * @Route("/ColaConsulta", name="ColaConsulta", )
     */
    public function ColaConsultaAction(Request $request){
        //=================================================================================
        //LEE LA COLA DE TURNOS PARA ARMAR EN PANTALLA'
        //=================================================================================
        $fecha_hoy   = new \DateTime();
        $f_hoyd      = $fecha_hoy->format('d/m/Y');

        //---------------------------------------------------
        //ACTULIZA LA HORA ACTUAL
        $f_hoyh      = $fecha_hoy->format('H:i');
        $cant        = 0;
        $output[]    = array();


        if ($cant == 0){
            $output[]   = array("cola_ticket" => '-', "f_fechad" => $f_hoyd, "f_fechah" => $f_hoyh);
        }

        return new JsonResponse($output);
    }//end Cola Consulta

}