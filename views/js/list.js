$(document).ready(function () {
    window.prestashop.component.initComponents(
        [
            'MultistoreConfigField',
            'Grid',
        ],
    );

    const imageSliderkGrid = new window.prestashop.component.Grid('is_imageslider');

    imageSliderkGrid.addExtension(new prestashop.component.GridExtensions.AsyncToggleColumnExtension());
    imageSliderkGrid.addExtension(new window.prestashop.component.GridExtensions.SortingExtension());
    imageSliderkGrid.addExtension(new window.prestashop.component.GridExtensions.PositionExtension());
    imageSliderkGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitRowActionExtension());
});
