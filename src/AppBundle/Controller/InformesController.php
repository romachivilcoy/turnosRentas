<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Boxes;
use AppBundle\Entity\ColaTurnos;
use AppBundle\Entity\Tickets;
use AppBundle\Entity\Turnos;
use AppBundle\Entity\Usuarios;
use AppBundle\Entity\UsuariosBox;
use AppBundle\Entity\Tramites;
use AppBundle\Entity\TramitesMotivos;
use AppBundle\Entity\Padron;
use AppBundle\Entity\Estados;
use AppBundle\Entity\HistoricoTurnos;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints\DateTime;
use Doctrine\ORM\Query\ResultSetMapping;

class InformesController extends DefaultController
{

    /**
     * @Route("/estadisticas", name="estadisticas")
     */
    public function estadisticasAction(Request $request)
    {
        //-------------------------------------------------------------
        //BUSCA LOS USUARIO Y BOXES
        //-------------------------------------------------------------
        $iduser     = $request->getSession()->get("usuarioLogeado");

        $sqles = "SELECT U.usTrUsuario, B.usbxNrobox ";
        $sqles = $sqles . "FROM  AppBundle:UsuariosTramites U JOIN AppBundle:UsuariosBox B WITH U.usTrUsuario = B.usbxIdus WHERE B.usbxVigente='S' GROUP BY U.usTrUsuario, B.usbxNrobox ORDER BY B.usbxNrobox";
        $query_box   = $this->getDoctrine()->getEntityManager()->createQuery($sqles);
        $r_cant      = $query_box->getResult();

        $fecha_hoy   = new \DateTime();
        $hora_hoy    = $fecha_hoy->format('H:i');

        //$datos_box[]=array();
        foreach ($r_cant as $datos_d){
            $id_usuario  = $datos_d['usTrUsuario'];

            $usu = $this->getDoctrine()->getEntityManager('sigmu')->getRepository(Usuarios::class)->findByusId($id_usuario);
            $nomUsuario  = utf8_encode($usu[0]->getUsNombre());

            $nro_box     = $datos_d['usbxNrobox']; //NRO BOX USUARIO

            $nro_turno      = "-";
            $dni_turno      = "";
            $n_ape_nom      = "";
            $ticket_turno   = "";
            $fecha_turno    = "";
            $fecha_box      = "";
            $t_espera       = 0;
            $t_atendido     = 0;
            $t_vacio        ="-";
            $busca_vacio    ="N";
            $estado_box     ="A";
            $ttramite       ="";

            //busca datos del box
            $rg_box = $this->getDoctrine()->getRepository(Boxes::class)->findBy(array("boxUsuario" => $id_usuario, "boxEstado"  => array('S', 'A')));
            if ($rg_box != null) {
                foreach ($rg_box as $datos_r) {
                    $t_activo        = $datos_r->getBoxTiempo();
                    //if ($t_vacio == 0){
                        $t_inicio       = $datos_r->getBoxFecha();
                        $hora_inicio    = $t_inicio->format('H:i');
                        $diferencia_h   = (strtotime($hora_hoy) - strtotime($hora_inicio)) / 60;
                        $t_vacio        = $diferencia_h + $t_activo;
                    //}

                    //--------------------------------------
                    //BUSCA DATOS TURNO
                    //--------------------------------------
                    $nro_turno = $datos_r->getBoxIdturno();
                    $rg_turnosi = $this->getDoctrine()->getRepository(Turnos::class)->findOneBytrIdTurno($nro_turno);
                    if (count($rg_turnosi) > 0) {
                        $dni_turno      = $rg_turnosi->getTrNroDoc();
                        $ticket_turno   = $rg_turnosi->getTrTicket();
                        $fecha_turno    = $rg_turnosi->getTrFechahoraOrigen();//hora que saco el turno
                        $fecha_box      = $rg_turnosi->getTrFechahoraTurno(); //hora que el box lo empezo a atendenr

                        $hora_origen    = $fecha_turno->format('H:i');
                        $hora_box       = $fecha_box->format('H:i');

                        $diferencia_h   = (strtotime($hora_box) - strtotime($hora_origen)) / 60;//hora que entro al box menos la hora de que saco
                        $t_espera       = $diferencia_h;


                        $diferencia_h   = (strtotime($hora_hoy) - strtotime($hora_box)) / 60; //hora actual menos la hora que entro al box
                        $t_atendido     = $diferencia_h;

                        $idTramite      = $rg_turnosi->getTrTipoTramite();
                        /* BUSCA TIPO TRAMITE */
                        $tramite    = $this->getDoctrine()->getRepository(Tramites::class)->findOneBytraIdTramite($idTramite);
                        $ttramite = $tramite->getDescripcion();
                    }//end if

                    //----------------------------------------------
                    //BUSCA NOMBRE PADRON
                    //----------------------------------------------
                    $n_ape_nom = "";
                    if ($dni_turno <> "") {
                        $sigmu_sn = $this->getDoctrine()->getEntityManager('sigmu')->getRepository(Padron::class)->findOneBypdoc($dni_turno);
                        if (count($sigmu_sn) > 0) {
                            $n_ape_nom = trim($this->cambiar_utf8($sigmu_sn->getApellidoYNombre()));
                        }
                    }//if

                }//for
            }else{
                $busca_vacio = "S";
            }

            if ($busca_vacio == "S"){
                $estado_box ="I";

                $t_vacio =0;
                $rg_box = $this->getDoctrine()->getRepository(Boxes::class)->findOneByboxUsuario($id_usuario);
                if ($rg_box != null) {
                    $t_vacio        = $rg_box->getBoxTiempo();//busca el  tiempo que estuvo activo
                }
            }//if vacio


            //----------------------------------
            //TOTAL BOX
            $sql_total      ="SELECT  count(tr.trIdTurno) as total FROM AppBundle:Turnos tr WHERE tr.trUsuarioBox=".$id_usuario."";
            $query_total    = $this->getDoctrine()->getEntityManager()->createQuery($sql_total);
            $rtotal         = $query_total->getResult();
            foreach ($rtotal as $datos_t){
                $total_box = $datos_t['total'];
            }

            $datos_box[]=array("id_usuario" => $nomUsuario, "estado_box" => $estado_box, "nro_box" => $nro_box, "ttramite" => $ttramite, "dni_turno" => $dni_turno, "ticket_turno" =>$ticket_turno, "fecha_turno" =>$t_espera, "fecha_box"=>$t_atendido, "t_vacio" => $t_vacio, "total_box" => $total_box);
        }//for usuario box


        //------------------------------------------------------------------------------
        //BUSCA LISTA PENDIENTE
        //------------------------------------------------------------------------------
        $estadope    = 1; //pendientes
        $estadob     = 2; //en box
        $estadopr    = 3; //presente
        $estadocl    = 5; //'cancelado'
        $estadoe     = 7; //espera

        $fecha_hoy   = new \DateTime();
        $today       = $fecha_hoy->format('Y-m-d');
        $hora_hoy    = $fecha_hoy->format('H:i');

        //----------------------------------
        //TOTAL EN ESPERA
        //----------------------------------

        $total_espera   = 0;
        $sql_total      = "SELECT count(T.trIdTurno) as total ";
        $sql_total      = $sql_total . "FROM  AppBundle:Turnos T";
        $sql_total      = $sql_total  . " WHERE (T.trEstadoTurno =". $estadope ." )";//OR (T.trEstadoTurno =". $estadocl ." )
        $sql_total      = $sql_total  . "AND  T.trFechahoraTurno >= '$today'";
        $query_total    = $this->getDoctrine()->getEntityManager()->createQuery($sql_total);
        $rtotal         = $query_total->getResult();
        foreach ($rtotal as $datos_t){
            $total_espera = $datos_t['total'];
        }

        //----------------------------------
        $sqles = "SELECT T ";
        $sqles = $sqles . "FROM  AppBundle:Turnos T JOIN AppBundle:Tramites R WITH T.trTipoTramite = R.traIdTramite";
        $sqles = $sqles  . " WHERE (T.trEstadoTurno =". $estadope ." ) ";
        $sqles = $sqles  . "AND  T.trFechahoraTurno >= '$today' ORDER BY T.trEstadoTurno DESC, R.traPrioridad DESC, T.trIdTurno";

        $query_listado  = $this->getDoctrine()->getEntityManager()->createQuery($sqles)->setMaxResults(4);
        $rpers          = $query_listado->getResult();
        $nro_oden       = 0;
        $tipo_ventana   = 1;
        $turnosEspera   = 0;

        $lista_pendientes[]=array();
        foreach ($rpers as $datos_r){
            // -------------------------------------------------
            //genera la ruta para abrir el form de modificacion
            // -------------------------------------------------
            $n_fecha_or  = $datos_r->getTrFechahoraOrigen();
            $nro_ticket  = $datos_r->getTrTicket();
            $idTramite   = $datos_r->getTrTipoTramite();
            $n_estado    = $datos_r->getTrEstadoTurno();
            $hora_origen = $n_fecha_or->format('H:i');


            if ($n_estado === 3 || $n_estado === 4 ){
                $t_espera  = 0;
            }else{
                $diferencia_h= ( strtotime($hora_hoy) - strtotime($hora_origen) )/60;
                $t_espera    = $diferencia_h;
            }//end if

            //NO ESTA CANCELADO
            //
            /*---------------------------*/
            /* BUSCA TIPO TRAMITE */
            $tramite    = $this->getDoctrine()->getRepository(Tramites::class)->findOneBytraIdTramite($idTramite);
            $n_ttramite = $tramite->getDescripcion();

            //------------------------------
            //crea el array con los datos
            $lista_pendientes[]=array("a_ticket" => $nro_ticket, "a_fecha" => $t_espera, "a_tramite" => $n_ttramite );
            $nro_oden +=1;

        }//end foreach

        if ($nro_oden == 0){
            $lista_pendientes[]=array("a_ticket" => '', "a_fecha" => '', "a_tramite"  => '');
        }
        //---------------------------------------------------------


        //'------------------------------'
        //BUSCA TOTAL DIA
        //--------------------------------
        $fecha_hoy  = new \DateTime();
        $today      = $fecha_hoy->format('Y-m-d');

        $query_f = $this->getDoctrine()->getEntityManager()->createQuery("SELECT  tr.trTipoTramite as tptramite, COUNT(tr.trIdTurno) as cantidad FROM AppBundle:Turnos tr group by tr.trTipoTramite"); //WHERE tr.trEstadoTurno = 4
        $rg_tk   = $query_f->getResult();

        $total_escribano    = 0;
        $total_inmueble     = 0;
        $total_comercio     = 0;
        $total_moto         = 0;
        $total_cementerio   = 0;
        $total_juicio       = 0;
        $total_varios       = 0;
        $total_arba         = 0;
        $total_otros        = 0;
        foreach ($rg_tk as $datos_d){

            $tipo_tk = $datos_d['tptramite'];


            switch ($tipo_tk){
                case 1:
                    $total_inmueble     = $datos_d['cantidad'];
                    break;
                case 2:
                    $total_comercio     = $datos_d['cantidad'];
                    break;
                case 3:
                    $total_moto         = $datos_d['cantidad'];
                    break;
                case 4:
                    $total_cementerio   = $datos_d['cantidad'];
                    break;
                case 5:
                    $total_escribano    = $datos_d['cantidad'];
                    break;
                case 6:
                    $total_juicio       = $datos_d['cantidad'];
                    break;
                case 7:
                    $total_varios      = $datos_d['cantidad'];
                    break;
                    break;
                case 9:
                    $total_arba        = $datos_d['cantidad'];
                    break;
                default:
                    $total_otros       = $datos_d['cantidad'];
                    break;
            }//end SWITCH
        }//end SWITCH

        $rg_cancelados= $this->getDoctrine()->getRepository(Turnos::class)->findBy(array('trEstadoTurno' => 5));
        if ($rg_cancelados != null) {
            $total_otros= count($rg_cancelados);
        }//end if

        $total = $total_escribano + $total_inmueble + $total_comercio + $total_moto + $total_cementerio + $total_juicio + $total_varios + $total_arba + $total_otros;

        // -----------------------------------------
        //CALCULA TOTAL MES
        //-----------------------------------------
        $mes_d    = date("M");//Actual Mes Palabra
        $mes_a    = date("m");//Actual mes numero
        $an_a     = date("Y");//Actual año numero
        $diaActual = date("d");

        $fecha_desde    = $an_a .'-'. $mes_a . '-' . '01';
        $fecha_hasta    = $an_a .'-'. $mes_a . '-' . $diaActual;//'29';

        $query_m  = $this->getDoctrine()->getEntityManager()->createQuery("SELECT  ht.htTipoTramite as httramite, COUNT(ht.htIdTurno) as cantidad FROM AppBundle:HistoricoTurnos ht WHERE ht.htFechaTurno >= '$fecha_desde' AND ht.htEstadoTurno <> 5 group by ht.htTipoTramite");
        $rg_tmes  = $query_m->getResult();

        $total_escribano_mes        = 0;
        $total_inmueble_mes         = 0;
        $total_comercio_mes         = 0;
        $total_moto_mes             = 0;
        $total_cementerio_mes       = 0;
        $total_juicio_mes           = 0;
        $total_varios_mes           = 0;
        $total_arba_mes             = 0;
        $total_otros_mes            = 0;
        foreach ($rg_tmes as $datos_mes){

            $tipo_tk = $datos_mes['httramite'];
            switch ($tipo_tk){
                case 1:
                    $total_inmueble_mes = $datos_mes['cantidad'];
                    break;
                case 2:
                    $total_comercio_mes = $datos_mes['cantidad'];
                    break;
                case 3:
                    $total_moto_mes     = $datos_mes['cantidad'];
                    break;
                case 4:
                    $total_cementerio_mes  = $datos_mes['cantidad'];
                    break;
                case 5:
                    $total_escribano_mes  = $datos_mes['cantidad'];
                    break;
                case 6:
                    $total_juicio_mes   = $datos_mes['cantidad'];
                    break;
                case 7:
                    $total_varios_mes   = $datos_mes['cantidad'];
                    break;
                case 9:
                    $total_arba_mes   = $datos_mes['cantidad'];
                    break;
                default:
                    $total_otros_mes    = $datos_mes['cantidad'];
                    break;
            }//end SWITCH
        }//end SWITCH

        $query_c = $this->getDoctrine()->getEntityManager()->createQuery("SELECT  ht.htEstadoTurno as httramite, COUNT(ht.htIdTurno) as cantidad FROM AppBundle:HistoricoTurnos ht WHERE ht.htFechaTurno >= '$fecha_desde' AND ht.htEstadoTurno = 5 group by ht.htEstadoTurno");
        $rg_cancel  = $query_c->getResult();
        foreach ($rg_cancel as $datos_cancel){
            $total_otros_mes = $datos_cancel['cantidad'];
        }

        $mes_total = $total_escribano_mes + $total_inmueble_mes + $total_comercio_mes + $total_moto_mes + $total_cementerio_mes + $total_juicio_mes+ $total_varios_mes+ $total_arba_mes  + $total_otros_mes;

        //----------------------------------
        //LISTA ULTIMO DIAS
        //---------------------------------
        $sql  = 'SELECT TOP(10) CONVERT(varchar(10),ht.ht_fecha_turno,120) AS fecha_turno, count(ht.ht_id_turno) as cantidad FROM Historico_turnos ht WHERE  ht.ht_estado_turno=4 GROUP BY CONVERT(varchar(10),ht.ht_fecha_turno,120) ORDER BY CONVERT(varchar(10),ht.ht_fecha_turno,120) DESC';
        $stmt = $this->getDoctrine()->getEntityManager()->getConnection()->prepare($sql);
        $stmt->execute();
        $rg_consulta = $stmt->fetchAll();

        //----------------------------------
        //LISTA X USUARIO
        //---------------------------------
        $query_f = "SELECT u.us_nombre, COUNT(*) as cantidad FROM Historico_turnos ht LEFT JOIN SIGMU.dbo.Usuarios u ON u.us_id = ht.ht_usuario_box WHERE ht_fecha_turno BETWEEN '$fecha_desde' AND '$fecha_hasta' GROUP BY u.us_nombre";
        $stmt = $this->getDoctrine()->getEntityManager()->getConnection()->prepare($query_f);
        $stmt->execute();
        $rg_x_usuario = $stmt->fetchAll();


        $query_t ="SELECT  u.us_nombre, t.descripcion, count(*) as cantidad FROM Historico_turnos ht LEFT JOIN SIGMU.dbo.Usuarios u ON u.us_id = ht.ht_usuario_box LEFT JOIN Tramites t ON t.tra_id_tramite = ht.ht_tipo_tramite WHERE ht.ht_fecha_turno BETWEEN '$fecha_desde' AND '$fecha_hasta' GROUP BY u.us_nombre, t.descripcion ORDER BY  u.us_nombre,t.descripcion ASC";
        $stmt = $this->getDoctrine()->getEntityManager()->getConnection()->prepare($query_t);
        $stmt->execute();
        $rg_x_tramite_user = $stmt->fetchAll();


        $tipoTramite = $this->getDoctrine()->getRepository(Tramites::class)->findBy(array("vigente" => 'S'),array("descripcion" => "ASC"));

        $modulos = $this->recuperarModulosUsuario($iduser);

        return $this->render('informes/estadisticas.html.twig', array('modulos' => $modulos, "datosEstadisticas" => $rg_consulta, "total" => $total,  "total_escribano" => $total_escribano, "total_inmueble" => $total_inmueble, "total_comercio" => $total_comercio, "total_moto" => $total_moto, "total_cementerio" => $total_cementerio, "total_juicio" => $total_juicio, "total_varios" => $total_varios, "total_arba" => $total_arba, "total_otros" => $total_otros, "mes_hoy" => $mes_d, "mes_total" => $mes_total,  "total_escribano_mes" => $total_escribano_mes, "total_inmueble_mes" => $total_inmueble_mes, "total_comercio_mes" => $total_comercio_mes, "total_moto_mes" => $total_moto_mes, "total_cementerio_mes" => $total_cementerio_mes, "total_juicio_mes" => $total_juicio_mes, "total_varios_mes" => $total_varios_mes, "total_arba_mes" => $total_arba_mes, "total_otros_mes" => $total_otros_mes, "arr_box" => $datos_box, "arr_pendientes" => $lista_pendientes, "total_espera" => $total_espera, "total_x_usuario" => $rg_x_usuario, "total_x_tramite_user" => $rg_x_tramite_user, "tipoTramite" => $tipoTramite));
    }

    /**
     * @Route("/listaEstadisticas", name="listaEstadisticas")
     */
    public function listaEstadisticasAction(Request $request)
    {
        //------------------------------------------------
        //busca el nro box del usuario
        //------------------------------------------------
        $iduser       = $request->getSession()->get("usuarioLogeado");
        $id_box_sel   = 0;
        $p_datos_box  = $this->getDoctrine()->getRepository(UsuariosBox::class)->findOneByusbxIdus($iduser);
        if (count($p_datos_box) > 0){
            $id_box_sel   = $p_datos_box->getUsbxNrobox();
        }

        //------------------------------------------------
        //busca estado del box
        //------------------------------------------------
        $estado_box = 'N';
        $p_est_box  = $this->getDoctrine()->getRepository(Boxes::class)->findOneByboxNumero($id_box_sel);
        if ( count($p_est_box) > 0){
            $estado_box   = $p_est_box->getBoxEstado();
        }

        //-------------------------------
        //'BUSCA EN ESPERA'
        //-------------------------------
        $estadope = 1; //'pendiente'
        $estadob  = 2; //'en box'
        $estadopr = 3; //'presente'
        $estadoa  = 4; //'atendido'
        $estadoes = 7; //'en espera'


        $fecha_hoy   = new \DateTime();
        $today       = $fecha_hoy->format('Y-m-d');
        $dia_hoy     = $fecha_hoy->format('d/m/Y');
        $hora_hoy    = $fecha_hoy->format('H:i');

        $sqles = "SELECT COUNT(T.trEstadoTurno) AS Tespera ";
        $sqles = $sqles . "FROM  AppBundle:Turnos T";
        $sqles = $sqles  . " WHERE ((T.trEstadoTurno =". $estadope ." ) OR (T.trEstadoTurno =". $estadob ." AND T.trBoxAtencion=".$id_box_sel." ) OR (T.trEstadoTurno =". $estadoes ." ))";
        $sqles = $sqles  . "AND  T.trFechahoraTurno >= '$today'";

        $query_box  = $this->getDoctrine()->getEntityManager()->createQuery($sqles);
        $r_cant     = $query_box->getResult();

            foreach ($r_cant as $datos_d){
                $cant_espera  = $datos_d['Tespera'];
            }

        if ($cant_espera == 0){
            $mensaje = "Sin Turnos Pendientes";
        }else{
            $mensaje = $cant_espera . " Turno/s en Cola/s";
        }


        $modulos = $this->recuperarModulosUsuario($iduser);


        return $this->render('boxes/boxes.html.twig', array( 'modulos' => $modulos,'p_dia' => $dia_hoy, 'p_hora' => $hora_hoy,'p_nro_box' => $id_box_sel, 'p_est_box' => $estado_box, 'p_espera' => $mensaje));
    }

    /**
     * @Route("/atenciones", name="atenciones")
     */
    public function atencionesAction(Request $request)
    {
        //-----------------------------------------------------
        //FORM ATENCIONES
        //BUSCA LAS TURNOS DEL DIA
        //-----------------------------------------------------
        $iduser     = $request->getSession()->get("usuarioLogeado");
        $modulos = $this->recuperarModulosUsuario($iduser);


        $datosTurnos       = $this->getDoctrine()->getRepository(Turnos::class)->findall();
        if ($datosTurnos != null){
            $dEstados       = $this->getDoctrine()->getRepository(Estados::class)->findall();
            $dTramites      = $this->getDoctrine()->getRepository(Tramites::class)->findall();
            $dMotivos       = $this->getDoctrine()->getRepository(TramitesMotivos::class)->findall();

            foreach ($datosTurnos as $datos_r){
                $n_doc              = $datos_r->getTrNroDoc();
                $n_fechahoraorigen  = $datos_r->getTrFechahoraOrigen();
                $trEstadoTurno      = $datos_r->getTrEstadoTurno();
                $trBoxAtencion      = $datos_r->getTrBoxAtencion();
                $trFechahoraBox     = $datos_r->getTrFechahoraBox();
                if ($datos_r->getTrTipoTramite() != null){
                    $trTipoTramite      = $datos_r->getTrTipoTramite();
                }else{
                    $trTipoTramite ="";
                }
                $trMotivo           = $datos_r->getTrMotivo();

                $sigmu_sn = $this->getDoctrine()->getEntityManager('sigmu')->getRepository(Padron::class)->findOneBypdoc($n_doc);
                if (count($sigmu_sn) > 0) {
                    $n_ape_nom= trim($sigmu_sn->getApellidoYNombre());/*trim($this->cambiar_utf8($sigmu_sn->getApellidoYNombre()));*/
                }else{
                    $n_ape_nom  ="-";
                }

                $resultado[]=array("a_ndoc" => $n_doc, "a_ape_nom" =>$n_ape_nom, "a_fechahoraorigen" => $n_fechahoraorigen, "a_estadoturno" => $trEstadoTurno, "a_boxbtencion" => $trBoxAtencion, "a_fechahorabox" => $trFechahoraBox, "a_tipotramite" => $trTipoTramite, "a_motivo" => $trMotivo );
            }//end foreach

            $dataE = $this->convert_from_latin1_to_utf8_recursively($resultado);
            return $this->render('informes/atenciones.html.twig', array('modulos' => $modulos,'resultado' => $datosTurnos, 'resultado' => $dataE,'mensaje' => '', 'tipoMensaje' => '','dEstados'=>$dEstados,'dTramites'=>$dTramites,'dMotivos'=>$dMotivos));
        }else{
            return $this->render('informes/atenciones.html.twig',array('modulos' => $modulos,'mensaje' => '', 'tipoMensaje' => ''));
        }
    }

    /**
     * @Route("/listarAtenciones", name="listarAtenciones")
     */
    public function listarAtencionesAction(Request $request)
    {
        $iduser     = $request->getSession()->get("usuarioLogeado");
        $modulos = $this->recuperarModulosUsuario($iduser);

        $fechad      = $request->get("txt_fecha_desde");
        $fechah      = $request->get("txt_fecha_hasta");
        $persona     = $request->get("persona");
        $nroBox      = $request->get("nroBox");
        $estado      = $request->get("estado");
        $tramite     = $request->get("tramite");
        $motivo      = $request->get("motivo");

        $sqlt="";
        $sqlht="";
        //if (!empty($persona ) or !empty($nroBox )){
            $sqlt  ="select t from AppBundle:Turnos t  where   1=1  ";
            $sqlht ="select t from AppBundle:HistoricoTurnos t  where  1=1 ";
        //}

        if (!empty($fechad)){

            $fecha_seld = $fechad ." 00:00:00";
            $fecha_desde= $fecha_seld;//\DateTime::createFromFormat("Y-m-d H:i:s", $fecha_seld)->format("d/m/Y H:i:s");
            $sqlt       = $sqlt . " and t.trFechahoraTurno>='$fecha_desde'";
            $sqlht      = $sqlht . " and t.htFechaTurno>='$fecha_desde'";
        }

        if (!empty($fechah)){
            $fecha_selh     = $fechah ." 23:59:00";
            $fecha_hasta    = $fecha_selh;//\DateTime::createFromFormat("Y-m-d H:i:s", $fecha_selh)->format("d/m/Y H:i:s");//->format("Y-m-d H:i:s");
            $sqlt        = $sqlt . " and t.trFechahoraTurno<='$fecha_hasta'";
            $sqlht       = $sqlht . " and t.htFechaTurno<='$fecha_hasta'";
        }

        if (!empty($persona)){
            $sqlt= $sqlt . " and t.trNroDoc=".$persona;
            $sqlht=$sqlht . " and t.htNroDoc=".$persona;
        }

        if ($nroBox <>"t"){
            if (strlen($nroBox)>0){
                $sqlt =$sqlt . " and t.trBoxAtencion=".$nroBox;
                $sqlht=$sqlht . " and t.htBoxAtencion=".$nroBox;
            }
        }

        if ($estado <>"t" && $estado<>"undefined"){
            if (strlen($estado)>0){
                $sqlt=$sqlt . " and t.trEstadoTurno=".$estado;
                $sqlht=$sqlht . " and t.htEstadoTurno=".$estado;
            }
        }

        if ($tramite <>"t" && $tramite<>"undefined"){
            if (strlen($tramite)>0){
                $sqlt=$sqlt . " and t.trTipoTramite=".$tramite;
                $sqlht=$sqlht . " and t.htTipoTramite=".$tramite;
            }
        }
        if ($motivo<>"t" && $motivo<>"undefined"){
            if (strlen($tramite)>0){
                $sqlt=$sqlt . " and t.trMotivo=".$motivo;
                $sqlht=$sqlht . " and t.htMotivo=".$motivo;
            }
        }

        $sqlt           = $sqlt . " order by t.trIdTurno";
        $sqlht          = $sqlht . " order by t.htIdTurno";
       // die($sqlt);

        $query          = $this->getDoctrine()->getEntityManager()->createQuery($sqlt);
        $datosTurnos    = $query->getResult();
        foreach ($datosTurnos as $datos_r){
            $n_doc              = $datos_r->getTrNroDoc();
            $n_fechahoraorigen  = $datos_r->getTrFechahoraOrigen();
            $trEstadoTurno      = $datos_r->getTrEstadoTurno();
            $trBoxAtencion      = $datos_r->getTrBoxAtencion();
            $trFechahoraBox     = $datos_r->getTrFechahoraBox();
            if ($datos_r->getTrTipoTramite() != null){
                $trTipoTramite      = $datos_r->getTrTipoTramite();
            }

            $trMotivo           = $datos_r->getTrMotivo();

            $sigmu_sn = $this->getDoctrine()->getEntityManager('sigmu')->getRepository(Padron::class)->findOneBypdoc($n_doc);
            if (count($sigmu_sn) > 0) {
                $n_ape_nom= trim($this->cambiar_utf8($sigmu_sn->getApellidoYNombre()));
            }else{
                $n_ape_nom  ="-";
            }

            $resultado[]=array("a_ndoc" => $n_doc, "a_ape_nom" =>$n_ape_nom, "a_fechahoraorigen" => $n_fechahoraorigen, "a_estadoturno" => $trEstadoTurno, "a_boxbtencion" => $trBoxAtencion, "a_fechahorabox" => $trFechahoraBox, "a_tipotramite" => $trTipoTramite, "a_motivo" => $trMotivo );
        }//end foreach


        $query          = $this->getDoctrine()->getEntityManager()->createQuery($sqlht);
        $datosTurnosHist   = $query->getResult();
        foreach ($datosTurnosHist as $datos_hr){
            $n_doc              = $datos_hr->getHtNroDoc();
            $n_fechahoraorigen  = $datos_hr->getHtFechaOrigen();
            $trEstadoTurno      = $datos_hr->getHtEstadoTurno();
            $trBoxAtencion      = $datos_hr->getHtBoxAtencion();
            $trFechahoraBox     = $datos_hr->getHtBoxFechahora();

            if ($datos_hr->getHtTipoTramite() != null){
                $trTipoTramite  = $datos_hr->getHtTipoTramite();
            }
            $trMotivo           = $datos_hr->getHtMotivo();

            $sigmu_sn = $this->getDoctrine()->getEntityManager('sigmu')->getRepository(Padron::class)->findOneBypdoc($n_doc);
            if (count($sigmu_sn) > 0) {
                $n_ape_nom= trim($this->cambiar_utf8($sigmu_sn->getApellidoYNombre()));
            }else{
                $n_ape_nom  ="-";
            }

            $resultadoht[]=array("a_ndoc" => $n_doc, "a_ape_nom" =>$n_ape_nom, "a_fechahoraorigen" => $n_fechahoraorigen, "a_estadoturno" => $trEstadoTurno, "a_boxbtencion" => $trBoxAtencion, "a_fechahorabox" => $trFechahoraBox, "a_tipotramite" => $trTipoTramite, "a_motivo" => $trMotivo );
        }//end foreach


        $dEstados       = $this->getDoctrine()->getRepository(Estados::class)->findall();
        $dTramites      = $this->getDoctrine()->getRepository(Tramites::class)->findall();
        $dMotivos       = $this->getDoctrine()->getRepository(TramitesMotivos::class)->findall();

        if (isset($resultado)  && isset($resultadoht)) {
            return $this->render('informes/atenciones.html.twig', array('modulos' => $modulos,"resultado" => $resultado, "resultadoht" => $resultadoht, 'mensaje' => '', 'tipoMensaje' => '', 'persona' => $persona, 'nroBox' => $nroBox, 'estado' => $estado, 'tramite' => $tramite, 'motivo' => $motivo, 'dEstados' => $dEstados, 'dTramites' => $dTramites, 'dMotivos' => $dMotivos));
        }elseif (isset($resultado) && !isset($resultadoht)){
            return $this->render('informes/atenciones.html.twig', array( 'modulos' => $modulos,"resultado" => $resultado,'mensaje' => '', 'tipoMensaje' => '','persona'=>$persona,'nroBox'=>$nroBox,'estado'=>$estado,'tramite'=>$tramite,'motivo'=>$motivo,'dEstados'=>$dEstados,'dTramites'=>$dTramites,'dMotivos'=>$dMotivos));
        }elseif (isset($resultadoht) && !isset($resultado)){
            return $this->render('informes/atenciones.html.twig', array( 'modulos' => $modulos,"resultadoht" => $resultadoht,'mensaje' => '', 'tipoMensaje' => '','persona'=>$persona,'nroBox'=>$nroBox,'estado'=>$estado,'tramite'=>$tramite,'motivo'=>$motivo,'dEstados'=>$dEstados,'dTramites'=>$dTramites,'dMotivos'=>$dMotivos));
        }else{
            return $this->render('informes/atenciones.html.twig',array('modulos' => $modulos,'mensaje' => 'No hay registros con los datos de búsqueda ingresado', 'tipoMensaje' => 'warning'));
        }
    }

    /**
     * @Route("/buscaDatosTurno", name="buscaDatosTurno")
     */
    public function buscaDatosTurnoAction(Request $request)
    {
        $idTurno = $request ->get("idTurno");
        $query = $this->getDoctrine()->getEntityManager()->createQuery( "SELECT t.trFechahoraTurno as horaTurno, t.trBoxAtencion as boxAtencion, t.trFechahoraBox as horaBox, e.etTipoEstado as estadoTurno, tt.descripcion as descTramite, tm.descripcion as descMotivo FROM AppBundle:Turnos t ,AppBundle:Tramites tt ,AppBundle:Estados e ,AppBundle:TramitesMotivos tm   where t.trTipoTramite=tt.traIdTramite and t.trMotivo=tm.idMotivo and e.etIdEstado=t.trEstadoTurno and t.trIdTurno=".$idTurno);
        $dTurno = $query->getResult();
        if (count($dTurno) <= 0){
            $query = $this->getDoctrine()->getEntityManager()->createQuery( "SELECT t.htFechaTurno as horaTurno,t.htBoxAtencion as boxAtencion,t.htBoxFechahora as horaBox, e.etTipoEstado as estadoTurno,tt.descripcion as descTramite, tm.descripcion as descMotivo FROM AppBundle:HistoricoTurnos t ,AppBundle:Tramites tt ,AppBundle:Estados e,AppBundle:TramitesMotivos tm where t.htTipoTramite=tt.traIdTramite and t.htMotivo=tm.idMotivo and e.etIdEstado=t.htEstadoTurno and t.htIdTurno=".$idTurno);
            $dTurno = $query->getResult();
        }
        return new JsonResponse($dTurno);
    }

    /**
     * @Route("/buscaBoxUtilizados", name="buscaBoxUtilizados")
     */
    public function buscaBoxUtilizadosAction(Request $request)
    {
        $query = $this->getDoctrine()->getEntityManager()->createQuery( "SELECT ub.usbxNrobox FROM AppBundle:UsuariosBox ub  group by ub.usbxNrobox order by ub.usbxNrobox asc");
        $boxes = $query->getResult();

        $querye   = $this->getDoctrine()->getEntityManager()->createQuery( "SELECT e.etIdEstado, e.etTipoEstado FROM AppBundle:Estados e  order by e.etTipoEstado asc");
        $estados = $querye->getResult();

        $queryt = $this->getDoctrine()->getEntityManager()->createQuery( "SELECT t.traIdTramite, t.traTipoTramite, t.descripcion FROM AppBundle:Tramites t WHERE t.vigente='S'  order by t.descripcion asc");
        $tramites = $queryt->getResult();

        $datosM = array();
        $querym  = $this->getDoctrine()->getEntityManager()->createQuery( "SELECT CONCAT(t.descripcion,'-', tm.descripcion) as descripcion, tm.idMotivo FROM AppBundle:Tramites t, AppBundle:TramitesMotivos tm where  t.traTipoTramite=tm.tipoTramite order by descripcion, tm.orden asc");
        $motivos = $querym->getResult();
        if (count($motivos)>0){
            foreach ($motivos as $dataR){
                $datosM[]= array("descripcion" => $dataR['descripcion'], "idMotivo" => $dataR['idMotivo']);
            }
        }else{$datosM[]=array();}
        $datosMoti = $this->convert_from_latin1_to_utf8_recursively($datosM);

        return new JsonResponse(array('result' => $boxes, 'resul_estados' =>$estados, 'resul_tramites' =>$tramites, 'resul_motivos' =>$datosMoti));
    }

    /**
     * @Route("/buscaEstados", name="buscaEstados")
     */
    public function buscaEstadosAction(Request $request)
    {
        $estados = $this->getDoctrine()->getRepository(Estados::class)->findBy(array(),array("etTipoEstado" => "ASC"));
        if ($estados != null){
            return new JsonResponse($estados);
        }
    }

    /**
     * @Route("/buscaTramites", name="buscaTramites")
     */
    public function buscaTramitesAction(Request $request)
    {
        $tramites = $this->getDoctrine()->getRepository(Tramites::class)->findBy(array(),array("descripcion" => "ASC"));
        if ($tramites != null){
            return new JsonResponse($tramites);
        }
    }

    /**
     * @Route("/buscaMotivos_informe", name="buscaMotivos_informe")
     */
    public function buscaMotivosAction(Request $request)
    {
        $idTipoTramite = $request ->get("idTramite");
        if ($idTipoTramite <> "t" ){

            //BUSCA LETRA TIPO TRAMITE
            $tramite = $this->getDoctrine()->getRepository(Tramites::class)->findByTraIdTramite($idTipoTramite);
            if (count($tramite)>0){
                $letra_tipo_tramite    = trim($tramite[0]->getTraTipoTramite());
            }//if

            $motivos = $this->getDoctrine()->getRepository(TramitesMotivos::class)->findBy(array("tipoTramite" => $letra_tipo_tramite),array("descripcion" => "ASC"));
            foreach ($motivos as $datos_r){

                $n_mot_id       = $datos_r->getIdMotivo();
                $n_mot_descrip  = $datos_r->getDescripcion();

                $output[]=array("a_mot_id" => $n_mot_id, "a_mot_descrip" => $n_mot_descrip );
            }//end foreach

        }else{
            $motivos = $this->getDoctrine()->getRepository(TramitesMotivos::class)->findBy(array(),array("descripcion" => "ASC"));
            foreach ($motivos as $datos_r){

                $n_mot_id       = $datos_r->getIdMotivo();
                $n_mot_descrip  = $datos_r->getDescripcion();

                $output[]=array("a_mot_id" => $n_mot_id, "a_mot_descrip" => $n_mot_descrip );
            }//end foreach
        }
        $dateE = $this->convert_from_latin1_to_utf8_recursively($output);

        return new JsonResponse($dateE);
    }


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
