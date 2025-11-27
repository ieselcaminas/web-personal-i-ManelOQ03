<?php

namespace App\Controller;

use App\Entity\Mueble;
use App\Entity\Tienda;
use App\Repository\MuebleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Form\MuebleFormType as MuebleType;
use Symfony\Component\HttpFoundation\Request;

final class MuebleController extends AbstractController
{
    

    #[Route('/mueble/buscar/{texto}', name: 'buscar_mueble')]
    public function buscar(ManagerRegistry $doctrine, $texto): Response{
        if(!$this->getUser()){
            return $this->redirectToRoute("app_login");
        }
        $repositorio = $doctrine->getRepository(Mueble::class);
        $muebles = $repositorio->findByName($texto);

        return $this->render('lista_muebles.html.twig', [
            'muebles' => $muebles
        ]);
    }

    #[Route('/mueble/nuevo', name: 'nuevo')]
    public function nuevo(ManagerRegistry $doctrine, Request $request) {
        if(!$this->getUser()){
            return $this->redirectToRoute("app_login");
        }
        $mueble = new Mueble();
        $formulario = $this->createForm(MuebleType::class, $mueble);
        $formulario->handleRequest($request);

        if ($formulario->isSubmitted() && $formulario->isValid()) {
            $mueble = $formulario->getData();
            
            $entityManager = $doctrine->getManager();
            $entityManager->persist($mueble);
            $entityManager->flush();
            return $this->redirectToRoute('ficha_mueble', ["codigo" => $mueble->getId()]);
        }
        return $this->render('nuevo.html.twig', array(
            'formulario' => $formulario->createView()
        ));
    }

    #[Route('/mueble/editar/{codigo}', name: 'editar', requirements:["codigo"=>"\d+"])]
    public function editar(ManagerRegistry $doctrine, Request $request, int $codigo) {
        if(!$this->getUser()){
            return $this->redirectToRoute("app_login");
        }

        $repositorio = $doctrine->getRepository(Mueble::class);

        //En este caso, los datos los obtenemos del repositorio de muebles

        $mueble = $repositorio->find($codigo);

        if ($mueble){

            $formulario = $this->createForm(MuebleType::class, $mueble);



            $formulario->handleRequest($request);



            if ($formulario->isSubmitted() && $formulario->isValid()) {

                //Esta parte es igual que en la ruta para insertar

                $mueble = $formulario->getData();

                $entityManager = $doctrine->getManager();

                $entityManager->persist($mueble);

                $entityManager->flush();

                return $this->redirectToRoute('ficha_mueble', ["codigo" => $mueble->getId()]);

            }

            return $this->render('editar.html.twig', array(

                'formulario' => $formulario->createView()

            ));

        }else{

            return $this->render('ficha_mueble.html.twig', [

                'mueble' => NULL

            ]);
        }
    }

    #[Route('/mueble/borrar/{id}', name: 'eliminar_mueble')]
    public function borrar(ManagerRegistry $doctrine, $id): Response{
        if(!$this->getUser()){
            return $this->redirectToRoute("app_login");
        }
        $entityManager = $doctrine->getManager();
        $repositorio = $doctrine->getRepository(Mueble::class);
        $mueble = $repositorio->find($id);
        if($mueble){
            try{
                $entityManager->remove($mueble);
                $entityManager->flush();
                return new Response("Mueble eliminado");
            } catch (\Exception $e){
                return new Response("Error eliminando objeto");
            }
        } else {
            return $this->render('ficha_mueble.html.twig', [
                'mueble' => null
            ]);
        }
    }

    #[Route('/mueble/insertarConTienda', name: 'insertar_con_tienda_mueble')]
    public function insertarConProvincia(ManagerRegistry $doctrine): Response{
        if(!$this->getUser()){
            return $this->redirectToRoute("app_login");
        }
        $entityManager = $doctrine->getManager();
        $tienda = new Tienda();
        $tienda->setNombre("Bricomark");
        $mueble = new Mueble();
        $mueble->setTipo("prueba con tienda");
        $mueble->setMaterial("roble");
        $mueble->setAcabado("blanco mate");
        $mueble->setTienda($tienda);
        $entityManager->persist($tienda);
        $entityManager->persist($mueble);
        
        $entityManager->flush();
        return $this->render('ficha_mueble.html.twig', [
            'mueble' => $mueble
        ]);

    }

    #[Route('/mueble/insertarSinTienda', name: 'insertar_sin_tienda_mueble')]
    public function insertarSinProvincia(ManagerRegistry $doctrine): Response{
        if(!$this->getUser()){
            return $this->redirectToRoute("app_login");
        }
        $entityManager = $doctrine->getManager();
        $repositorio = $doctrine->getRepository(Tienda::class);

        $tienda = $repositorio->findOneBy(["nombre" =>"Bricomark"]);

        $mueble = new Mueble();

        $mueble->setTipo("prueba sin tienda");
        $mueble->setMaterial("roble");
        $mueble->setAcabado("blanco mate");
        $mueble->setTienda($tienda);

        $entityManager->persist($mueble);
        
        $entityManager->flush();
        return $this->render('ficha_mueble.html.twig', [
            'mueble' => $mueble
        ]);

    }

    #[Route('/mueble/{codigo?1}', name: 'ficha_mueble')]
    public function ficha(ManagerRegistry $doctrine, $codigo): Response
    {
        if(!$this->getUser()){
            return $this->redirectToRoute("app_login");
        }
        $repositorio = $doctrine->getRepository(Mueble::class);
        $mueble = $repositorio->find($codigo);

        return $this->render('ficha_mueble.html.twig', [
            'mueble' => $mueble
        ]);
    }

    // private $muebles = [
    //     1 => ["tipo" => "Mesa", "material" => "Roble", "acabado" => "Barnizado"],
    //     2 => ["tipo" => "Armario", "material" => "Fresno", "acabado" => "Barnizado"],
    //     5 => ["tipo" => "Silla", "material" => "Cerezo", "acabado" => "Barnizado"],
    //     7 => ["tipo" => "Silla", "material" => "Abedul", "acabado" => "Pintura azul claro"],
    //     9 => ["tipo" => "Puerta", "material" => "Contrachapado", "acabado" => "Pintura verde claro"]
    // ];

    // #[Route('/mueble/insertar', name: 'insertar_mueble')]
    // public function insertar(ManagerRegistry $doctrine)
    // {
    //     $entityManager = $doctrine->getManager();
    //     foreach($this->muebles as $m){
    //         $mueble = new Mueble();
    //         $mueble->setTipo($m["tipo"]);
    //         $mueble->setMaterial($m["material"]);
    //         $mueble->setAcabado($m["acabado"]);
    //         $entityManager->persist($mueble);
    //     }
    //     try{Contacto
    //         $entityManager->flush();
    //         return new Response("Contactos insertados");
    //     }catch(\Exception $e){
    //         return new Response("Error insertando objetos");
    //     }
    // }
}
