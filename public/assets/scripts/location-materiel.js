$(function () {
    const containerEngins = $('.engins-container');
    const containerArticle = $('.article-container');
    const containerDocumentEngins = $('.column-engins-document');
    var indexEngins = containerEngins.children('.row-colonne').length;
    var indexArticle = containerArticle.children('.row-colonne').length;
    var indexDocumentEngins = containerDocumentEngins.children('.row-colonne').length;
    
    const addLink = $('.add-ligne');
    const addLinkDocument = $('.add-ligne-document');
    const addEngins = $('.add-engins');

    if (indexArticle > 0)
    {
        existingCollection(containerArticle);
    }

    if (indexDocumentEngins > 0)
    {
        existingCollection(containerDocumentEngins);
    }

    if (indexEngins > 0)
    {
        containerEngins.children('.row-colonne').each(function ()
        {
            const $this = $(this);
            addDeleteLink($this);
            $this.find("select").each(function () {
                const $this = $(this);
                init_select2($this, null, '#exampleModalSizeLg2');
            });
        });
    }

    addContainer(indexDocumentEngins, addLinkDocument);

    addLink.click(function (e) {
        const $this = $(this);
        const proto_class = $this.attr('data-protoclass');
        const name = $this.attr('data-protoname');
        const containerArticle = $($this.attr('data-container'));
        addLine(containerArticle, name, proto_class, indexArticle);
        indexArticle++; // Mettre à jour l'index des articles
        e.preventDefault(); // évite qu'un # apparaisse dans l'URL
    });

    addEngins.click(function (e)
    {
        const $this = $(this);
        const proto_class = $this.attr('data-protoclass');
        const name = $this.attr('data-protoname');
        const containerEngins = $($this.attr('data-container'));
        addLine(containerEngins, name, proto_class, indexEngins);
        indexEngins++; // Mettre à jour l'index des engins
        e.preventDefault(); // évite qu'un # apparaisse dans l'URL
    });

    function existingCollection(container)
    {
        container.children('.row-colonne').each(function ()
        {
            const $this = $(this);
            const proto_class = $this.attr('data-protoclass');
            const name = $this.attr('data-protoname');

            // addDownloadLink($this);
            $this.find("select").each(function () {
                const $this = $(this);
                init_select2($this, null, '#exampleModalSizeLg2');
            });
        });
    }

    function addContainer(index, addButton)
    {  
        addButton.click(function (e) {
            const $this = $(this);
            const proto_class = $this.attr('data-protoclass');
            const name = $this.attr('data-protoname');
            const container = $($this.attr('data-container'));
            addLine(container, name, proto_class, index);
            index++; // Mettre à jour l'index des articles
            e.preventDefault(); // évite qu'un # apparaisse dans l'URL
        });
    }

    // La fonction qui ajoute un formulaire Categorie
    function addLine(container, name, proto_class, index)
    {
        // Dans le contenu de l'attribut « data-prototype », on remplace :
        // - le texte "__name__label__" qu'il contient par le label du champ
        // - le texte "__name__" qu'il contient par le numéro du champ
        var $prototype = $($(proto_class).attr('data-prototype')
            .replace(new RegExp(name + 'label__', 'g'), 'Colonne ' + (index + 1))
            .replace(new RegExp(name, 'g'), index));

        init_select2($prototype.find('select'), null, '.modal-location');

        // On ajoute au prototype un lien pour pouvoir supprimer la prestation
        addDeleteLink($prototype, name);
        // On ajoute le prototype modifié à la fin de la balise <div>
        container.append($prototype);

        $('.no-auto').each(function () {
            var $this = $(this);
            var $id = $('#' + $this.attr('id'));
            init_date_picker($this, 'down', (start, e) => {
                $this.val(start.format('DD/MM/YYYY'));
            }, null, null, false);

            $this.on('apply.daterangepicker', function (ev, picker) {
                $(this).val(picker.startDate.format('DD/MM/YYYY'));
            });
        });

        let next_index = index + 1;
        index++;
    }

    function addDeleteLink($prototype, name = null) {
        // Création du lien
        var deleteLink = $('<a href="#" class="btn btn-danger btn-xs"><span class="fa fa-trash"></span></a>');
        // Ajout du lien
        $prototype.find(".del-col").append(deleteLink);
        // Ajout du listener sur le clic du lien
        deleteLink.click(function (e) {
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

    function addDownloadLink(prototype, name = null)
    {
        // Création du lien
        // fichier_index
        var deleteLink = $(`<a class="btn btn-dark btn-xs" target="_blank" title="Télécharger le document" href="" download=""> <i class="fe fe-upload"></i>
            <i class= "fa fa-download"></i></a>`);
        // Ajout du lien
        prototype.find(".del-col").append(deleteLink);
        // Ajout du listener sur le clic du lien
        deleteLink.click(function (e) {
            const $this = $(this);
            const parent = $this.closest($this.parent('div').attr('data-parent'));
            
            if (index > 0) {
                index -= 1;
            }
            e.preventDefault(); // évite qu'un # apparaisse dans l'URL
        });

    }
});