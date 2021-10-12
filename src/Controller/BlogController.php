<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\NewArticleFormType;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Préfixes de la route et du nom des pages du blog
 *
 * @Route("/blog", name="blog_")
 */
class BlogController extends AbstractController
{
    /**
     * Page admin permettant de créer une nouvelle publication
     *
     * @Route("/nouvelle-publication/", name="new_publication")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function newPublication(Request $request): Response
    {

        $newArticle = new Article();

        $form = $this->createForm(NewArticleFormType::class, $newArticle);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $newArticle
                ->setAuthor($this->getUser())
                ->setPublicationDate( new DateTime() )
            ;

            $em = $this->getDoctrine()->getManager();

            $em->persist($newArticle);

            $em->flush();

            $this->addFlash('success', 'Article publié avec succès !');


            return $this->redirectToRoute('blog_publication_view', [
                'slug' => $newArticle->getSlug(),
                ]);
        }

        return $this->render('blog/newPublication.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Page qui liste tous les articles
     *
     * @Route("/publications/liste/", name="publication_list")
     */
    public function publicationList(): Response
    {

        $articleRepo = $this->getDoctrine()->getRepository(Article::class);

        $articles = $articleRepo->findAll();

        return $this->render('blog/publicationList.html.twig', [
            'articles' => $articles,
        ]);
    }

    /**
     * Page permettant de voir un article en détail
     *
     * @Route("/publication/{slug}/", name ="publication_view")
     */
    public function publicationView(Article $article): Response
    {

        return $this->render('blog/publicationView.html.twig', [
            'article' => $article,
        ]);
    }

}