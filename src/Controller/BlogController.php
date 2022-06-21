<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use phpDocumentor\Reflection\Types\This;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\FormTypeInterface;

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

    #[Route('/blog/new', name:'blog_create')]
    public function create(Request $request, EntityManagerInterface $manager){
        $article = new Article();
        $form = $this->createFormBuilder($article)
                     ->add('title',TextType::class,[
                         'attr' => [
                             'placeholder' => "Titre de l'article",
                             'class' => "form-control"
                         ]
                     ])
                     ->add('content',TextareaType::class,[
                         'attr' => [
                             'placeholder' => "Contenu de l'article",
                             'class' => "form-control"
                         ]
                     ])
                     ->add('image',TextType::class,[
                         'attr' => [
                             'placeholder' => "Image de l'article",
                             'class' => "form-control"
                         ]
                     ])
                     ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $article->setCreatedAt(new \DateTimeImmutable());

            $manager->persist($article);
            $manager->flush();

            return $this->redirectToRoute('blog_show',['id' => $article->getId()]);
        }

        return $this->render('blog/create.html.twig', [
            'formArticle' => $form->createView()
        ]);
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
