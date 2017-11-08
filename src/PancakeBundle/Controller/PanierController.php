<?php

namespace PancakeBundle\Controller;


use PancakeBundle\Entity\Pancake;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use ClassesWithParents\D;
use PancakeBundle\Entity\Pancakes;
use Symfony\Component\DependencyInjection\Tests\Compiler\H;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use PancakeBundle\Entity\Historique;
use PancakeBundle\Entity\User;
use Symfony\Component\Validator\Constraints\DateTime;


class PanierController extends Controller
{
    private $namePanier;
    private $array;

   
    /**
     * @Route("/panier", name="panier")
     */
    public function panierAction(Session $session)
    {
        if (!$session->has('panier')) {
            $session->set('panier', array());
            $session->getFlashBag()->add('success', "Session créer avec succès!");
            $articles = 0;
        } else {

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
            'totalPrice' => $this->totalPrice($session)));

        //return $this->render('PancakeBundle:Default:panier.html.twig',array('panier'=>$session->get('panier')));
        // return $this->render('PancakeBundle:Default:panier.html.twig', array('panier' => $articles));

    }

    /**
     * @Route("/supprimer/{id}", name="supprimer", requirements={"id"="\d+"})
     */
    public function removeAction(Session $session, $id){


        //$request = Request::createFromGlobals(); // remplace $session = $this->getRequest()->getSession();

        $panier = $session->get('panier');

        if (array_key_exists($id, $panier)) {
            unset($panier[$id]);
            $session->set('panier', $panier);
            $session->getFlashBag()->add('success', 'Article supprimé avec succès');

        }

        return $this->redirect($this->generateUrl('panier'));
    }

    /**
     * @Route("/ajouter/{id}", name="ajouter", requirements={"id"="\d+"})
     */
    public function addAction(Session $session, $id)
    {
        $request = Request::createFromGlobals();

        if (!$session->has('panier')) {
            /* echo '<p>passage création panier</p>';
             $session = new Session();
             $session->start();*/
            $session->set('panier', array());

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

        if (array_key_exists($id, $panier)) {
            if ($request->query->get('quantity') != null) {
                $panier[$id] += $request->query->get('quantity');
            }
        } else {
            if ($request->query->get('quantity') != null) {
                $panier[$id] = $request->query->get('quantity');
            } else {
                $panier[$id] = 1;
            }

            $session->getFlashBag()->add('success', 'Article ajouté avec succès');
        }

        $session->set('panier', $panier);



        //return $this->render('PancakeBundle:Default:panier.html.twig',array('panier'=>$session->get('panier')));
        return $this->redirect($this->generateUrl('panier'));

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

    /**
     * @Route("/valider", name="valider")
     */
    public function validerAction(Session $session)
    {
        if ($this->getUser() != null) {
                $this->createHistorique($session);
                $session->clear();
            return $this->redirect($this->generateUrl('panier'));

          /*  if (!$this->getUser()->getPurchases()->isEmpty()) {
                $currentDate = new \DateTime('now');
                foreach ($this->getUser()->getPurchases() as $elem) {
                    $diffDate = $currentDate->diff($elem->getDate());
                    if ($diffDate->h < 1) {
                        foreach ($session->get('panier') as $pan => $qty) {
                            if ($pan == $elem->getPancakeArray()->getId()) {
                                $elem->setQuantity($elem->getQuantity() + $qty);
                                $elem->setDate($currentDate);
                                $this->getDoctrine()->getManager()->persist($elem);
                                $this->getDoctrine()->getManager()->flush();
                            }
                        }

                    } else {
                   // $this->createSingleHistorique($currentDate, , $elem->getPancakeArray());

                       if($session->get('panier'))
                        $qty = (int)array_values($session->get('panier'));
                        $this->createSingleHistorique($currentDate,$qty,);
                     }
                }
            } else {
                $historique[] = $this->createHistorique($session);
            }
            $session->clear();
            return $this->redirect($this->generateUrl('panier'));*/
        } else {
            return $this->redirectToRoute('login');
        }
    }

    /**
     * @Route("/show/historique", name="showHistorique")
     */
    public function showUserHistoriqueAction()
    {
        if ($this->getUser() != null) {
            $cpt =0;
            foreach($this->getUser()->getPurchases() as $elem){

                $tab[$cpt] = $this->getDoctrine()->getManager()->getRepository('PancakeBundle:Pancake')->findOneById($elem->getPancakeArray());
                    $cpt++;
            }
            return $this->render('PancakeBundle:Default:historique.html.twig', array('historique' =>$this->getUser()->getPurchases(),
                'pancake'=>$tab));
        } else {
            return $this->redirectToRoute('login');
        }
    }

    /**
     * @Route("/show/all_historique", name="showAllUserHistorique")
     */
    public function showAllUserHistorique()
    {
        $em = $this->getDoctrine()->getManager()->getRepository('PancakeBundle:Historique')->findAll();
        $cpt = 0;
        foreach($em as $elem){
            $tab[$cpt] = $this->getDoctrine()->getManager()->getRepository('PancakeBundle:Pancake')->findOneById($elem->getPancakeArray());
            $cpt++;
        }
        return $this->render('PancakeBundle:Default:allHistorique.html.twig', array('historique' =>$em,
            'pancake'=>$tab));
    }

    public function totalPrice(Session $session)
    {
        $total = 0;
        $produits = $this->getDoctrine()->getManager()->getRepository('PancakeBundle:Pancake');
        foreach (array_keys($session->get('panier')) as $elem) {
            $pancake[$elem] = $produits->findOneById($elem);
            $total += $pancake[$elem]->getPrice() * $session->get('panier')[$elem];
        }
        return $total;
    }



    public function createHistorique(Session $session)
    {
        $date = new \DateTime();
        $date->format("now");
        $em = $this->getDoctrine()->getManager()->getRepository('PancakeBundle:Pancake');

        foreach ($session->get('panier') as $elem => $qty) {
            $historique = new Historique();
            $historique->setDate($date);
            $historique->setUser($this->getUser());
            $historique->setQuantity($qty);
            $historique->setPancakeArray($em->findOneById($elem));
            $tabHistorique[$elem] = $historique;
            $this->getDoctrine()->getManager()->persist($historique);
            $this->getDoctrine()->getManager()->flush();
        }
        return $tabHistorique;
    }

    public function createSingleHistorique(\DateTime $date, $qty, Pancake $pancake)
    {
        $historique = new Historique();
        $historique->setDate($date);
        $historique->setUser($this->getUser());
        $historique->setQuantity($qty);
        $historique->setPancakeArray($pancake);
        $this->getDoctrine()->getManager()->persist($historique);
        $this->getDoctrine()->getManager()->flush();
    }

}

?>