<?php

declare(strict_types=1);

namespace Pehapkari\Registration\Controller;

use Pehapkari\Exception\ShouldNotHappenException;
use Pehapkari\Mailer\PehapkariMailer;
use Pehapkari\Registration\Entity\TrainingRegistration;
use Pehapkari\Registration\Form\RegistrationFormType;
use Pehapkari\Registration\Repository\RegistrationRepository;
use Pehapkari\Training\Entity\TrainingTerm;
use Pehapkari\Validation\EmailValidation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;

final class RegistrationController extends AbstractController
{
    private RegistrationRepository $trainingRegistrationRepository;

    private PehapkariMailer $pehapkariMailer;

    private EmailValidation $emailValidation;

    public function __construct(
        RegistrationRepository $trainingRegistrationRepository,
        PehapkariMailer $pehapkariMailer,
        EmailValidation $emailValidation
    ) {
        $this->trainingRegistrationRepository = $trainingRegistrationRepository;
        $this->pehapkariMailer = $pehapkariMailer;
        $this->emailValidation = $emailValidation;
    }

    /**
     * @Route(path="registrace/{slug}", name="registration", methods={"GET", "POST"})
     *
     * @see https://github.com/symfony/demo/blob/master/src/Controller/Admin/BlogController.php
     */
    public function __invoke(Request $request, TrainingTerm $trainingTerm): Response
    {
        $this->ensureTrainingTermIsOpened($trainingTerm);

        $trainingRegistration = $this->createTrainingRegistration($trainingTerm);

        $form = $this->createForm(RegistrationFormType::class, $trainingRegistration);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->processRegistrationForm($trainingRegistration);
        }

        return $this->render('registration/registration.twig', [
            'training' => $trainingTerm->getTraining(),
            'training_term' => $trainingTerm,
            'form' => $form->createView(),
        ]);
    }

    private function ensureTrainingTermIsOpened(TrainingTerm $trainingTerm): void
    {
        if ($trainingTerm->isRegistrationOpened()) {
            return;
        }

        throw new AccessDeniedException('Toto školení aktuálně nemá otevřený termín');
    }

    private function createTrainingRegistration(TrainingTerm $trainingTerm): TrainingRegistration
    {
        $trainingRegistration = new TrainingRegistration();
        $trainingRegistration->setTrainingTerm($trainingTerm);
        $trainingRegistration->setPrice($trainingTerm->getPrice());

        return $trainingRegistration;
    }

    private function processRegistrationForm(TrainingRegistration $trainingRegistration): RedirectResponse
    {
        $email = $trainingRegistration->getEmail();
        if ($email === null) {
            throw new ShouldNotHappenException();
        }

        // is email valid?
        if (! $this->emailValidation->isEmailValid($email)) {
            throw new AccessDeniedHttpException(sprintf('Email "%s" jsme nenašli', $email));
        }

        $this->trainingRegistrationRepository->save($trainingRegistration);
        $this->pehapkariMailer->sendRegistrationConfirmation($trainingRegistration);

        return $this->redirectToRoute('registration_thank_you', [
            'slug' => $trainingRegistration->getTrainingTermSlug(),
        ]);
    }
}
