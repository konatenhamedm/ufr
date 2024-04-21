function refresh() {
    let index = 0
    $('.row-colonne').each(function () {
        index++;
        $(this).attr('data-numberKey', index)
        $(this).find('.numero:first').val(index);
    })
    }
$(function () {
    refresh();
    init_select2('select');
     $('.no-auto').each(function () {
        const $this = $(this);
        const $id = $('#' + $this.attr('id'));
        init_date_picker($id,  'down', (start, e) => {
            //$this.val(start.format('DD/MM/YYYY'));
        }, null, null, false);

        $id.on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('DD/MM/YYYY'));
        });
    });



   
    const $container = $('.row-container');

    var index = $container.find('.row-colonne').length;

    const $addLink = $('.add-ligne');


    $addLink.click(function (e) {
        const $this  = $(this);
        const $container = $($this.attr('data-container')).closest('.row-container');
        addLine($container);
        refresh();

        e.preventDefault(); // évite qu'un # apparaisse dans l'URL
    });
// On ajoute un premier champ automatiquement s'il n'en existe pas déjà un (cas d'une nouvelle annonce par exemple).
/*if (index == 0) {
        addimputation($container);
        } else {*/
    if (index > 0) {
        $container.children('.row-colonne').each(function () {
            const $this = $(this);
            addDeleteLink($this);
            $this.find("select").each(function () {
                const $this = $(this);
                init_select2($this, null, '#modal-lg');
            });
        });
    }

// La fonction qui ajoute un formulaire Categorie
    function addLine($container) {
    // Dans le contenu de l'attribut « data-prototype », on remplace :
    // - le texte "__name__label__" qu'il contient par le label du champ
    // - le texte "__name__" qu'il contient par le numéro du champ
        var $prototype = $($(".proto-container").attr('data-prototype').replace(/__name__label__/g, 'Ligne ' + (
        index + 1)).replace(/__name__/g, index));
        // On ajoute au prototype un lien pour pouvoir supprimer la prestation
        addDeleteLink($prototype);
        // On ajoute le prototype modifié à la fin de la balise <div>
        $container.append($prototype);

        $prototype.find("select").each(function () {
        const $this = $(this);
            init_select2($this, null, '#modal-lg');
        });

    
        index++;

    }

    function addDeleteLink($prototype) {
       
        // Création du lien
        $deleteLink = $('<a href="#" class="btn btn-danger btn-xs"><span class="bi bi-trash"></span></a>');
        // Ajout du lien
        $prototype.find(".del-col").append($deleteLink);
        // Ajout du listener sur le clic du lien
        $deleteLink.click(function (e) {
             const $this = $(this);
           //  alert('')
            const $parent =  $this.closest('.row-colonne');
            $parent.remove();
 refresh();
            if (index > 0) {
                index -= 1;
            }

            e.preventDefault(); // évite qu'un # apparaisse dans l'URL
        });
    }

});