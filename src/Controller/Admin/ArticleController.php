<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Form\ArticleType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ArticleController
 * @package App\Controller\Admin
 * @Route("/article")
 */
class ArticleController extends Controller
{
    /**
     * @Route("/")
     */
    public function index()
    {
        /*
         * faire la page qui liste les articles dans un tableau html avec nom de la catégorie
         * et nom de l'auteur, date au format français
         */

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(Article::class);
        $articles = $repository->findAll();

        return $this->render(
            'admin/article/index.html.twig',
            [
                'articles' => $articles
            ]
        );
    }

    /**
     *@Route("/edition/{id}", defaults={"id": null}, requirements={"id"="\d+"})
     */
    public function edit(Request $request, $id)
    {
        /*
         * Faire le rendu du formulaire et son traitement
         * mettre un lien ajouter dans la page de liste
         * Validation: tous les champs obligatoires
         * En création:
         *  -set l'auteur avec l'utilisateur connecté ($this->>getUser() depuis le controleur)
         *  - mettre la date de pub à maintenant
         * Adapter la route et le contenu de la méthode
         * pour faire fonctionner la page en modification
         * et ajouter le bouton modifier
         *
         * Enregistrer l'article en bdd
         *puis rediriger vers la liste avec un message de confirmation
         */

        $em = $this->getDoctrine()->getManager();
        $originalImage = null;

        if (is_null($id)){
            $article = new Article();
            $article->setAuthor($this->getUser());
            $article->setPublicationDate(new \DateTime());
        } else { // modification
            $article = $em->find(Article::class, $id);

            if(!is_null($article->getImage())){
                // nom du fichier venant de la bdd
                $originalImage = $article->getImage();

                // on set l'image avec un objet File pour le traitement par le formulaire
                $article->setImage(
                    new File($this->getParameter('upload_dir') . $originalImage)
                );
            } else {
                // sans upload, on garde l'ancienne image
                $article->setImage($originalImage);
            }

            // 404 si l'id  reçun'est pas en bdd
            if (is_null($article)){
                throw new NotFoundHttpException();
            }
        }

        // création d'un formulaire lié à l'article
        $form = $this->createForm(ArticleType::class, $article);


        // le formulaire analyse la requête HTTP
        $form->handleRequest($request);

        // si le formulaire a été envoyé
        if ($form->isSubmitted()) {
            // les attributs de l'objet Articleont été set
            // à partir des chamlps de formulaire

            // valide la saisie du formulaire à partir des annotations Assert dans l'entité article
            if ($form->isValid()) {
                /**
                 * @var UploadedFile|null
                 */
                $image = $article->getImage();

                // si une image a été upload
                if (!is_null($image)){
                    $filename = uniqid() . '.' . $image->guessExtension();

                    $image->move(
                        // répertoire de destination
                        //cf config/services.yaml
                        $this->getParameter('upload_dir'),
                        // nom du fichier
                        $filename
                    );
                    // on set l'attribut image de l'article avec le nom
                    // de l'image pour enregistrement en bdd
                    $article->setImage($filename);

                    // si on est en modif, on supprime l'ancienne image du repertoire public
                    if(!is_null($originalImage)){
                        unlink($this->getParameter('upload_dir') . $originalImage);
                    }
                }
                $em->persist($article);
                $em->flush();

                // message de confirmation
                $this->addFlash(
                    'success',
                    'L\'article est enregistrée'
                );

                //redirection vers la page de la liste
                return $this->redirectToRoute('app_admin_article_index');
            } else {
                $this->addFlash(
                    'error',
                    'le formulaire contient des erreurs'
                );
            }
        }

        return $this->render(
            'admin/article/edit.html.twig',
            [
                'form' => $form->createView(),
                'original_image' => $originalImage
            ]
        );
    }


    /**
     * @Route("/suppression/{id}")
     */
    public function delete(Article $article)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($article);
        $em->flush();

        $this->addFlash(
            'success',
            'L\'article est supprimé'
        );

        return $this->redirectToRoute('app_admin_article_index');
    }
}

