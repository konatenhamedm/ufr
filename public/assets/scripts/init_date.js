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