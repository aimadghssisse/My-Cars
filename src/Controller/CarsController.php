<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Cars;
use App\Entity\Colors;
use App\Entity\Brand;
use DateTime;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class CarsController extends AbstractController
{
    /**
     * @Route("/", name="cars")
     */
    public function index()
    {
      $repository = $this->getDoctrine()->getRepository(Cars::class);
      $cars = $repository->findByInactive(false);

        return $this->render('cars/index.html.twig', [
          'cars' => $cars
        ]);
    }

    /**
     * @Route("/car/{id}", name="single_car",
     *     requirements={
     *         "id": "\d+"
     *     })
     */
    public function show($id)
    {
      $repository = $this->getDoctrine()->getRepository(Cars::class);
      $cars = $repository->findBy([
        'id' => $id,
        'inactive' => false
      ]);
      if(empty($cars)) {
        $session = new Session();
        $session->set('error', 'Car Introuvable');
        return $this->redirectToRoute('cars');
      }
        return $this->render('cars/index.html.twig', [
          'cars' => $cars
        ]);
    }

    /**
     * @Route("/car/delete/{car}", name="delete_car",
     *     methods={"POST"},
     *     requirements={
     *         "id": "\d+"
     *     })
     */
    public function delete(Cars $car)
    {
      $em = $this->get('doctrine')->getManager();
      $car->setInactive(true)->setDeletedAt(new DateTime());
      $em->persist($car);
      $em->flush();
      $session = new Session();
      $session->set('success', 'Delete successfully');
      return $this->redirectToRoute('cars');
    }


    /**
     * @Route("/new", name="new_car")
     */

    public function new(Request $request)
    {
        $car = new Cars();
        $form = $this->createFormBuilder($car)
            ->add('name', TextType::class)
            ->add('model', TextType::class)
            ->add('description', TextType::class)
            ->add('year', TextType::class)
            ->add('brand', EntityType::class, [
                 'class' => Brand::class,
                 'choice_label' => 'name',
            ])
            ->add('colors', EntityType::class, [
                 'class' => Colors::class,
                 'choice_label' => 'name',
            ])
            ->add('save', SubmitType::class, ['label' => 'Create Task'])
            ->getForm();

        $form->handleRequest($request);
         if ($form->isSubmitted() && $form->isValid()) {
           $em = $this->getDoctrine()->getManager();
           $em->persist($car);
           $em->flush();
           $session = new Session();
           $session->set('success', 'News added successfully');
           return $this->redirectToRoute('cars');

         }
        return $this->render('cars/new.html.twig', [
          'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/edit/{id}", name="edit_car")
     */

    public function edit(Request $request, $id)
    {
      $session = new Session();
      $repository = $this->getDoctrine()->getRepository(Cars::class);
      $car = $repository->findOneBy([
        'id' => $id,
        'inactive' => false
      ]);
      if(empty($car)) {
        $session = new Session();
        $session->set('error', 'Car Introuvable');
        return $this->redirectToRoute('cars');
      }

        $form = $this->createFormBuilder($car)
            ->add('name', TextType::class)
            ->add('model', TextType::class)
            ->add('description', TextType::class)
            ->add('year', TextType::class)
            ->add('brand', EntityType::class, [
                 'class' => Brand::class,
                 'choice_label' => 'name',
            ])
            ->add('colors', EntityType::class, [
                 'class' => Colors::class,
                 'choice_label' => 'name',
            ])
            ->add('save', SubmitType::class, ['label' => 'Create Task'])
            ->getForm();
        $form->handleRequest($request);
         if ($form->isSubmitted() && $form->isValid()) {
           $em = $this->getDoctrine()->getManager();
           $em->persist($car);
           $em->flush();
           $session->set('success', 'Update successfully');
           return $this->redirectToRoute('cars');
         }
        return $this->render('cars/new.html.twig', [
          'form' => $form->createView()
        ]);
    }
}
