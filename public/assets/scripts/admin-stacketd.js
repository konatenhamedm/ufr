$(document).ready(function () {
    var elements = Array.prototype.slice.call(document.querySelectorAll("[data-bs-stacked-modal]"));
    if (elements && elements.length > 0) {
        elements.forEach((element) => {
            if (element.getAttribute("data-kt-initialized") === "1") {
                return;
            }
            element.setAttribute("data-kt-initialized", "1");
        });

        // Écouteur d'événement click pour les modales
        $(document).off('click', "[data-bs-stacked-modal]").on('click', "[data-bs-stacked-modal]", function (e)
        {
            e.preventDefault();
            const $this = $(this);
            const options = $this.data('options');
            var url = $this.attr('href');

            const modalEl = document.querySelector(this.getAttribute("data-bs-stacked-modal"));

            if (modalEl) {
                console.log("Debut du stacked");
                var content = $(modalEl).find('.modal-content');
                const modal = new bootstrap.Modal(modalEl);
                var existingModal = $('.modal.show');

                if (url && $this.attr('href')[ 0 ] != '#' && content !== null) {
                    content.load(url, function () {
                        $('.datepicker').each(function () {
                            var $this = $(this);
                            var $id = $('#' + $this.attr('id'));
                            // $this.flatpickr();
                            init_date_picker($this);
                        });
                        modal.show();

                    });
                }

                if ($this.attr('data-href')) {
                    $this.find('.modal-content').load($this.attr('data-href'));
                }

                // Écouteur d'événement pour la fermeture des modales
                $(modalEl).off('hidden.bs.modal').on('hidden.bs.modal', function ()
                {
                    // Vérifier si la dernière modal est fermée
                    if (!$('.modal.show').length) {
                        // Masquer l'arrière-plan
                        $('.modal-backdrop').removeClass('show');
                    }
                });
            }

            // Écouteur d'événement pour la fermeture de la première modal
            existingModal.off('hidden.bs.modal').on('hidden.bs.modal', function ()
            {
                // Vérifier si la dernière modal est fermée
                if (!$('.modal.show').length) {
                    // Masquer l'arrière-plan
                    $('.modal-backdrop').removeClass('show');
                    $('.modal-backdrop').remove();
                }
            });
        });
    }
});
