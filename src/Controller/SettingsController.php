<?php
namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class SettingsController extends AbstractController
{
    #[Route('/settings', name: 'app_settings')]
    public function index(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage): Response
    {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('You must be logged in to change your password.');
        }

        // Formulario de cambio de contraseña
        $changePasswordForm = $this->createFormBuilder()
            ->add('current_password', PasswordType::class, ['label' => 'Current Password'])
            ->add('new_password', PasswordType::class, ['label' => 'New Password'])
            ->add('change', SubmitType::class, ['label' => 'Change Password'])
            ->getForm();

        // Formulario de borrado de cuenta
        $deleteAccountForm = $this->createFormBuilder()
            ->add('confirm', CheckboxType::class, [
                'label' => 'I understand that this action is permanent and cannot be undone.',
                'mapped' => false,
                'required' => true,
            ])
            ->add('delete', SubmitType::class, ['label' => 'Delete My Account'])
            ->getForm();

        // Formulario de configuración de notificaciones
        $notificationSettingsForm = $this->createFormBuilder($user)
            ->add('email_notifications', CheckboxType::class, [
                'label' => 'Receive email notifications',
                'required' => false,
            ])
            ->add('app_notifications', CheckboxType::class, [
                'label' => 'Receive app notifications',
                'required' => false,
            ])
            ->add('save', SubmitType::class, ['label' => 'Save Changes'])
            ->getForm();

        // Manejo de formularios
        $changePasswordForm->handleRequest($request);
        $deleteAccountForm->handleRequest($request);
        $notificationSettingsForm->handleRequest($request);

        // Procesar formulario de cambio de contraseña
        if ($changePasswordForm->isSubmitted() && $changePasswordForm->isValid()) {
            $data = $changePasswordForm->getData();
            if ($passwordHasher->isPasswordValid($user, $data['current_password'])) {
                $newPassword = $passwordHasher->hashPassword($user, $data['new_password']);
                $user->setPassword($newPassword);
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Password updated successfully.');
            } else {
                $this->addFlash('error', 'Current password is incorrect.');
            }
        }

        // Procesar formulario de borrado de cuenta
        if ($deleteAccountForm->isSubmitted() && $deleteAccountForm->isValid()) {
            // Elimina el usuario y guarda los cambios
            $entityManager->remove($user);
            $entityManager->flush();

            // Desautentica al usuario
            $tokenStorage->setToken(null);
            $request->getSession()->invalidate();

            // Redirige a la página de inicio o despedida
            $this->addFlash('success', 'Your account has been deleted.');
            return $this->redirectToRoute('login');  // O redirige a la página de inicio de sesión
        }

        // Procesar formulario de notificaciones
        if ($notificationSettingsForm->isSubmitted() && $notificationSettingsForm->isValid()) {
            // Guarda los cambios de las notificaciones
            $entityManager->flush(); // Solo persistir si es necesario
            $this->addFlash('success', 'Notification settings updated successfully.');
        }

        return $this->render('settings/index.html.twig', [
            'page_title' => 'Settings',
            'changePasswordForm' => $changePasswordForm->createView(),
            'deleteAccountForm' => $deleteAccountForm->createView(),
            'notificationSettingsForm' => $notificationSettingsForm->createView(),
        ]);
    }
}