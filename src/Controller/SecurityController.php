<?php

namespace App\Controller;

use App\Form\UserType;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="security_login")
     */
    public function login(AuthenticationUtils $helper)
    {
        $user = $this->getUser();
        if ($user){
            return $this->redirectToRoute('home');
        } else {
            return $this->render('Security/login.html.twig', [
                'last_username' => $helper->getLastUsername(),
                'error' => $helper->getLastAuthenticationError(),
            ]);
        }
    }

    /**
     * @Route("/register", name="security_register")
     */
    public function registerAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = $this->getUser();
        if ($user){
            return $this->redirectToRoute('home');
        } else {
            $newUser = new User();
            $form = $this->createForm(UserType::class, $newUser);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $password = $passwordEncoder->encodePassword($newUser, $newUser->getPassword());
                $newUser->setPassword($password);
                $newUser->setRoles(['ROLE_USER']);
                $em = $this->getDoctrine()->getManager();
                $em->persist($newUser);
                $em->flush();
                return $this->redirectToRoute('security_login');
            }
            return $this->render(
                'Security/register.html.twig',
                array('form' => $form->createView())
            );
        }
    }

    /**
     * @Route("/logout", name="security_logout")
     */
    public function logout()
    {
        throw new \Exception('This should never be reached!');
    }
}