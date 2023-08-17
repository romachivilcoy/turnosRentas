<?php

namespace AppBundle\Controller;

use AppBundle\AppBundle;
use AppBundle\Entity\Tareas;
use AppBundle\Entity\Menues;
use AppBundle\Entity\Usuarios;
use AppBundle\Entity\UsuariosBox;
use AppBundle\Entity\UsuariosTareas;
use Symfony\Component\HttpFoundation\JsonResponse;
use PDO;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;



class DefaultController extends Controller
{
    const SECCION_BOX_REEMPLAZO = "";
    const SECCION_ESTADISTICA = "";
    const SECCION_HISTORICO = "";
    const SECCION_ADMINISTRACION = "";
    const SECCION_MOTIVO = "";
    const SECCION_REEMPLAZO = "";
    const SECCION_ATENCION = "";
    const SECCION_CONFIGURACION = "";

     /**
     * @Route("/login/{idSigmu}", name="loginSigmu")
     */
    public function indexAction(Request $request, $idSigmu = null, UserInterface $usuarioLogeado = null)
    {
        /*$idSigmu = $request->get($idSigmu . '-sigmuUser');*/

        //$operador = $this->getDoctrine()->getRepository(Asistente::class)->findOneBy(array("usId" => $idSigmu));
        //$operador = $this->getDoctrine()->getEntityManager('sigmu')->getRepository(Usuarios::class)->findOneByusId($idSistema);
        /*$error = $authUtils->getLastAuthenticationError();
        dump($error);
        die();*/

        $operador = $this->getDoctrine()->getEntityManager()->getRepository(UsuariosBox::class)->findOneByusbxIdus($idSigmu);

        if ($operador != null) {
            // Si existe y estÃ¡ vigente, logeo

           $token = new UsernamePasswordToken($operador, null, 'main', ['ROLE_USER']);

           $this->get('security.token_storage')->setToken($token);

           $this->get('session')->set('_security_main', serialize($token));

           $session = $request->getSession();

           $session->set("usuarioLogeado", $idSigmu);

            $iduser     = $request->getSession()->get("usuarioLogeado");


           return $this->redirectToRoute('panelPrincipal');
           //return $this->forward('AppBundle\Controller\BoxesController::listaBoxesAction', array('p_iduser' => $idSigmu));

        }else{
            return $this->redirectToRoute('logout');
        }

        /*if ($idSigmu != null and $idSigmu != "") {
            return $this->forward('AppBundle\Controller\BoxesController::listaBoxesAction', array('p_iduser' => $idSigmu));
        } else {
            //return $this->render('default/inicioTk.html.twig');
            die('noEntroMenu');
        }*/
    }

    /**
     * @Route("/homepage", name="homepage")
     */
    public function indexdddAction(Request $request)
    {
        return $this->render('default/panel.html.twig');
    }



    /**
     * @Route("/dibujarMenu", name="dibujarMenu")
     */
    public function dibujarMenuAction(Request $request, UserInterface $usuarioLogeado = null)
    {

        //$idUser  = $request ->get("idUser");
       // $idUser  = $request->getSession()->get("usuarioLogeado");
        $idUser  = $request->getSession()->get("usuarioLogeado");

        $query   = $this->getDoctrine()->getEntityManager()->createQuery("select m.mnDescripcion as menu,m.mnOrden,t.taDescripcion as tarea, t.taOrden, t.taUrl from AppBundle:UsuariosTareas ut , AppBundle:Tareas t,AppBundle:Menues m  where ut.utVigente='S' and ut.utIdUsuario=".$idUser." and t.taIdTarea=ut.utIdTarea and m.mnIdMenu=t.taIdMenue and t.taVigente='S' ORDER BY m.mnOrden, t.taOrden" );
        $rg_tm   = $query->getResult();



        $datoUsu = $this->getDoctrine()->getEntityManager('sigmu')->getRepository(Usuarios::class)->findOneByusId($idUser);

        $nomUsuario  = trim($datoUsu->getUsNombre());

        if (count($rg_tm)>0){
            return new JsonResponse(array("menuTareas" => $rg_tm, "nomUsuario" => utf8_encode($nomUsuario), "idUser" => $idUser));
        }else{
            die('noEntroMenu');
        }
    }


    /**
     * @Route("/panelPrincipal", name="panelPrincipal")
     */
    public function panelPrincipalAction(Request $request, $msg_ok = null, UserInterface $usuarioLogeado = null)
    {
        //dump($usuarioLogeado->getUsbxIdus());
        //die();
        $usuarioBox  = $request->getSession()->get("usuarioLogeado");


        if ($usuarioBox) {
            $modulos = $this->recuperarModulosUsuario($usuarioBox);

            //return $this->forward('AppBundle\Controller\BoxesController::listaBoxesAction', array('p_iduser' => $idSigmu));

            return $this->render('default/panel.html.twig', [
                'modulos' => $modulos
            ]);

        } else {
            die('noEntroMenuPP:'.$usuarioBox);
            return $this->redirectToRoute('logout');
        }
    }


    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction(Request $request, $msg_ok = null, UserInterface $usuarioLogeado = null)
    {

        return $this->render('default/prueba2.html.twig');
    }



    public function recuperarModulosUsuario($user)
    {
        $modulos=[
            "BOX_REEMPLAZO"=> false,
            "ESTADISTICA"=> false,
            "HISTORICO"=>false,
            "ADMINISTRACION"=>false,
            "MOTIVO"=>false,
            "REEMPLAZO"=>false,
            "ATENCION"=>false,
            "CONFIGURACION"=>false
        ];


        $operadorPermisos = $this->getDoctrine()->getRepository(UsuariosTareas::class)->findBy(array("utIdUsuario" => $user), array("utIdTarea" => 'ASC'));

        //$operadorPermisos = $this->getDoctrine()->getRepository(AsistentePermiso::class)->findBy(array("idasistente" => $operador->getIdasistente()), array("idpermisos" => 'ASC'));
        foreach ($operadorPermisos as $resul) {
            $permiso = $resul->getUtIdTarea();

            switch ($permiso) {
                case 1:
                    $modulos["BOX_REEMPLAZO"] = true;
                    break;
                case 2:
                    $modulos["ESTADISTICA"] = true;
                    break;
                case 3:
                    $modulos["HISTORICO"] = true;
                    break;
                case 4:
                    $modulos["ADMINISTRACION"] = true;
                    break;
                case 5:
                    $modulos["MOTIVO"] = true;
                    break;
                case 6:
                    $modulos["REEMPLAZO"] = true;
                    break;
                case 7:
                    $modulos["ATENCION"] = true;
                    break;
                case 8:
                    $modulos["CONFIGURACION"] = true;
                    break;
            }
        }//for

        return $modulos;
    }//reportes

    /**
     * @Route("/prueba4", name="prueba4")
     */
    public function prueba4Action(Request $request)
    {

        return $this->render('default/prueba4.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]);
    }

    /**
     * Normaliza un string para que se muestren correctamente los caracteres del español
     * @param string $word string a normalizar
     * @return string el string normalizado
     */
    protected function normalizarString($word)
    {
        $unwanted_array = array('Š' => 'S', 'š' => 's', 'Ž' => 'Z', 'ž' => 'z', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E',
            'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U',
            'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o',
            'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'þ' => 'b', 'ÿ' => 'y');
        $str = strtr($word, $unwanted_array);

        return strtoupper($str);
    }

    /**
     * Normaliza un string para que se muestren correctamente los caracteres del español
     * @return string el string normalizado
     */
    protected function cambiar_utf8 ($nowytekst) {
        $nowytekst = str_replace('Ã¡','Á',$nowytekst);
        $nowytekst = str_replace('Ã©','É',$nowytekst);
        $nowytekst = str_replace('Ã­','Í',$nowytekst);
        $nowytekst = str_replace('Ã³','Ó',$nowytekst);
        $nowytekst = str_replace('Ãº','Ú',$nowytekst);
        $nowytekst = str_replace('Ã±','Ñ',$nowytekst);

        $nowytekst = str_replace('Ã','Á',$nowytekst);
        $nowytekst = str_replace('Ã‰','É',$nowytekst);
        $nowytekst = str_replace('Ã','Í',$nowytekst);

        $nowytekst = str_replace('Ã“','Ó',$nowytekst);
        $nowytekst = str_replace('Ãš','Ú',$nowytekst);
        $nowytekst = str_replace('Ï¿½','Ñ',$nowytekst);
        $nowytekst = str_replace('&#65533;','Ñ',$nowytekst);
        $nowytekst = str_replace('\u00d1','Ñ',$nowytekst);    //ł
        $nowytekst = str_replace('\u00f1','Ñ',$nowytekst);    //ń
        $nowytekst = str_replace('&#241;','Ñ',$nowytekst);    //ó
        $nowytekst = str_replace('&#209;','Ñ',$nowytekst);    //ś
        $nowytekst = str_replace('Ã‘','Ñ',$nowytekst);    //ź
        $nowytekst = str_replace('&ntilde;','Ñ',$nowytekst);    //ż
        $nowytekst = str_replace('&Ntilde;','Ñ',$nowytekst);    //ż
        $nowytekst = str_replace('&#209;','Ñ',$nowytekst);    //ż
        $nowytekst = str_replace('&#241;','Ñ',$nowytekst);    //ż
        $nowytekst = str_replace('?','Ñ',$nowytekst);    //ż
        return ($nowytekst);
    }
}
