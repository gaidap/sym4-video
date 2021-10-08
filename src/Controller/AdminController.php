<?php

namespace App\Controller;

use App\Categories\CategoriesTreeAdminPage;
use App\Categories\CategoriesTreeOptionList;
use App\Entity\Category;
use App\Form\CategoryType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/", name="admin_main_page")
     */
    public function index(): Response
    {
        return $this->render('admin/my_profile.html.twig');
    }

    /**
     * @Route("/categories", name="categories", methods={"GET","POST"})
     */
    public function categories(CategoriesTreeAdminPage $categories, Request $request): Response
    {
        $categories->buildCategoryList();

        $newCategory = new Category();
        $form = $this->createForm(CategoryType::class);
        if ($this->saveCategory($newCategory, $form, $request)) {

            return $this->redirectToRoute('categories');
        }

        $isInvalid = null;
        if ($request->isMethod('POST')) {
            $isInvalid = ' is-invalid';
        }

        return $this->render('admin/categories.html.twig', [
            'categories' => $categories,
            'form' => $form->createView(),
            'is_invalid' => $isInvalid,
        ]);
    }

    private function saveCategory($category, $form, $request): bool
    {
        $form->handleRequest($request);
        if (!$form->isSubmitted() || !$form->isValid()) {
            return false;
        }
        $category->setName($request->request->get('category')['name']);
        $parent = $this
            ->getDoctrine()
            ->getRepository(Category::class)
            ->find($request->request->get('category')['parent']);
        $category->setParent($parent);
        $em = $this->getDoctrine()->getManager();
        $em->persist($category);
        $em->flush();

        return true;
    }

    public function getAllCategoryOptions(CategoriesTreeOptionList $categories, ?Category $editedCategory = null): Response
    {
        $categories->buildCategoryListOptions();

        return $this->render('admin/_all_category_options.html.twig', [
            'categories' => $categories->getCategoriesAsOptions(),
            'editedCategory' => $editedCategory,
        ]);
    }

    /**
     * @Route("/edit-category/{id}", name="edit_category", methods={"GET","POST"})
     */
    public function editCategory(Category $category, Request $request): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        if ($this->saveCategory($category, $form, $request)) {

            return $this->redirectToRoute('categories');
        }

        $isInvalid = null;
        if ($request->isMethod('POST')) {
            $isInvalid = ' is-invalid';
        }

        return $this->render('admin/edit_category.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
            'is_invalid' => $isInvalid,
        ]);
    }

    /**
     * @Route("/delete-category/{id}", name="delete_category")
     */
    public function deleteCategory(Category $category): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($category);
        $em->flush();

        return $this->redirectToRoute('categories');
    }

    /**
     * @Route("/videos", name="videos")
     */
    public function videos(): Response
    {
        return $this->render('admin/videos.html.twig');
    }

    /**
     * @Route("/upload-video", name="upload_video")
     */
    public function uploadVideo(): Response
    {
        return $this->render('admin/upload_video.html.twig');
    }

    /**
     * @Route("/users", name="users")
     */
    public function users(): Response
    {
        return $this->render('admin/users.html.twig');
    }
}
