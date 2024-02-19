
const handleFileInputChange = () => {
  const $fileInput = $('#image_slider [type="file"]');

  $fileInput.on('change', (e) => {
    const $input = $(e.currentTarget);
    const $relatedImage = $(`[data-related-field="${$input.attr('id')}"]`);
    const files = $input[0].files;

    if (FileReader && files && files.length) {
      const reader = new FileReader();

      reader.onload = function () {
        $relatedImage.attr('data-placeholder', $relatedImage.attr('src'));
        $relatedImage.attr('src', reader.result)
      }

      reader.readAsDataURL(files[0]);
    }
  })
}

const handleImageTypeSwitch = () => {
  const $imageSwitch = $('.js-toggle-images-types');

  $imageSwitch.on('change', (e) => {
    const $radio = $(e.currentTarget);
    const $form = $radio.closest('form');
    const data = {};
    const $token = $('#image_slider__token');
    const $edit = $('#image_slider_edit');

    data[$radio.attr('name')] = $radio.val();
    data[$token.attr('name')] = $token.val();
    data[$edit.attr('name')] = 1; // We don't want to trigger NotEmpty constraint on the image fields

    $.ajax({
      method: $form.attr('method'),
      data,
      success: (response) => {
        const $newImagesFields = $(response).find('.js-image-fields');

        $form.find('.js-image-fields').replaceWith($newImagesFields);
      }
    })
  })
}

$(() => {
    handleFileInputChange();
    handleImageTypeSwitch();

    window.prestashop.component.initComponents(
        [
            'TranslatableField',
            'TinyMCEEditor',
            'TranslatableInput',
        ],
    );

    const choiceTree = new window.prestashop.component.ChoiceTree('#image_slider_shop_association');
    choiceTree.enableAutoCheckChildren();
});

