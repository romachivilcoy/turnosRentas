<?php

namespace AppBundle\Controller;

use AppBundle\AppBundle;
use AppBundle\Entity\ColaTurnos;
use AppBundle\Entity\Contador;
use AppBundle\Entity\Tickets;
use AppBundle\Entity\Turnos;
use AppBundle\Entity\Tramites;
use AppBundle\Entity\TramitesMotivos;
use AppBundle\Entity\Padron;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints\DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;


class TicketsController extends Controller
{

    /**
     * @Route("/inicioPantalla", name="inicioPantalla")
     */
    public function inicioPantallaAction(Request $request, $id = null)
    {
        return $this->render('tickets/inicioTk.html.twig');
    }

    /**
     * @Route("/documento", name="documento")
     */
    public function documentoAction(Request $request)
    {

        return $this->render('tickets/documento.html.twig', array('error_repedido' => 'NO'));
    }

    /**
     * @Route("/tramites/{dniIngresado}", name="tramites")
     */
    public function tramitesAction(Request $request, $dniIngresado = null)
    {

        $repetidoTurno ="NO";

        //VERIFICA SI EL DOCUMENTO YA ESTA CARGADO
        //SI TIENE TURNO - Y SI ESTA ,ATENDIDO O CANCELADO. PUEDE CARGAR - SI NO VUELVE A PANTALLA DE INICIO
        $rg_box = $this->getDoctrine()->getRepository(Turnos::class)->findBy(array("trNroDoc" => $dniIngresado), array("trIdTurno"  => 'DESC'));
        if ($rg_box != null) {
            foreach ($rg_box as $datos_r) {
                $estadoTurno = $datos_r->getTrEstadoTurno();
                if ($estadoTurno == 4 || $estadoTurno == 5){
                    $repetidoTurno="NO";
                }else{
                    $repetidoTurno="SI";
                    $nroTicket = $datos_r->getTrTicket();
                }
                break;
            }
        }//

        if ($repetidoTurno == "SI"){
            return $this->render('tickets/documento.html.twig',array('error_repedido' => $repetidoTurno,'nroTicket' => $nroTicket));
        }else{
            $arr_imponibles = $this->getDoctrine()->getRepository(Tramites::class)->findBy(array('vigente' => 'S'),array('traPrioridad' => 'ASC'));
            return $this->render('tickets/tramites.html.twig',array('arr_tramites' => $arr_imponibles, 'dniIngresado'=>$dniIngresado));
        }

    }

    /**
     * @Route("/AltaTicket", name="AltaTicket", )
     */
    public function AltaTicketIniAction(Request $request){
        //==================================================
        //ALTA TICKET
        //==================================================
        $tipo_tk        = $request ->get("tipo_tk");
        $motivo         = $request ->get("n_motivo");
        $dniIngresado   = $request ->get("dniIngresado");

        $fecha_hoy  = new \DateTime();

        $today      = $fecha_hoy->format('Y-m-d');
        $letra      ="H";
        $nro_tsel   ='0';

        //---------------------
        //BUSCA CONTATOR TICKETS
        $rg_contador = $this->getDoctrine()->getRepository(Contador::class)->findBy(array('contadorId' => '1'));
        if ($rg_contador != null) {
            foreach ($rg_contador as $rg_c) {
                $nro_contador = $rg_c->getContadorNumero();
                $contador_bandera = $rg_c->getContadorBandera();
            }

            $nro_contador -=1;
            if ($nro_contador < $contador_bandera){
                $cambiar_papel=1;
            }else{
                $cambiar_papel=0;
            }
            $rg_c->setContadorNumero($nro_contador);
            $rg_c->setContadorAviso($cambiar_papel);

            $entityManager = $this->getDoctrine()->getEntityManager();
            $entityManager->flush();
        }



        $query_f = $this->getDoctrine()->getEntityManager()->createQuery("SELECT tk FROM AppBundle:Tickets tk WHERE  tk.tkFecha >= '$today'" );
        $rg_tk   = $query_f->getResult();
        if (count($rg_tk)>0){
            foreach ($rg_tk as $datos_d){
                switch ($tipo_tk){
                    case 'P':
                        $nro_tsel= $datos_d->getTkInmueble();
                        break;
                    case 'C':
                        $nro_tsel= $datos_d->getTkComercio();
                        break;
                    case 'M':
                        $nro_tsel= $datos_d->getTkMoto();
                        break;
                    case 'T':
                        $nro_tsel= $datos_d->getTkCementerio();
                        break;
                    case 'E':
                        $nro_tsel= $datos_d->getTkEscribano();
                        break;
                    case 'J':
                        $nro_tsel= $datos_d->getTkJuicio();
                        break;
                    case 'V':
                        $nro_tsel= $datos_d->getTkVarios();
                        break;
                    case 'A':
                        $nro_tsel= $datos_d->getTkArba();
                        break;
                }//end SWITCH

                $n_ticket   =substr($nro_tsel,1,2);
                $n_ticket   = intval( $n_ticket)+1;
                if (strlen($n_ticket) == 1){
                    $n_ticket= '0'.$n_ticket;
                }
                /*---------------------
                ACTUALIZA EN TICKETS
                ----------------------*/
                $datos_d->setTkFecha(new \DateTime());

                switch ($tipo_tk){
                    case 'P':
                        $letra ='P'.$n_ticket;
                        $datos_d->setTkInmueble($letra);
                        break;
                    case 'C':
                        $letra ='C'.$n_ticket;
                        $datos_d->setTkComercio($letra);
                        break;
                    case 'M':
                        $letra ='M'.$n_ticket;
                        $datos_d->setTkMoto($letra);
                        break;
                    case 'T':
                        $letra ='T'.$n_ticket;
                        $datos_d->setTkCementerio($letra);
                        break;
                    case 'E':
                        $letra ='E'.$n_ticket;
                        $datos_d->setTkEscribano($letra);
                        break;
                    case 'J':
                        $letra ='J'.$n_ticket;
                        $datos_d->setTkJuicio($letra);
                        break;
                    case 'V':
                        $letra ='V'.$n_ticket;
                        $datos_d->setTkVarios($letra);
                        break;
                    case 'A':
                        $letra ='A'.$n_ticket;
                        $datos_d->setTkArba($letra);
                        break;
                }//end SWITCH
                $entityManager = $this->getDoctrine()->getEntityManager();
                $entityManager->flush();

            }//end foreach


            $tramite = $this->getDoctrine()->getRepository(Tramites::class)->findByTraTipoTramite($tipo_tk);
            if (count($tramite)>0){
                $idTramite    = $tramite[0]->getTraIdTramite();
            }


            $sigmu_sn = $this->getDoctrine()->getEntityManager('sigmu')->getRepository(Padron::class)->findOneBypdoc($dniIngresado);
            if (count($sigmu_sn) > 0) {
                $n_ape_nom= mb_convert_encoding(trim($sigmu_sn->getApellidoYNombre()), 'UTF-8', 'UTF-8');
            }else{
                $n_ape_nom  ="-";
            }

            /****************************
             * ALTA EN TURNO
             */
            $n_turnos = new Turnos();
            $n_turnos->setTrNroDoc($dniIngresado);
            $n_turnos ->setTrFechahoraTurno(new \DateTime());
            $n_turnos ->setTrFechahoraOrigen(new \DateTime());
            $n_turnos ->setTrEstadoTurno(1); //ESTADO PENDIENTE
            $n_turnos ->setTrTipoTramite($idTramite);
            $n_turnos ->setTrTicket($letra);
            //$n_turnos ->setTrMotivo($motivo);

            // Persisto el objeto en la base de datos
            $this->getDoctrine()->getManager()->persist($n_turnos);
            $this->getDoctrine()->getManager()->flush();

            $ultimo_id = $n_turnos->getTrIdTurno();
            /****************************
             * ALTA EN COLA
             */
            $n_cola = new ColaTurnos();
            $n_cola ->setClIdTurno($ultimo_id);
            $n_cola ->setClEstado('N');
            $n_cola ->setClColor('H00');
            $n_cola ->setClUbicacion(0);

            // Persisto el objeto en la base de datos
            $this->getDoctrine()->getManager()->persist($n_cola);
            $this->getDoctrine()->getManager()->flush();
        }

        $output[]=array("c_tramite" => 'listo', "c_nrotk" => $letra, "c_apeynom" => $n_ape_nom);

        return new JsonResponse($output);
        //==================================================
    }


    /**
     * @Route("/imprimirTicket", name="imprimirTicket", )
     */
    public function imprimirTicketAction(Request $request){
        /*/{nrotk}/{ttramite} , $nrotk = null, $ttramite= null*/
        $nrotk    = $request ->get("nrotk");
        $ttramite = $request ->get("ttramite");

        //==================================================
        $tramite="-";
        switch ($ttramite){
            case 'P':
                $tramite = 'INMUEBLES';
                break;
            case 'C':
                $tramite = 'COMERCIOS';
                break;
            case 'M':
                $tramite = 'RODADOS';
                break;
            case 'T':
                $tramite = 'CEMENTERIO';
                break;
            case 'E':
                $tramite = 'ESCRIBANO';
                break;
            case 'J':
                $tramite = 'JUICIO';
                break;
            case 'V':
                $tramite = 'VARIOS';
                break;
            case 'A':
                $tramite = 'ARBA';
                break;
        }

        //return $this->render('tickets/ticket.html.twig',array("nrotk" => $nrotk,"ttramite" => $tramite));

        /*$pdf = new \FPDF();

        $pdf->AddPage();
        $pdf->SetFont('Arial','B',16);
        $pdf->Cell(40,10,'Hello World!');
        $pdf->Output('hola.pdf', 'F');


        $param=('false');
        $script="print($param);";
        $this->IncludeJS($script);*/


        $pdf=new PDF_AutoPrint($orientation='P',$unit='mm', array(77,120));
        $pdf->AddPage();
        /*$pdf->SetFont('Arial','',20);
        $pdf->Text(90, 50, 'Print me!');*/
        $pdf->SetFont('Arial','',8);    //Letra Arial, negrita (Bold), tam. 20
        $textypos = 5;
        $pdf->setY(2);
        $pdf->setX(48);
        $pdf->Cell(5,$textypos,"22/05/2019 10:19");
        $pdf->SetFont('Arial','',10);
        $textypos += 12;
        $pdf->setY(2);
        $pdf->setX(2);
        $pdf->Cell(5,$textypos,"              DIRECCION DE RENTAS");
        $pdf->SetFont('Arial','',12);
        $textypos+=6;
        $pdf->setX(2);
        $pdf->Cell(5,$textypos,'--------------------------------------------------');
        $textypos+=6;
        $pdf->setX(2);
        $pdf->Cell(5,$textypos,'                      Nro.Ticket');
        $textypos+=6;
        $pdf->setX(2);
        $pdf->Cell(5,$textypos,'                  ');
        $pdf->SetFont('Arial','B',38);
        $textypos+=14;
        $pdf->setX(2);
        $pdf->Cell(5,$textypos,'      R04');
        $pdf->SetFont('Arial','',12);
        $textypos+=18;
        $pdf->setX(2);
        $pdf->Cell(5,$textypos,'                Tramite:Rodados');
        $pdf->SetFont('Arial','',12);
        $textypos+=6;
        $pdf->setX(2);
        $pdf->Cell(5,$textypos,'--------------------------------------------------');
        $textypos+=10;
        $pdf->setX(2);
        $pdf->Cell(5,$textypos,'          Municipalidad de Chivilcoy');
        //Open the print dialog
        $pdf->AutoPrint(false);
        //$pdf->Output('F','filas.pdf');
        $pdf->IncludeJS("this.print({bUI: false, bSilent: true, bShrinkToFit: true});");
        //$pdf->Output();
        $pdf->Output('foobar.pdf','I');

        //$pdf->IncludeJS("print('true');");

        //$pdf->Output('filas.pdf','I');
        //$pdf->Output();

        //return new Response($pdf->Output(), 200, array(  'Content-Type' => 'application/pdf'));

        //return new JsonResponse($pdf->Output(), 200, array(  'Content-Type' => 'application/pdf'));
        return new Response();
       //$output[]=array("c_tramite" => 'listo');

        //return new JsonResponse($pdf->Output(), 200, array(  'Content-Type' => 'application/pdf'));
       // $response = $this->forward('AppBundle\Controller\TicketsController::inicioPantallaAction');
       // return $response;



        //return $this->render('tickets/ticket.html.twig',array("nrotk" => $nrotk,"ttramite" => $tramite));

    }



    /**
     * @Route("/BuscaMotivos", name="BuscaMotivos", )
     */
    public function BuscaMotivosIniAction(Request $request){
        //==================================================
        //Busca motivos segun el tipo de tramite seleccionado

        ///{tipoTramite}
        //, $tipoTramite = null)
        //==================================================
        $tipoTramite    = $request ->get("txt_tipo");
        $nroDoc         = $request ->get("txt_doc");

        $rg_motivos = $this->getDoctrine()->getRepository(TramitesMotivos::class)->findBy(array("tipoTramite" => $tipoTramite, "vigente" => 'S'),array("orden" => "ASC"));

        return $this->render('tickets/motivos.html.twig', array( "datosMotivos" => $rg_motivos, "tipoTramite" => $tipoTramite , "dniIngresado" => $nroDoc));
        //==================================================
    }


}

class PDF_JavaScript extends \FPDF {

    protected $javascript;
    protected $n_js;

    function IncludeJS($script, $isUTF8=false) {
        /*if(!$isUTF8)
            $script=utf8_encode($script);*/
        $this->javascript=$script;
    }

    function _putjavascript() {
        $this->_newobj();
        $this->n_js=$this->n;
        $this->_put('<<');
        $this->_put('/Names [(EmbeddedJS) '.($this->n+1).' 0 R]');
        $this->_put('>>');
        $this->_put('endobj');
        $this->_newobj();
        $this->_put('<<');
        $this->_put('/S /JavaScript');
        $this->_put('/JS '.$this->_textstring($this->javascript));
        $this->_put('>>');
        $this->_put('endobj');
    }

    function _putresources() {
        parent::_putresources();
        if (!empty($this->javascript)) {
            $this->_putjavascript();
        }
    }

    function _putcatalog() {
        parent::_putcatalog();
        if (!empty($this->javascript)) {
            $this->_put('/Names <</JavaScript '.($this->n_js).' 0 R>>');
        }
    }
}

class PDF_AutoPrint extends PDF_JavaScript
{
    function AutoPrint($dialog=false)
    {
        //Open the print dialog or start printing immediately on the standard printer
        //$param=($dialog ? 'true' : 'false');
        $script="print(false);";
        /*$this->IncludeJS($script);*/

        // Open the print dialog
        /*if($printer)
        {
            $printer = str_replace('\\', '\\\\', $printer);
            $script = "var pp = getPrintParams();";
            $script .= "pp.interactive = pp.constants.interactionLevel.full;";
            $script .= "pp.printerName = '$printer'";
            $script .= "print(pp);";
        }*/
        $this->IncludeJS($script);

    }

    function AutoPrintToPrinter($server, $printer, $dialog=false)
    {
        //Print on a shared printer (requires at least Acrobat 6)
        $script = "var pp = getPrintParams();";
        if($dialog)
            $script .= "pp.interactive = pp.constants.interactionLevel.full;";
        else
            $script .= "pp.interactive = pp.constants.interactionLevel.automatic;";
        $script .= "pp.printerName = '\\\\\\\\".$server."\\\\".$printer."';";
        $script .= "print(pp);";
        $this->IncludeJS($script);
        /*
        $script = "document.contentWindow.print();";
        $this->IncludeJS($script);*/
    }
}