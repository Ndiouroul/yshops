document.addEventListener('DOMContentLoaded', function () {
    getShopDetails()
})

document.querySelector("#recherche-avancee-accueil").addEventListener('submit', function () {
    searchedShops();
})

function searchedShops() {
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    const form = $('#recherche-avancee-accueil')[0]; // l’id de ton <form>
    const formData = new FormData(form); // récupère tous les champs du formulaire

    $.ajax({
        type: 'POST', // ou 'PUT' selon ton endpoint Laravel
        url: $('meta[name="route-post-shop-details"]').attr("content"), // ton URL dynamique
        data: formData,
        processData: false, // indispensable avec FormData
        contentType: false, // idem
        dataType: 'json',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        success: function(response) {
            if (response.success === true) {
                // Exemple : afficher un message de succès ou mettre à jour l'UI
                alert('Boutique enregistrée avec succès !');
                createBlocDetailsStructure(response.structures);
            } else {
                alert(response.message || 'Une erreur est survenue.');
            }
        },
        error: function(xhr) {
            if (xhr.status === 422) {
                // Erreurs de validation Laravel
                const errors = xhr.responseJSON.errors;
                let errorText = '';
                for (let field in errors) {
                    errorText += `${errors[field][0]}\n`;
                }
                alert(errorText);
            } else {
                // Autres erreurs (ex : 500)
                let errorMessage = xhr.responseJSON && xhr.responseJSON.message
                    ? xhr.responseJSON.message
                    : 'Erreur inattendue du serveur.';
                alert(errorMessage);
            }
        }
    });
}


function getShopDetails() {
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    $.ajax({
        type: 'get',
        url: $('meta[name="route-get-shop-details-accueil"]').attr("content"),
        // data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        headers: {
            // '_method': 'PUT',
            'X-CSRF-TOKEN': csrfToken, // Utilise le token CSRF récupéré de la balise meta
            'Accept': 'application/json' // Indique que nous attendons une réponse JSON
        },
        success: function(response) {
            if (response.success === true) { // Vérifie la propriété 'success' du JSON du contrôleur Laravel
                // alert(JSON.stringify(response.structure));
                createBlocDetailsStructure(response.structures);
            }

        },
        error: function(xhr) {
            if (xhr.status === 422) { // Erreurs de validation de Laravel
                const errors = xhr.responseJSON.errors;
                alert(errors);
            } else { // Autres erreurs serveur (par exemple 500)
                let errorMessage = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Erreur inattendue du serveur.';
                alert(errorMessage);
            }
        }
    });
}
function createBlocDetailsStructure(details) {

    let container = document.querySelector('#container #shops-container');
    container.innerHTML = ''; // On vide d'abord le container

    let baseRoute = $('meta[name="route-open-shop-accueil"]').attr("content");

    // Parcourir les packages
    Object.entries(details).forEach(([packageCode, shops]) => {

        // S'il n'y a pas de boutiques dans ce package, on passe
        if (!shops || shops.length === 0) return;

        // // Bloc titre du package
        // let title = document.createElement('h2');
        // title.classList.add('text-lg', 'font-semibold', 'mt-6', 'mb-2', 'text-gray-700');
        // title.textContent = `Package : ${packageCode}`;
        // container.appendChild(title);

        // Conteneur flex pour les boutiques de ce package
        let packageContainer = document.createElement('div');
        packageContainer.classList.add('flex', 'flex-wrap', 'gap-4', 'mb-6');
        container.appendChild(packageContainer);

        // Parcourir les boutiques de ce package
        shops.forEach((element, index) => {
            let shopUrl = baseRoute.replace('__MATRICULE__', element.structure_matricule);

            // Bloc principal
            let divSubContainer = document.createElement('div');
            divSubContainer.classList.add(
                'shadow-md', 'cursor-pointer', 'items-center', 'flex', 'flex-col',
                'gap-7', 'ml-2', 'w-[175px]', 'h-auto',
                'opacity-0', 'translate-y-4', 'transition-all', 'duration-500'
            );

            divSubContainer.innerHTML = `
                <a href="${shopUrl}" class="relative block rounded-md overflow-hidden shadow-md">
                    <!-- Image -->
                    <img src="/storage/${element.logo}" alt="Logo"
                         class="w-47 h-47 object-cover rounded-md transition-transform duration-300 hover:scale-105">

                    <!-- Overlay avec texte -->
                    <div class="absolute bottom-0 right-0 w-full p-2 bg-black/40 backdrop-blur-md">
                        <p class="text-white text-[13px] font-semibold text-left drop-shadow-md">
                            ${element.nom_structure}
                            ${element.sigle ? '(' + element.sigle + ')' : ''}
                        </p>
                    </div>
                </a>
            `;


            packageContainer.appendChild(divSubContainer);

            // Animation (fade-in + slide)
            setTimeout(() => {
                divSubContainer.classList.remove('opacity-0', 'translate-y-4');
                divSubContainer.classList.add('opacity-100', 'translate-y-0');
            }, index * 150);
        });
    });
}
