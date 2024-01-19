$(function () {
    const $container = $('.proto-container');

    var index = $container.find('.row-colonne_universitaire').length;


    $(document).on('select2:select', '.select-type', function (e) {
        const $this = $(this);
        let field_str = $this.find('option:selected').attr('data-require-fields');
        const $parent = $this.closest('.row-colonne_universitaire');
        let fields = [];
        if (typeof field_str != 'undefined') {
            fields = field_str.split(',');
            for (let field of fields ) {
                $parent.find('.' + field).removeClass('d-none');
            }
        } else {
            $parent.find('.source,.valeurs').addClass('d-none');
        }
    });


    const $addLink = $('.add_line_universitaire');
    $addLink.click(function(e) {
        const $this  = $(this);
        const proto_class = $this.attr('data-protoclass');
        const name = $this.attr('data-protoname');
        const $container = $($this.attr('data-container'));



        addLine($container, name, proto_class);



        e.preventDefault(); // évite qu'un # apparaisse dans l'URL
    });

    if (index > 0) {
        $container.children('.row-colonne_universitaire').each(function() {
            const $this = $(this);
            addDeleteLink($this);
            const $select = $this.find("select");



            $select.each(function() {
                const $this = $(this);
                init_select2($this, null, '.document');
                if ($this.hasClass('select-type')) {
                    let field_str = $this.find('option:selected').attr('data-require-fields');
                    const $parent = $this.closest('.row-colonne_universitaire');
                    let fields = [];
                    if (typeof field_str != 'undefined') {
                        fields = field_str.split(',');
                        for (let field of fields ) {
                            $parent.find('.' + field).removeClass('d-none');
                        }
                    } else {
                        $parent.find('.source,.valeurs').addClass('d-none');
                    }
                }
            });

        });

    }




    // La fonction qui ajoute un formulaire Categorie
    function addLine($container, name, proto_class) {
        // Dans le contenu de l'attribut « data-prototype », on remplace :
        // - le texte "__name__label__" qu'il contient par le label du champ
        // - le texte "__name__" qu'il contient par le numéro du champ

        var $prototype = $($(proto_class).attr('data-prototype')
            .replace(new RegExp(name + 'label__', 'g'), 'Colonne ' + (index+1))
            .replace(new RegExp(name, 'g'), index));


        init_select2($prototype.find('select'), null );


        // On ajoute au prototype un lien pour pouvoir supprimer la prestation
        addDeleteLink($prototype, name);
        // On ajoute le prototype modifié à la fin de la balise <div>
        $container.prepend($prototype);

        index++;
    }


    function addDeleteLink($prototype, name = null) {
        // Création du lien
        $deleteLink = $('<a href="#" class="btn btn-danger btn-xs" style="margin-top: 21px"><span class="fa fa-trash"></span></a>');
        // Ajout du lien
        $prototype.find(".del-col_universitaire").append($deleteLink);



        // Ajout du listener sur le clic du lien
        $deleteLink.click(function(e) {
            const $this = $(this);
            const $parent = $this.closest($this.parent('div').attr('data-parent'));

            //console.log($(this).attr('data-parent'), $(this));
            $parent.remove();

            if (index > 0) {
                index -= 1;
            }

            e.preventDefault(); // évite qu'un # apparaisse dans l'URL
        });
    }



})