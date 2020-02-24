<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Category;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;

class FortuneController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function homepageAction(Request $request)
    {
        /**
         * @var EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();  // Enabling the Doctrine filter that I created on the homepage. The argument that is passed in the enable method is what I called the filter in config.yml
        $filter = $em->getFilters()
            ->enable('fortune_cookie_discontinued');
        $filter->setParameter('discontinued', false);

        $categoryRepository = $this->getDoctrine()
            ->getManager()
            ->getRepository('AppBundle:Category');

        $search = $request->query->get('q'); // Searches in the URL for a 'q' which means that a search is being performed for a query
        if ($search) { // If the URL contains a q
            $categories = $categoryRepository->search($search); // Then call the search() method in categoryRepository with the $search value which will be the term that we are trying to search by.
        } else {
            $categories = $categoryRepository->findAllOrdered(); // Else just display all the other queries
        }



        return $this->render('fortune/homepage.html.twig',[
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/category/{id}", name="category_show")
     */
    public function showCategoryAction($id)
    {
        $categoryRepository = $this->getDoctrine()
            ->getManager()
            ->getRepository('AppBundle:Category');

        $category = $categoryRepository->findWithFortunesJoin($id);

        if (!$category) {
            throw $this->createNotFoundException();
        }

        $fortunesData = $this->getDoctrine()
            ->getRepository('AppBundle:FortuneCookie')
            ->countNumberPrintedForCategory($category); // This will return the raw, summed number of category objects
        $fortunesPrinted = $fortunesData['fortunesPrinted']; // Here I am passing the aliases from the DQL query so that we can use the data that we got back from that query
        $averagePrinted = $fortunesData['fortunesAverage'];
        $categoryName = $fortunesData['name'];

        return $this->render('fortune/showCategory.html.twig',[
            'category' => $category,
            'fortunesPrinted' => $fortunesPrinted,
            'averagePrinted' => $averagePrinted,
            'categoryName' => $categoryName,
        ]);
    }
}
