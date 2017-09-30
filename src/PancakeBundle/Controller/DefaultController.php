<?php

namespace PancakeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use PancakeBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Security\Core\SecurityContext;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="home")
     */
    public function indexAction()
    {
        return $this->render('PancakeBundle:Default:index.html.twig');
    }

    /**
     * @Route("/user/new", name="newUser")
     */
    public function newUserAction(Request $request)
    {
        $user = new User();
        $form = $this->createFormBuilder($user)
           ->add('name',      TextType::class)
           ->add('last_name', TextType::class)
           ->add('email',     EmailType::class)
           ->add('phone',     TextType::class)
           ->add('plainPassword', RepeatedType::class, array(
            'type' => PasswordType::class,
            'invalid_message' => 'Les champs de mot de passe doivent correspondre',
            'options' => array('attr' => array('class' => 'password-field')),
            'required' => true,
            'first_options'  => array('label' => 'Mot de passe'),
            'second_options' => array('label' => 'Confirmation du mot de passe')))
           ->add('save',      SubmitType::class, array('label' => 'Créer le compte'))
           ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $user->setUsername($user->getEmail());
            $user->setEnabled(true);
            $user->addRole("ROLE_USER");

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('showUser', array('id' => $user->getId()));
        }

        return $this->render('PancakeBundle:Default:newUser.html.twig', array('form' => $form->createView(),
        ));
    }

    /**
     * @Route("/user/show/{id}", name="showUser", requirements={"id" = "\d+"})
     */
    public function showUserAction($id, Request $request) {
        $em = $this->getDoctrine()->getManager();

        // On récupère l'annonce $id
        $user = $em->getRepository('PancakeBundle:User')->find($id);

        if (null === $user) {
          throw new NotFoundHttpException("L'utilisateur d'id ".$id." n'existe pas.");
        }

        return $this->render('PancakeBundle:Default:showUser.html.twig', array('user' => $user));
    }

    /**
     * @Route("/user/edit/{id}", name="editUser", requirements={"id" = "\d+"})
     */
    public function editUserAction($id, Request $request) {
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('PancakeBundle:User')->find($id);

        if (null === $user) {
          throw new NotFoundHttpException("L'utilisateur d'id ".$id." n'existe pas.");
        }

        $form = $this->createFormBuilder($user)
           ->add('name',      TextType::class)
           ->add('last_name', TextType::class)
           ->add('email',     EmailType::class)
           ->add('phone',     TextType::class)
           ->add('save',      SubmitType::class, array('label' => 'Modifier le compte'))
           ->getForm();
        ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
          $em->flush();

          $request->getSession()->getFlashBag()->add('notice', 'Utilisateur bien modifiée.');

          return $this->redirect($this->generateUrl('showUser', array('id' => $user->getId())));
        }

        return $this->render('PancakeBundle:Default:editUser.html.twig', array(
                'form'   => $form->createView(),
                'user' => $user
            ));
    }

    /**
     * @Route("/user/delete/{id}", name="deleteUser", requirements={"id" = "\d+"})
     */
    public function deleteAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('PancakeBundle:User')->find($id);

        if (null === $user) {
          throw new NotFoundHttpException("L'utilisateur d'id ".$id." n'existe pas.");
        }

        $form = $this->createFormBuilder()->getForm();

        if ($form->handleRequest($request)->isValid()) {
          $em->remove($user);
          $em->flush();

          $request->getSession()->getFlashBag()->add('info', "L'utilisateur a bien été supprimé.");

          return $this->redirect($this->generateUrl('newUser'));
        }

        return $this->render('PancakeBundle:Default:deleteUser.html.twig', array(
          'user' => $user,
          'form'   => $form->createView()
        ));
    }
}
