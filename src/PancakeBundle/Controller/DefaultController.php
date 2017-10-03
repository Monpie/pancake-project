<?php

namespace PancakeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use PancakeBundle\Entity\Pancake;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class DefaultController extends Controller
{
    /**
     * @Route("/",name="home")
     */
    public function indexAction()
    {
        $pancake = $this->getDoctrine()->getManager()->getRepository('PancakeBundle:Pancake');
        
        if(!$pancake){
            throw $this->createNotFoundException('Aucun produit trouvé');
        }
        
        return $this->render('PancakeBundle:Default:index.html.twig', array('pancake' => $pancake));
    }
    
    /**
    * @Route("/crepe/new",name="newCrepe")
    */
    public function newCrepeAction(Request $request){
        $pancake = new Pancake();
        
        $form = $this->createFormBuilder($pancake)
            ->add('name', TextType::class)
            ->add('price', MoneyType::class)
            ->add('description', TextareaType::class)
            ->add('image', FileType::class)
            ->add('avaibility', CheckboxType::class)
            ->add('save', SubmitType::class, array('label'=>'Créer l\'article'))
            ->getForm();
        
        $form->handleRequest($request);
        if($form->isSubmitted()&&$form->isValid()){
            /*Permet d'ajouter le fichier uploader dans le repertoire ci-dessous*/
            $dir = "/bundles/images/";
            $totalDir = "C:\wamp64\www\pancake-project\web".$dir;
            $file = $form['image']->getData();
            $file->move($totalDir, $file->getClientOriginalName());
            
            $pancake->setImage($dir.$file->getClientOriginalName());
            
            $pancake = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($pancake);
            $em->flush();
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
    * @Route("/details-crepe/{id}",name="details-crepe", requirements={"id"="\d+"})
    */
    public function detailsCrepeAction($id){
        $pancake = $this->getDoctrine()->getManager()->getRepository('PancakeBundle:Pancake')->find($id);
        
        if(!$pancake){
            throw $this->createNotFoundException('Aucun produit trouvé pour l\'identifiant : '+$id);
        }
        
        return $this->render('PancakeBundle:Default:details-crepe.html.twig', array('pancake' => $pancake));
    }
    
    /**
    * @Route("/crepes",name="crepes")
    */
    public function pageCrepesAction(){
        $pancake = $this->getDoctrine()->getManager()->getRepository('PancakeBundle:Pancake');
        
        if(!$pancake){
            throw $this->createNotFoundException('Aucun produit trouvé');
        }
        
        return $this->render('PancakeBundle:Default:crepes.html.twig', array('pancake' => $pancake));
    }
    
    /**
    * @Route("/panier",name="panier")
    */
    public function panierAction(){
        return $this->render('PancakeBundle:Default:panier.html.twig');
    }

    /**
    * @Route("/test/{id}",name="test", requirements={"id"="\d+"})
    */
    public function showPancake($id){
        $pancake = $this->getDoctrine()->getManager()->getRepository('PancakeBundle:Pancake')->find($id);
        
        if(!$pancake){
            throw $this->createNotFoundException('Aucun produit trouvé pour l\'identifiant : '+$id);
        }
        
        return $this->render('PancakeBundle:Default:test.html.twig', array('pancake' => $pancake));
    }
}
