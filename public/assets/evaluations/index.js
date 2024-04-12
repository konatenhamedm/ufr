function refresh() {
    let index = 0
    $('.row-colonne-row').each(function () {
        index++;
        $(this).attr('data-numberKey', index)
        $(this).find('.numero:first').val(index);
    })
    }
    function refreshColone() {
    let index = 0
    $('.row-type').each(function () {
        index++;
        $(this).attr('data-numberKey', index)
        $(this).find('.numero:first').val(index);
    })
    }


     $(function () {
       // alert('ok');
        refresh();
        refreshColone();
        //init_select2('select');
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
        var nombre_note = 1;
     
        var nombre_note_groupe = 10;

        ///alert(nombre_note_groupe);
        //let grid_val = `${etat}`;

        const $addLink = $('.add-ligne');

    
        $addLink.click(function (e) {
       
            addLine($container);
            e.preventDefault(); // évite qu'un # apparaisse dans l'URL
            refreshColone();
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
                    init_select2($this);
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
           // $container.append($prototype);
         //alert(__name__)

        

            nombre_note++;
            nombre_note_groupe++;

            
            $('#tutorial').find('.ligne').each(function(){ 
                 // const $this = $(this);
                    
                var index_note = $(this).find('.numero').val()-1;
                var index_note_value = $(this).find('.numero').val();

               // $(this).find('.source').eq(-3).after('<td width="10%" class="p-2">ffff</td>'); 
                $(this).find('th').eq(-3).after('<th width="10%" class="p-2">Note '+nombre_note+'</th>'); 
                $(this).find('td').eq(-3).after('<td class="p-2 row-colonne"><input class="form-control form-control-sm " type="text" id="controle_notes_'+index_note+'_valeurNotes_'+nombre_note_groupe+'_note" name="controle[notes]['+index_note+'][valeurNotes]['+nombre_note_groupe+'][note]" required="required" maxlength="255" value="0"></td>');
            });
            $('#tutorial').find('.ligne_head').each(function(){ 
               
                var index_note = $(this).find('.numero').val()-1;
                var index_note_value = $(this).find('.numero').val();

               // $(this).find('.source').eq(-3).after('<td width="10%" class="p-2">ffff</td>'); 
                //$(this).find('th').eq(-3).after('<th width="10%" class="p-2">Note '+nombre_note+'</th>'); 

                var resIntervention;
                $.ajax({
                    url:  '/admin/controle/type/controle/liste/type/controle',
                    type:  'get',
                    async: false,
                    dataType:   'json',
                    success: function(json){

                     // $('#'+ $('.matiere').attr("id")).html(''); //je vide la 2ème list
                      //$('#'+ $('.matiere').attr("id")).append('<option value selected="default" >Choisissez</option>');

                        $.each(json, function(index, value) { // et une boucle sur la réponse contenu dans la variable passé à la function du success "json"       
                              
                          //  $("#"+ $('.matiere').attr("id")).append('<option value="'+ value.id +'"  >' + value.libelle +'</option>');
                         // <option value="2" data-select2-id="select2-data-16283-qrxgsx">TP</option>
                            resIntervention += ` 
                           
                            <option value="${value.id}"  >${value.libelle}</option>
                        `;
                      

                          });
                    }
                });


                $(this).find('th').eq(-3).after(`<th class="p-2  row-colonne" style="background-color:white !important" data-select2-id="select2-data-${nombre_note_groupe}-ebse"> 
                       <table width="100%">
                        <tbody>
                        <tr>
                            <td>
                        <div class="input-group">
                            <input class="form-control form-control-sm  datepicker no-auto" type="text" id="controle_groupeTypes_${nombre_note_groupe}_dateNote" name="controle[groupeTypes][${nombre_note_groupe}][dateNote]" required="required" autocomplete="off">
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <span class="far fa-calendar"></span>
                                </span>
                            </div>
                        </div>
                            </td>
                        </tr>
                         <tr>
                            <td>
                           <select  class="form-control form-control-sm has-select2 select2-hidden-accessible" id="controle_groupeTypes_${nombre_note_groupe}_type" name="controle[groupeTypes][${nombre_note_groupe}][type]" required="required" tabindex="-1" aria-hidden="true" >
                             ${
                                resIntervention
                             }
                          
                       
                           </select>
                            </td>
                        </tr>
                        <tr>
                            <td >
                               <select class="form-control form-control-sm has-select2 select2-hidden-accessible" id="controle_groupeTypes_${nombre_note_groupe}_coef" name="controle[groupeTypes][${nombre_note_groupe}][coef]" required="required" tabindex="-1" aria-hidden="true" >
                               <option value="20" data-select2-id="select2-data-20008-yq4g55">20</option>
                               <option value="10" selected="selected" data-select2-id="select2-data-20009-wthh00">10</option>
                               </select>
                             </td>
                        </tr>
                        
                    </tbody>
                </table>
                
                 </th>`);
                

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
            });

           init_select2('select');

            $prototype.find("select").each(function () {
            const $this = $(this);
                init_select2($this);
                 
            });

  
            //$prototype.find('.field-matiere').css('display', 'block');

        
            index++;

        }

        function addDeleteLink($prototype) {
            // Création du lien
            let $deleteLink = $('<a href="#" class="btn btn-danger btn-xs"><span class="bi bi-trash"></span></a>');
            // Ajout du lien
            if ($prototype.find('.del-col').find('.btn-danger').length == 0) {
                $prototype.find(".del-col").append($deleteLink);
            } else {
                $deleteLink = $prototype.find('.del-col').find('.btn-danger');
            }
            
            // Ajout du listener sur le clic du lien
            $deleteLink.click(function (e) {
                const $parent = $(this).closest('.row-colonne');
                $parent.remove();

                if (index > 0) {
                    index -= 1;
                }

                e.preventDefault(); // évite qu'un # apparaisse dans l'URL
            });
        }

        $(".del-col").on("click", function(){
           // alert('ok');
            $("table tbody tr").find("td:eq(-3)").remove();
            $("table thead tr").find("th:eq(-3)").remove();
          });

    });