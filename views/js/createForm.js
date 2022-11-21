$(document).ready(function () {
    handleFileInputChange();

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


function handleFileInputChange() {
    const $fileInput = $('#image_slider [type="file"]');

    $fileInput.on('change', (e) => {
        const $input = $(e.currentTarget);
        const $relatedImage = $(`[data-related-field="${$input.attr('id')}"]`);
        const files = $input[0].files;

        if (FileReader && files && files.length) {
            const reader = new FileReader();

            reader.onload = function () {
                $relatedImage.attr('src', reader.result)
            }

            reader.readAsDataURL(files[0]);
        }
    })
}
