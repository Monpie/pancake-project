<?php

namespace PancakeBundle\Controller;

use PancakeBundle\Entity\Pancake;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;


class PanierController extends Controller
{
    private $namePanier;
    private $array;
    /**
     * @Route("/panier", name="panier")
     */
    public function panierAction(Session $session){

        if (!$session->has('panier')) {
            /*echo '<p>passage création panier</p>';
            $session = new Session();
            $session->start();*/
            $session->set('panier',array());
            $session->getFlashBag()->add('success',"Session créer avec succès!");
            $articles = 0;
        }else {
            $articles = count($session->get('panier'));
        }

        $pancake = array();
        $produits = $this->getDoctrine()->getManager()->getRepository('PancakeBundle:Pancake');
        foreach(array_keys($session->get('panier')) as $elem)
        {
            $pancake[$elem] = $produits->findOneById($elem);
            //echo $produits->findOneById($elem)->getId();

        }
        return $this->render('PancakeBundle:Default:panier.html.twig', array('pancake' => $pancake,
                                                                                    'panier' => $session->get('panier'),
                                                                                    'totalPrice' =>$this->totalPrice($session)));

        //return $this->render('PancakeBundle:Default:panier.html.twig',array('panier'=>$session->get('panier')));
       // return $this->render('PancakeBundle:Default:panier.html.twig', array('panier' => $articles));
    }

    /**
     * @Route("/supprimer/{id}", name="supprimer", requirements={"id"="\d+"})
     */
    public function removeAction(Session $session, $id){

        //$request = Request::createFromGlobals(); // remplace $session = $this->getRequest()->getSession();

        $panier = $session->get('panier');

        if (array_key_exists($id, $panier))
        {
            unset($panier[$id]);
            $session->set('panier',$panier);
            $session->getFlashBag()->add('success','Article supprimé avec succès');
        }

        return $this->redirect($this->generateUrl('panier'));
    }

    /**
     * @Route("/ajouter/{id}", name="ajouter", requirements={"id"="\d+"})
     */
    public function addAction(Session $session, $id)
    {
        $request = Request::createFromGlobals();

        if(!$session->has('panier'))
        {
           /* echo '<p>passage création panier</p>';
            $session = new Session();
            $session->start();*/
            $session->set('panier',array());
        }

        $panier = $session->get('panier');

        if(array_key_exists($id,$panier)){
            if( $request->query->get('quantity') != null){
                $panier[$id] +=  $request->query->get('quantity');
            }
        } else{
                if( $request->query->get('quantity') != null){
                    $panier[$id] =  $request->query->get('quantity');
                }else{
                    $panier[$id] = 1;
                }
            $session->getFlashBag()->add('success','Article ajouté avec succès');
        }

        $session->set('panier',$panier);


        //return $this->render('PancakeBundle:Default:panier.html.twig',array('panier'=>$session->get('panier')));
        return $this->redirect($this->generateUrl('panier'));
      }

    /**
     * @Route("/valider", name="valider")
     */
      public function validerAction(Session $session){
        $session->clear();
        return $this->redirect($this->generateUrl('panier'));
      }

      public function totalPrice(Session $session){
          $total = 0;
          $produits = $this->getDoctrine()->getManager()->getRepository('PancakeBundle:Pancake');
          foreach(array_keys($session->get('panier')) as $elem){
              $pancake[$elem] = $produits->findOneById($elem);
              $total += $pancake[$elem]->getPrice()*$session->get('panier')[$elem];
          }
          return $total;
      }
      public function getDataBdd(Session $session, $id){
          if(!$session->has('panier'))
          {
              /* echo '<p>passage création panier</p>';
               $session = new Session();
               $session->start();*/
              $session->set('panier',array());
          }

          $pancake = $this->getDoctrine()->getManager()->getRepository('PancakeBundle:Pancake')->find($id);
          $this->render('PancakeBundle:Default:panier.html.twig',array('pancake'=>$pancake));
      }
}
?>