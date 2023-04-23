<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Controller;

use Oksydan\IsImageslider\Cache\TemplateCache;
use Oksydan\IsImageslider\Entity\ImageSlider;
use Oksydan\IsImageslider\Exceptions\DateRangeNotValidException;
use Oksydan\IsImageslider\Filter\ImageSliderFileters;
use Oksydan\IsImageslider\Handler\FileEraser;
use Oksydan\IsImageslider\Translations\TranslationDomains;
use PrestaShop\PrestaShop\Core\Grid\Position\Exception\PositionDataException;
use PrestaShop\PrestaShop\Core\Grid\Position\Exception\PositionUpdateException;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Entity\Shop;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class IsImagesliderController extends FrameworkBundleAdminController
{
    /**
     * @var FileEraser
     */
    private $fileEraser;

    /**
     * @var array
     */
    private $languages;

    /**
     * @var TemplateCache
     */
    private $templateCache;

    public function __construct(FileEraser $fileEraser, $languages, TemplateCache $templateCache)
    {
        $this->fileEraser = $fileEraser;
        $this->languages = $languages;
        $this->templateCache = $templateCache;
    }

    public function index(ImageSliderFileters $filters): Response
    {
        $imageSliderGridFactory = $this->get('oksydan.is_imageslider.grid.image_slider_grid_factory');
        $imageSliderGrid = $imageSliderGridFactory->getGrid($filters);

        $configurationForm = $this->get('oksydan.is_imageslider.image_slider_configuration.form_handler')->getForm();

        return $this->render('@Modules/is_imageslider/views/templates/admin/index.html.twig', [
            'translationDomain' => TranslationDomains::TRANSLATION_DOMAIN_ADMIN,
            'imageSliderkGrid' => $this->presentGrid($imageSliderGrid),
            'configurationForm' => $configurationForm->createView(),
            'help_link' => false,
        ]);
    }

    public function create(Request $request): Response
    {
        $formDataHandler = $this->get('oksydan.is_imageslider.form.identifiable_object.builder.image_slider_form_builder');
        $form = $formDataHandler->getForm();
        $form->handleRequest($request);

        $formHandler = $this->get('oksydan.is_imageslider.form.identifiable_object.handler.image_slider_form_handler');

        try {
            $result = $formHandler->handle($form);

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
        $formBuilder = $this->get('oksydan.is_imageslider.form.identifiable_object.builder.image_slider_form_builder');
        $form = $formBuilder->getFormFor((int) $slideId);
        $form->handleRequest($request);

        $formHandler = $this->get('oksydan.is_imageslider.form.identifiable_object.handler.image_slider_form_handler');

        try {
            $result = $formHandler->handleFor($slideId, $form);

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
        $imageSlide = $this->getDoctrine()
            ->getRepository(ImageSlider::class)
            ->find($slideId);

        if (!empty($imageSlide)) {
            $multistoreContext = $this->get('prestashop.adapter.shop.context');
            $entityManager = $this->get('doctrine.orm.entity_manager');

            if ($multistoreContext->isAllShopContext()) {
                $imageSlide->clearShops();

                foreach ($this->languages as $language) {
                    $langId = (int) $language['id_lang'];
                    $imageSliderLang = $imageSlide->getImageSliderLangByLangId($langId);

                    if ($imageSliderLang->getImage()) {
                        $this->eraseFile($imageSliderLang->getImage());
                    }

                    if ($imageSliderLang->getImageMobile()) {
                        $this->eraseFile($imageSliderLang->getImageMobile());
                    }
                }

                $entityManager->remove($imageSlide);
            } else {
                $shopList = $this->getDoctrine()
                    ->getRepository(Shop::class)
                    ->findBy(['id' => $multistoreContext->getContextListShopID()]);

                foreach ($shopList as $shop) {
                    $imageSlide->removeShop($shop);
                    $entityManager->flush();
                }

                if (count($imageSlide->getShops()) === 0) {
                    $entityManager->remove($imageSlide);
                }
            }

            $this->clearTemplateCache();
            $entityManager->flush();
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

        $form = $this->get('oksydan.is_imageslider.image_slider_configuration.form_handler')->getForm();
        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            return $redirectResponse;
        }

        if ($form->isValid()) {
            $data = $form->getData();
            $saveErrors = $this->get('oksydan.is_imageslider.image_slider_configuration.form_handler')->save($data);

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
        $entityManager = $this->get('doctrine.orm.entity_manager');
        $imageSlide = $entityManager
            ->getRepository(ImageSlider::class)
            ->findOneBy(['id' => $slideId]);

        if (empty($imageSlide)) {
            return $this->json([
                'status' => false,
                'message' => sprintf('Image slide %d doesn\'t exist', $slideId),
            ]);
        }

        try {
            $imageSlide->setActive(!$imageSlide->getActive());
            $entityManager->flush();
            $this->clearTemplateCache();

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
            $positionsData = [
                'positions' => $request->request->get('positions'),
            ];

            $positionDefinition = $this->get('oksydan.is_imageslider.grid.position_definition');

            $positionUpdateFactory = $this->get('prestashop.core.grid.position.position_update_factory');
            $positionUpdate = $positionUpdateFactory->buildPositionUpdate($positionsData, $positionDefinition);

            $updater = $this->get('prestashop.core.grid.position.doctrine_grid_position_updater');

            $updater->update($positionUpdate);
            $this->clearTemplateCache();

            $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));
        } catch (PositionDataException|PositionUpdateException $e) {
            $errors = [$e->toArray()];
            $this->flashErrors($errors);
        }

        return $this->redirectToRoute('is_imageslider_controller');
    }

    private function eraseFile(string $fileName): bool
    {
        return $this->fileEraser->remove($fileName);
    }

    private function clearTemplateCache()
    {
        $this->templateCache->clearTemplateCache();
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
