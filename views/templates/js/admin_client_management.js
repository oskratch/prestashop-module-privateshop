$(document).ready(function() {
    $('.js-approve-customer-row-action').on('click', function(e) {
        e.preventDefault();

        var customerId = $(this).data('customer-id');
        var approveUrl = $(this).data('customer-approve-url');

        showModalConfirm("¿Estás seguro de querer aprobar este cliente?", "success", function(isConfirmed) {
            if (isConfirmed) {
                $.ajax({
                    url: approveUrl,
                    method: 'POST',
                    success: function(response) {
                        const parsedResponse = JSON.parse(response);
                        //console.log(parsedResponse);
                        if (parsedResponse.success) {
                            $('tr[data-customer-id="' + customerId + '"]').remove();
                            showAlert('¡Cliente aprobado!');
                        } else {
                            showAlert('Error al aprobar el cliente.');
                        }
                    },
                    error: function() {
                        showAlert('Error en la comunicación con el servidor.');
                    }
                });
            }
        });
    });

    $('.js-delete-customer-row-action').on('click', function(e) {
        e.preventDefault();

        var customerId = $(this).data('customer-id');
        var deleteUrl = $(this).data('customer-delete-url');

        showModalConfirm("¿Estás seguro de querer eliminar este cliente?", "danger", function(isConfirmed) {
            if (isConfirmed) {
                $.ajax({
                    url: deleteUrl,
                    method: 'POST',
                    success: function(response) {
                        const parsedResponse = JSON.parse(response);
                        //console.log(parsedResponse);
                        if (parsedResponse.success) {
                            $('tr[data-customer-id="' + customerId + '"]').remove();
                            showAlert('¡Cliente eliminado correctamente!');
                        } else {
                            showAlert('Error al eliminar el cliente.');
                        }
                    },
                    error: function() {
                        showAlert('Error en la comunicación con el servidor.');
                    }
                });
            }
        });
    });

    function showModalConfirm(message, typeClass, callback) {

        $('#confirmButton').removeClass().addClass('btn').addClass("btn-" + typeClass);
        $("#confirmModalLabel").removeClass().addClass('modal-title').addClass("text-" + typeClass);
        $('#confirmMessage').text(message);
    
        $('#confirmModal').modal('show');
    
        $('#confirmButton').off('click').on('click', function() {
            callback(true);
            $('#confirmModal').modal('hide');
        });
    
        $('#confirmModal').on('hidden.bs.modal', function() {
            callback(false);
        });
    }

    function showAlert(message) {
        $('#alertMessage').text(message);
        $('#alertModal').modal('show');
    }

    // Shipping

    $('.shipping-restriction-toggle').on('change', function () {
        var customerId = $(this).data('customer-id');
        var value = $('input[name="shipping_restriction_' + customerId + '"]:checked').val();
        var url = $("#url-shipping").attr("data-value");

        /*console.log('Customer ID:', customerId);
        console.log('Shipping Restriction Value:', value);
        console.log('URL:', url);*/
    
        $.ajax({
            url: url,
            method: 'POST',
            data: {
                ajax: true,
                id_customer: customerId,
                shipping_restriction: value,
            },
            success: function(response) {
                console.log(response);
                try {
                    const parsedResponse = JSON.parse(response);
                    if (parsedResponse.success) {
                        showSuccessMessage('La opción de envío a domicilio ha sido actualizada correctamente.');
                    } else {
                        showAlert('Error al cambiar la opción de envío.');
                    }
                } catch (e) {
                    showErrorMessage('Respuesta del servidor no válida: ' + e.message);
                }
            },
            error: function (xhr, status, error) {
                showErrorMessage('Se ha producido un error en el servidor: ' + error);
            }
        });
    });
});
