<?php

namespace App\Controller;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\HashRepository;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\Mapping\Annotation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;


class ListHashController extends AbstractController
{

    /**
     * This route will return all hashes that were generated and sored in the database.
     * Can be paged and sorted by attempts made to find the key.
     *
     * To change the page 'page=2'
     * To change the order 'sort=h.attempts&direction={DESC or ASC}'
     *
     * @Route("/list", name="list_hash")
     * @param PaginatorInterface $paginator
     * @param Registry $registry
     * @return Response
     */
    public function listHash(PaginatorInterface $paginator, ManagerRegistry $registry): Response
    {
        $hashRepository = new HashRepository($registry);
        $hashRepository1 = $hashRepository;
        $page = $_GET['page'] ?? 1;
        $query = $hashRepository1->createQueryBuilder('h')->select('h.batch', 'h.string', 'h.block', 'h.attempts', 'h.key_string')->getQuery()->execute();
        $list = $paginator->paginate($query, $page, 5)->getItems();
        $coutList = count((array)$list);
        
        if ($coutList == 0 )
        {
            return new JsonResponse(['Code' => '404 - No data found'], Response::HTTP_NOT_FOUND,  [
                'Access-Control-Allow-Origin: ' => '*',
                'Content-Type: ' => 'application/json; charset=UTF-8',
                'Access-Control-Allow-Methods: ' => 'GET',
                'Access-Control-Allow-Headers: ' => 'Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With'
            ]);
        }

        foreach ($list as $l) {
            $json[] = array(
                'batch' => $l['batch'],
                'block' => $l['block'],
                'string' => $l['string'],
                'key' => $l['key_string'],
                'attempts' => $l['attempts']
            );
        }

        return new JsonResponse($json, Response::HTTP_OK,  [
            'Access-Control-Allow-Origin: ' => '*',
            'Content-Type: ' => 'application/json; charset=UTF-8',
            'Access-Control-Allow-Methods: ' => 'GET',
            'Access-Control-Allow-Headers: ' => 'Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With'
        ]);
    }




}
