<?php

namespace PancakeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use PancakeBundle\Entity\Pancake;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;


use PancakeBundle\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Security\Core\SecurityContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


class DefaultController extends Controller
{
    /**
     * @Route("/",name="home")
     */
    public function indexAction()
    {

        $pancake = $this->getDoctrine()->getManager()->getRepository('PancakeBundle:Pancake')->findAll();
        
        if(!$pancake){
            throw $this->createNotFoundException('Aucun produit trouvé');
        }
        
        return $this->render('PancakeBundle:Default:index.html.twig', array('pancake' => $pancake, 'userConnected' => $this->getUser()));
    }

     /**
     * @Route("/presentation",name="presentation")
     */
    public function presentationAction()
    {

        $pancake = $this->getDoctrine()->getManager()->getRepository('PancakeBundle:Pancake')->findAll();
        
        if(!$pancake){
            throw $this->createNotFoundException('Aucun produit trouvé');
        }
        
        return $this->render('PancakeBundle:Default:presentation.html.twig', array('pancake' => $pancake, 'userConnected' => $this->getUser()));
    }
    
    /**
    * @Route("/crepe/new",name="newCrepe")
    * @Security("has_role('ROLE_STAFF')")
    */
    public function newCrepeAction(Request $request){
        $pancake = new Pancake();
        
        $form = $this->createFormBuilder($pancake)
            ->add('name', TextType::class)
            ->add('price', MoneyType::class)
            ->add('description', TextareaType::class)
            ->add('image', FileType::class)
            ->add('avaibility', CheckboxType::class, ['required' => false])
            ->add('pancake', CheckboxType::class, ['required' => false])
            ->add('promotion', CheckboxType::class, ['required' => false])
            ->add('save', SubmitType::class, array('label'=>'Créer l\'article'))
            ->getForm();
        
        $form->handleRequest($request);
        if($form->isSubmitted()&&$form->isValid()) {
            /*Permet d'ajouter le fichier uploader dans le repertoire ci-dessous*/
            $dir = "/bundles/images/";

            $totalDir = "C:\xammp\htdocs\pancake-project\web".$dir;

            $totalDir = "C:\xammp\htdocs\pancake-project\web" . $dir;

            $file = $form['image']->getData();

            if (strcmp($file->guessExtension(), "jpeg") == 0 || strcmp($file->guessExtension(),"png") == 0) {
                $file->move($totalDir, $file->getClientOriginalName());
                $pancake->setImage($dir . $file->getClientOriginalName());

                $pancake = $form->getData();
                $em = $this->getDoctrine()->getManager();
                $em->persist($pancake);
                $em->flush();
            } else {
                $this->get('session')->getFlashBag()->add('info','Erreur format fichier');
                echo 'Format fichier invalide';
                $pancake->setImage(null);
                return $this->render('PancakeBundle:Default:newCrepe.html.twig', array('form' => $form->createView()));

            }
        }
        return $this->render('PancakeBundle:Default:newCrepe.html.twig', array('form'=>$form->createView()));
    }
    
    /**
    * @Route("/inscription",name="inscription")
    */
    public function inscriptionAction(){
        return $this->render('PancakeBundle:Default:inscription.html.twig');
    }

    /**
    * @Route("/contact",name="contact")
    */
    public function contactAction(){
        return $this->render('PancakeBundle:Default:contact.html.twig');
    }
    
    
    /**
    * @Route("/details-crepe/{id}",name="details-crepe", requirements={"id"="\d+"})
    */
    public function detailsCrepeAction($id ){
        $pancake = $this->getDoctrine()->getManager()->getRepository('PancakeBundle:Pancake')->find($id);

        if(!$pancake){
            throw $this->createNotFoundException('Aucun produit trouvé pour l\'identifiant : '+$id);
        }

        return $this->render('PancakeBundle:Default:details-crepe.html.twig', array('pancake' => $pancake));
    }

    /**
     * @Route("/panier", name="panier")
     */
    public function panierAction(){
        return $this->render('PancakeBundle:Default:panier.html.twig');
    }
    
    /**
    * @Route("/crepes",name="crepes")
    */
    public function pageCrepesAction(){

        $em = $this->getDoctrine()->getManager();
        $crepes = $em->getRepository('PancakeBundle:Pancake')->findByPancake(false);
        $pancakes = $em->getRepository('PancakeBundle:Pancake')->findByPancake(true);
        
        if(!$crepes){
            throw $this->createNotFoundException('Aucune crêpe trouvée');
        }

        if(!$pancakes){
            throw $this->createNotFoundException('Aucun pancake trouvé');
        }
        
        return $this->render('PancakeBundle:Default:crepes.html.twig', array('pancakes' => $pancakes, 'crepes' => $crepes));
    }


    /**
    * @Route("/pancakes",name="pancakes")
    */
    public function pagePancakesAction(){

        $pancake = $this->getDoctrine()->getManager()->getRepository('PancakeBundle:Pancake')->findAll();
        
        if(!$pancake){
            throw $this->createNotFoundException('Aucun produit trouvé');
        }
        
        return $this->render('PancakeBundle:Default:pancakes.html.twig', array('pancakes' => $pancake));
    }


    /**
    * @Route("/test",name="test")
    */
    public function showPancake(Request $request){
      /*  $panier = new Panier();
        $form = $this->createFormBuilder($panier)->add('quantity', TextType::class)->add('submit',SubmitType::class)->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $panier = $form->getData();
            return $this->render('PancakeBundle:Default:panier.html.twig', array('panier'=>$panier));
            //return $this->redirectToRoute("panier", array('panier'=>$panier));
        }
        return $this->render('PancakeBundle:Default:test.html.twig', array('form'=> $form->createView()));*/


        return $this->render('PancakeBundle:Default:test.html.twig', array('pancake' => $this->getDoctrine()->getManager()->getRepository('PancakeBundle:Pancake')->findAll()));
    }


    /**
     * @Route("/test/{id}", name="editPancake")
     */
    public function editPancakeAction($id){
        $em = $this->getDoctrine()->getManager();
        $pancake = $em->getRepository('PancakeBundle:Pancake')->find($id);
        $request = Request::createFromGlobals();

        if($request->query->get('pancakeName'.$id)!= null) {
            $pancake->setName($request->query->get('pancakeName' . $id));
        }

        if($request->query->get('pancakePrice'.$id) != null) {
            $pancake->setPrice($request->query->get('pancakePrice' . $id));
        }

        if($request->query->get('pancakeDescription'.$id)!= null) {
            $pancake->setDescription($request->query->get('pancakeDescription' . $id));
        }

        if($request->query->get('pancakeAvailability'.$id)!= null) {
            $pancake->setAvaibility($request->query->get('pancakeAvailability' . $id));
        }

        if($request->query->get('pancakeImage'.$id)!= null) {
            $pancake->setImage($request->query->get('pancakeImage' . $id));
        }



        $em->flush();
        return $this->redirect($this->generateUrl('test'));
    }

    /**
     * @Route("/test/{id}", name="removePancake", requirements={"id"="\d+"})
     */
    public function removePancakeAction($id){
        $pancake = $this->getDoctrine()->getManager()->getRepository('PancakeBundle:Pancake')->find($id);
        $em = $this->getDoctrine()->getManager();
        foreach($this->getUser()->getPurchases() as $elem){
            if($elem->getPancakeArray() == $pancake ){
                $em->remove($elem);
            }
        }
        $this->getDoctrine()->getManager()->remove($pancake);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirect($this->generateUrl('test'));
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
         /**
     * @Route("/user/edit/{id}", name="editUser", requirements={"id" = "\d+"})
     */
    public function editUserAction($id, Request $request) {
        $usr = $this->get('security.token_storage')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('PancakeBundle:User')->find($id);

        if (null === $user) {
            throw new NotFoundHttpException("L'utilisateur d'id ".$id." n'existe pas.");
        } elseif ($usr != $user) {
            return $this->render('PancakeBundle:Default:editUser.html.twig', array('user' => null));
        } else {
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

              $request->getSession()->getFlashBag()->add('success', 'Utilisateur bien modifié.');

              return $this->redirect($this->generateUrl('showUser', array('id' => $user->getId())));
            }

            return $this->render('PancakeBundle:Default:editUser.html.twig', array(
                    'form'   => $form->createView(),
                    'user' => $user
                ));
        }

      }

    /**
     * @Route("/user/editPwd/{id}", name="editPwd", requirements={"id" = "\d+"})
     */
    public function editPasswordAction($id, Request $request) {
        $usr = $this->get('security.token_storage')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('PancakeBundle:User')->find($id);

        if (null === $user) {
            throw new NotFoundHttpException("L'utilisateur d'id ".$id." n'existe pas.");
        } elseif ($usr != $user) {
            return $this->render('PancakeBundle:Default:editPassword.html.twig', array('user' => null));
        } else {

            $form = $this->createFormBuilder($user)
                ->add('oldPassword',      PasswordType::class, array('mapped' => false))
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
                $oldPassword = $form->get('oldPassword')->getData();

                if($encoder->isPasswordValid($usr, $oldPassword)) {
                    $user->setPlainPassword($data->getPlainPassword());
                    $this->get('fos_user.user_manager')->updateUser($user);
                    $request->getSession()->getFlashBag()->add('success', 'Mot de passe bien modifié.');
                    return $this->redirect($this->generateUrl('editUser', array('id' => $user->getId())));
                } else {
                    $request->getSession()->getFlashBag()->add('danger', 'Mot de passe incorrecte.');
                }
            }

            return $this->render('PancakeBundle:Default:editPassword.html.twig', array(
                    'form'   => $form->createView(),
                    'user' => $user
                ));
        }
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


}
