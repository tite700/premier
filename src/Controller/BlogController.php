<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use App\Repository\ArticleRepository;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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

    /**
     * @Route("/blog/new", name="blog_create")
     */

    public function create(Request $request, EntityManagerInterface $manager): Response
    {
        $article = new Article();

        $form = $this->createFormBuilder($article)
                     ->add('title',TextType::class,[
                         'attr' => [
                             'placeholder' => "Titre de l'article",
                             'class' => "form-control"
                         ]
                     ])
                    ->add('category',EntityType::class,[
                        'class' => Category::class,
                        'choice_label' => 'title',
                        'attr' => [
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
    public function show(ManagerRegistry $doctrine,$id,Request $request,EntityManagerInterface $manager) : Response
    {
        $article = $doctrine->getRepository(Article::class)->find($id);
        $comment = new Comment();

        $form = $this->createFormBuilder($comment)
            ->add('text',TextareaType::class,[
                'attr' => [
                    'placeholder' => "Contenu de l'article",
                    'class' => "form-control"
                    ]
            ])
            ->add('author',TextType::class,[
                'attr' => [
                    'placeholder' => "Auteur de l'article",
                    'class' => "form-control"
                ]
            ])
            ->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $comment->setCreatedAt(new \DateTimeImmutable());
            $comment->setArticle($article);


            $manager->persist($comment);
            $manager->flush();
            return $this->redirectToRoute('blog_show',['id' => $article->getId()]);
        }
        return $this->render('blog/show.html.twig', [
                'article' => $article,
                'formComment' => $form->createView()
        ]);

    }

    #[Route('/blog/{id}/edit',name: 'blog_edit')]
    public function edit(Article $article,Request $request, EntityManagerInterface $manager) : Response
    {
        $form = $this->createFormBuilder($article)
            ->add('title',TextType::class,[
                'attr' => [
                    'placeholder' => $article->getTitle(),
                    'class' => "form-control"
                ]
            ])
            ->add('category',EntityType::class,[
                'class' => Category::class,
                'choice_label' => 'title',
                'attr' => [
                    'class' => "form-control"
                ]
            ])
            ->add('content',TextareaType::class,[
                'attr' => [
                    'placeholder' => $article->getContent(),
                    'class' => "form-control"
                ]
            ])
            ->add('image',TextType::class,[
                'attr' => [
                    'placeholder' => $article->getImage(),
                    'class' => "form-control"
                ]
            ])
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $manager->persist($article);
            $manager->flush();

            return $this->redirectToRoute('blog_show', ['id' => $article->getId()]);
        }
        return $this->render('blog/edit.html.twig', [
            'formArticle' => $form->createView()
        ]);

    }

}
