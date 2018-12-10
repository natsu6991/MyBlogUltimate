<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\User;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Form\UserType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

use Symfony\Component\Security\Core\User\UserInterface;

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
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(User::class);
        $users = $repository->findBy(array());
        $repositoryA = $em->getRepository(Article::class);
        $articles = $repositoryA->findBy(array());
        $repositoryC = $em->getRepository(Comment::class);
        $comments = $repositoryC->findBy(array());
        return $this->render('Admin/index.html.twig', ['users' => $users, 'articles' => $articles, 'comments' => $comments]);
    }

    /**
     * @Route("/admin/role/", name="change")
     */
    public function changeRoleAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(User::class);
        $users = $repository->findBy(array());
        if($request->getMethod() == 'POST'){
            $role = $request->get('rolechoice');
            $userId = $request->get('userchoice');

            $user = $repository->findOneBy(array('id'=>(int)$userId));
            $user->setRoles(array($role));
            $em->persist($user);
            $em->flush();
            return $this->render('Admin/change.html.twig', ['users'=>$users]);
        }

        return $this->render('Admin/change.html.twig', ['users'=>$users]);
    }

    /**
     * @Route("/home", name="home")
     */
    public function homeAction()
    {
        $user = $this->getUser();
        if ($user){
            $userRoles = $user->getRoles();
            if(in_array('ROLE_SUPERADMIN', $userRoles)){
                return $this->redirectToRoute('admin');
            }else{
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
    public function blogAction()
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(Article::class);
        $articles = $repository->findBy(array());

        return $this->render('Default/blog.html.twig', ['articles'=>$articles]);
    }

    /**
     * @Route("/article/create", name="create")
     */
    public function createArticleAction(Request $request)
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        $user = $this->getUser();

        if ($form->isSubmitted() && $form->isValid())
        {
            $image = $article->getImage();
            $imageName = $this->generateUniqueFileName().'.'.$image->guessExtension();
            try {
                $image->move(
                    $this->getParameter('images_directory'),
                    $imageName
                );
            } catch (FileException $e) {

            }
            $article->setImage($imageName);
            $article->setDate(new \DateTime());
            $article->setAuthor($user);
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

            return $this->redirectToRoute('blog');
        }

        return $this->render('Article/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/article/{id}", name="article")
     */
    public function showArticleAction(Request $request, Article $articleId)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(Article::class);
        $article = $repository->findOneBy(array('id'=>$articleId));
        $comments = $article->getComments()->toArray();
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        $user = $this->getUser();

        if ($form->isSubmitted() && $form->isValid())
        {
            $comment->setAuthor($user);
            $comment->setArticle($article);
            $comment->setDate(new \DateTime());
            $em->persist($comment);
            $em->flush();
            return $this->redirectToRoute('article',['id' => $article->getId()]);
        }

        return $this->render('Article/article.html.twig', ['article'=>$article, 'comments'=>$comments, 'form' => $form->createView()]);
    }
    /**
     * @Route("/article/edit/{id}", name="editArticle")
     */
    public function editArticleAction(Request $request, Article $articleId){
        $user = $this->getUser();
        if ($user != null){
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(Article::class);
            $article = $repository->findOneBy(
                array('id'=>$articleId)
            );
            $form = $this->createForm(ArticleType::class, $article);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid())
            {

                $image = $article->getImage();
                if($image) {
                    $imageName = $this->generateUniqueFileName() . '.' . $image->guessExtension();
                    try {
                        $image->move(
                            $this->getParameter('images_directory'),
                            $imageName
                        );
                    } catch (FileException $e) {

                    }
                    $article->setImage($imageName);
                }
                $em = $this->getDoctrine()->getManager();
                $em->persist($article);
                $em->flush();
                return $this->redirectToRoute('article',['id' => $article->getId()]);
            }

            return $this->render('Article/edit.html.twig',
                ['form' => $form->createView(),'user'=>$user,'id' => $article->getId()]
            );
        }else{
            return $this->redirectToRoute('blog');
        }
    }

    /**
     * @Route("/article/remove/{id}", name="removeArticle")
     */
    public function removeArticleAction(Request $request, Article $articleId )
    {
        $user = $this->getUser();
        if ($user){
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(Article::class);
            $article = $repository->findOneBy(
                array('id'=>$articleId)
            );
            if ($article != null){
                $em->remove($article);
                $em->flush();
            }
            return $this->redirectToRoute('blog');
        }
    }

    /**
     * @Route("/comment/remove/{mode}/{id}", name="removeComment")
     */
    public function removeCommentAction(Request $request, $mode, Comment $commentId)
    {
        $user = $this->getUser();
        if ($user){
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(Comment::class);
            $comment = $repository->findOneBy(
                array('id'=>$commentId)
            );
            if ($comment != null){
                $em->remove($comment);
                $em->flush();
            }
            if ($mode == "admin"){
                return $this->redirectToRoute('admin');
            }else{
                return $this->redirectToRoute('article',['id' => $comment->getArticle()->getId()]);
            }
        }
    }

    /**
     * @Route("/profile", name="profile")
     */
    public function profileAction()
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $repositoryA = $em->getRepository(Article::class);
        $articles = $repositoryA->findBy(array('author'=>$user));
        $repositoryC = $em->getRepository(Comment::class);
        $comments = $repositoryC->findBy(array('author'=>$user));
        return $this->render('Profile/profile.html.twig', ['user' => $user, 'articles' => $articles, 'comments' => $comments]);
    }

    /**
     * @Route("/profile/edit", name="profileEdit")
     */
    public function editProfileAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = $this->getUser();
        if ($user){
            $form = $this->createForm(UserType::class, $user);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid())
            {
                $password = $passwordEncoder->encodePassword($user, $user->getPassword());
                $user->setPassword($password);
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                return $this->redirectToRoute('profile');
            }
            return $this->render('Profile/edit.html.twig', ['user' => $user, 'form' => $form->createView()]);
        }
    }

    /**
     * @Route("/user/{id}", name="profileUser")
     */
    public function userProfileAction(Request $request, User $userId)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(User::class);
        $user = $repository->findOneBy(
            array('id'=>$userId)
        );
        $repositoryA = $em->getRepository(Article::class);
        $articles = $repositoryA->findBy(array('author'=>$user));
        $repositoryC = $em->getRepository(Comment::class);
        $comments = $repositoryC->findBy(array('author'=>$user));
        return $this->render('Profile/user.html.twig', ['user' => $user, 'articles' => $articles, 'comments' => $comments]);
    }

    /**
     * @return string
     */
    private function generateUniqueFileName()
    {
        return md5(uniqid());
    }


}
