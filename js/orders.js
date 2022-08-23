const form = $('#send-product-form');

$(document).ready( function () {
  var table = $('#orders-table').DataTable(
    {
      order: [[6, 'desc']],
      rowReorder: {
        selector: 'td:nth-child(3)'
      },
      responsive: true
    }
  );
} );


