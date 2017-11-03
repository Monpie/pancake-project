<?php

namespace PancakeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class PrivilegeController extends Controller
{

    /**
     * @Route("/login")
     */
    public function loginAction(Request $request)
    {
        // Si le visiteur est déjà identifié, on le redirige vers l'accueil
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
          return $this->redirectToRoute('home');
        }
        
        // Le service authentication_utils permet de récupérer le nom d'utilisateur
        // et l'erreur dans le cas où le formulaire a déjà été soumis mais était invalide
        // (mauvais mot de passe par exemple)
        $authenticationUtils = $this->get('security.authentication_utils');

        return $this->render('PancakeBundle:Privilege:login.html.twig', array(
          'last_username' => $authenticationUtils->getLastUsername(),
          'error'         => $authenticationUtils->getLastAuthenticationError(),
        ));
    }

    /**
     * @Route("/admin/users", name="listUsers")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function listUsersAction()
    {
      $em = $this->getDoctrine()->getManager();
      $users = $em->getRepository('PancakeBundle:User')->findAll();

      return $this->render('PancakeBundle:Privilege:listUsers.html.twig', array('users' => $users));
    }

    /**
     * @Route("/admin/edit/user/{id}", name="editAdminUser", requirements={"id" = "\d+"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function editAdminUser($id, Request $request)
    {
      $usr = $this->get('security.token_storage')->getToken()->getUser();

      $em = $this->getDoctrine()->getManager();

      $user = $em->getRepository('PancakeBundle:User')->find($id);

      if (null === $user) {
          throw new NotFoundHttpException("L'utilisateur d'id ".$id." n'existe pas.");
      } elseif (null === $usr) {
          return $this->render('PancakeBundle:Privilege:editAdminUser.html.twig', array('connectedUser' => null));
      } else {

        $form = $this->createFormBuilder($user)
           ->add('name',      TextType::class)
           ->add('last_name', TextType::class)
           ->add('email',     EmailType::class)
           ->add('phone',     TextType::class)
           ->add('staff',     CheckboxType::class, ['required' => false])
           ->add('admin',     CheckboxType::class, ['required' => false])
           ->add('save',      SubmitType::class, array('label' => 'Modifier le compte'))
           ->getForm();
        ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
          $em->flush();

          $request->getSession()->getFlashBag()->add('success', 'Utilisateur bien modifié.');

          return $this->redirect($this->generateUrl('listUsers'));
        }

        return $this->render('PancakeBundle:Privilege:editAdminUser.html.twig', array(
                'form'   => $form->createView(),
                'connectedUser' => $usr,
                'user' => $user
        ));
      }
    }

    /**
     * @Route("/admin/edit/userPwd/{id}", name="editAdminUserPwd", requirements={"id" = "\d+"})
     */
    public function editAdminUserPasswordAction($id, Request $request) {
        $usr = $this->get('security.token_storage')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('PancakeBundle:User')->find($id);

        if (null === $user) {
            throw new NotFoundHttpException("L'utilisateur d'id ".$id." n'existe pas.");
        } elseif (null === $usr) {
            return $this->render('PancakeBundle:Privilege:editAdminUserPassword.html.twig', array('connectedUser' => null));
        } else {

            $form = $this->createFormBuilder($user)
                ->add('plainPassword', RepeatedType::class, array(
                    'type' => PasswordType::class,
                    'invalid_message' => 'Les champs de mot de passe doivent correspondre',
                    'options' => array('attr' => array('class' => 'password-field')),
                    'required' => true,
                    'first_options'  => array('label' => 'Nouveau mot de passe'),
                    'second_options' => array('label' => 'Confirmation du nouveau mot de passe')))
                ->add('save',      SubmitType::class, array('label' => 'Modifier le mot de passe'))
                ->getForm();
            ;

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();
                $encoder = $this->container->get('security.password_encoder');

                $user->setPlainPassword($data->getPlainPassword());
                $this->get('fos_user.user_manager')->updateUser($user);
                $request->getSession()->getFlashBag()->add('success', 'Mot de passe bien modifié.');
                return $this->redirect($this->generateUrl('editAdminUser', array('id' => $user->getId())));
            }

            return $this->render('PancakeBundle:Privilege:editAdminUserPassword.html.twig', array(
                    'form'   => $form->createView(),
                    'connectedUser' => $usr,
                    'user' => $user
                ));
        }
    }
}