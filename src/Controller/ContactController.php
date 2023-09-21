<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Service\MailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact_index')]
    public function index(Request $request, EntityManagerInterface $manager, MailService $mailService): Response
    {
        $contact = new Contact();

        // Si l'utilisateur existe, je remplie directement ses informations
        if ($this->getUser()) {
            $contact->setFullName($this->getUser()->getFullName())
                ->setEmail($this->getUser()->getEmail());
        }

        $form = $this->createForm(ContactType::class, $contact);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $contact = $form->getData();

            $manager->persist($contact);
            $manager->flush();

            // J'appelle ma fonction d'envoie d'email créee dans le service
            /* Je remplie les paramètre de la fonction que j'avais déclarer dans le service (ex à côté de chaque paramètre) */
            $mailService->sendEmail(
                $contact->getEmail(), // $from
                $contact->getSubject(), // $subject
                'emails/contact.html.twig', // $htmlTemplate
                ['contact' => $contact], //$context

                /* $to n'est pas ici car il est directement définit dans la création de la fonction
                   (voir la fonction sendEmail pour comparer) */
            );

            // Message de succès
            $this->addFlash(
                'success',
                'Votre message a bien été envoyé.'
            );

            return $this->redirectToRoute('contact_index');
        }

        return $this->render('pages/contact/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
