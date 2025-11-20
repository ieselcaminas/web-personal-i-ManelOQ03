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


final class MuebleController extends AbstractController
{
    

    #[Route('/mueble/buscar/{texto}', name: 'buscar_mueble')]
    public function buscar(ManagerRegistry $doctrine, $texto): Response{
        $repositorio = $doctrine->getRepository(Mueble::class);
        $muebles = $repositorio->findByName($texto);

        return $this->render('lista_muebles.html.twig', [
            'muebles' => $muebles
        ]);
    }

    #[Route('/mueble/borrar/{id}', name: 'eliminar_mueble')]
    public function borrar(ManagerRegistry $doctrine, $id): Response{
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
    //     try{
    //         $entityManager->flush();
    //         return new Response("Contactos insertados");
    //     }catch(\Exception $e){
    //         return new Response("Error insertando objetos");
    //     }
    // }
}
