<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Controller;

use Oksydan\IsImageslider\Cache\TemplateCache;
use Oksydan\IsImageslider\Exceptions\DateRangeNotValidException;
use Oksydan\IsImageslider\Filter\ImageSliderFileters;
use Oksydan\IsImageslider\Handler\Slide\DeleteSlideHandler;
use Oksydan\IsImageslider\Handler\Slide\ToggleSlideActivityHandler;
use Oksydan\IsImageslider\Handler\Slide\UpdateSliderPositionHandler;
use Oksydan\IsImageslider\Repository\ImageSliderRepository;
use Oksydan\IsImageslider\Translations\TranslationDomains;
use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerInterface as IdentifiableObjectFormHandlerInterface;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\GridInterface;
use PrestaShop\PrestaShop\Core\Grid\Position\Exception\PositionDataException;
use PrestaShop\PrestaShop\Core\Grid\Position\Exception\PositionUpdateException;
use PrestaShop\PrestaShop\Core\Grid\Presenter\GridPresenter;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class ImagesliderController extends FrameworkBundleAdminController
{
    private TemplateCache $templateCache;

    private DeleteSlideHandler $deleteSlideHandler;

    private GridFactoryInterface $imagsliderGridFactory;

    private FormHandlerInterface $imagesliderConfigurationFormHandler;

    private GridPresenter $gridPresenter;

    private FormBuilderInterface $imagesliderFormBuilder;

    private IdentifiableObjectFormHandlerInterface $imagesliderFormHandler;

    private TranslatorInterface $translator;

    private ImageSliderRepository $imageSliderRepository;

    private ToggleSlideActivityHandler $toggleSlideActivityHandler;

    private UpdateSliderPositionHandler $updateSliderPositionHandler;

    public function __construct(
        TemplateCache $templateCache,
        DeleteSlideHandler $deleteSlideHandler,
        GridFactoryInterface $imagsliderGridFactory,
        FormHandlerInterface $imagesliderConfigurationFormHandler,
        GridPresenter $gridPresenter,
        FormBuilderInterface $imagesliderFormBuilder,
        IdentifiableObjectFormHandlerInterface $imagesliderFormHandler,
        TranslatorInterface $translator,
        ImageSliderRepository $imageSliderRepository,
        ToggleSlideActivityHandler $toggleSlideActivityHandler,
        UpdateSliderPositionHandler $updateSliderPositionHandler
    ) {
        parent::__construct();
        $this->templateCache = $templateCache;
        $this->deleteSlideHandler = $deleteSlideHandler;
        $this->imagsliderGridFactory = $imagsliderGridFactory;
        $this->imagesliderConfigurationFormHandler = $imagesliderConfigurationFormHandler;
        $this->gridPresenter = $gridPresenter;
        $this->imagesliderFormBuilder = $imagesliderFormBuilder;
        $this->imagesliderFormHandler = $imagesliderFormHandler;
        $this->translator = $translator;
        $this->imageSliderRepository = $imageSliderRepository;
        $this->toggleSlideActivityHandler = $toggleSlideActivityHandler;
        $this->updateSliderPositionHandler = $updateSliderPositionHandler;
    }

    public function index(ImageSliderFileters $filters): Response
    {
        $imageSliderGrid = $this->imagsliderGridFactory->getGrid($filters);

        $configurationForm = $this->imagesliderConfigurationFormHandler->getForm();

        return $this->render('@Modules/is_imageslider/views/templates/admin/index.html.twig', [
            'translationDomain' => TranslationDomains::TRANSLATION_DOMAIN_ADMIN,
            'imageSliderkGrid' => $this->presentGrid($imageSliderGrid),
            'configurationForm' => $configurationForm->createView(),
            'help_link' => false,
        ]);
    }

    public function create(Request $request): Response
    {
        $form = $this->imagesliderFormBuilder->getForm();
        $form->handleRequest($request);

        try {
            $result = $this->imagesliderFormHandler->handle($form);

            if (null !== $result->getIdentifiableObjectId()) {
                $this->addFlash(
                    'success',
                    $this->trans('Successful creation.', 'Admin.Notifications.Success')
                );

                $this->clearTemplateCache();

                return $this->redirectToRoute('is_imageslider_controller');
            }
        } catch (\Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->render('@Modules/is_imageslider/views/templates/admin/form.html.twig', [
            'imageSliderForm' => $form->createView(),
            'title' => $this->trans('Image slider', TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
            'help_link' => false,
        ]);
    }

    public function edit(Request $request, int $slideId): Response
    {
        $form = $this->imagesliderFormBuilder->getFormFor($slideId);
        $form->handleRequest($request);

        try {
            $result = $this->imagesliderFormHandler->handleFor($slideId, $form);

            if (null !== $result->getIdentifiableObjectId()) {
                $this->addFlash(
                    'success',
                    $this->trans('Successful edition.', 'Admin.Notifications.Success')
                );

                $this->clearTemplateCache();

                return $this->redirectToRoute('is_imageslider_controller');
            }
        } catch (\Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->render('@Modules/is_imageslider/views/templates/admin/form.html.twig', [
            'imageSliderForm' => $form->createView(),
            'title' => $this->trans('Image slider edition', TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
            'help_link' => false,
        ]);
    }

    public function delete(Request $request, int $slideId): Response
    {
        $imageSlide = $this->imageSliderRepository->find($slideId);

        if (!empty($imageSlide)) {
            $this->deleteSlideHandler->handle($imageSlide);

            $this->addFlash(
                'success',
                $this->trans('Successful deletion.', 'Admin.Notifications.Success')
            );

            return $this->redirectToRoute('is_imageslider_controller');
        }

        $this->addFlash(
            'error',
            $this->trans('Cannot find slider %d', TranslationDomains::TRANSLATION_DOMAIN_ADMIN, ['%d' => $slideId])
        );

        return $this->redirectToRoute('is_imageslider_controller');
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function saveConfiguration(Request $request): Response
    {
        $redirectResponse = $this->redirectToRoute('is_imageslider_controller');

        $form = $this->imagesliderConfigurationFormHandler->getForm();
        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            return $redirectResponse;
        }

        if ($form->isValid()) {
            $data = $form->getData();
            $saveErrors = $this->imagesliderConfigurationFormHandler->save($data);

            if (0 === count($saveErrors)) {
                $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));
                $this->clearTemplateCache();

                return $redirectResponse;
            }
        }

        $formErrors = [];

        foreach ($form->getErrors(true) as $error) {
            $formErrors[] = $error->getMessage();
        }

        $this->flashErrors($formErrors);

        return $redirectResponse;
    }

    /**
     * @param Request $request
     * @param int $slideId
     *
     * @return Response
     */
    public function toggleStatus(Request $request, int $slideId): Response
    {
        $imageSlide = $this->imageSliderRepository->find($slideId);

        if (empty($imageSlide)) {
            return $this->json([
                'status' => false,
                'message' => sprintf('Image slide %d doesn\'t exist', $slideId),
            ]);
        }

        try {
            $this->toggleSlideActivityHandler->handle($imageSlide);

            $response = [
                'status' => true,
                'message' => $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success'),
            ];
        } catch (\Exception $e) {
            $response = [
                'status' => false,
                'message' => sprintf(
                    'There was an error while updating the status of slide %d: %s',
                    $slideId,
                    $e->getMessage()
                ),
            ];
        }

        return $this->json($response);
    }

    public function updatePositionAction(Request $request): Response
    {
        try {
            $this->updateSliderPositionHandler->handle($request->request->get('positions'));

            $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));
        } catch (PositionDataException|PositionUpdateException $e) {
            $errors = [$e->toArray()];
            $this->flashErrors($errors);
        }

        return $this->redirectToRoute('is_imageslider_controller');
    }

    private function clearTemplateCache()
    {
        $this->templateCache->clearTemplateCache();
    }

    /**
     * @inerhitDoc
     */
    protected function presentGrid(GridInterface $grid)
    {
        return $this->gridPresenter->present($grid);
    }

    /**
     * @inerhitDoc
     */
    protected function trans($key, $domain, array $parameters = [])
    {
        return $this->translator->trans($key, $parameters, $domain);
    }

    /**
     * Provides translated error messages for exceptions
     *
     * @return array
     */
    private function getErrorMessages(): array
    {
        return [
            DateRangeNotValidException::class => [
                $this->trans(
                    'The selected date range is not valid. Date to must be greater than date from.',
                    TranslationDomains::TRANSLATION_DOMAIN_EXCEPTION
                ),
            ],
        ];
    }
}
