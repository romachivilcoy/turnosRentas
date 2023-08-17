<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Boxes;
use AppBundle\Entity\CallesChivilcoy;
use AppBundle\Entity\ColaTurnos;
use AppBundle\Entity\Turnos;
use AppBundle\Entity\TramitesMotivos;
use AppBundle\Entity\UsuariosBox;
use AppBundle\Entity\Tramites;
use AppBundle\Entity\UsuariosTramites;
use AppBundle\Entity\Padron;
use AppBundle\Entity\Contador;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\HttpFoundation\Response;


class BoxesController extends DefaultController
{

    /**
     * @Route("/boxes", name="boxes")
     */
    public function boxesAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('boxes/boxes.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]);
    }

    /**
     * @Route("/actulizaCambioPapel", name="actulizaCambioPapel")
     */
    public function actulizaCambioPapelAction(Request $request)
    {
        $estado_papel = $request ->get("estado_papel");
        //--------------------------------------------
        $contador_id = 1 ;
        $rg_box = $this->getDoctrine()->getEntityManager()->getRepository(Contador::class)->findOneBycontadorId($contador_id);
        if (count($rg_box) > 0) {

            //ACTULIZA
            $rg_box->setContadorNumero(600);
            $rg_box->setContadorAviso($estado_papel);

            // Persisto el objeto en la base de datos
            $entityManager = $this->getDoctrine()->getEntityManager();
            $entityManager->flush();
        }//end if

        //--------------------------------------------
        $output[]=array("b_estado" => $estado_papel);
        return new JsonResponse($output);
    }//actulizaCambio


    /**
     * @Route("/listaBoxes", name="listaBoxes")
     */
    public function listaBoxesAction(Request $request)
    {
        $iduser     = $request->getSession()->get("usuarioLogeado");


        $id_box_sel = 0;
        //------------------------------------------------
        //busca estado del box
        //------------------------------------------------
        $estado_box    = 'N';
        $rpers  = $this->getDoctrine()->getRepository(Boxes::class)->findby(array("boxUsuario" => $iduser, "boxEstado" =>  array('S', 'A')),array());
        if ($rpers != null) {
            foreach ($rpers as $datos_d){
                $id_box_sel = $datos_d->getBoxNumero();
                $estado_box = 'S';
            }//endFor
        }//end if

        //------------------------------------------------
        //busca el nro box del usuario
        //------------------------------------------------
        $idPrioridad = 0;
        //if ($id_box_sel == 0){
            $rg_user  = $this->getDoctrine()->getRepository(UsuariosBox::class)->findby(array("usbxIdus" => $iduser, "usbxDefault" => 'S'),array());
            if ($rg_user != null) {
                foreach ($rg_user as $datos_u){
                    $id_box_sel = $datos_u->getUsbxNrobox();
                    $idPrioridad = $datos_u->getUsbxPrioridad();
                }
            }
        //}//end if


        //-------------------------------
        //'BUSCA EN ESPERA'
        //-------------------------------
        $estadope = 1; //'pendiente'
        $estadob  = 2; //'en box'
        $estadopr = 3; //'presente'
        $estadoa  = 4; //'atendido'
        $estadocl = 5; //'cancelado'
        $estadoes = 7; //'en espera'

        $cant_espera    = 0;
        $cant_cancelado = 0;

        $fecha_hoy   = new \DateTime();
        $today       = $fecha_hoy->format('Y-m-d');
        $dia_hoy     = $fecha_hoy->format('d/m/Y');
        $hora_hoy    = $fecha_hoy->format('H:i');

        $sqles = "SELECT COUNT(T.trEstadoTurno) AS Tespera, T.trEstadoTurno ";
        $sqles = $sqles . "FROM  AppBundle:Turnos T";
        $sqles = $sqles  . " WHERE ((T.trEstadoTurno =". $estadope ." ) OR (T.trEstadoTurno =". $estadob ." AND T.trBoxAtencion=".$id_box_sel." ) OR (T.trEstadoTurno =". $estadoes ." ) OR (T.trEstadoTurno =". $estadocl ." ))";
        $sqles = $sqles  . "AND  T.trFechahoraTurno >= '$today' GROUP BY T.trEstadoTurno";

        $query_box  = $this->getDoctrine()->getEntityManager()->createQuery($sqles);
        $r_cant     = $query_box->getResult();

        foreach ($r_cant as $datos_d){
            if ($datos_d['trEstadoTurno'] == 5){
                $cant_cancelado = $datos_d['Tespera'];
            }else{
                $cant_espera  = $datos_d['Tespera'];
            }
        }//end foreach

        if ($cant_espera == 0){
            $mensaje = "Sin Turnos Pendientes";
        }else{
            $mensaje = $cant_espera . " Turno/s en Cola";
        }//if

        //--------------------------
        //BUSCA CALLES
        //--------------------------
        $arr_calles = $this->getDoctrine()->getEntityManager('sigmu')->getRepository(CallesChivilcoy::class)->findAll();

        $modulos = $this->recuperarModulosUsuario($iduser);

        return $this->render('boxes/boxes.html.twig', array('modulos' => $modulos,'p_usuario' => $iduser, 'p_dia' => $dia_hoy, 'p_hora' => $hora_hoy,'p_nro_box' => $id_box_sel, 'p_est_box' => $estado_box, 'p_espera' => $mensaje, 'arr_calles' => $arr_calles, 'idPrioridad' => $idPrioridad));//, 'p_msj_cancelado' => $msj_cancelado
    }

    /**
     * @Route("/listaEstadisticas", name="listaEstadisticas")
     */
    public function listaEstadisticasAction(Request $request)
    {
        return $this->render('boxes/boxes.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]);
    }

    /**
     * @Route("/buscaListado", name="buscaListado", )
     */
    public function buscaListadoAction(Request $request){
        //=================================================================================
        //BUSCA LOS TICKET 1-PENDIETES, 2-EN BOX; 7-ACTIVOS
        //=================================================================================

        $id_box_sel  = $request ->get("nro_box");
        $iduser      = $request ->get("nusuario");
        $idPrioridad = $request ->get("idPrioridad");

        $fecha_hoy   = new \DateTime();
        $today       = $fecha_hoy->format('Y-m-d');
        $hora_hoy    = $fecha_hoy->format('H:i');

        $estadope    = 1; //pendientes
        $estadob     = 2; //en box
        $estadopr    = 3; //presente
        $estadocl    = 5; //'cancelado'
        $estadoe     = 7; //espera
        $cant_cancelado = 0;

        //'---------------------------------------------------------'
        //BUSCO LOS TIPOS DE TRAMITE QUE TIENE ASIGNADO EL USUARIO DEL BOX
        //'-------------------------------------------------------'
        $sql_tarea="";
        $rg_tareas = $this->getDoctrine()->getRepository(UsuariosTramites::class)->findBy(array('usTrUsuario' => $iduser));
        if ($rg_tareas != null) {
            foreach ($rg_tareas as $rg_t) {
                $nro_tramite = $rg_t->getUsTrTramite()->getTraIdTramite();
                $user_tra[]=array("ustramite" => trim($nro_tramite));

                if (strlen($sql_tarea) >0){
                    $sql_tarea = $sql_tarea . " OR (T.trTipoTramite=".$rg_t->getUsTrTramite()->getTraIdTramite() .")";
                }else{
                    $sql_tarea = " (T.trTipoTramite=".$rg_t->getUsTrTramite()->getTraIdTramite() .")";
                }//end if
            }//for
        }//if

        //-------------------------------------------------------------
        //CANTIDAD TURNOS
        //-------------------------------------------------------------
        $sqles = "SELECT COUNT(T.trEstadoTurno) AS Tespera ";
        $sqles = $sqles . "FROM  AppBundle:Turnos T";
        $sqles = $sqles  . " WHERE ((T.trEstadoTurno =". $estadope ." ) OR (T.trEstadoTurno =". $estadob ." AND T.trBoxAtencion=".$id_box_sel.") OR (T.trEstadoTurno =". $estadoe ." ))";
        $sqles = $sqles  . " AND (". $sql_tarea ." ) ";
        $sqles = $sqles  . " AND T.trFechahoraTurno >= '$today'";

        $query_box  = $this->getDoctrine()->getEntityManager()->createQuery($sqles);
        $r_cant     = $query_box->getResult();

        foreach ($r_cant as $datos_d){
            $cant_espera  = $datos_d['Tespera'];//$query_box->getSingleScalarResult();
        }

        //---------------------
        //BUSCA CONTATOR TICKETS
        $papel_sn = 0;
        $rg_contador = $this->getDoctrine()->getRepository(Contador::class)->findBy(array('contadorId' => '1'));
        if ($rg_contador != null) {
            foreach ($rg_contador as $rg_c) {
                $papel_sn = $rg_c->getContadorAviso();
            }
        }//endIf

        if ($cant_espera == 0){
            $mensaje = "Sin Turnos Pendientes";
        }else{
            $mensaje = $cant_espera . " Turno/s en Cola";
        }

        //------------------------------------------------------------------------------
        //------------------------------------------------------------------------------
        $sqles = "SELECT T ";
        $sqles = $sqles . "FROM  AppBundle:Turnos T JOIN AppBundle:Tramites R WITH T.trTipoTramite = R.traIdTramite";
        $sqles = $sqles . " LEFT JOIN AppBundle:UsuariosBox B WITH T.trTipoTramite = B.usbxPrioridad AND (B.usbxNrobox=".$id_box_sel.")";
        $sqles = $sqles . " WHERE ((T.trEstadoTurno =". $estadope ." ) OR (T.trEstadoTurno =". $estadob ."  AND T.trBoxAtencion=".$id_box_sel.") OR (T.trEstadoTurno =". $estadopr ." AND T.trBoxAtencion=".$id_box_sel.") OR (T.trEstadoTurno =". $estadocl ." ) OR (T.trEstadoTurno =". $estadoe ." AND T.trBoxAtencion=". $id_box_sel ."  ))";
        //$sqles = $sqles  . " AND (T.trBoxRetorno <> ".$id_box_sel." or T.trBoxRetorno is null)";
        $sqles = $sqles . " AND  T.trFechahoraTurno >= '$today' ORDER BY T.trEstadoTurno DESC,  T.trFechahoraOrigen , T.trIdTurno ";//B.usbxPrioridad DESC,


        $query_listado  = $this->getDoctrine()->getEntityManager()->createQuery($sqles);
        $rpers          = $query_listado->getResult();
        $nro_oden       = 1;
        $tipo_ventana   = 1;
        $output[]=array();
        $turnosEspera = 0;

        foreach ($rpers as $datos_r){
            // -------------------------------------------------
            //genera la ruta para abrir el form de modificacion
            // -------------------------------------------------
            $n_idturno  = $datos_r->getTrIdTurno();
            $n_fecha_or = $datos_r->getTrFechahoraOrigen();
            $n_doc_sel  = $datos_r->getTrNroDoc();
            $idTramite  = $datos_r->getTrTipoTramite();
            $n_estado   = $datos_r->getTrEstadoTurno();
            $n_ticket   = $datos_r->getTrTicket();
            $id_motivo   = $datos_r->getTrMotivo();
            $hora_origen = $n_fecha_or->format('H:i');

            if ($n_doc_sel == '-'){
                $n_doc_sel='';
            }

            if ($n_estado === 5){
                //SI ESTA EN ESTADO CANCELADO

                $n_fecha_cancelado = $datos_r->getTrFechahoraCancelado();
                $hora_cancelado    = $n_fecha_cancelado->format('H:i');

                $diferencia_h= ( strtotime($hora_hoy) - strtotime($hora_cancelado) )/60;
                $t_espera    = $diferencia_h; //$diferencia_h . '-' .$hora_hoy . '-' . $hora_cancelado ;//$hr_sel + $min_sel;

                $permite_sn="N";
                foreach ($user_tra as $nro_tramite) {
                    if ($idTramite == $nro_tramite["ustramite"]){
                        $permite_sn="S";
                        break;
                    }
                }//for

                if (($t_espera < 11) && ($permite_sn =='S')){
                    $cant_cancelado = $cant_cancelado + 1;
                }
            }else{
                //PRESENTE O ATENDIDO
                if ($n_estado === 3 || $n_estado === 4 ){
                    $t_espera  = 0;
                }else{
                    $diferencia_h= ( strtotime($hora_hoy) - strtotime($hora_origen) )/60;
                    $t_espera    = $diferencia_h;
                }//end if
            }//end if

            if (($n_estado != 5 ) ){
                //NO ESTA CANCELADO

                $n_ape_nom     = "-";
                if (is_null($n_doc_sel)) {
                    $n_doc_sel = "";
                }else{
                    //------------------------------------------------
                    //BUSCA APE Y NOMBRE DE LA PERSONA QUE SACO TURNO
                    //------------------------------------------------
                    $sigmu_sn = $this->getDoctrine()->getEntityManager('sigmu')->getRepository(Padron::class)->findOneBypdoc($n_doc_sel);
                    if (count($sigmu_sn) > 0) {
                        //BUSCA APE Y NOMBRE
                        $n_ape_nom= trim($this->normalizarString($sigmu_sn->getApellidoYNombre()));
                        //$n_ape_nom = str_replace("&#039;", "", $n_ape_nom);
                    }//endIf
                }//endIf

                $permite_sn="N";
                foreach ($user_tra as $nro_tramite) {
                    if ($idTramite == $nro_tramite["ustramite"]){
                        $permite_sn="S";
                        break;
                    }
                }//for

                if ($permite_sn =='S'){
                    /*---------------------------*/
                    /* BUSCA TIPO TRAMITE */

                    if ($t_espera > 10){
                        $turnosEspera = $turnosEspera + 1;
                    }//endIf

                    $tramite    = $this->getDoctrine()->getRepository(Tramites::class)->findOneBytraIdTramite($idTramite);
                    if (count($tramite)>0){
                        $n_ttramite = $tramite->getDescripcion();
                    }else{
                        $n_ttramite = "-";
                    }


                    /*---------------------------*/
                    /* BUSCA TIPO MOTIVO */
                    $tramite    = $this->getDoctrine()->getRepository(TramitesMotivos::class)->findOneByidMotivo($id_motivo);
                    if (count($tramite)>0){
                        $n_motivo = $tramite->getDescripcion();
                    }else{
                        $n_motivo = "-";
                    }

                    switch ($n_estado){
                        case 1://pendiente
                            $n_icon = "<span  class='fa fa-bell' data-toggle='tooltip' title='LLAMAR TURNO' aria-hidden='true'  onclick='javascript:Turnosel($n_idturno, $nro_oden ,1, $id_box_sel, $tipo_ventana, 1, $n_doc_sel)' style='cursor: pointer; font-size: 20px;'></span>";
                            break;
                        case 2: //en box
                            $n_icon = "<span  class='fa fa-plus' data-toggle='tooltip' title='CONFIRMAR PRESENCIA'  aria-hidden='true'  onclick='javascript:Turnosel($n_idturno, $nro_oden ,2, $id_box_sel, $tipo_ventana, 1, $n_doc_sel)' style='cursor: pointer; font-size: 22px;'></span> <span  class='fa fa-times-circle pl-2' data-toggle='tooltip' title='CANCELAR TURNO'  aria-hidden='true'  onclick='javascript:Turnosel($n_idturno, $nro_oden ,5, $id_box_sel, $tipo_ventana, 1)' style='cursor: pointer; font-size: 22px;'></span>";
                            break;
                        case 3://presente
                            $n_icon = "<span  class='fa fa-thumbs-up' data-toggle='tooltip' title='FINALIZAR TURNO'  aria-hidden='true'  onclick='javascript:Turnosel($n_idturno, $nro_oden ,3, $id_box_sel, $tipo_ventana, 1, $n_doc_sel)' style='cursor: pointer; font-size: 22px;'></span> <span  class='fa fa-mail-reply pl-2' data-toggle='tooltip' title='FINALIZAR Y VOLVER A LA COLA'  aria-hidden='true'  onclick='javascript:VolverAlaCola($id_box_sel, 1, $n_idturno)' style='cursor: pointer; font-size: 22px;'></span>";
                            break;
                        case 4://atendido
                            $n_icon = "<span  class='fa fa-check' aria-hidden='true'  onclick='javascript:Turnosel($n_idturno, $nro_oden ,4, $id_box_sel, $tipo_ventana, 1, $n_doc_sel)' style='cursor: pointer; font-size: 22px;'></span>";
                            break;
                        case 5://cancelado
                            $n_icon = "<span  class='fa fa-history' data-toggle='tooltip' title='RETOMAR TICKET'  aria-hidden='true' onclick='javascript:Turnosel($n_idturno, $nro_oden ,8, $id_box_sel, $tipo_ventana, 1, $n_doc_sel)' style='cursor: pointer; font-size: 22px;'></span>";
                            break;
                        default:
                    }//end switch

                    if ($n_doc_sel == "-"){
                        $n_persona_icon = "";
                    }else{
                        $n_persona_icon = "<span  class='fa fa-user' data-toggle='tooltip' title='VER DATOS PERSONALES'  aria-hidden='true'  onclick='javascript:VerPersona($n_idturno, $nro_oden ,$n_doc_sel, $tipo_ventana)' style='cursor: pointer; font-size: 22px;'></span>";
                    }//endIf

                    //------------------------------
                    //crea el array con los datos
                    $output[]=array("a_doc" => $n_doc_sel, "a_fecha" => $t_espera, "a_apeynom"  => $n_ape_nom, "a_tramite" => $n_ttramite, "a_motivo" => $n_motivo, "a_estado" => $n_estado, "a_ticket" => $n_ticket, "a_icon" => $n_icon, "a_persona_icon" => $n_persona_icon, "a_turno" => $n_idturno, "a_orden" => $nro_oden, "a_box" => $id_box_sel, "a_mensaje" => $mensaje, 'a_msj_cancelado' => $cant_cancelado,  "a_hora" => $hora_hoy, "papel_sn" => $papel_sn, 'turnosEspera' => $turnosEspera );
                    //array_push($output,
                    $nro_oden +=1;
                }//permite
            }//if
        }//end foreach

        if ($nro_oden == 1){
            $output[]=array("a_doc" => '', "a_fecha" => '', "a_apeynom"  => '', "a_tramite" => '', "a_estado" => '', "a_ticket" => '', "a_icon" => '', 'a_persona_icon' => '', "a_turno" => '', "a_orden" => '', "a_box" => '', "a_mensaje" => $mensaje, 'a_msj_cancelado' => $cant_cancelado,  "a_hora" => $hora_hoy, "papel_sn" => $papel_sn, 'turnosEspera' => $turnosEspera );
        }
        $dateE = $this->convert_from_latin1_to_utf8_recursively($output);
        return new JsonResponse($dateE);
    }//end buscaListadoAction

    /**
     * @Route("/buscaAlerta", name="buscaAlerta", )
     */
    public function buscaAlertaAction(Request $request){
        //=================================================================================
        //BUSCA LOS TICKET PENDIENTES EN ESPERA MAS DE 10 min
        //=================================================================================

        $iduser      = $request ->get("nusuario");
        $fecha_hoy   = new \DateTime();
        $today       = $fecha_hoy->format('Y-m-d');
        $hora_hoy    = $fecha_hoy->format('H:i');
        $estadope    = 1; //pendientes

        //-------------------------------------------------------------
        //BUSCA LOS TRAMITES ASIGNADO AL USUARIO
        //$iduser      = $request->getSession()->get("usuarioLogeado");
        $user_tr     = $this->getDoctrine()->getRepository(UsuariosTramites::class)->findByusTrUsuario($iduser);
        if ($user_tr != null) {
            foreach ($user_tr as $data_ut){
                $nro_tramite = $data_ut->getUsTrTramite()->getTraIdTramite();
                $user_tra[]=array("ustramite" => trim($nro_tramite));
            }
        }//if

        //-------------------------------------------------------------------------------------
        $sqles = "SELECT T ";
        $sqles = $sqles . "FROM  AppBundle:Turnos T JOIN AppBundle:Tramites R WITH T.trTipoTramite = R.traIdTramite";
        $sqles = $sqles  . " WHERE (T.trEstadoTurno =". $estadope ." ) ";
        $sqles = $sqles  . "AND  T.trFechahoraTurno >= '$today' ORDER BY R.traPrioridad DESC,  T.trIdTurno ";

        $query_listado  = $this->getDoctrine()->getEntityManager()->createQuery($sqles);
        $rpers          = $query_listado->getResult();

        $output[]=array();
        $turnosEspera = 0;
        foreach ($rpers as $datos_r){
            // -------------------------------------------------
            // recorre todos los turnos pendientes
            // -------------------------------------------------
            $n_fecha_or  = $datos_r->getTrFechahoraOrigen();
            $idTramite   = $datos_r->getTrTipoTramite();
            $hora_origen = $n_fecha_or->format('H:i');

            //CALCULA EL TIEMPO DE ESPERA DESDE QUE SACO EL TURNO  Y HORA ACTUAL
            $diferencia_h= ( strtotime($hora_hoy) - strtotime($hora_origen) )/60;
            $t_espera    = $diferencia_h;

            $permite_sn="N";
            foreach ($user_tra as $nro_tramite) {
                if ($idTramite == $nro_tramite["ustramite"]){
                    $permite_sn="S";
                    break;
                }
            }//for

            if (($permite_sn =='S') && ( $t_espera > 10)) {
                $turnosEspera +=1;
            }//permite
        }//end foreach


        $output[]=array("turnosEspera" => $turnosEspera );
        return new JsonResponse($output);
    }//end buscaAlerta


    /**
     * @Route("/buscaDatosPersona", name="buscaDatosPersona", )
     */
    public function buscaDatosPersonaAction(Request $request){
        //=================================================================================
        //BUSCA LOS DATOS DE LA PERSONA
        //=================================================================================

        $n_doc_sel  = $request ->get("docpersona");
        $fecha_hoy   = new \DateTime();

        $sigmu_sn = $this->getDoctrine()->getEntityManager('sigmu')->getRepository(Padron::class)->findOneBypdoc($n_doc_sel);
        if (count($sigmu_sn) > 0) {
            //BUSCA DATOS PERSONALES
            $n_ape_nom      = $sigmu_sn->getApellidoYNombre();
            $n_calle        = $sigmu_sn->getNombrecalle();
            $n_nro_calle    = $sigmu_sn->getNrocalle();
            $n_tel          = $sigmu_sn->getTelefono();
            $fec_nac        = trim($sigmu_sn->getFecNac());
            if (!empty($fec_nac)){
                $elements = explode('-', $fec_nac);
                if (count($elements) > 1){
                    $n_fec_nac    = $fec_nac;
                }else{
                    $n_fec_nac    = \DateTime::createFromFormat("d/m/Y", $fec_nac)->format("Y-m-d");
                }
            }else{
                $n_fec_nac ="";;
            }
            $n_mail    = $sigmu_sn->getMail();

            //------------------------------
            //crea el array con los datos
            $output[]=array("a_ape_nom" => $n_ape_nom, "a_calle" => $n_calle, "a_nro_calle"  => $n_nro_calle, "a_tel" => $n_tel, "a_fec_nac" => $n_fec_nac, "a_mail" => $n_mail );

        }else{
            $output[]=array("a_ape_nom" => '', "a_calle" => '', "a_nro_calle"  => '', "a_tel" => '', "a_fec_nac" => '', "a_mail" => '');
        }
        $dataE = $this->convert_from_latin1_to_utf8_recursively($output);
        return new JsonResponse($dataE);
    }//end buscaDatosPersona

    /**
     * @Route("/guardaDatosPersona", name="guardaDatosPersona", )
     */
    public function guardaDatosPersonaAction(Request $request){
        //--------------------------------------------
        //MODIFCA LOS DATOS PERSONALES DEL PADRON
        //--------------------------------------------

        $nro_doc    = $request->get("docpersona");
        $ape_nom    = $request->get("ape_nom");
        $telefono   = $request->get("telefono");
        $email      = $request->get("email");
        $calle      = $request->get("calle");
        $nro_calle  = $request->get("nro_calle");
        $fec_nac    = $request->get("fec_nac");


        $persona= $this->getDoctrine()->getEntityManager('sigmu')->getRepository(Padron::class)->findOneBypdoc($nro_doc);
        if (count($persona) > 0) {

            $persona->setApellidoYNombre($ape_nom);
            $persona->setTelefono($telefono);
            $persona->setMail($email);
            $persona->setNombrecalle($calle);
            $persona->setNrocalle($nro_calle);
            if (strlen($fec_nac) > 8){
                $persona->setFecNac(\DateTime::createFromFormat("Y-m-d", $fec_nac)->format("d/m/Y"));
            }

            $entityManager = $this->getDoctrine()->getEntityManager('sigmu');
            $entityManager->flush();
        }else{

            $rg_turno_ate = new Padron();
            $rg_turno_ate->setPdoc($nro_doc);
            $rg_turno_ate->setApellidoYNombre($ape_nom);
            $rg_turno_ate->setMail($email);
            $rg_turno_ate->setTelefono($telefono);
            $rg_turno_ate->setNombrecalle($calle);
            $rg_turno_ate->setNrocalle($nro_calle);
            if (strlen($fec_nac) > 8){
                $rg_turno_ate->setFecNac(\DateTime::createFromFormat("Y-m-d", $fec_nac)->format("d/m/Y"));
            }
            $entityManager = $this->getDoctrine()->getEntityManager('sigmu');
            $entityManager->persist($rg_turno_ate);
            $entityManager->flush();
        }//end if

        return new JsonResponse('S');

    }//end GuardaDatosPersonales

    /**
     * @Route("/boxConsultaEstado", name="boxConsultaEstado", )
     */
    public function boxConsultaEstadoAction(Request $request){
        //=================================================================================
        //'BUSCA SI EL BOX INICIO EN LA FECHA'
        //=================================================================================

        $id_box_sel = $request ->get("nro_box");
        $id_usuario = $request ->get("n_usuario");
        $esta_turno = $request ->get("n_estado");
        $estadob    = "N";

        //------------------------------------------------
        //busca el nro box del usuario
        //------------------------------------------------
        if ($id_box_sel == 0){
            $rg_user  = $this->getDoctrine()->getRepository(UsuariosBox::class)->findby(array("usbxIdus" => $id_usuario, "usbxDefault" => 'S'),array());
            if ($rg_user != null) {
                foreach ($rg_user as $datos_u){
                    $id_box_sel = $datos_u->getUsbxNrobox();
                }
            }//endIf
        }//end if

        $p_id=0;

        $query_turnos  = $this->getDoctrine()->getEntityManager()->createQuery("SELECT rbx.boxEstado, rbx.boxId FROM AppBundle:Boxes rbx  WHERE rbx.boxUsuario = '$id_usuario' AND rbx.boxNumero = '$id_box_sel'");
        $rpers         = $query_turnos->getResult();
        foreach ($rpers as $datos_d){
            $estadob = $datos_d['boxEstado'];
            $p_id    = $datos_d['boxId'];
        }//for

        //--------------------------------------------
        if ($esta_turno =='2' || $esta_turno =='8'){
            $rg_box = $this->getDoctrine()->getEntityManager()->getRepository(Boxes::class)->findOneByboxId($p_id);
            if (count($rg_box) > 0) {
                //ACTULIZA
                $rg_box->setBoxSonido('N');

                // Persisto el objeto en la base de datos
                $entityManager = $this->getDoctrine()->getEntityManager();
                $entityManager->flush();
            }//end if
        }//if
        //--------------------------------------------
        $output[]=array("b_estado" => $estadob);

        return new JsonResponse($output);
    }//end boxConsultaEstadoAction


    /**
     * @Route("/boxInicio", name="boxInicio", )
     */
    public function boxInicioAction(Request $request){
        //=================================================================================
        //BOX: CREA O ACTULIZA EL ESTADO DEL BOX
        //=================================================================================
        $id_box_sel = $request ->get("nro_box");
        $estado_sel = $request ->get("estadob");
        $usuario    = $request ->get("usuario");

        $rg_box = $this->getDoctrine()->getEntityManager()->getRepository(Boxes::class)->findBy(array(),array("boxOrden" => "DESC"));
        $nroorden= 1;
        if ( $rg_box != null) {
            $nroorden= $nroorden + $rg_box[0]->getBoxOrden();
        }

        if ($estado_sel == "N"){
            $nroorden= 0;
        }//end if

        $fecha_hoy   = new \DateTime();
        $hora_hoy    = $fecha_hoy->format('H:i');

        //------------------------------------------------
        //busca el nro box del usuario
        //------------------------------------------------
        if ($id_box_sel == 0){
            $rg_user  = $this->getDoctrine()->getRepository(UsuariosBox::class)->findby(array("usbxIdus" => $usuario, "usbxDefault" => 'S'),array());
            if ($rg_user != null) {
                foreach ($rg_user as $datos_u){
                    $id_box_sel = $datos_u->getUsbxNrobox();
                }//for
            }//endIf
        }//end if

        //'--------------------------------------------------------'
        //'BUSCA SI EL BOX INICIO EN LA FECHA'
        //'--------------------------------------------------------'
        $boxex_si= $this->getDoctrine()->getRepository(Boxes::class)->findOneByBoxNumero($id_box_sel);
        if ($boxex_si != null){

            $fecha_ultima   = $boxex_si->getBoxFecha();//ultima fecha que inicio
            $hora_origen    = $fecha_ultima->format('H:i');
            $tiempo_activo  = $boxex_si->getBoxTiempo();//tiempo total actico

            $diferencia_h   = ( strtotime($hora_hoy) - strtotime($hora_origen) )/60;
            $t_espera       = $tiempo_activo + $diferencia_h;

            $boxex_si->setBoxEstado($estado_sel);
            $boxex_si->setBoxOrden($nroorden);
            $boxex_si->setBoxSonido('S');
            $boxex_si->setBoxIdturno($usuario);
            $boxex_si->setBoxFecha(new \DateTime());
            $boxex_si->setBoxUsuario($usuario);
            if ($estado_sel == 'N'){
                $boxex_si->setBoxTiempo($t_espera);
            }

            $this->getDoctrine()->getManager()->flush();
        }else{
            $tra_per_sn = new Boxes();
            $tra_per_sn->setBoxOrden($nroorden);
            $tra_per_sn->setBoxNumero($id_box_sel);
            $tra_per_sn->setBoxEstado($estado_sel);
            $tra_per_sn->setBoxSonido('S');
            $tra_per_sn->setBoxUsuario($usuario);
            $tra_per_sn->setBoxFecha(new \DateTime());
            $tra_per_sn->setBoxTiempo(0);

            $entityManager = $this->getDoctrine()->getEntityManager();
            $entityManager->persist($tra_per_sn);
            $entityManager->flush();
        }//end if

        $output[]=array();
        $output[]=array("boxet" => $estado_sel);

        return new JsonResponse($output);
    }//end BoxInicio

    /**
     * @Route("/boxTurno", name="boxTurno", )
     */
    function boxTurno(Request $request){
        //----------------------------------------------------
        //EL BOX TOMA TURNO: ELIMINA DE  COLA, Y ACTULIZA TURNO
        //----------------------------------------------------
        $nroturno    = $request->get("idturno");
        $nrobox      = $request->get("boxaten");
        $estasel     = $request->get("estasel");
        $nusuario    = $request->get("nusuario");
        $fechahoy    = new \DateTime();

        //--------------------------------------------------------'
        //BUSCA EL MAYOR ORDEN'
        //--------------------------------------------------------'
        $rg_box = $this->getDoctrine()->getRepository(Boxes::class)->findBy(array(),array("boxOrden" => "DESC"));
        $nroorden= 1;
        if ($rg_box != null) {
            $nroorden= $nroorden + $rg_box[0]->getBoxOrden();
        }//if

        //------------------------------------'
        //BUSCA EL TURNO EN LA COLA SI EXISTE'
		//------------------------------------'
        $ejesn = "N";

        //estado pendiente
        if ($estasel == 1){
            //'ACTULIZA A BOX'

                $rgcola = $this->getDoctrine()->getRepository(ColaTurnos::class)->findOneByclIdTurno($nroturno);
                if (count($rgcola) > 0) {
                        $ejesn  = "S";
                        $nrocola= $rgcola->getClIdTurno();
                        $ncolor = $rgcola->getClColor();

                        //------------------------------
                        //ELIMINA DE LA COLA
                        //------------------------------
                        $em        = $this->getDoctrine()->getManager();
                        $rgcola_el = $em->getRepository(ColaTurnos::class)->findOneByclIdTurno($nroturno);
                        if ($rgcola_el) {
                            $em->remove($rgcola_el);
                            $em->flush();
                        }//end if

                        //------------------------------
                        //ACTULIZA ESTADO TURNO
                        //------------------------------
                        $turno_r = $this->getDoctrine()->getRepository(Turnos::class)->findOneBytrIdTurno($nroturno);
                        if ($turno_r != null){
                            if (!is_null($turno_r->getTrBoxAtencion())){
                                //'----------------------------------------------'
                                //YA TIENE BOX
                                //------------------------------------------
                                /*$nroboxaten = $turno_r->getTrBoxAtencion();
                                $fbox       = $turno_r->getTrFechahoraTurno();*/
                                $ejesn      = "N";

                            }else{
                                //-------------------------------
                                //SI ES NULO ACTUALIZO
                                $turno_r->setTrEstadoTurno(2);
                                $turno_r->setTrUsuarioBox($nusuario);
                                $turno_r->setTrBoxAtencion($nrobox);
                                $turno_r->setTrFechahoraTurno($fechahoy);
                                $this->getDoctrine()->getManager()->flush();
                            }//if
                        }//end if
                }//end if

                if ($ejesn == "N") {
                    //---------------------------------------------------------------------------------------
                    //SI EL TURNO ESTA OCUPADO, BUSCA EL BOX QUE LO ESTA ATENDIENDO
                    //---------------------------------------------------------------------------------------
                        $rg_turnosi = $this->getDoctrine()->getRepository(Turnos::class)->findOneBytrIdTurno($nroturno);
                        if (count($rg_turnosi) > 0) {
                                $nroboxaten = $rg_turnosi->getTrBoxAtencion();
                                $fbox       = $rg_turnosi->getTrFechahoraTurno();
                        }//end if
                }else{
                    //------------------------------
                    //ACTULIZA ESTADO  BOX
                    //------------------------------
                    $boxex_nro= $this->getDoctrine()->getRepository(Boxes::class)->findOneByBoxNumero($nrobox);
                    if ($boxex_nro != null){
                        $boxex_nro->setBoxEstado('A');
                        $boxex_nro->setBoxOrden(0);
                        $boxex_nro->setBoxIdturno($nroturno);

                        $this->getDoctrine()->getManager()->flush();
                    }//end if
                }//end if
            //estado pendiente

        }elseif ($estasel == 2 || $estasel == 8){ //en box -- retornar
            //------------------------------
            //'ACTULIZA A PRESENTE'
            //------------------------------

            $ndoc     = $request->get("ndoc");
            $ape_sel  = $request->get("ape_sel");
            $tel_sel  = $request->get("tel_sel");
            $email_sel= $request->get("email_sel");
            $tra_sel  = $request->get("tra_sel");
            $mot_sel  = "";//$request->get("mot_sel");
            //$ape_sel = trim($this->normalizarString($ape_sel));

            $ejesn   ="S";
            $rg_turno_ate = $this->getDoctrine()->getRepository(Turnos::class)->findOneBytrIdTurno($nroturno);
            if (count($rg_turno_ate) > 0) {

                $rg_turno_ate->setTrEstadoTurno(3);
                $rg_turno_ate->setTrNroDoc($ndoc);
                $rg_turno_ate->setTrMotivo($mot_sel);
                $rg_turno_ate->setTrBoxAtencion($nrobox);
                $rg_turno_ate->setTrFechahoraPresente($fechahoy);

                //BUSCA LETRA TIPO TRAMITE
                $tramite = $this->getDoctrine()->getRepository(Tramites::class)->findByTraTipoTramite($tra_sel);
                if (count($tramite)>0){
                    $nro_tipo_tramite    = trim($tramite[0]->getTraIdTramite());
                }//if

                $rg_turno_ate->setTrTipoTramite($nro_tipo_tramite);
                $entityManager = $this->getDoctrine()->getEntityManager();
                $entityManager->flush();
            }//end if

            //------------------------------
            //ACTUALIZA Padron
            //------------------------------
            $rg_turno_ate = $this->getDoctrine()->getEntityManager('sigmu')->getRepository(Padron::class)->findOneBypdoc($ndoc);
            if (count($rg_turno_ate) > 0) {
                $rg_turno_ate->setApellidoYNombre($this->normalizarString($ape_sel));
                $rg_turno_ate->setMail($email_sel);
                $rg_turno_ate->setTelefono($tel_sel);
                $entityManager = $this->getDoctrine()->getEntityManager('sigmu');
                $entityManager->flush();
            }else{

                $rg_turno_ate = new Padron();
                $rg_turno_ate->setPdoc($ndoc);
                $rg_turno_ate->setApellidoYNombre($ape_sel);
                $rg_turno_ate->setMail($email_sel);
                $rg_turno_ate->setTelefono($tel_sel);

                $entityManager = $this->getDoctrine()->getEntityManager('sigmu');
                $entityManager->persist($rg_turno_ate);
                $entityManager->flush();
            }//end if

            //------------------------------
            //ACTULIZA ESTADO  BOX
            //------------------------------
            $boxex_up= $this->getDoctrine()->getRepository(Boxes::class)->findOneByBoxNumero($nrobox);
            if ($boxex_up != null){
                $boxex_up->setBoxEstado('A');
                $boxex_up->setBoxOrden(0);
                $boxex_up->setBoxSonido('N');
                $boxex_up->setBoxIdturno($nroturno);

                $this->getDoctrine()->getManager()->flush();
            }//end if

        }elseif ($estasel == 5){
            //---------------------------
            //'ACTULIZA A CANCELADO'
            //---------------------------
            $ejesn   ="S";

            $rg_turno_ate = $this->getDoctrine()->getRepository(Turnos::class)->findOneBytrIdTurno($nroturno);
            if ($rg_turno_ate != null) {
                $rg_turno_ate->setTrEstadoTurno(5);
                $rg_turno_ate->setTrFechahoraCancelado($fechahoy);
                $rg_turno_ate->setTrUsuarioCancela($nusuario);
                $rg_turno_ate->setTrBoxAtencion(null);

                $this->getDoctrine()->getManager()->flush();
            }//end if

            //---------------------------
            //'ACTULIZA A BOX LIBRE'
            //---------------------------
            $boxex_up= $this->getDoctrine()->getRepository(Boxes::class)->findOneByBoxNumero($nrobox);
            if ($boxex_up != null){
                $boxex_up->setBoxEstado('S');
                $boxex_up->setBoxOrden($nroorden);
                $boxex_up->setBoxSonido('S');
                $boxex_up->setBoxIdturno(null);

                $this->getDoctrine()->getManager()->flush();
            }//end if

        }else{
            //---------------------------
            //'ACTULIZA A TURNO A ATENDIDO'
            //---------------------------
            $ejesn   = "S";
            $rg_turno_ate = $this->getDoctrine()->getRepository(Turnos::class)->findOneBytrIdTurno($nroturno);
            if (count($rg_turno_ate) > 0) {
                $rg_turno_ate->setTrEstadoTurno(4);
                $rg_turno_ate->setTrFechahoraBox($fechahoy);
                $entityManager = $this->getDoctrine()->getEntityManager();
                $entityManager->flush();
            }//end if

            //---------------------------
            //'ACTULIZA A BOX LIBRE'
            //---------------------------
            $boxex_up= $this->getDoctrine()->getRepository(Boxes::class)->findOneByBoxNumero($nrobox);
            if ($boxex_up != null){
                $boxex_up->setBoxEstado('S');
                $boxex_up->setBoxOrden($nroorden);
                $boxex_up->setBoxSonido('S');
                $boxex_up->setBoxIdturno(null);

                $this->getDoctrine()->getManager()->flush();
            }//end if
        }//end if $estasel

        $output[]=array();
        if ($ejesn =="S"){
            $nroboxaten=0;
            $fbox      =0;

            $output[]=array("nbox" => $nroboxaten, "fechabox" => $fbox);
        }else{
            $output[]=array("nbox" => $nroboxaten, "fechabox" => $fbox);

        }
        return new JsonResponse($output);
    }//end boxTurno


    /**
     * @Route("/volverCola", name="volverCola", )
     */
    function volverCola(Request $request){
        //----------------------------------------------------
        //EL BOX VUELVE EL TURNO A LA COLA, ACTULIZA ESTADO DEL BOX
        //----------------------------------------------------

        $nroturno    = $request->get("idturno");
        $nrobox      = $request->get("boxaten");
        $nusuario    = $request->get("nusuario");
        $tra_sel     = trim($request->get("tra_sel"));
        $mot_sel     = "";//$request->get("mot_sel");

        //--------------------------------------------------------'
        //BUSCA EL MAYOR ORDEN'
        //--------------------------------------------------------'
        $rg_box = $this->getDoctrine()->getRepository(Boxes::class)->findBy(array(),array("boxOrden" => "DESC"));
        $nroorden= 1;
        if ($rg_box != null) {
            $nroorden= $nroorden + $rg_box[0]->getBoxOrden();
        }

        //------------------------------------'
        //ALTA EN COLA
        //------------------------------------'
        $n_cola = new ColaTurnos();
        $n_cola ->setClIdTurno($nroturno);
        $n_cola ->setClEstado('N');
        $n_cola ->setClColor('H00');
        $n_cola ->setClUbicacion(0);

        // Persisto el objeto en la base de datos
        $this->getDoctrine()->getManager()->persist($n_cola);
        $this->getDoctrine()->getManager()->flush();

        //------------------------------
        //ACTULIZA ESTADO  BOX
        //------------------------------
        $boxex_nro= $this->getDoctrine()->getRepository(Boxes::class)->findOneByBoxNumero($nrobox);
        if ($boxex_nro != null){
            $boxex_nro->setBoxEstado('S');
            $boxex_nro->setBoxIdturno(null);
            $boxex_nro->setBoxOrden($nroorden);
            $boxex_nro->setBoxSonido('S');

            $this->getDoctrine()->getManager()->flush();
        }//end if


        //------------------------------------
        //busca id del tipo tramite
        //------------------------------------
        $tramite = $this->getDoctrine()->getRepository(Tramites::class)->findOneBytraTipoTramite($tra_sel);
        if (count($tramite)>0){
            $idTramite    = $tramite->getTraIdTramite();
        }else{
            $idTramite    = "";
        }

        //------------------------------
        //ACTULIZA ESTADO TURNO
        //------------------------------
        $turno_r = $this->getDoctrine()->getRepository(Turnos::class)->findOneBytrIdTurno($nroturno);
        if ($turno_r != null){
            $turno_r->setTrEstadoTurno(1); #vuelve en espera
            $turno_r->setTrUsuarioBox(null);
            $turno_r->setTrBoxAtencion(null);
            $turno_r->setTrBoxRetorno($nrobox);
            if (strlen($idTramite)>0){
                $turno_r->setTrTipoTramite($idTramite);
            }

            if (strlen($mot_sel)>0){
                $turno_r->setTrMotivo($mot_sel);
            }

            $this->getDoctrine()->getManager()->flush();
        }//end if

        $nroboxaten=0;
        $fbox      =0;
        $output[]=array();
        $output[]=array("nbox" => $nroboxaten, "fechabox" => $fbox);

        return new JsonResponse($output);
    }//end VolverCola


    /**
     * @Route("/volverAtendido", name="volverAtendido", )
     */
    function volverAtendido(Request $request){
        //----------------------------------------------------
        //EL BOX FINALIZA Y VUELVE EL TURNO A LA COLA, ACTULIZA ESTADO DEL BOX
        //----------------------------------------------------

        $nroturno    = $request->get("idturno");
        $nrobox      = $request->get("boxaten");
        $tra_sel     = trim($request->get("tra_sel"));
        $mot_sel     = "";//$request->get("mot_sel");
        $fechahoy    = new \DateTime();
        $letra       = "";

        //-------------------------------
        //'ACTULIZA A TURNO A ATENDIDO'
        //------------------------------
        $rg_turno_ate = $this->getDoctrine()->getRepository(Turnos::class)->findOneBytrIdTurno($nroturno);
        if (count($rg_turno_ate) > 0) {
            $letra       = $rg_turno_ate->getTrTicket();
            $fechaorigen = $rg_turno_ate->getTrFechahoraOrigen();

            $rg_turno_ate->setTrEstadoTurno(4);
            $rg_turno_ate->setTrFechahoraBox($fechahoy);
            $entityManager = $this->getDoctrine()->getEntityManager();
            $entityManager->flush();
        }//end if


        //--------------------------------------------------------'
        //BUSCA EL MAYOR ORDEN'
        //--------------------------------------------------------'
        $rg_box = $this->getDoctrine()->getRepository(Boxes::class)->findBy(array(),array("boxOrden" => "DESC"));
        $nroorden= 1;
        if ($rg_box != null) {
            $nroorden= $nroorden + $rg_box[0]->getBoxOrden();
        }

        //---------------------------
        //'ACTULIZA A BOX LIBRE'
        //---------------------------
        $boxex_up= $this->getDoctrine()->getRepository(Boxes::class)->findOneByBoxNumero($nrobox);
        if ($boxex_up != null){
            $boxex_up->setBoxEstado('S');
            $boxex_up->setBoxOrden($nroorden);
            $boxex_up->setBoxSonido('S');
            $boxex_up->setBoxIdturno(null);

            $this->getDoctrine()->getManager()->flush();
        }//end if

        //------------------------------------
        //busca id del tipo tramite
        //------------------------------------
        $tramite = $this->getDoctrine()->getRepository(Tramites::class)->findOneBytraTipoTramite($tra_sel);
        if (count($tramite)>0){
            $idTramite    = $tramite->getTraIdTramite();
        }

        /****************************
         * ALTA EN TURNO
         */
        $n_turnos = new Turnos();
        $n_turnos ->setTrFechahoraTurno($fechaorigen);
        $n_turnos ->setTrFechahoraOrigen($fechaorigen);
        $n_turnos ->setTrEstadoTurno(1); //ESTADO PENDIENTE
        $n_turnos ->setTrTipoTramite($idTramite);
        $n_turnos ->setTrTicket($letra);
        $n_turnos ->setTrMotivo($mot_sel);

        // Persisto el objeto en la base de datos
                                                                            $this->getDoctrine()->getManager()->persist($n_turnos);
        $this->getDoctrine()->getManager()->flush();

        $ultimo_id = $n_turnos->getTrIdTurno();

        //------------------------------------'
        //ALTA EN COLA
        //------------------------------------'
        $n_cola = new ColaTurnos();
        $n_cola ->setClIdTurno($ultimo_id);
        $n_cola ->setClEstado('N');
        $n_cola ->setClColor('H00');
        $n_cola ->setClUbicacion(0);

        // Persisto el objeto en la base de datos
        $this->getDoctrine()->getManager()->persist($n_cola);
        $this->getDoctrine()->getManager()->flush();

        $nroboxaten=0;
        $fbox      =0;
        $output[]=array();
        $output[]=array("nbox" => $nroboxaten, "fechabox" => $fbox);

        return new JsonResponse($output);
    }//end VolverCola


    /**
     * @Route("/buscaEstadoTicket", name="buscaEstadoTicket", )
     */
    function buscaEstadoTicket(Request $request){
        //----------------------------------------------------
        //COMPRUEBA ESTADO TURNO
        //----------------------------------------------------
        $nroturno  = $request->get("idturno");
        $nrobox    = $request->get("nrobox");
        $nusuario  = $request->get("nusuario");
        $fechahoy  = new \DateTime();
        $nroboxaten ="0";

        //---------------------------------------------------------------------------------------
        //BUSCA ESTADO TICKET
        //---------------------------------------------------------------------------------------
        $rg_turnosi = $this->getDoctrine()->getRepository(Turnos::class)->findOneBytrIdTurno($nroturno);

        if (count($rg_turnosi) > 0) {
            $estado = $rg_turnosi->getTrEstadoTurno();
            if (!is_null($rg_turnosi->getTrBoxAtencion())){
                $nroboxaten = $rg_turnosi->getTrBoxAtencion();
            }
        }//end if

        if ($estado == 5){
            //----------------------------------------
            //SI ESTA CANCELADO ACTULIZA ESTADO  BOX
            //----------------------------------------
            $boxex_nro= $this->getDoctrine()->getRepository(Boxes::class)->findOneByBoxNumero($nrobox);
            if ($boxex_nro != null){
                $boxex_nro->setBoxEstado('A');
                $boxex_nro->setBoxOrden(0);
                $boxex_nro->setBoxIdturno($nroturno);

                $this->getDoctrine()->getManager()->flush();
            }//end if

            //------------------------------
            //ACTULIZA ESTADO TURNO
            //------------------------------
            $turno_r = $this->getDoctrine()->getRepository(Turnos::class)->findOneBytrIdTurno($nroturno);
            if ($turno_r != null){
                $turno_r->setTrEstadoTurno(2);
                $turno_r->setTrUsuarioBox($nusuario);
                $turno_r->setTrBoxAtencion($nrobox);
                $turno_r->setTrFechahoraTurno($fechahoy);

                $this->getDoctrine()->getManager()->flush();
            }//end if
        }//end if

        $output[]=array("nbox" => $nroboxaten);
        return new JsonResponse($output);
    }//end boxTurno


    /**
     * @Route("/datosPersonales", name="datosPersonales", )
     */
    public function datosPersonalesAction(Request $request){
        //=================================================================================
        //BUSCA DATOS PERSONALES
        //=================================================================================
        $n_doc_sel = $request ->get("nrodoc");
        $nroturno  = $request ->get("nroturno");

        /* ---------------------------------
            BUSCA EL MOTIVO DEL TURNO
        -----------------------*/
        $n_tramite=0;
        $n_id_motivo=0;

        /*$query_f = $this->getDoctrine()->getEntityManager()->createQuery("SELECT tm FROM AppBundle:Turnos tr INNER JOIN AppBundle:TramitesMotivos tm WITH tr.trMotivo = tm.idMotivo WHERE  tr.trIdTurno = '$nroturno'" );
        $rg_tk   = $query_f->getResult();
        if (count($rg_tk)>0){
            foreach ($rg_tk as $datos_r) {
                $n_id_motivo = $datos_r->getIdMotivo();   //idMotivo
                $n_tramite   = $datos_r->getTipoTramite();//tipo tramite
            }
        }//endIf*/

        $query_f = $this->getDoctrine()->getEntityManager()->createQuery("SELECT tm FROM AppBundle:Turnos tr INNER JOIN AppBundle:Tramites tm WITH tr.trTipoTramite = tm.traIdTramite WHERE  tr.trIdTurno = '$nroturno'" );
        $rg_tk   = $query_f->getResult();
        if (count($rg_tk)>0){
            foreach ($rg_tk as $datos_r) {
                $n_tramite   = $datos_r->getTraTipoTramite();//tipo tramite
            }
        }//endIf


        /*$rg_turno_ate = $this->getDoctrine()->getRepository(Turnos::class)->findOneBytrIdTurno($nroturno);
        if (count($rg_turno_ate) > 0) {
            $n_id_motivo = $rg_turno_ate->getTrMotivo();
        }//end if*/

        /* ---------------------------------
            BUSCA EL MOTIVO TIPO TRAMITE
        -----------------------*/
        /*$rg_turno_ate = $this->getDoctrine()->getRepository(TramitesMotivos::class)->findOneByIdMotivo($n_id_motivo);
        if (count($rg_turno_ate) > 0) {
            $n_tramite = $rg_turno_ate->getTipoTramite();
        }//end if*/
        $tramites[] = array();
        $rg_tramite = $this->getDoctrine()->getRepository(TramitesMotivos::class)->findByTipoTramite($n_tramite);
        if (count($rg_tk)>0) {
            foreach ($rg_tramite as $datos_r) {
                $n_mot_id = $datos_r->getIdMotivo();
                $n_mot_descrip = $datos_r->getDescripcion();

                $tramites[] = array("a_mot_id" => $n_mot_id, "a_mot_descrip" => $n_mot_descrip);
            }//end foreach
        }

        $sigmu_sn = $this->getDoctrine()->getEntityManager('sigmu')->getRepository(Padron::class)->findOneBypdoc($n_doc_sel);
        if (count($sigmu_sn) > 0) {
            $n_ape_nom= trim($this->normalizarString($sigmu_sn->getApellidoYNombre()));

            if ( is_null($sigmu_sn->getMail())){
                $n_email  ="";
            }else{
                $n_email  = $sigmu_sn->getMail();
            }

            if ( is_null($sigmu_sn->getTelefono())){
                $n_tel    ="";
            }else{
                $n_tel    = $sigmu_sn->getTelefono();
            }//endIf
        }else{
            $n_ape_nom  ="";
            $n_tel      ="";
            $n_email    ="";
        }//endIf
        $n_tramite = trim($n_tramite);

        $output[]=array("a_apeynom"  => $n_ape_nom, "a_email"  => $n_email, "a_tel"  => $n_tel, "a_sel_tramite"  => $n_tramite, "a_motivo"  => $n_id_motivo, "a_tramites" => $tramites);
        $dataE = $this->convert_from_latin1_to_utf8_recursively($output);
        return new JsonResponse($dataE);

        /*$response = new Response();
        $response->setContent(json_encode($output, JSON_UNESCAPED_UNICODE));
        $response->headers->set("Content-Type", "application/json");
        return $response;*/
    }//end datosPersonales

    /**
     * @Route("/buscaMotivos", name="buscaMotivos", )
     */
    public function buscaMotivosAction(Request $request){
        //=================================================================================
        //BUSCA MOTIVO TRAMITE SELECCIONADO
        //=================================================================================
        $n_tramite   = $request ->get("n_tramite");
        $nro_usuario = $request ->get("nrouser");

        /*$motivos = $this->getDoctrine()->getRepository(TramitesMotivos::class)->findBy(array("tipoTramite" => $n_tramite),array("descripcion" => "ASC"));
        foreach ($motivos as $datos_r){
            $n_mot_id       = $datos_r->getIdMotivo();
            $n_mot_descrip  = $datos_r->getDescripcion();

            $output[]=array("a_mot_id" => $n_mot_id, "a_mot_descrip" => $n_mot_descrip, 'permite' => 'S' );
        }//end foreach*/

        //-------------------------------------------------------------
        //BUSCA LOS TRAMITES ASIGNADO AL USUARIO

        $permite_sn = 'N';
        /*$user_tr     = $this->getDoctrine()->getRepository(UsuariosTramites::class)->findByusTrUsuario($nro_usuario);
        if (count($user_tr)>0){
            foreach ($user_tr as $data_ut){
                $nro_tramite = $data_ut->getUsTrTramite()->getTraTipoTramite();
                $user_tra[]  = array("ustramite" => trim($nro_tramite));
            }//for

            foreach ($user_tra as $nro_tramite) {
                if ($n_tramite == $nro_tramite["ustramite"]){
                    $permite_sn="S";
                    break;
                }
            }//for
        }*/
        $output[]=array("a_mot_id" => 0, "a_mot_descrip" => 0, 'permite' => $permite_sn  );

        return new JsonResponse($output);
    }//end buscaMotivos

    /**
     * @Route("/buscaCancelados", name="buscaCancelados")
     */
    public function buscaCanceladosAction(Request $request){
        //---------------------------------------------------
        //BUSCA CANCELADOS
        //---------------------------------------------------
        $id_box_sel  = $request ->get("nro_box");
        $iduser      = $request ->get("nusuario");

        $fecha_hoy   = new \DateTime();
        $today       = $fecha_hoy->format('Y-m-d');
        $hora_hoy    = $fecha_hoy->format('H:i');

        //-------------------------------------------------------------
        //BUSCA LOS TRAMITES ASIGNADO AL USUARIO
        $user_tr     = $this->getDoctrine()->getRepository(UsuariosTramites::class)->findByusTrUsuario($iduser);
        if ($user_tr != null) {
            foreach ($user_tr as $data_ut){
                $nro_tramite = $data_ut->getUsTrTramite()->getTraIdTramite();
                $user_tra[]  = array("ustramite" => trim($nro_tramite));
            }
        }//endIf

        $estadocl = 5; //cancelados
        $sqles = "SELECT T ";
        $sqles = $sqles . "FROM  AppBundle:Turnos T";
        $sqles = $sqles  . " WHERE (T.trEstadoTurno =". $estadocl ." ) ";
        $sqles = $sqles  . "AND  T.trFechahoraTurno >= '$today'";

        $query_listado  = $this->getDoctrine()->getEntityManager()->createQuery($sqles);
        $rpers          = $query_listado->getResult();

        $nro_oden=1;
        $tipo_ventana =2;

        $output[]=array();
        foreach ($rpers as $datos_r){
            // -------------------------------------------------
            //genera la ruta para abrir el form de modificacion
            // -------------------------------------------------
            $n_idturno          = $datos_r->getTrIdTurno();
            $n_ticket           = $datos_r->getTrTicket();
            $idTramite          = $datos_r->getTrTipoTramite();
            $n_fecha_cancelado  = $datos_r->getTrFechahoraCancelado();
            $hora_cancelado     = $n_fecha_cancelado->format('H:i');
            $nroDocumento       = $datos_r->getTrNroDoc();

            $diferencia_h= ( strtotime($hora_hoy) - strtotime($hora_cancelado) )/60;
            $t_espera    = $diferencia_h;

            $permite_sn="N";
            foreach ($user_tra as $nro_tramite) {
                if ($idTramite == $nro_tramite["ustramite"]){
                    $permite_sn="S";
                    break;
                }
            }//for

            if (($t_espera <11 ) && ($permite_sn == "S")){

                $n_icon = "<span  class='fa fa-history' data-toggle='tooltip' title='RETOMAR TICKET'  aria-hidden='true' onclick='javascript:Turnosel($n_idturno, $nro_oden ,2, $id_box_sel , $tipo_ventana, 5,$nroDocumento)' style='cursor: pointer; font-size: 22px;'></span>";
                //------------------------------
                //crea el array con los datos

                $output[]=array("a_fecha" => $t_espera, "a_ticket" => $n_ticket, "a_icon" => $n_icon, "a_turno" => $n_idturno,  "a_hora" => $hora_cancelado );
                $nro_oden +=1;
            }//endIf
        }//end foreach

        return new JsonResponse($output);
    }//end function


    /**
     * Encode array from latin1 to utf8 recursively
     * @param $dat
     * @return array|string
     */
    public static function convert_from_latin1_to_utf8_recursively($dat)
    {
        if (is_string($dat)) {
            return utf8_encode($dat);
        } elseif (is_array($dat)) {
            $ret = [];
            foreach ($dat as $i => $d) $ret[ $i ] = self::convert_from_latin1_to_utf8_recursively($d);

            return $ret;
        } elseif (is_object($dat)) {
            foreach ($dat as $i => $d) $dat->$i = self::convert_from_latin1_to_utf8_recursively($d);

            return $dat;
        } else {
            return $dat;
        }
    }
}
