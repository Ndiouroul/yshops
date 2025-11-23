
$(document).ready(function() {
    // Récupérer le token CSRF et l'URL de la route depuis les balises meta
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    // const storeInscriptionRoute = $('meta[name="route-store-inscription"]').attr('content');

    function envoyerFormulaire(formId, Route, global_message) {
        // Réinitialise tous les messages d'erreur et le message global
        $('#identifiant').removeClass('border border-red-500')
        const $globalMessage = global_message;
        $globalMessage.text('');
        $globalMessage.hide();
        $globalMessage.removeClass('success-message error-global-message');

        let identifiant = document.querySelector('#form-vente-recherche #identifiant').value;
        document.querySelector('#form-vente-commande #shopnameinput').value = document.querySelector('#form-vente-recherche #shopMatricule').value;
        let quantite = 0;
        if (identifiant.includes(' ')){
            let tabIdent = identifiant.split(' ');
            let newidentifiant = tabIdent[0];
            quantite = tabIdent[1];
        }
        if (formId === 'form-vente-commande') {
            document.querySelector("#form-vente-commande #shopmatricule").value = localStorage.getItem('shopmatricule');
            // alert(document.querySelector("#form-vente-commande #shopmatricule").value);
        }


        let formData = new FormData($('#' + formId)[0]);

        $.ajax({
            type: 'POST',
            url: Route, // Utilise l'URL récupérée de la balise meta
            data: formData,
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
                    $globalMessage.addClass('text-green-700 bg-green-50 border-green-300').removeClass('text-red-700 bg-red-50 border-red-300')
                    // $globalMessage.text(response.message);
                    // $globalMessage.addClass('success-message').show();
                    $('#' + formId)[0].reset(); // Réinitialise le formulaire
                    if (response.type === 'newProductSearchedCodeBarre'){
                        for (let element of response.data) {
                            if (element.quantite < quantite) {
                                insererProduitCommande(response.nom, element.prix_vente, element.quantite);
                                quantite -= element.quantite;
                            } else {
                                insererProduitCommande(response.nom, element.prix_vente, quantite);
                                break; // 🚀 ici ça marche, ça arrête bien la boucle
                            }
                        }
                    }else if(response.type === 'newProductSearchedName')
                    {
                        // alert(JSON.stringify(response.data));
                        displayProductSentByName(response.data);
                    }
                    else if(response.type === "venteRas"){
                        $globalMessage.text(response.message);
                        $globalMessage.addClass('success-message').show();
                        setTimeout(()=>{
                            window.location.reload();
                        }, 1000)
                    }
                    else{
                        // alert(JSON.stringify(response))
                    }
                }else if(response.success === false){
                    $globalMessage.text(response.message).removeClass('text-green-700 bg-green-50 border-green-300').addClass('text-red-700 bg-red-50 border-red-300').show();
                    // alert(JSON.stringify(response))
                }

            },
            error: function(xhr) {
                if (xhr.status === 422 || xhr.status === 400) { // Erreurs de validation de Laravel
                    $globalMessage.removeClass('text-green-700 bg-green-50 border-green-300').addClass('text-red-700 bg-red-50 border-red-300')
                    $globalMessage.text('Veuillez renseigner un nom ou un code-barre.');
                    $globalMessage.addClass('error-global-message').show();
                    $('#identifiant').addClass('border border-red-500');
                } else { // Autres erreurs serveur (par exemple 500)
                    let errorMessage = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Erreur inattendue du serveur.';
                    $globalMessage.removeClass('text-green-700 bg-green-50 border-green-300').addClass('text-red-700 bg-red-50 border-red-300')
                    $globalMessage.text(errorMessage);
                    $globalMessage.addClass('error-global-message').show();
                 }
            }
        });
    }

    // Gestion de la soumission du formulaire d'inscription
    $('#form-vente-commande').submit(function(event) {
        event.preventDefault();
        envoyerFormulaire('form-vente-commande', $('meta[name="route-valid-Commande"]').attr('content'), $('#form-vente-commande #global-message-add-product'));
    });
    $('#form-vente-recherche').submit(function(event) {
        event.preventDefault();
        envoyerFormulaire('form-vente-recherche', $('meta[name="route-search-Product-Commande"]').attr('content'), $('#form-vente-commande #global-message-add-product'));
    });
});

const fermer = document.querySelector("#fermerDisplayProduct");
fermer.addEventListener("click", ()=>{
    document.querySelector("#container-display").classList.add('hidden');
})

// déclarer en haut du script (global)
let cmt = 0;
let general = 0;

// utilitaire de conversion sûre
function sanitizeNumber(val) {
    if (val == null) return 0;
    const s = String(val).trim().replace(/\s/g, '').replace(',', '.');
    const n = parseFloat(s);
    return isNaN(n) ? 0 : n;
}

function updateTotalGeneral() {
    const totals = document.querySelectorAll('[id^="total"]');
    let sum = 0;
    totals.forEach(t => {
        sum += sanitizeNumber(t.value);
    });
    general = sum;

    // Formater le nombre avec séparateur d'espaces et 2 décimales
    const formattedSum = sum.toLocaleString('fr-FR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });

    // Mettre à jour l'affichage
    const totalgenaral = document.querySelector("#form-vente-commande #totalgeneral");
    if (totalgenaral) totalgenaral.textContent = formattedSum; // input prend value

    // Si tu as un champ caché pour le total réel (ex: pour soumettre au serveur)
    const hiddenInput = document.querySelector("#inputtotalgeneral");
    if (hiddenInput) {
        hiddenInput.value = sum; // valeur numérique brute
        document.querySelector("#inputtotalgeneral").value = sum;
    }
}

function insererProduitCommande(nom, prix, quantiteArg) {
    const container = document.querySelector("#form-vente-commande #container-fieldset");
    if (!container) return;

    const prixNumber = sanitizeNumber(prix);
    const quantiteNumber = sanitizeNumber(quantiteArg) || 1;

    cmt++; // incrémente pour obtenir un id unique
    document.querySelector("#index").value = cmt;

    const idx = cmt;

    container.insertAdjacentHTML('beforeend', `
        <div id="bloc-product${idx}" class="flex w-[95%] items-center gap-2">
            <input name="nom${idx}" id="nom${idx}" type="text" class="text-[13px] w-full" readonly value="${nom}">
            <input name="prix_unitaire${idx}" id="prix_unitaire${idx}" type="text" class="text-center text-[13px] w-[20%]" readonly value="${prixNumber}">
            <input name="quantite${idx}" id="quantite${idx}" type="number" class="text-center text-[13px] w-[14%]" value="${quantiteNumber}">
            <input name="total${idx}" id="total${idx}" type="text" class="text-center text-[13px] w-[20%]" readonly value="${(quantiteNumber * prixNumber).toFixed(2)}">
        </div>
        <hr class="w-full text-gray-300">
    `);

    // Attacher le listener SUR LE NOUVEL ÉLÉMENT (immédiatement après insertion)
    const quantInput = document.getElementById(`quantite${idx}`);
    const prixInput  = document.getElementById(`prix_unitaire${idx}`);
    const totalInput = document.getElementById(`total${idx}`);

    if (quantInput && prixInput && totalInput) {
        const onQtyChange = () => {
            const qv = sanitizeNumber(quantInput.value) || 0;
            const pv = sanitizeNumber(prixInput.value) || 0;
            totalInput.value = (qv * pv).toFixed(2);
            updateTotalGeneral();
        };
        // 'input' pour réagir pendant la saisie, 'change' pour après perte de focus
        quantInput.addEventListener('input', onQtyChange);
        quantInput.addEventListener('change', onQtyChange);
    }

    // Met à jour le total général après ajout
    updateTotalGeneral();
}

function displayProductSentByName(products) {
    let cmt = 0;
    const containerParent = document.querySelector("#container-display");
    const container = document.querySelector("#displayProductSentByName");
    container.innerHTML = "";
    let diventete = document.createElement('div');
    diventete.classList.add("flex", "m-2", "rounded-md", "justify-between", "text-[13px]", "items-center");
    diventete.innerHTML = `
            <p class="text-center w-[20%]"></p>
            <p class="text-center w-[40%] font-bold">Nom</p>
            <p class="text-center w-[20%] font-bold">Quantite</p>
            <p class="text-center w-[20%] font-bold">Prix</p>
        `;
    container.appendChild(diventete);
    products.forEach(element => {
        let div = document.createElement('div');
        div.id = `div${cmt}`;
        div.classList.add("flex", "m-2", "cursor-pointer", "shadow-md", "rounded-md", "justify-between", "text-[13px]", "items-center", "h-[50px]");

        div.innerHTML = `
            <img src="storage/${element.image}" alt="Photo produit" class="w-[20%] p-[3px] h-[50px]">
            <p class="text-center w-[40%]">${element.nom_produit}</p>
            <p class="text-center w-[20%]">${element.quantite}</p>
            <p class="text-center w-[20%]">${element.prix_vente}</p>
        `;

        container.appendChild(div);
        container.querySelector('#'+`div${cmt}`).addEventListener("click", ()=>{
            insererProduitCommande(element.nom_produit, element.prix_vente, 1)
            document.querySelector("#container-display").classList.add('hidden');
        })
        containerParent.classList.remove('hidden');
        cmt ++;
    });
}
document.addEventListener('DOMContentLoaded', function () {
    getStructureDetails();
})

let nomshop = '';

function getStructureDetails() {
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    let route = $('meta[name="route-get-structure"]').attr("content");

    $.ajax({
        type: 'get',
        url: route,
        processData: false,
        contentType: false,
        dataType: 'json',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        success: function(response) {
            if (response.success === true) {
                const select = document.querySelector('#shopMatricule');
                select.innerHTML = ""; // vider avant de remplir

                // alert(response.data[0].structure_matricule)
                const saved = localStorage.getItem('shopmatricule');
                if(!saved){
                    localStorage.setItem('shopmatricule', response.data[0].structure_matricule);
                }

                // 1️⃣ Injecter toutes les structures sauf celle du localStorage
                response.data.forEach(element => {

                    if (saved !== element.structure_matricule) {
                        const option = document.createElement('option');
                        option.value = element.structure_matricule;
                        option.textContent = element.nom_structure;
                        select.appendChild(option);

                        // récupérer le nom de la boutique sauvegardée si elle correspond
                        if (saved === element.structure_matricule) {
                            nomshop = element.nom_structure;
                        }
                    } else {
                        nomshop = element.nom_structure;
                    }
                });

                // 2️⃣ Injecter l’option sauvegardée en première position
                if (saved) {
                    const opt = document.createElement('option');
                    opt.value = saved;
                    opt.textContent = nomshop;
                    select.insertBefore(opt, select.firstChild);
                    select.value = saved; // sélectionner par défaut
                }

                // 3️⃣ Mettre à jour le localStorage quand l’utilisateur change
                select.addEventListener('change', function () {
                    localStorage.setItem('shopmatricule', select.value);
                });
            }
        },

        error: function(xhr) {
            const msg = xhr.responseJSON?.message || 'Erreur serveur inattendue';
            alert(msg);
        }
    });
}

