$('.delete-car').click(function () {
  $('.form-delete').attr('action', '/car/delete/'+$(this).data('id'))
})

$('#modalDelete').on('hidden.bs.modal', function () {
  $('.form-delete').attr('action', '')
})
