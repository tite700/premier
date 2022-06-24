<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/inscription', name: 'security_registration')]
    public function registration(Request $request,EntityManagerInterface $manager, UserPasswordHasherInterface $passwordHasher) : Response {
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $user->getPassword()
            );
            $user->setPassword($hashedPassword);
            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('security_login');
        }

        return $this->render('security/registration.html.twig',[
            'form' => $form->createView()
        ]);
    }

    #[Route('/connexion',name: 'security_login')]
    public function login(AuthenticationUtils $authenticationUtils, Request $request) : Response
    {

        dump($request);

        if($request->getMethod() == 'POST'){
            dump("POST");
        }
        else {
            dump("GET");
        }


        if($this->getUser())
        {
            return $this->redirect($request->server->get('HTTP_REFERER'));
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        dump($request->server->get('HTTP_REFERER'));



        return $this->render('security/login.html.twig', [
                         'last_username' => $lastUsername,
                         'error'         => $error,
                            'target' => $request->server->get('HTTP_REFERER')
        ]);
    }

    #[Route('/deconnexion',name: 'security_logout')]
    public function logout () {

    }

}
