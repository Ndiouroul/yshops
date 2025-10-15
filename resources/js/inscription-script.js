$(document).ready(function() {
    // Récupérer le token CSRF et l'URL de la route depuis les balises meta
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    const storeInscriptionRoute = $('meta[name="route-store-inscription"]').attr('content');
    const loader = document.querySelector('#loader');


    function envoyerFormulaire(formId) {
        // Réinitialise tous les messages d'erreur et le message global
        $('.error-message').text('');
        const $globalMessage = $('#global-message');
        $globalMessage.text('');
        $globalMessage.hide();
        $globalMessage.removeClass('text-red-600')
        $globalMessage.removeClass('success-message error-global-message');


        let formData = new FormData($('#' + formId)[0]);

        $.ajax({
            type: 'POST',
            url: storeInscriptionRoute, // Utilise l'URL récupérée de la balise meta
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': csrfToken, // Utilise le token CSRF récupéré de la balise meta
                'Accept': 'application/json' // Indique que nous attendons une réponse JSON
            },
            success: function(response) {
                if (!loader.classList.contains('hidden')){
                    loader.classList.add('hidden');
                }
                if (response.success === true) { // Vérifie la propriété 'success' du JSON du contrôleur Laravel
                    $globalMessage.text(response.message);
                    $globalMessage.addClass('success-message').show();
                    $('#' + formId)[0].reset(); // Réinitialise le formulaire
                    setTimeout(()=>{
                        window.location.href = response.redirect;
                    }, 1500)
                } else {
                    // Cas pour une erreur métier retournée par le contrôleur Laravel, si applicable
                    $globalMessage.text(response.message || 'Une erreur est survenue lors de l\'inscription.');
                    $globalMessage.addClass('error-global-message').show();
                }
            },
            error: function(xhr) {
                if (!loader.classList.contains('hidden')){
                    loader.classList.add('hidden');
                }
                if (xhr.status === 422) { // Erreurs de validation de Laravel
                    const errors = xhr.responseJSON.errors;
                    for (const field in errors) {
                        $('#' + field + '-error').text(errors[field][0]); // Affiche le premier message d'erreur
                    }
                    $globalMessage.text('Veuillez corriger les erreurs du formulaire.');
                    $globalMessage.addClass('text-red-600', 'border-red-300');
                    $globalMessage.removeClass('border-green-300');
                    $globalMessage.addClass('error-global-message').show();
                } else { // Autres erreurs serveur (par exemple 500)
                    let errorMessage = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Erreur inattendue du serveur.';
                    $globalMessage.text(errorMessage);
                    $globalMessage.addClass('error-global-message').show();
                    $globalMessage.addClass('text-red-600')
                }
            }
        });
    }

    // Gestion de la soumission du formulaire d'inscription
    $('#inscriptionForm').submit(function(event) {
        event.preventDefault();
        if(loader.classList.contains('hidden')){
            loader.classList.remove('hidden');
        }
        envoyerFormulaire('inscriptionForm');
    });
});
