<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 09/08/2018
 * Time: 12:09
 */

namespace App\Controller\Admin;


use App\Entity\Category;
use App\Form\CategoryType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CategoryController
 * @package App\Controller\Admin
 * @Route("/categorie")
 */
class CategoryController extends Controller
{
    /**
     * @Route("/")
     */
    public function index()
    {
        // Lister les catégories (id et nom) dans un tableau html

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(Category::class);
        //$categories = $repository->findAll();
        $categories = $repository->findBy([], ['id' => 'asc']);

        return $this->render(
            'admin/category/index.html.twig',
            [
                'categories' => $categories
            ]
        );
    }

    /**
     * @param Request $request
     * {id} est optionelle grâce à defaults et doit être un nombre (requirements)
     * @Route("/edition/{id}", defaults={"id": null}, requirements={"id"="\d+"})
     */
    public function edit(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        if (is_null($id)){
        $category = new Category();
        } else { // modification
            $category = $em->find(Category::class, $id);

            // 404 si l'id  reçun'est pas en bdd
            if (is_null($category)){
                throw new NotFoundHttpException();
            }
        }

        // création d'un formulaire lié à la catégorie
        $form = $this->createForm(CategoryType::class, $category);

        // le formulaire analyse la requête HTTP
        $form->handleRequest($request);

        // si le formulaire a été envoyé
        if ($form->isSubmitted()) {
            // les attributs de l'objet Category ont été set
            // à partir des chamlps de formulaire
            // dump($category);

            // valide la saisie du formulaire à partir des annotations Assert dans l'entité category
            if ($form->isValid()) {
                $em->persist($category);
                $em->flush();

                // message de confirmation
                $this->addFlash(
                    'success',
                    'La catégorie est enregistrée'
                );

                //redirection vers la page de la liste
                return $this->redirectToRoute('app_admin_category_index');
            } else {
                $this->addFlash(
                    'error',
                    'le formulaire contient des erreurs'
                );
            }
        }

        return $this->render(
            'admin/category/edit.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }

    /**
     * @Route("/suppression/{id}")
     */
    public function delete(Category $category)
    {
        if($category->getArticle()->isEmpty()){
        $em = $this->getDoctrine()->getManager();
        //préparation de la suppresion en bdd
        $em->remove($category);
        // supresssion effective
        $em->flush();

            $this->addFlash(
                'success',
                'La catégorie est supprimée'
            );
         }else {
            $this->addFlash(
                'error',
                'La catégorie ne peut pas être supprimée car elle contient des articles'
            );
        }
        return $this->redirectToRoute('app_admin_category_index');
    }
}