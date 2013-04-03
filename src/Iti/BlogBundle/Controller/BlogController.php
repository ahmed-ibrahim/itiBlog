<?php

namespace Iti\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BlogController extends Controller {
    /**
     * this function used to show home page
     * @author ahmed
     */
    public function indexAction() {
        //get latest 6 posts
        $em = $this->getDoctrine()->getEntityManager();
        $postRepo = $em->getRepository('ItiBlogBundle:Post');

        $latestPosts = $postRepo->findBy(array(), array('createdAt' => 'desc'), $limit = 6);
//        $latestPosts = $postRepo->getLatestPosts(6);
        //cut post content 
        foreach ($latestPosts as $post) {
            if (strlen($post->getContent()) > 150)
                $post->setContent(substr($post->getContent(), 0, 150).'....');
        }

        return $this->render('ItiBlogBundle:Blog:index.html.twig', array(
                    'latestPosts' => $latestPosts
                ));
    }

    /**
     * this function used to show about page
     * @author ahmed
     */
    public function aboutUsAction() {
        return $this->render('ItiBlogBundle:Blog:aboutUs.html.twig', array(
                ));
    }

}
