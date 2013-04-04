<?php

namespace Iti\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class BlogController extends Controller {
    
    public function sideBarAction(){
        
        return $this->render('ItiBlogBundle:Blog:sideBar.html.twig', array(
                    
                ));
    }

    /**
     * this function used to show tag posts
     * @author ahmed
     * @param integer $id
     * @param integer $page
     */
    public function tagsAction($id, $page) {
        $em = $this->getDoctrine()->getEntityManager();
        $postRepo = $em->getRepository('ItiBlogBundle:Post');
        $tagRepo = $em->getRepository('ItiBlogBundle:Tag');

        $tag = $tagRepo->find($id);

        if (!$tag) {
            throw $this->createNotFoundException('Unable to find this tag.');
        }

        $itemsPerPage = $this->container->getParameter('posts_per_page');

        $posts = $postRepo->getTagPosts($id, $page, $itemsPerPage);
        foreach ($posts as $post) {
            if (strlen($post->getContent()) > 150)
                $post->setContent(substr($post->getContent(), 0, 150) . '....');
        }

        $postsCount = $postRepo->countTagPosts($id);
        $postsCount = $postsCount['0']['postsCount'];

        //calculate the last page number
        $lastPageNumber = (int) ($postsCount / $itemsPerPage);
        if (($postsCount % $itemsPerPage) > 0) {
            $lastPageNumber++;
        }

        return $this->render('ItiBlogBundle:Blog:tag.html.twig', array(
                    'tag' => $tag,
                    'id' => $id,
                    'posts' => $posts,
                    'page' => $page,
                    'lastPageNumber' => $lastPageNumber
                ));
    }

    /**
     * this function used to show category posts
     * @author ahmed
     * @param integer $id
     * @param integer $page
     */
    public function categoryAction($id, $page) {
        $em = $this->getDoctrine()->getEntityManager();
        $postRepo = $em->getRepository('ItiBlogBundle:Post');
        $categoryRepo = $em->getRepository('ItiBlogBundle:Category');

        $category = $categoryRepo->find($id);

        if (!$category) {
            throw $this->createNotFoundException('Unable to find this category.');
        }

        $itemsPerPage = $this->container->getParameter('posts_per_page');

        $posts = $postRepo->getCategoryPosts($id, $page, $itemsPerPage);
        foreach ($posts as $post) {
            if (strlen($post->getContent()) > 150)
                $post->setContent(substr($post->getContent(), 0, 150) . '....');
        }

        $postsCount = $postRepo->countCategoryPosts($id);
        $postsCount = $postsCount['0']['postsCount'];

        //calculate the last page number
        $lastPageNumber = (int) ($postsCount / $itemsPerPage);
        if (($postsCount % $itemsPerPage) > 0) {
            $lastPageNumber++;
        }

        return $this->render('ItiBlogBundle:Blog:category.html.twig', array(
                    'category' => $category,
                    'id' => $id,
                    'posts' => $posts,
                    'page' => $page,
                    'lastPageNumber' => $lastPageNumber
                ));
    }

    /**
     * this function used to add post comment
     * @author ahmed
     * @param integer $postId
     * @param string $commentText
     */
    public function addPostCommentAction($postId, $commentText) {
        $em = $this->getDoctrine()->getEntityManager();
        $postRepo = $em->getRepository('ItiBlogBundle:Post');

        //get the post
        $post = $postRepo->find($postId);
        if ($post) {
            //create new comment
            $newComment = new \Iti\BlogBundle\Entity\Comment();
            $newComment->setComment($commentText);
            $newComment->setPost($post);

            //increment post no of comments
            $post->setNoOfComments($post->getNoOfComments() + 1);

            $em->persist($newComment);
            $em->flush();

            return $this->render('ItiBlogBundle:Blog:newComment.html.twig', array(
                        'comment' => $newComment
                    ));
        } else {
            return new Response('faild');
        }
    }

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
                $post->setContent(substr($post->getContent(), 0, 150) . '....');
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
