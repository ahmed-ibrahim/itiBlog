<?php

namespace Iti\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BlogController extends Controller
{
    /**
     * this function used to show home page
     * @author ahmed
     */
    public function indexAction()
    {
        return $this->render('ItiBlogBundle:Blog:index.html.twig', array(
            
        ));
    }
    
    /**
     * this function used to show about page
     * @author ahmed
     */
    public function aboutUsAction(){
        return $this->render('ItiBlogBundle:Blog:aboutUs.html.twig', array(
            
        ));
    }
}
