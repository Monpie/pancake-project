<?php

namespace PancakeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use PancakeBundle\Entity\Enquiry;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ContactController extends Controller
{
	/**
     * @Route("/messagerie", name="messagerie")
     */
	public function contactAction(Request $request){
		$enquiry = new Enquiry();
		$form = $this->createFormBuilder($enquiry)
			->add('name', TextType::class)
        	->add('email', EmailType::class)
        	->add('subject',TextType::class)
        	->add('body', TextareaType::class)
           	->add('save', SubmitType::class, array('label' => 'Envoyer'))
           	->getForm();

		$form->handleRequest($request);

		if($form->isSubmitted() && $form->isValid()){

			if($form->isValid()){

				$message = \Swift_Message::newInstance()
            		->setSubject('Contact enquiry from crepe à gogo')
            		->setFrom('admin@email.com')
            		->setTo('employee@email.fr')
            		->setBody($this->renderView('PancakeBundle:Contact:contactEmail.txt.twig', array('enquiry' => $enquiry)));
        		$this->get('mailer')->send($message);

        		$request->getSession()
        		->getFlashBag()
        		->add('success', 'Message envoyé. Merci');
        		
				return $this->redirect($this->generateUrl('messagerie'));
			}
		}

		return $this->render('PancakeBundle:Contact:contact.html.twig', array('form' => $form->createView() ));
	}
}