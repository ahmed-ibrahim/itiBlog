<?php

namespace Iti\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Iti\UserBundle\Entity\User;

class SecurityController extends Controller {

    /**
     * this function used to create new user
     */
    public function signUpAction() {
        //initialize the form validation groups array
        $formValidationGroups = array('signup');
        //get the request object
        $request = $this->getRequest();
        //create an emtpy user object
        $user = new User();

        $em = $this->getDoctrine()->getEntityManager();

        //create a signup form
        $formBuilder = $this->createFormBuilder($user, array(
                    'validation_groups' => $formValidationGroups
                ))
                ->add('username')
                ->add('useremail', 'email')
//                ->add('file', 'file', array('required' => false, 'label' => 'صورتك', 'attr' => array('onchange' => 'readURL(this);')))
                ->add('password', 'repeated', array(
            'type' => 'password',
            'first_name' => 'Password',
            'second_name' => 'RePassword',
            'invalid_message' => "The passwords don't match",
                ));

        //create the form
        $form = $formBuilder->getForm();
        //check if this is the user posted his data
        if ($request->getMethod() == 'POST') {
            //fill the form data from the request
            $form->bindRequest($request);
            //check if the form values are correct
            if ($form->isValid()) {
                //get the user object from the form
                $user = $form->getData();
                echo 'done';exit;
                //user data are valid finish the signup process
//                return $this->finishSignUp($user);
            }
        }

        return $this->render('ItiUserBundle:Security:signUp.html.twig', array(
                    'form' => $form->createView()
                ));
    }

    /**
     * this function will used to show login form
     */
    public function loginAction() {
        //initialize an emtpy message string
        $message = '';
        //check if we have a logged in user
        if (TRUE === $this->get('security.context')->isGranted('ROLE_NOTACTIVE')) {
            //set a hint message for the user
            $message = $this->get('translator')->trans('you will be logged out and logged in as the new user');
        }

        $request = $this->getRequest();
        $session = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(
                    SecurityContext::AUTHENTICATION_ERROR
            );
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        return $this->render(
                        'ItiUserBundle:Security:login.html.twig', array(
                    // last username entered by the user
                    'last_username' => $session->get(SecurityContext::LAST_USERNAME),
                    'error' => $error,
                    'message' => $message
                        )
        );
    }

}
