<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
// use Doctrine\ORM\EntityManagerInterface;

class BlogController extends AbstractController
{

    #[Route('/blog', name: 'app_blog')]
    public function index(ManagerRegistry $doctrine): Response
    {
        // Retrieves a repository managed by the "default" entity manager
        $articles = $doctrine->getRepository(Article::class)->findAll();

        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
                'articles' => $articles
        ]);
    }

    #[Route('/', name: 'home')]
    public function home() :Response
    {
        return $this->render('blog/home.html.twig');
    }

    #[Route('/blog/{id}', name: 'blog_show')]
    public function show(ManagerRegistry $doctrine,$id) : Response
    {
        $article = $doctrine->getRepository(Article::class)->find($id);
        return $this->render('blog/show.html.twig',[
            'article' => $article
        ]);
    }

}
