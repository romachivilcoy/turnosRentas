<?php

namespace AppBundle\Controller;
use AppBundle\AppBundle;
use AppBundle\Entity\Tramites;
use AppBundle\Entity\TramitesMotivos;
use AppBundle\Entity\Tareas;
use AppBundle\Entity\Menues;
use AppBundle\Entity\UsuariosBox;
use AppBundle\Entity\Usuarios;
use AppBundle\Entity\UsuariosTramites;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class ConfiguracionController extends DefaultController
{
    /**
     * @Route("/configuracionBoxes", name="configuracionBoxes")
     */
    public function configuracionBoxesAction(Request $request){
        //---------------------------------------
        //BUSCA LOS USUARIOS POR BOX
        //---------------------------------------
        $iduser     = $request->getSession()->get("usuarioLogeado");
        //$usPrioridad = 0;

        $query = $this->getDoctrine()->getEntityManager()->createQuery( "SELECT ub FROM AppBundle:UsuariosBox ub WHERE ub.usbxVigente='S' AND ub.usbxDefault='S'  order by ub.usbxIdus,ub.usbxNrobox");
        $usuBox = $query->getResult();

        $usuarios[]=array();
        foreach ($usuBox as $datos_usu){
            $usbxId         = $datos_usu->getUsbxId();
            $usbxIdus       = $datos_usu->getUsbxIdus();
            $usbxNrobox     = $datos_usu->getUsbxNrobox();
            $usbxVigente    = $datos_usu->getUsbxVigente();
            $usbxdefault    = $datos_usu->getUsbxDefault();
            $usPrioridad    = $datos_usu->getUsbxPrioridad();

            //---------------------------------------
            //BUSCA NOMBRE USUARIO
            //---------------------------------------
            $usu = $this->getDoctrine()->getEntityManager('sigmu')->getRepository(Usuarios::class)->findByusId($usbxIdus);
            $nomUsuario  = utf8_encode($usu[0]->getUsNombre());

            //---------------------------------------
            //BUSCA TRAMITE POR USUARIO
            //---------------------------------------
            $user_tr     = $this->getDoctrine()->getRepository(UsuariosTramites::class)->findByusTrUsuario($usbxIdus);
            if ($user_tr != null) {//
                foreach ($user_tr as $data_ut){
                    $nro_tramite    = $data_ut->getUsTrTramite()->getTraTipoTramite();
                    $us_id_tra      = $data_ut->getUsTrUsuario();

                    $user_tra[]=array("ustramite" => trim($nro_tramite), 'ustrId' => $us_id_tra);
                }
            }else{
                $user_tra[]=array("ustramite" => '-', 'ustrId' => $usbxId);
            }

            $usuariosBox[]=array("user_tr" => $user_tra, "usbxId" => $usbxId, "usbxIdus"  => $usbxIdus, "usbxNrobox" => $usbxNrobox, "usbxVigente" => $usbxVigente, "usbxDefault" => $usbxdefault, "usbxPrioridad" => $usPrioridad, "nomUsuario" => $nomUsuario );
        }


        $listaTramites = $this->getDoctrine()->getRepository(Tramites::class)->findBy(array(), array("traIdTramite" => "ASC"));

        $modulos = $this->recuperarModulosUsuario($iduser);

        return $this->render('configuracion/configuracionBoxes.html.twig', array('modulos' => $modulos, "usuBox" => $usuariosBox, "listaTramite" => $listaTramites,  "mensaje"=>"", "tipoMensaje"=>""));//"tramitesMotivos" => $rg_tm, "vigente"=>"S",
    }

    /**
     * @Route("/buscarBoxUsuarios", name="buscarBoxUsuarios")
     */
    public function buscarBoxUsuariosAction(Request $request)
    {
        $idUsuario = $request ->get("idUsuario");

        $query = $this->getDoctrine()->getEntityManager('sigmu')->createQuery( "SELECT u FROM AppBundle:UsuariosSistemas us,AppBundle:Usuarios u where us.idSistema=37 and us.idUsuario=u.usId");
        $resul_usist = $query->getResult();

        //$usu_sistema   = array();
        //$box_ocupados  = array();
        foreach ($resul_usist as $datos_usu_sist){
            $usist_id       = $datos_usu_sist->getUsId();

            //-----------------------------------------------------------
            //-----------------------------------------------------------
            if ($idUsuario == 0){

                //-------------------------------------------------
                //busca al usuario si tiene algun box en vigencia
                //-------------------------------------------------
                $cargar_sn   = 1;
                $usuario_box = $this->getDoctrine()->getRepository(UsuariosBox::class)->findByUsbxIdus($usist_id);
                foreach ($usuario_box as $regbox){
                    $estado_box_user     = $regbox->getUsbxVigente();
                    if ($estado_box_user == 'S'){
                        $cargar_sn = 0;
                        $box_ocupados[] = array("nro_box_ocupado" => $regbox->getUsbxNrobox());
                        break;
                    }
                }//endFor

                //------------------------------------------------------------
                //CARGO EL USUARIO PORQUE NO EXISTE O PORQUE ESTA EN VIGENCIA N
                //------------------------------------------------------------
                if ($cargar_sn == 1){
                    $usist_nombre   = utf8_encode($datos_usu_sist->getUsNombre());
                    $usu_sistema[]  = array("usist_id" => $usist_id, "usist_nombre" => $usist_nombre);
                }//endIf


            }else{
                $usist_nombre   = utf8_encode($datos_usu_sist->getUsNombre());
                $usu_sistema[]  = array("usist_id" => $usist_id, "usist_nombre" => trim($usist_nombre));
                $box_ocupados[] = array("nro_box_ocupado" => 0);
            }//endIf
        }//endFor


        $output[]=array("arr_usuarios" => $usu_sistema,"arr_box_ocupados" => $box_ocupados);
        return new JsonResponse($output);
    }

    /**
     * @Route("/buscaVigencia", name="buscaVigencia")
     */
    public function buscaVigenciaAction(Request $request)
    {
        $idUsuario = $request ->get("idUsuario");
        $nroBox = $request ->get("nroBox");

        $usuario_box = $this->getDoctrine()->getRepository(UsuariosBox::class)->findOneBy(array('usbxNrobox' => $nroBox, 'usbxVigente' => 'S'));
        if ($usuario_box != null ) {
            $idbox      = $usuario_box->getUsbxIdus();
            if ($idUsuario == $idbox){
                $usuario   = "N";
            }else{
                $usu        = $this->getDoctrine()->getEntityManager('sigmu')->getRepository(Usuarios::class)->findByusId($idbox);
                $usuario    = utf8_encode($usu[0]->getUsNombre());
            }

        }else{
            $usuario   = "N";
        }//end if

        $output[]=array("usuario" => $usuario);
        return new JsonResponse($output);
    }


    /**
     * @Route("/guardaUsuarioTramite", name="guardaUsuarioTramite")
     */
    public function guardaUsuarioTramiteAction(Request $request){
        //---------------------------------------
        //GUARDA LOS TRAMITES ASIGNADO AL USUARIO
        //---------------------------------------
        $nroBox     = $request ->get("nroBox");
        $nroUser    = $request ->get("nroUser");

        $sn_inmueble    = $request ->get("sn_inmueble");
        $sn_comercio    = $request ->get("sn_comercio");
        $sn_moto        = $request ->get("sn_motos");
        $sn_cementerio  = $request ->get("sn_cementerio");
        $sn_escribano   = $request ->get("sn_escribano");
        $sn_juicio      = $request ->get("sn_juicio");
        $sn_varios      = $request ->get("sn_varios");
        $sn_arba        = $request ->get("sn_arba");

        $prioridadUser  = $request ->get("prioridadUser");

        //busca por usuario si tiene el tramite
        for ( $i=1; $i < 10; $i++){
            $query = $this->getDoctrine()->getManager()->createQuery( "SELECT ut FROM AppBundle:UsuariosTramites ut WHERE ut.usTrUsuario=".$nroUser." AND ut.usTrTramite=".$i ."");
            $rg_user_tra = $query->getResult();

            if (count($rg_user_tra)>0){
                //SI EXIXTE EN LA BASE,
                if (($sn_inmueble != 'P' && $i ==1) || ($sn_comercio != 'C' && $i ==2) || ($sn_moto != 'M' && $i == 3) || ($sn_cementerio != 'T' && $i ==4) || ($sn_escribano != 'E' && $i ==5)|| ($sn_juicio != 'J' && $i ==6)|| ($sn_varios != 'V' && $i ==7)|| ($sn_arba != 'A' && $i ==9)){
                    //SI NO ESTA SELECCIONADA
                    //LA ELIMINA DE LA BASE DE DATOS
                    $query = $this->getDoctrine()->getManager()->createQuery( "DELETE FROM AppBundle:UsuariosTramites ut WHERE ut.usTrUsuario=".$nroUser." AND ut.usTrTramite=".$i ."");
                    $query->execute();
                }
            }else{
                //SI NO ESTA CARGADA EN LA BASE
                if (($sn_inmueble == 'P' && $i ==1) || ($sn_comercio == 'C' && $i ==2) || ($sn_moto == 'M' && $i == 3) || ($sn_cementerio == 'T' && $i ==4) || ($sn_escribano == 'E' && $i ==5)|| ($sn_juicio == 'J' && $i ==6)|| ($sn_varios == 'V' && $i ==7)|| ($sn_arba == 'A' && $i ==9)){
                    //CARGA EL NUEVO TRAMITE-USUARIO
                    $n_us_tr = new UsuariosTramites();
                    $n_us_tr ->setUsTrUsuario($nroUser);
                    $n_us_tr ->setUsTrTramite($this->getDoctrine()->getRepository(Tramites::class)->findOneByTraIdTramite($i));

                    // Persisto el objeto en la base de datos
                    $this->getDoctrine()->getEntityManager()->persist($n_us_tr);
                    $this->getDoctrine()->getEntityManager()->flush();
                }//end if
            }//end if
        }//end for

        //------------------------------------------------------
        //BUSCA SI EL USUARIO TIENE ASIGNADO EL NUMERO DE BOX
        //------------------------------------------------------
        $cargar_nuevo ="S";
        $usuario_box  = $this->getDoctrine()->getRepository(UsuariosBox::class)->findByUsbxIdus($nroUser);
        if ($usuario_box != null ){
            foreach ($usuario_box as $datos_r){
                $nro_box_reg       = $datos_r->getUsbxNrobox();
                if ($nro_box_reg == $nroBox){
                    //SI EXISTE ACTULIZO A VIGENTE Y DEFAULT
                    $datos_r->setUsbxVigente('S');
                    $datos_r->setUsbxDefault('S');
                    $cargar_nuevo = "N";
                }else{
                    //SI TIENE OTROS , LO PASO A NO VIGENTE Y N DEFEULT
                    $datos_r->setUsbxVigente('N');
                    $datos_r->setUsbxDefault('N');
                }
                $datos_r->setUsbxPrioridad($prioridadUser);

                $this->getDoctrine()->getManager()->flush();
            }
        }

        if ($cargar_nuevo == "S"){
            //si no existe crea nuevo box-usuario

            $nuevoUsuBox = new UsuariosBox();
            $nuevoUsuBox->setUsbxIdus($nroUser);
            $nuevoUsuBox->setUsbxNrobox($nroBox);
            $nuevoUsuBox->setUsbxVigente("S");
            $nuevoUsuBox->setUsbxDefault("S");

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($nuevoUsuBox);
            $entityManager->flush();
        }//endIf

        //BUSCA EL NRO.BOX, SI OTROS USUARIOS TIENEN EL MISMO NROBOX, PASO A NO VIGENTE Y DEFAULT
        $usuario_nrobox = $this->getDoctrine()->getRepository(UsuariosBox::class)->findByUsbxNrobox($nroBox);
        if ($usuario_nrobox != null ){
            foreach ($usuario_nrobox as $datos_u){
                $nro_usuario       = $datos_u->getUsbxIdus();

                if ($nroUser != $nro_usuario){
                    //SI EXISTE ACTULIZO A NO VIGENTE Y NO DEFAULT

                    $datos_u->setUsbxVigente('N');
                    $datos_u->setUsbxDefault('N');
                    $this->getDoctrine()->getManager()->flush();
                }//if
            }//for
        }//if
        return new JsonResponse('S');

    }

    /**
     * @Route("/listaMotivos", name="listaMotivos")
     */
    public function listaMotivosindexAction(Request $request)
    {
        //$arr_imponibles = $this->getDoctrine()->getRepository(Tramites::class)->findAll();
        $iduser     = $request->getSession()->get("usuarioLogeado");

        $query = $this->getDoctrine()->getEntityManager()->createQuery("SELECT T from AppBundle:Tramites T WHERE T.vigente='S' order by T.traIdTramite" );
        $arr_imponibles   = $query->getResult();

        $query = $this->getDoctrine()->getEntityManager()->createQuery("select tm.vigente, tm.idMotivo as idMotivo,t.descripcion as tramite, tm.descripcion as motivo, tm.orden as prioridad from AppBundle:Tramites t, AppBundle:TramitesMotivos tm where tm.vigente='S' and t.vigente='S' and t.traTipoTramite=tm.tipoTramite order by t.descripcion, tm.orden" );
        $rg_tm   = $query->getResult();
        if (count($rg_tm)>0){
            $modulos = $this->recuperarModulosUsuario($iduser);
            return $this->render('configuracion/listaMotivos.html.twig', array('modulos' => $modulos,'arr_tramites' => $arr_imponibles,"tramitesMotivos" => $rg_tm, "vigente"=>"S", "mensaje"=>"", "tipoMensaje"=>""));
        }
    }

    /**
     * @Route("/guardaHorarios", name="guardaHorarios")
     */
    public function guardaHorariosAction(Request $request){
        //**********************************************
        //GUARDA HORARIOS DE LOS TIPOS DE TRAMITES
        //**********************************************
        $hinicio = trim($request->get('hinicio'));
        $hfin    = trim($request->get('hfin'));
        $tramite = trim($request->get('tramite'));


        $estado = 'N';
        $rg_tramite = $this->getDoctrine()->getManager()->getRepository(Tramites::class)->findOneBytraTipoTramite($tramite);
        if (count($rg_tramite)>0){

            $rg_tramite->setTraHoraInicio(\DateTime::createFromFormat('H:i',$hinicio));

            $rg_tramite->setTraHoraFin(\DateTime::createFromFormat('H:i',$hfin));

            // Persisto el objeto en la base de datos
            $entityManager = $this->getDoctrine()->getEntityManager();
            $entityManager->flush();
            $estado = 'S';
        }//end if

        //--------------------------------------------
        $output[]=array("b_estado" => $estado);
        return new JsonResponse($output);
    }


    /**
     * @Route("/altaMotivo", name="altaMotivo")
     */
    public function altaMotivoAction(Request $request)
    {
        $iduser     = $request->getSession()->get("usuarioLogeado");
        $arr_imponibles = $this->getDoctrine()->getRepository(Tramites::class)->findAll();
        $modulos = $this->recuperarModulosUsuario($iduser);
        return $this->render('configuracion/nuevoMotivo.html.twig', array('modulos' => $modulos,'arr_tramites' => $arr_imponibles, 'mensaje' => '', 'tipoMensaje' => ''));
    }

    /**
     * @Route("/cambiarVigMotivo/{idMotivo}", name="cambiarVigMotivo")
     */
    public function cambiarVigMotivoAction(Request $request, $idMotivo = NULL)
    {

        $modifVigencia = $this->getDoctrine()->getRepository(TramitesMotivos::class)->find($idMotivo);
        $vigencia= $modifVigencia->getVigente();

        if ($vigencia=="S"){
            $modifVigencia->setVigente("N");
        }else{
            $modifVigencia->setVigente("S");
        };
        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute('listaMotivos');
    }

    /**
     * @Route("/guardarMotivo", name="guardarMotivo")
     */
    public function guardarMotivoAction(Request $request)
    {
        $tipo      = $request->get("tipo");
        $nombre      = $request->get("nombre");

        $query = $this->getDoctrine()->getEntityManager()->createQuery("select tm from AppBundle:TramitesMotivos tm where tm.tipoTramite='".$tipo."' order by tm.orden desc");
        $rg_otm   = $query->getResult();
        if (count($rg_otm)>0){
            $orden= $rg_otm[0]->getOrden();
        }else{
            $orden= 1;
        }


        $query = $this->getDoctrine()->getEntityManager()->createQuery("select tm from AppBundle:TramitesMotivos tm where tm.tipoTramite='".$tipo."' and tm.descripcion='".$nombre."'" );
        $rg_tm   = $query->getResult();
        if (count($rg_tm)>0){
            $mensaje= "Ya existe el motivo para el imponible seleccionado";
            $tipo_mensaje="danger";
            $arr_imponibles = $this->getDoctrine()->getRepository(Tramites::class)->findAll();
            return $this->render('configuracion/nuevoMotivo.html.twig', array('arr_tramites' => $arr_imponibles, 'mensaje' => $mensaje, 'tipoMensaje' => $tipo_mensaje));
        }else{
            $nuevoTraMotivo = new TramitesMotivos();
            $nuevoTraMotivo->setDescripcion($nombre);
            $nuevoTraMotivo->setTipoTramite($tipo);
            $nuevoTraMotivo->setVigente("S");
            $nuevoTraMotivo->setOrden($orden+1);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($nuevoTraMotivo);
            $entityManager->flush();
            $mensaje= "El motivo para el imponible seleccionada se ha guardado correctamente";
            $tipo_mensaje="success";
            return $this->redirectToRoute('listaMotivos');
        }
    }

    /**
     * @Route("/motivosNoVigentes", name="motivosNoVigentes")
     */
    public function motivosNoVigentesAction(Request $request)
    {
        $iduser     = $request->getSession()->get("usuarioLogeado");
        $modulos = $this->recuperarModulosUsuario($iduser);

        $query = $this->getDoctrine()->getEntityManager()->createQuery("SELECT T from AppBundle:Tramites T WHERE T.vigente='S' order by T.traIdTramite" );
        $arr_imponibles   = $query->getResult();

        $query = $this->getDoctrine()->getEntityManager()->createQuery("select tm.vigente,tm.idMotivo as idMotivo,t.descripcion as tramite, tm.descripcion as motivo, tm.orden as prioridad from AppBundle:Tramites t, AppBundle:TramitesMotivos tm where tm.vigente='N' and t.vigente='S' and t.traTipoTramite=tm.tipoTramite order by t.descripcion, tm.orden" );
        $rg_tm   = $query->getResult();
        if (count($rg_tm)>0){
            //return $this->redirectToRoute('listaMotivos');
            return $this->render('configuracion/listaMotivos.html.twig', array('modulos' => $modulos,"tramitesMotivos" => $rg_tm, 'arr_tramites' => $arr_imponibles, "vigente"=>"N", "mensaje"=>"", "tipoMensaje"=>""));
        }else{
            $mensaje="No hay motivos con estado NO VIGENTES.";
            $tipo_mensaje="danger";
            $query = $this->getDoctrine()->getEntityManager()->createQuery("select tm.vigente, tm.idMotivo as idMotivo,t.descripcion as tramite, tm.descripcion as motivo, tm.orden as prioridad from AppBundle:Tramites t, AppBundle:TramitesMotivos tm where tm.vigente='S' and t.vigente='S' and t.traTipoTramite=tm.tipoTramite order by t.descripcion, tm.orden" );
            $rg_tm   = $query->getResult();
            if (count($rg_tm)>0){
                return $this->render('configuracion/listaMotivos.html.twig', array('modulos' => $modulos,"tramitesMotivos" => $rg_tm, 'arr_tramites' => $arr_imponibles, "vigente"=>"S",'mensaje' => $mensaje, 'tipoMensaje' => $tipo_mensaje));
            }
        }
    }

    /**
     * @Route("/habilitarReemplazos", name="habilitarReemplazos")
     */
    public function habilitarReemplazosAction(Request $request)
    {
        $iduser     = $request->getSession()->get("usuarioLogeado");
        $query = $this->getDoctrine()->getEntityManager()->createQuery( "SELECT ub FROM AppBundle:UsuariosBox ub order by ub.usbxIdus,ub.usbxNrobox");
        $usuBox = $query->getResult();

        $usuarios[]=array();
        foreach ($usuBox as $datos_usu){
            $usbxId         = $datos_usu->getUsbxId();
            $usbxIdus       = $datos_usu->getUsbxIdus();
            $usbxNrobox     = $datos_usu->getUsbxNrobox();
            $usbxVigente    = $datos_usu->getUsbxVigente();
            $usbxdefault    = $datos_usu->getUsbxDefault();

            $usu = $this->getDoctrine()->getEntityManager('sigmu')->getRepository(Usuarios::class)->findByusId($usbxIdus);
            $nomUsuario  = utf8_encode($usu[0]->getUsNombre());

            $usuariosBox[]=array("usbxId" => $usbxId, "usbxIdus"  => $usbxIdus, "usbxNrobox" => $usbxNrobox, "usbxVigente" => $usbxVigente, "usbxDefault" => $usbxdefault, "nomUsuario" => $nomUsuario );
        }
        $modulos = $this->recuperarModulosUsuario($iduser);

        return $this->render('configuracion/habilitarBox.html.twig',array('modulos' => $modulos,"usuBox" => $usuariosBox,'mensaje' => '', 'tipoMensaje' => ''));
    }

    /**
     * @Route("/cambiarVigUsuBox/{idUsuBox}", name="cambiarVigUsuBox")
     */
    public function cambiarVigUsuBoxAction(Request $request, $idUsuBox = NULL)
    {
        $modifVigencia = $this->getDoctrine()->getRepository(UsuariosBox::class)->find($idUsuBox);
        $vigencia= $modifVigencia->getUsbxVigente();

        if ($vigencia=="S"){
            $modifVigencia->setUsbxVigente("N");
        }else{
            $modifVigencia->setUsbxVigente("S");
        }
        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute('habilitarReemplazos');
    }

    /**
     * @Route("/cambiarPriUsuBox/{idUsuBox}", name="cambiarPriUsuBox")
     */
    public function cambiarPriUsuBoxAction(Request $request, $idUsuBox = NULL)
    {
        $modifPrioridad = $this->getDoctrine()->getRepository(UsuariosBox::class)->find($idUsuBox);
        $prioridad= $modifPrioridad->getUsbxDefault();
        $usuario=  $modifPrioridad->getUsbxIdus();

        $q = $this->getDoctrine()->getEntityManager()->createQuery("update AppBundle:UsuariosBox ub set ub.usbxDefault='N' where ub.usbxIdus= ".$usuario );
        $numUpdated = $q->execute();
        $modifPrioridad->setUsbxDefault("S");

        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute('habilitarReemplazos');
    }

    /**
     * @Route("/altaUsuBox", name="altaUsuBox")
     */
    public function altaUsuBoxAction(Request $request)
    {
        $iduser     = $request->getSession()->get("usuarioLogeado");

        $query = $this->getDoctrine()->getEntityManager('sigmu')->createQuery( "SELECT u FROM AppBundle:UsuariosSistemas us,AppBundle:Usuarios u where us.idSistema=37 and us.idUsuario=u.usId");
        $usuarios = $query->getResult();
        $modulos = $this->recuperarModulosUsuario($iduser);

        return $this->render('configuracion/nuevoUsuBox.html.twig', array('modulos' => $modulos,'arr_usuarios' => $usuarios, 'mensaje' => '', 'tipoMensaje' => ''));
    }

    /**
     * @Route("/buscaBox", name="buscaBox")
     */
    public function buscaBoxAction(Request $request)
    {
        $idUsuario = $request ->get("idUsuario");
        $query = $this->getDoctrine()->getEntityManager()->createQuery( "SELECT ub.usbxNrobox FROM AppBundle:UsuariosBox ub where ub.usbxIdus=".$idUsuario);
        $usuBox = $query->getResult();

        return new JsonResponse($usuBox);
    }

    /**
     * @Route("/guardarAltaUsuBox", name="guardarAltaUsuBox")
     */
    public function guardarAltaUsuBoxAction(Request $request)
    {
        $iduser     = $request->getSession()->get("usuarioLogeado");

        $idUsuario      = $request ->get("usuario");
        $nroBox         = $request ->get("nroBox");

        $nuevoUsuBox    = new UsuariosBox();
            $nuevoUsuBox->setUsbxIdus($idUsuario);
            $nuevoUsuBox->setUsbxNrobox($nroBox);
            $nuevoUsuBox->setUsbxVigente("S");
            $nuevoUsuBox->setUsbxDefault("N");

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($nuevoUsuBox);
            $entityManager->flush();

        $mensaje= "El Box para el usuario seleccionado ha sido guardado correctamente";
        $tipo_mensaje="success";
        $query = $this->getDoctrine()->getEntityManager('sigmu')->createQuery( "SELECT u FROM AppBundle:UsuariosSistemas us,AppBundle:Usuarios u where us.idSistema=37 and us.idUsuario=u.usId");
        $usuarios = $query->getResult();


        $modulos = $this->recuperarModulosUsuario($iduser);

        return $this->render('configuracion/nuevoUsuBox.html.twig', array('modulos' => $modulos,'arr_usuarios' => $usuarios, 'mensaje' => $mensaje, 'tipoMensaje' => $tipo_mensaje));
    }


}
