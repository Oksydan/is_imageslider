<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Controller\Admin;

use Oksydan\IsImageslider\Adapter\CommandBusInterface;
use Oksydan\IsImageslider\Cache\TemplateCache;
use Oksydan\IsImageslider\Domain\ImageSlider\Command\CreateImageSliderCommand;
use Oksydan\IsImageslider\Domain\ImageSlider\Command\EditImageSliderCommand;
use Oksydan\IsImageslider\Entity\ImageSlider;
use Oksydan\IsImageslider\Exceptions\DateRangeNotValidException;
use Oksydan\IsImageslider\Filter\ImageSliderFileters;
use Oksydan\IsImageslider\Form\Type\ImageSliderType;
use Oksydan\IsImageslider\Handler\Slide\DeleteSlideHandler;
use Oksydan\IsImageslider\Handler\Slide\ToggleSlideActivityHandler;
use Oksydan\IsImageslider\Handler\Slide\UpdateSliderPositionHandler;
use Oksydan\IsImageslider\Repository\ImageSliderRepository;
use Oksydan\IsImageslider\Translations\TranslationDomains;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\GridInterface;
use PrestaShop\PrestaShop\Core\Grid\Position\Exception\PositionDataException;
use PrestaShop\PrestaShop\Core\Grid\Position\Exception\PositionUpdateException;
use PrestaShop\PrestaShop\Core\Grid\Presenter\GridPresenter;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route(path="config", name="admin_imageslider_controller_")
 */
class ImagesliderController extends FrameworkBundleAdminController
{
    private TemplateCache $templateCache;

    private DeleteSlideHandler $deleteSlideHandler;

    private GridFactoryInterface $imagsliderGridFactory;

    private GridPresenter $gridPresenter;

    private TranslatorInterface $translator;

    private ImageSliderRepository $imageSliderRepository;

    private ToggleSlideActivityHandler $toggleSlideActivityHandler;

    private UpdateSliderPositionHandler $updateSliderPositionHandler;

    public function __construct(
        TemplateCache $templateCache,
        DeleteSlideHandler $deleteSlideHandler,
        GridFactoryInterface $imagsliderGridFactory,
        GridPresenter $gridPresenter,
        TranslatorInterface $translator,
        ImageSliderRepository $imageSliderRepository,
        ToggleSlideActivityHandler $toggleSlideActivityHandler,
        UpdateSliderPositionHandler $updateSliderPositionHandler
    ) {
        parent::__construct();
        $this->templateCache = $templateCache;
        $this->deleteSlideHandler = $deleteSlideHandler;
        $this->imagsliderGridFactory = $imagsliderGridFactory;
        $this->gridPresenter = $gridPresenter;
        $this->translator = $translator;
        $this->imageSliderRepository = $imageSliderRepository;
        $this->toggleSlideActivityHandler = $toggleSlideActivityHandler;
        $this->updateSliderPositionHandler = $updateSliderPositionHandler;
    }

    /**
     * @Route(path="/index", name="index", methods={"GET", "POST"})
     */
    public function index(ImageSliderFileters $filters): Response
    {
        $imageSliderGrid = $this->imagsliderGridFactory->getGrid($filters);

        return $this->render('@Modules/is_imageslider/views/templates/admin/index.html.twig', [
            'translationDomain' => TranslationDomains::TRANSLATION_DOMAIN_ADMIN,
            'imageSliderkGrid' => $this->presentGrid($imageSliderGrid),
            'help_link' => false,
        ]);
    }

    private function flattenUploadedImagesArray(array $files): array
    {
        if (!empty($files['image_slider']['slider_langs'])) {
            return $files['image_slider']['slider_langs'];
        }

        return [];
    }

    /**
     * @Route(path="/create", name="create", methods={"GET", "POST"})
     */
    public function create(Request $request, CommandBusInterface $commandBus): Response
    {
        $form = $this->createForm(ImageSliderType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $commandBus->handle(new CreateImageSliderCommand(
                    $form->getData(),
                    $this->flattenUploadedImagesArray($request->files->all())
                ));

                $this->addFlash(
                    'success',
                    $this->trans('Successful creation.', 'Admin.Notifications.Success')
                );

                $this->clearTemplateCache();

                return $this->redirectToRoute('admin_imageslider_controller_index');
            } catch (\Exception $e) {
                $this->addFlash('error', $this->getErrorMessagesForExceptions($e, $this->getErrorMessages()));
            }
        }

        return $this->render('@Modules/is_imageslider/views/templates/admin/form.html.twig', [
            'imageSliderForm' => $form->createView(),
            'title' => $this->trans('Image slider', TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
            'help_link' => false,
        ]);
    }

    /**
     * @Route(path="/edit/{slideId}", name="edit", methods={"GET", "POST"})
     *
     * @ParamConverter("imageSlider", class="Oksydan\IsImageslider\Entity\ImageSlider", options={"id" = "slideId"})
     */
    public function edit(Request $request, ImageSlider $imageSlider, CommandBusInterface $commandBus): Response
    {
        $form = $this->createForm(ImageSliderType::class, $imageSlider);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $commandBus->handle(new EditImageSliderCommand(
                    $form->getData(),
                    $this->flattenUploadedImagesArray($request->files->all())
                ));

                $this->addFlash(
                    'success',
                    $this->trans('Successful edition.', 'Admin.Notifications.Success')
                );

                $this->clearTemplateCache();

                return $this->redirectToRoute('admin_imageslider_controller_index');
            } catch (\Exception $e) {
                $this->addFlash('error', $this->getErrorMessagesForExceptions($e, $this->getErrorMessages()));
            }
        }

        return $this->render('@Modules/is_imageslider/views/templates/admin/form.html.twig', [
            'imageSliderForm' => $form->createView(),
            'title' => $this->trans('Image slider edition', TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
            'help_link' => false,
        ]);
    }

    /**
     * @Route(path="/delete/{$slideId}", name="delete", methods={"GET", "POST"})
     *
     * @Entity("imageSlider", expr="repository.find(slideId)")
     */
    public function delete(Request $request, ImageSlider $imageSlider): Response
    {
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
     * @Route(path="/toggleStatus/{$slideId}", name="toggle_status", methods={"GET", "POST"})
     *
     * @Entity("imageSlider", expr="repository.find(slideId)")
     */
    public function toggleStatus(Request $request, ImageSlider $imageSlider): Response
    {
        try {
            $this->toggleSlideActivityHandler->handle($imageSlider);

            $response = [
                'status' => true,
                'message' => $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success'),
            ];
        } catch (\Exception $e) {
            $response = [
                'status' => false,
                'message' => sprintf(
                    'There was an error while updating the status of slide %d: %s',
                    $imageSlider->getId(),
                    $e->getMessage()
                ),
            ];
        }

        return $this->json($response);
    }

    /**
     * @Route(path="/updatePosition/", name="update_position", methods={"POST"})
     */
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

    protected function presentGrid(GridInterface $grid)
    {
        return $this->gridPresenter->present($grid);
    }

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

    protected function getErrorMessagesForExceptions(\Exception $e, array $messages)
    {
        $exceptionType = get_class($e);
        $exceptionCode = $e->getCode();

        if (isset($messages[$exceptionType])) {
            $message = $messages[$exceptionType];

            if (is_string($message)) {
                return $message;
            }

            if (is_array($message) && isset($message[$exceptionCode])) {
                return $message[$exceptionCode];
            }
        }

        return $this->trans(
            'An unexpected error occurred. [%type% code %code%]',
            'Admin.Notifications.Error',
            [
                '%type%' => $exceptionType,
                '%code%' => $exceptionCode,
            ]
        );
    }
}
