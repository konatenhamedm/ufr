$(function ()
{
    var $container = $('.column-container');
    // const proto_class = $this.attr('data-protoclass');
    var proto_class = $('.ordre-jour-prototype').length;
    var index = $container.children('.row-colonne').length;

    const $addLink = $('.add-ligne');

    $addLink.click(function (e) {
        const $this = $(this);
        const proto_class = $this.attr('data-protoclass');
        const name = $this.attr('data-protoname');
        const $container = $($this.attr('data-container'));
       
        addLine($container, name, proto_class);
        
        e.preventDefault(); // évite qu'un # apparaisse dans l'URL
    });

    if (index > 0) {
        $container.children('.row-colonne').each(function (e) {
            const $this = $(this);
            addDeleteLink($this);
            $this.find("select").each(function () {
                const $this = $(this);
                init_select2($prototype.find('select'), 'up', '#exampleModalSizeLg2');
            });
        });
    }


    // La fonction qui ajoute un formulaire Categorie
    function addLine($container, name, proto_class) {

        console.log(proto_class, name);
        // Dans le contenu de l'attribut « data-prototype », on remplace :
        // - le texte "__name__label__" qu'il contient par le label du champ
        // - le texte "__name__" qu'il contient par le numéro du champ
        var $prototype = $($(proto_class).attr('data-prototype')
            .replace(new RegExp(name + 'label__', 'g'), 'Colonne ' + (index + 1))
            .replace(new RegExp(name, 'g'), index));

        addDeleteLink($prototype, name);
        $prototype.find('.num_ordre_jour').val($container.children().length);

        init_select2($prototype.find('select'), null, '#exampleModalSizeLg2');

        // On ajoute au prototype un lien pour pouvoir supprimer la prestation
        // On ajoute le prototype modifié à la fin de la balise <div>
        $container.append($prototype);

        index++;
    }

    function addDeleteLink($prototype, name = null) {
        // Création du lien
       var $deleteLink = $('<a href="#" class="btn btn-danger btn-xs"><span class="fa fa-trash"></span></a>');
        // Ajout du lien
        $prototype.find(".del-col").append($deleteLink);
        // Ajout du listener sur le clic du lien
        $deleteLink.click(function (e) {
            const $this = $(this);
            const $parent = $this.closest($this.parent('div').attr('data-parent'));
            $parent.remove();

            if (index > 0) {
                index -= 1;
            }

            $('.num_ordre_jour').each(function (index, val) {
                $(this).val(index + 1);
            });
            $('.btn-ajax').prop('disabled', $('.row-colonne').length == 0);
            e.preventDefault(); // évite qu'un # apparaisse dans l'URL
        });
    }
});