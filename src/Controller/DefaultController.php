<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="index")
     */
    public function indexAction()
    {
        return $this->render('index.html.twig');
    }

    /**
     * @Route("/admin", name="admin")
     */
    public function adminAction()
    {
        return $this->render('Admin/index.html.twig');
    }

    /**
     * @Route("/home", name="home")
     */
    public function homeAction()
    {
        $user = $this->getUser();
        if ($user){
            $userRoles = $user->getRoles();
            if(in_array('ROLE_ADMIN', $userRoles)){
                return $this->redirectToRoute('admin');
            }else if(in_array('ROLE_USER', $userRoles)){
                return $this->render(
                    'Default/index.html.twig');
            }
        }else{
            return $this->redirectToRoute('index');
        }
    }

    /**
     * @Route("/blog", name="blog")
     */
    public function forumAction()
    {
        return $this->render('Default/blog.html.twig');
    }

}
