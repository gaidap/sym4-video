<?php

namespace App\Controller;

use App\Categories\CategoriesTreeFrontPage;
use App\Entity\Category;
use App\Entity\Video;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends AbstractController
{
    /**
     * @Route("/", name="main_page")
     */
    public function index(): Response
    {
        return $this->render('front/index.html.twig');
    }

    /**
     * @Route("/video-list/category/{name},{id}/{page}", defaults={"page": 1}, name="video_list")
     */
    public function videoList($id, $page, CategoriesTreeFrontPage $categories, Request $request): Response
    {
        $categories->buildCategoryListWithParent($id);
        $ids = $categories->getChildIds($id);
        $ids[] = $id;
        $videos = $this
            ->getDoctrine()
            ->getRepository(Video::class)
            ->findByChildIds($ids, $page, $request->get('sortOrder'));

        return $this->render('front/video_list.html.twig', [
            'categories' => $categories,
            'videos' => $videos,
        ]);
    }

    /**
     * @Route("/video-details", name="video_details")
     */
    public function videoDetails(): Response
    {
        return $this->render('front/video_details.html.twig');
    }

    /**
     * @Route("/pricing", name="pricing")
     */
    public function pricing(): Response
    {
        return $this->render('front/pricing.html.twig');
    }

    /**
     * @Route("/register", name="register")
     */
    public function register(): Response
    {
        return $this->render('front/register.html.twig');
    }

    /**
     * @Route("/login", name="login")
     */
    public function login(): Response
    {
        return $this->render('front/login.html.twig');
    }

    /**
     * @Route("/payment", name="payment")
     */
    public function payment(): Response
    {
        return $this->render('front/payment.html.twig');
    }

    /**
     * @Route("/search-results/{page}", methods={"GET"}, defaults={"page": "1"}, name="search_results")
     */
    public function searchResults(int $page, Request $request): Response
    {
        $query = $request->get('query');
        $videos = null;
        if ($query) {
            $videos = $this
                ->getDoctrine()
                ->getRepository(Video::class)
                ->findByTitle($query, $page, $request->get('sortOrder'));
            $videos = !$videos->getItems() ? null : $videos;
        }

        return $this->render('front/search_results.html.twig', [
            'videos' => $videos,
            'query' => $query,
        ]);
    }

    public function mainCategories(): Response
    {
        $categoryRepository = $this->getDoctrine()->getRepository(Category::class);
        $mainCategories = $categoryRepository->findBy(['parent' => null], ['name' => 'ASC']);

        return $this->render('front/_main_categories.html.twig', ['mainCategories' => $mainCategories]);
    }
}
