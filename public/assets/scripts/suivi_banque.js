$(function () {
    const map = JSON.parse($('.table-banque').attr('data-map'));
    const banques = JSON.parse($('.table-banque').attr('data-banques'));

      //selection banque
      // --
   
      $(document).on('change', 'select.select-banque', function (e) {
         let all_selected_banques = [];
     
        
        const $this = $(this);
        const $row = $this.closest('tr');
        const montant = +$row.attr('data-montant');
        const val = $this.val();
        if (val) {
           if (typeof all_selected_banques[val] == 'undefined') {
            all_selected_banques[val] = [montant];
            //console.log('XXXX');
           }
        }
        //console.log('XXXX00');
        $('select.select-banque').not($this).each(function () {
          const $_this = $(this);
          const $row = $_this.closest('tr');
          const montant = +$row.attr('data-montant');
          const val = $_this.val();
          //console.log( all_selected_banques);
        
           if (val) {
            if (typeof all_selected_banques[val] == 'undefined') {
              all_selected_banques[val] = [montant];
            } else {
              all_selected_banques[val].push(montant);
            }
          }
        });
        

        let selected_banques = Object.keys(all_selected_banques).map((x) => +x);

        for (let id_banque of selected_banques) {
          const $row_banque = $(`[data-banque="${id_banque}"]`);
          
          const $mvt = $row_banque.find('.col-mvt');
          const $solde = $row_banque.find('.col-solde');
          const solde = +$mvt.attr('data-solde');
          const total = array_sum(all_selected_banques[id_banque]);
        
          $mvt.text(setValue(total));
          $solde.text(setValue(solde - total));
          
        }

        let results = [];
        
        for (let id_banque of banques) {
          if (!selected_banques.includes(id_banque)) {
            results.push(id_banque);
          }
        }


         for (let id_banque of results) {
          const $row_banque = $(`[data-banque="${id_banque}"]`);
          
          const $mvt = $row_banque.find('.col-mvt');
          const $solde = $row_banque.find('.col-solde');
          const solde = +$mvt.attr('data-solde');
          $mvt.text(0);
          $solde.text(setValue(solde));
        }
       
      });

      function reinit_banque(exclue) {

      }

      $('select.select-banque').each(function () {
        const $this = $(this);
       
        if ($this.closest('tr').find('.select-ligne').is(':checked')) {
          $this.select2();
          $this.trigger('change');
        } else {
          $this.select2('destroy').attr('readonly', 'readonly');
        }
      });


      $('.select-ligne').on('click', function () {
        const $this = $(this);
        const $tr = $this.closest('tr');
        const $banque = $tr.find('select.select-banque');
        if ($this.is(':checked')) {
          $banque.select2().removeAttr('readonly');
        } else {
          $banque.select2('destroy').attr('readonly', 'readonly');
          $banque.val('').trigger('change');
        }
      });
})