
$(document).ready(function() {
    // Récupérer l'URL de la route depuis la balise meta
    const storeConnexionRoute = $('meta[name="route-store-connexion"]').attr('content');
    const loader = document.querySelector('#loader');

    function envoyerFormulaire(formId) {
        // Récupère le token CSRF à l'exécution (assure qu'il est bien chargé)
        const csrfToken = $('meta[name="csrf-token"]').attr('content');

        // Réinitialise tous les messages d'erreur et le message global
        $('.error-message').text('');
        const $globalMessage = $('#global-message');
        $globalMessage.text('');
        $globalMessage.hide();
        $globalMessage.removeClass('text-red-600', 'success-message error-global-message');

        let formData = new FormData($('#' + formId)[0]);

        $.ajax({
            type: 'POST',
            url: storeConnexionRoute,
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            xhrFields: {
                withCredentials: true // Essentiel pour que Laravel reçoive les cookies de session
            },
            success: function(response) {
                if (!loader.classList.contains('hidden')){
                    loader.classList.add('hidden');
                }
                if (response.success === true) {
                    // Stocker le pseudo dans localStorage (optionnel)

                    $globalMessage.text(response.message);
                    $globalMessage.addClass('success-message').show();
                    $('#' + formId)[0].reset(); // Réinitialise le formulaire

                    setTimeout(() => {
                        window.location.href = response.redirect;
                    }, 1500);
                } else {
                    // Cas pour une erreur métier retournée par le contrôleur Laravel
                    $globalMessage.text(response.message || 'Une erreur est survenue lors de la connexion.');
                    $globalMessage.addClass('error-global-message').show();
                }
            },
            error: function(xhr) {
                console.log('Response:', xhr.responseJSON); // Pour debug
                if (!loader.classList.contains('hidden')){
                    loader.classList.add('hidden');
                }
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    console.log('Validation errors:', errors);

                    for (const field in errors) {
                        $('#' + field + '-error').text(errors[field][0]);
                    }

                    $('#global-message')
                        .text('Veuillez corriger les erreurs.')
                        .addClass('text-red-600' ,'error-global-message')
                        .show();
                } else if (xhr.status === 419) {
                    $('#global-message')
                        .text('Jeton CSRF invalide ou expiré. Rechargez la page.')
                        .addClass('text-red-600' ,'error-global-message')
                        .show();
                } else {
                    $('#global-message')
                        .text('Erreur serveur. Veuillez réessayer plus tard.')
                        .addClass('text-red-600' ,'error-global-message')
                        .show();
                }
            }
        });
    }

    // Gestion de la soumission du formulaire de connexion
    $('#connexionForm').submit(function(event) {
        event.preventDefault(); // Empêche l'envoi classique
        if(loader.classList.contains('hidden')){
            loader.classList.remove('hidden');
        }
        envoyerFormulaire('connexionForm');
    });
});
