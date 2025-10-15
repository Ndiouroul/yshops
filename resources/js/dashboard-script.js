import Chart from 'chart.js/auto';
// import {elementAt} from "rxjs/operators";

const gauche = document.querySelector('#gauche');
const droite = document.querySelector('#droite');
gauche.querySelector('#parametre').click();

document.addEventListener('DOMContentLoaded', function () {
    chargerBoutique();
    gauche.querySelector('#parametre').click();

    if (droite.querySelector("#bloc-stats")) {
        gauche.querySelector("#stats").click();
    }

    const typeStructure = droite.querySelector("#bloc-option-param-boutique #type_structure");
    const categoryStructure = droite.querySelector("#bloc-option-param-boutique #bloc-cathegorie_structure-param");
    const typeStructureBoutique = droite.querySelector("#bloc-boutique #type_structure");
    const categoryStructureBoutique = droite.querySelector("#bloc-boutique #bloc-catehorie_structure");
    typeStructure.addEventListener('change', function () {
        if (typeStructure.value === "transformation"){
            categoryStructure.classList.remove('hidden');
        }else{
            if(!categoryStructure.classList.contains('hidden')){
                categoryStructure.classList.add('hidden');
            }
        }
    })

    typeStructureBoutique.addEventListener('change', function () {
        if (typeStructureBoutique.value === "transformation"){
            categoryStructureBoutique.classList.remove('hidden');
        }else{
            if(!categoryStructureBoutique.classList.contains('hidden')){
                categoryStructureBoutique.classList.add('hidden');
            }
        }
    })

});

gauche.addEventListener('click', function (e) {
    if(e.target === gauche.querySelector('#stats')){
        chargerDroite('stats', "bloc-stats")
        viderStats();
        chargerBoutiqueStats();
        chargerBoutiquesCompta();
    }else if(e.target === gauche.querySelector('#comptabilite')){
        viderCompta();
        chargerBoutiquesCompta();
        chargerDroite('comptabilite', "bloc-comptabilite")
    }
    else if(e.target === gauche.querySelector('#vente')){
        chargerDroite('vente', "bloc-vente")
    }
    else if(e.target === gauche.querySelector('#parametre')){
        viderParam();
        chargerDroite('parametre', "bloc-parametre")
    }
    else if(e.target === gauche.querySelector('#boutique')){
        chargerDroite('boutique', "bloc-boutique");
    }
});


function chargerDroite(boutton, block){
    if (!document.querySelector("#bloc-stats")) {
        alert('stats inexistant');
        document.querySelector("#parametre").click();
        return; // stoppe ici l'exécution de la fonction
    }
    const dictionnaire = {
        "stats"       : "bloc-stats",
        "comptabilite": "bloc-comptabilite",
        "vente"       : "bloc-vente",
        "parametre"   : "bloc-parametre",
        "boutique"   : "bloc-boutique",

        // "bloc-parametre #boutique-param"   : "bloc-parametre #bloc-option-param-boutique",

    };

    Object.values(dictionnaire).forEach(idBloc => {
        droite.querySelector("#" + idBloc).classList.add("hidden");
    });
    Object.keys(dictionnaire).forEach(idbutton => {
        const bouton = document.querySelector("#" + idbutton); // <- on récupère l'élément DOM
        if (!bouton) return;

        if (["font-bold", "rounded-md", "bg-blue-200", "text-[15px]"]
            .every(cls => bouton.classList.contains(cls))) {
            bouton.classList.remove("font-bold", "rounded-md", "bg-blue-200", "text-[15px]");
        }
        if(! droite.querySelector('#bloc-boutique-container').classList.contains('hidden')){
            droite.querySelector('#bloc-boutique-container').classList.add('hidden');
        }
    });

    const boutonClicked = document.querySelector("#" + boutton);
    const blocClicked = document.querySelector("#" + block);

    blocClicked.classList.remove("hidden", '-translate-x-[150%]');
    boutonClicked.classList.add("font-bold", "rounded-md", "bg-blue-200", "text-[15px]");

}


$(document).ready(function() {
    // Récupérer le token CSRF et l'URL de la route depuis les balises meta
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    // const storeInscriptionRoute = $('meta[name="route-store-inscription"]').attr('content');

    function envoyerFormulaire(formId, Route, global_message, error_msg, bloc, methode) {
        // Réinitialise tous les messages d'erreur et le message global
        error_msg.text('');
        error_msg.each(function () {
            if ($(this).hasClass('text-green-700')) {
                $(this).removeClass('text-green-700').addClass('text-red-700');
            }
        });


        const $globalMessage = global_message;
        $globalMessage.text('');
        $globalMessage.hide();
        $globalMessage.removeClass('success-message error-global-message');


        let formData = new FormData($('#' + formId)[0]);
        if (methode === 'PUT'){
            formData.append('_method', 'PUT');
            methode = 'POST';
        }

        $.ajax({
            type: methode,
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
                const loader = document.querySelector(bloc+" #loader");
                if (loader && !loader.classList.contains('hidden')) {
                    loader.classList.add('hidden');
                }
                if (response.success === true) { // Vérifie la propriété 'success' du JSON du contrôleur Laravel
                    $globalMessage.addClass('text-green-700 bg-green-50 border-green-300').removeClass('text-red-700 bg-red-50 border-red-300')
                    $globalMessage.text(response.message);
                    $globalMessage.addClass('success-message').show();
                    // alert("data :"+JSON.stringify(response.data));

                    if(formId !== "form-updating-element-everything-employes"){
                        $('#' + formId)[0].reset(); // Réinitialise le formulaire
                    }
                    if (response.type === 'checkPassword'){
                        error_msg.text('Mot de passe vérifié.').addClass('text-green-600').removeClass('text-red-600');
                        setTimeout(()=>{
                            $('#bloc-check-password').addClass('-translate-x-[150%] hidden');
                        }, 1000);
                        activerTousLesChamps('#form-update-profil input', '#form-update-profil select');
                    }
                    else if(response.type === 'updateProfil'){
                        desactiverTousLesChamps('#form-update-profil input', '#form-update-profil select');
                        activerEditionProfil();
                    }else if(response.type === 'newBill'){
                        document.querySelector("#button_add_new_facture").click();
                    }else if (response.type === "updatingEverything"){
                        // alert(response.data)
                    }else{
                        // alert(response.data);
                    }
                }
                else if (response.success === false){
                    // Cas pour une erreur métier retournée par le contrôleur Laravel, si applicable
                    $globalMessage.removeClass('text-green-600 bg-green-50 border-green-300').addClass('text-red-700 bg-red-50 border-red-300')
                    $globalMessage.text(response.message || 'Une erreur est survenue lors du processus.');
                    $globalMessage.addClass('error-global-message').show();
                    alert(response.message)

                    if(response.type === 'checkPassword'){
                        $('#bloc-check-password #password-error').text(response.message);
                    }
                    else if(response.type === 'newShopPackage'){
                        alert(response.message);
                    }
                    else if(response.type === 'codeBarreExists'){
                        $('#code_barre-error').text(response.message);
                    }
                    else if(response.type === "errorDuplicata"){
                        alert(response.message);
                    }
                    else if(response.type === "checkingPasswordUpdatingAccountDetails"){
                        $globalMessage.removeClass('text-green-600 bg-green-50 border-green-300').addClass('text-red-700 bg-red-50 border-red-300')
                        $globalMessage.text(response.message || 'Une erreur est survenue lors du processus.');
                        $globalMessage.addClass('error-global-message').show();
                    }
                }
            },
            error: function(xhr) {
                const loader = document.querySelector(bloc+" #loader");
                if (loader && !loader.classList.contains('hidden')) {
                    loader.classList.add('hidden');
                }
                if (xhr.status === 422) { // Erreurs de validation de Laravel
                    const errors = xhr.responseJSON.errors;
                    for (const field in errors) {
                        $(bloc + ' #' + field + '-error').text(errors[field][0]);
                    }
                    $globalMessage.removeClass('text-green-700 bg-green-50 border-green-300').addClass('text-red-700 bg-red-50 border-red-300')
                    $globalMessage.text('Veuillez corriger les erreurs du formulaire.');
                    $globalMessage.addClass('error-global-message').show();
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
    $('#form-add-new-produit').submit(function(event) {
        event.preventDefault();
        if(this.querySelector("#loader")){
            this.querySelector("#loader").classList.remove('hidden');
        }
        envoyerFormulaire('form-add-new-produit', $('meta[name="route-store-new-product"]').attr('content'), $('#form-add-new-produit #global-message-new-produit'), $('#form-add-new-produit .error-message'), '#bloc-add-new-produit', 'Post');
    });
    // Gestion de la soumission du formulaire de creation d'une nouvelle boutique
    $('#form-add-new-shop').submit(function(event) {
        event.preventDefault();
        if(this.querySelector("#loader")){
            this.querySelector("#loader").classList.remove('hidden');
        }
        envoyerFormulaire('form-add-new-shop', $('meta[name="route-store-new-structure"]').attr('content'), $('#form-add-new-shop #global-message-new-shop'), $('#form-add-new-shop .error-message'), '#bloc-boutique', 'post');
    });
      $('#form-add-new-shop-param').submit(function(event) {
        event.preventDefault();
        if(this.querySelector("#loader")){
            this.querySelector("#loader").classList.remove('hidden');
        }
        envoyerFormulaire('form-add-new-shop-param', $('meta[name="route-store-new-structure"]').attr('content'), $('#form-add-new-shop-param #global-message-new-shop-param'), $('#form-add-new-shop-param .error-message'), '#bloc-add-new-shop-param', 'post');
    });
    // Gestion de la soumission du formulaire de creation d'une nouvelle boutique
    $('#form-add-new-fournisseur').submit(function(event) {
        event.preventDefault();
        if(this.querySelector("#loader")){
            this.querySelector("#loader").classList.remove('hidden');
        }
        envoyerFormulaire('form-add-new-fournisseur', $('meta[name="route-store-new-fournisseur"]').attr('content'), $('#form-add-new-fournisseur #global-message-new-fournisseur'), $('#form-add-new-fournisseur .error-message'), '#bloc-add-new-fournisseur', 'POST');
    });
    // Gestion de la soumission du formulaire check du mot de passe
    $('#form-check-password').submit(function(event) {
        event.preventDefault();
        if(this.querySelector("#loader")){
            this.querySelector("#loader").classList.remove('hidden');
        }
        envoyerFormulaire('form-check-password', $('meta[name="route-store-check-password"]').attr('content'), $('#form-check-password #global-message-check-password'), $('#form-check-password .error-message'), '#bloc-check-password', 'POST');
    });
    $('#form-update-profil').submit(function(event) {
        event.preventDefault();
        if(this.querySelector("#loader")){
            this.querySelector("#loader").classList.remove('hidden');
        }
        envoyerFormulaire('form-update-profil', $('meta[name="route-store-edit-profil"]').attr('content'), $('#bloc-update-profil #global-message-new-update-profil'), $('#form-update-profil .error-message'), '#bloc-update-profil','PUT');
    });
    $('#form-add-new-gerant').submit(function(event) {
        event.preventDefault();
        if(this.querySelector("#loader")){
            this.querySelector("#loader").classList.remove('hidden');
        }
        envoyerFormulaire('form-add-new-gerant', $('meta[name="route-store-new-manager"]').attr('content'), $('#bloc-add-new-gerant #global-message-new-gerant'), $('#form-add-new-gerant .error-message'), '#bloc-add-new-gerant','POST');
    });
    $('#form-add-new-ingredient').submit(function(event) {
        event.preventDefault();
        if(this.querySelector("#loader")){
            this.querySelector("#loader").classList.remove('hidden');
        }
        envoyerFormulaire('form-add-new-ingredient', $('meta[name="route-store-new-ingredient"]').attr('content'), $('#bloc-add-new-ingredient #global-message-new-ingredient'), $('#form-add-new-ingredient .error-message'), '#bloc-add-new-ingredient','POST');
    });
    $('#form-add-new-vendeur').submit(function(event) {
        event.preventDefault();
        if(this.querySelector("#loader")){
            this.querySelector("#loader").classList.remove('hidden');
        }
        envoyerFormulaire('form-add-new-vendeur', $('meta[name="route-store-new-seller"]').attr('content'), $('#bloc-add-new-vendeur #global-message-new-vendeur'), $('#form-add-new-vendeur .error-message'), '#bloc-add-new-vendeur','POST');
    });
    $(document).on('submit', '#form-add-new-facture', function(event) {
        event.preventDefault();
        if(this.querySelector("#loader")){
            this.querySelector("#loader").classList.remove('hidden');
        }
        envoyerFormulaire('form-add-new-facture', $('meta[name="route-store-add-new-bill-products"]').attr('content'), $('#bloc-add-new-facture #global-message-facture'), $('#form-add-new-facture .error-message'), '#bloc-add-new-facture','POST');
    });
    $(document).on('submit', '#form-add-new-facture-transformation', function(event) {
        event.preventDefault();
        if(this.querySelector("#loader")){
            this.querySelector("#loader").classList.remove('hidden');
        }
        envoyerFormulaire('form-add-new-facture-transformation', $('meta[name="route-store-add-new-bill-transformation-products"]').attr('content'), $('#bloc-add-new-facture #global-message-facture'), $('#form-add-new-facture-transformation .error-message'), '#bloc-add-new-facture','POST');
    });
    $(document).on('submit', '#form-updating-element-everything-all', function(event) {
        event.preventDefault();
        if(this.querySelector("#loader")){
            this.querySelector("#loader").classList.remove('hidden');
        }
        envoyerFormulaire('form-updating-element-everything-all', $('meta[name="route-store-element-updating"]').attr('content'), $('#bloc-view-update-produits-all #global-message-produit'), $('#form-updating-element-everything-all .error-message'), '#bloc-view-update-produits-all','POST');
    });
    $(document).on('submit', '#form-updating-element-everything-on', function(event) {
        event.preventDefault();
        if(this.querySelector("#loader")){
            this.querySelector("#loader").classList.remove('hidden');
        }
        envoyerFormulaire('form-updating-element-everything-on', $('meta[name="route-store-element-updating"]').attr('content'), $('#bloc-view-update-produits-on #global-message-produit'), $('#form-updating-element-everything-on .error-message'), '#bloc-view-update-produits-on','POST');
    })
    $(document).on('submit', '#form-updating-element-everything-dealers', function(event) {
        event.preventDefault();
        if(this.querySelector("#loader")){
            this.querySelector("#loader").classList.remove('hidden');
        }
        envoyerFormulaire('form-updating-element-everything-dealers', $('meta[name="route-store-element-updating"]').attr('content'), $('#bloc-view-update-fournisseurs #global-message-fournisseur'), $('#form-updating-element-everything-dealers .error-message'), '#bloc-view-update-fournisseurs','POST');
    });
    $(document).on('submit', '#form-updating-element-everything-bills', function(event) {
        event.preventDefault();
        if(this.querySelector("#loader")){
            this.querySelector("#loader").classList.remove('hidden');
        }
        envoyerFormulaire('form-updating-element-everything-bills', $('meta[name="route-store-element-updating"]').attr('content'), $('#bloc-view-update-factures #global-message-facture'), $('#form-updating-element-everything-bills .error-message'), '#bloc-view-update-factures','POST');
    });
    $(document).on('submit', '#form-updating-element-everything-employes', function(event) {
        event.preventDefault();
        if(this.querySelector("#loader")){
            this.querySelector("#loader").classList.remove('hidden');
        }
        envoyerFormulaire('form-updating-element-everything-employes', $('meta[name="route-store-element-updating"]').attr('content'), $('#bloc-view-update-employes #global-message-employe'), $('#form-updating-element-everything-employes .error-message'), '#bloc-view-update-employes','POST');
    });
    $(document).on('submit', '#form-account-update-param', function(event) {
        event.preventDefault();
        if(this.querySelector("#loader")){
            this.querySelector("#loader").classList.remove('hidden');
        }
        envoyerFormulaire('form-account-update-param', $('meta[name="route-store-update-account-details"]').attr('content'), $('#bloc-option-param-compte #global-message-compte-param'), $('#form-account-update-param .error-message'), '#bloc-option-param-compte','POST');
    });

});


const bloc_update_profil = droite.querySelector("#bloc-parametre #bloc-update-profil");
const bloc_paramettre = document.querySelector("#bloc-parametre");
const button_annuler_update_profil = document.querySelector("#annuler-update-profil");
const profil_button = document.querySelector("#profil-param");
const input_photo_profil = document.querySelector("#photo_profil");
const icon_actualiser_photo_profil = document.querySelector("#icon-actualiser");
const photo_name = document.querySelector("#pnoto-name");

input_photo_profil.addEventListener('change', function () {
    if (this.files.length > 0) {
        photo_name.textContent = this.files[0].name;
    } else {
        photo_name.textContent = "Aucun fichier choisi";
    }
});

icon_actualiser_photo_profil.addEventListener('click', ()=>{
    input_photo_profil.click();
})

profil_button.addEventListener('click', ()=>{
    activerEditionProfil();
    bloc_update_profil.classList.remove('hidden');
    droite.appendChild(bloc_update_profil);
});
button_annuler_update_profil.addEventListener('click', ()=>{
    bloc_update_profil.classList.add('hidden');
    droite.appendChild(bloc_paramettre);
})


// -------- Gestion des modales -------- //
const dictionnaire_adding = {
    "button_view_produits": "bloc-view-produits",
    "button_view_fournisseurs": "bloc-view-fournisseurs",
    "button_view_factures": "bloc-view-factures",
    "button_view_vendeurs": "bloc-view-vendeurs",
    "button_view_gerants": "bloc-view-gerants",
    "button_add_new_shop": "bloc-add-new-shop",
    "button_add_new_fournisseur": "bloc-add-new-fournisseur",
    "button_add_new_produit": "bloc-add-new-produit",
    "button_add_new_facture": "bloc-add-new-facture",
    "button_add_new_vendeur": "bloc-add-new-vendeur",
    "button_add_new_gerant": "bloc-add-new-gerant",
    "button-update-profil-button": "bloc-check-password",
    "profil-param": "bloc-update-profil",
    "button_add_new_ingredient": "bloc-add-new-ingredient",
    "compte-param": "bloc-option-param-compte",
};
const liste = ["produits", "fournisseurs", "factures", "vendeurs", "gerants"]
const liste_view_element = ["bloc-view-update-produits-all", "bloc-view-update-produits-on", "bloc-view-update-fournisseurs", "bloc-view-update-employes", "bloc-view-update-factures"];
droite.addEventListener("click", (e) => {
    const bouton = e.target.closest("div, button, td, tr"); // selon ce que tu ajoutes
    if (!bouton) return;

    // Vérifie si l'id correspond à une clé du dictionnaire
    if (dictionnaire_adding[bouton.id]) {
        const blocId = dictionnaire_adding[bouton.id];
        const bloc = document.querySelector("#" + blocId);

        if (!bloc) return;

        // Fermer tous les blocs
        Object.values(dictionnaire_adding).forEach(idBloc => {
            const b = document.querySelector("#" + idBloc);
            if (b) b.classList.add("hidden", "-translate-x-[150%]");
        });

        // Ouvrir le bloc correspondant
        bloc.classList.remove("hidden", "-translate-x-[150%]");

        const form = bloc.querySelector("form");
        if (form) form.reset();

        const loader = bloc.querySelector("#loader");
        if(loader){
            if (!loader.closest('button')){
                loader.classList.remove('hidden');
            }
        }

        if (bloc.id === 'bloc-option-param-compte'){
            const loader = droite.querySelector('#bloc-option-param-compte #form-account-update-param #loader');
            if (loader){
                loader.classList.remove('hidden');
            }
            loadAccountDetails();
        }

        // Cas spécial facture
        if (bloc.id === "bloc-add-new-facture") {
            bloc.querySelectorAll("form .product-input-group").forEach(el => el.remove());
        }
        for (let options of liste_view_element){
            if (bloc.id === options){
                if (bloc.querySelector("#loader")){
                    bloc.querySelector("#loader").classList.remove('hidden');

                }
                const td = e.target.closest("td");
                const productMatricule = td.querySelector("span").textContent;
                // alert(bloc.id);
                loadViewUpdateOptions(bloc.id, productMatricule);
            }
        }

        // Vérifie si le bouton correspond à une catégorie de liste
        for (let element of liste) {
            if (bouton.id.includes(element)) {
                gettindEverything(element);
            }
        }
    }
});


const dict_close = {
    '#annuler-check-password': '#bloc-check-password',
    '#close-add-new-shops': '#bloc-add-new-shop',
    '#cancel-add-product-bill': '#bloc-add-product-bill',
    '#close-bloc-compte-param': '#bloc-option-param-compte',
    '#close-add-new-shops-param': '#bloc-option-param-boutique',
    '#close-option-packages-param': '#bloc-option-param-packages',
};

// On parcourt toutes les paires bouton → bloc
for (let [buttonSelector, blocSelector] of Object.entries(dict_close)) {
    const bouton = document.querySelector(buttonSelector);
    const bloc = document.querySelector(blocSelector);

    if (bouton && bloc) {
        bouton.addEventListener("click", () => {
            bloc.classList.add("-translate-x-[150%]", "hidden");
        });
    } else {
        console.warn(`⚠️ Élément manquant : ${buttonSelector} ou ${blocSelector}`);
    }
}


function activerEditionProfil() {
    // Récupérer l’URL de la route depuis le <meta>
    let url = $('meta[name="route-store-get-profil"]').attr("content");

    // Requête GET vers le serveur
    $.get(url, function (data) {
        console.log("Profil reçu :", data);

        // Remplir les champs du formulaire avec les données reçues
        $('#bloc-update-profil #prenom').val(data.prenom ? data.prenom : 'null');
        $('#bloc-update-profil #nom').val(data.nom);
        $('#bloc-update-profil #email').val(data.email ? data.email : 'null');          // si existe côté serveur
        $('#bloc-update-profil #telephone1').val(data.telephone1);
        $('#bloc-update-profil #telephone2').val(data.telephone2);
        $('#bloc-update-profil #adresse').val(data.adresse);
        $('#bloc-update-profil #profil').val(data.profil ?? 'null');
        $('#bloc-update-profil #photo_profil-output img').attr('src', '/storage/' + data.photo_profil);
        $('#bloc-update-profil #date_naissance').val(data.date_naissance);
        $('#bloc-update-profil #sexe').val(data.sexe);
        $('#bloc-update-profil #cni').val(data.cni);
    }).fail(function (xhr) {
        console.error("Erreur :", xhr.responseText);
        alert("Erreur lors du chargement du profil !");
    });
}

function activerTousLesChamps(...selectors) {
    droite.querySelector("#bloc-parametre #profil-param").click();
    selectors.forEach(selector => {
        const inputs = document.querySelectorAll(selector);
        inputs.forEach(input => {
            input.disabled = false;
        });
    });
    document.querySelector("#button-update-profil-button").classList.add('hidden');
    document.querySelector("#button-update-profil-submit").classList.remove('hidden');

}

function desactiverTousLesChamps(...selectors) {
    selectors.forEach(selector => {
        const inputs = document.querySelectorAll(selector);
        inputs.forEach(input => {
            input.disabled = true;
        });
    });
    document.querySelector("#button-update-profil-button").classList.remove('hidden');
    document.querySelector("#button-update-profil-submit").classList.add('hidden');

}

function chargerBoutique() {
    if (!document.querySelector("#bloc-stats")) {
        document.querySelector("#parametre").click();
        return; // stoppe ici l'exécution de la fonction
    }

    // Récupérer l’URL de la route depuis le <meta>
    let url = $('meta[name="route-store-get-shop"]').attr("content");

    // Requête GET vers le serveur
    $.get(url, function (data) {
        console.log("Boutiques reçues :", data);

        // Affiche le message renvoyé par Laravel
        // alert(JSON.stringify(data.data));

        let cmp = 0;
        let blocShopCard = document.createElement("div");
        blocShopCard.classList.add("flex");
        data.data.forEach(function(element) {
            for (let shop of element){
                // alert(JSON.stringify(shop));
                // alert(JSON.stringify(shop.type_structure));
                // Créer un bloc <div>
                let shopCard = document.createElement("div");
                shopCard.classList.add("shop-card"); // tu peux styliser ça en CSS
                // Remplir le HTML interne
                shopCard.innerHTML = `
               <div id="boutique${cmp}"  class="m-2 cursor-pointer w-[150px] h-[155px] rounded-t-[7px] shadow-md bg-white text-[14px]">
                    <img src="/storage/${shop.logo}" alt="Logo de la boutique" class="w-full h-[75%] p-[3px] rounded-t-[7px]">
                   <h3 class=" text-[13px]">${shop.nom_structure}</h3>
                   <span class=" text-[13px] hidden">${shop.structure_matricule}</span>
                   <p class=" text-[13px] hidden">${shop.type_structure}</p>
               </div>`;
                // Ajouter dans le container
                blocShopCard.appendChild(shopCard);
                shopCard.addEventListener('click', function () {
                    document.querySelector("#bloc-boutique").classList.add('hidden');
                    droite.appendChild(document.querySelector("#bloc-boutique-container"));
                    droite.querySelector("#bloc-boutique-container").classList.remove('hidden');
                    if(shop.type_structure === "transformation"){
                        inputBillTransformation();
                    } else if(shop.type_structure === 'revente') {
                        inputBillResend();
                    }

                    // Récupérer le nom de la boutique
                    const shomatricule = shop.structure_matricule;
                    const shoptype = shop.type_structure;

                    // Parcourir tous les formulaires dans le container et ajouter / mettre à jour l’input
                    const forms = droite.querySelectorAll("form");
                    forms.forEach(form => {
                        let inputMat = form.querySelector(".shopmatricule");
                        let inputType = form.querySelector(".shoptype");
                        if (inputMat) {
                            inputMat.value = shomatricule; // Mettre à jour si déjà présent
                        }else {
                            inputMat = document.createElement('input');
                            inputMat.type = "hidden";
                            inputMat.name = "shopmatricule";
                            inputMat.className = "shopmatricule";
                            inputMat.value = shomatricule;
                            form.appendChild(inputMat);
                        }
                        if (inputType) {
                            inputType.value = shoptype; // Mettre à jour si déjà présent
                        }else {
                            inputType = document.createElement('input');
                            inputType.type = "hidden";
                            inputType.name = "shoptype";
                            inputType.className = "shoptype";
                            inputType.value = shoptype;
                            form.appendChild(inputType);
                        }
                    });

                    if (document.querySelector("#form-add-new-produit .shoptype").value === "transformation"){
                        document.querySelector("#form-add-new-produit #add-new-product-price").classList.remove('hidden');
                    }else if(document.querySelector("#form-add-new-produit .shoptype").value === "revente"){
                        if ( !document.querySelector("#form-add-new-produit #add-new-product-price").classList.contains('hidden')){
                            document.querySelector("#form-add-new-produit #add-new-product-price").classList.add('hidden');
                        }
                    }
                    droite.querySelector("#button_view_factures").click();

                    if(shop.type_structure === "transformation"){

                        // const block_depense = document.querySelector("#espace-depense");

                        droite.querySelector('#form-add-new-facture-transformation #new-depense-bill')
                            .addEventListener('click', function (){
                                droite.querySelector("#bloc-add-product-fees")
                                    .classList.remove('-translate-x-[150%]', 'hidden');
                                getBillProducts("bloc-form-transformation-type #form-add-new-facture-transformation");

                            });

                        getBillProducts("form-add-new-facture-transformation");
                        chargerFacture('form-add-new-facture-transformation');

                    } else if(shop.type_structure === "revente"){
                        document.querySelector('#form-add-new-facture #new-produit-bill').addEventListener('click', () => {
                            document.querySelector("#bloc-add-product-bill")
                                .classList.remove('-translate-x-[150%]', 'hidden');

                            getBillProducts("bloc-form-revente-type #form-add-new-facture"); // pas besoin de retour, ça injectera tout seul
                        });
                        getDealerList('form-add-new-facture');
                        chargerFacture('form-add-new-facture')
                    }
                    // getBillProducts();
                    droite.querySelector("#button_view_factures").click();
                });
                cmp ++;
            }
        });

        document.querySelector("#bloc-boutique").appendChild(blocShopCard);

    }).fail(function (xhr) {
        console.error("Erreur :", xhr.responseText);
        // alert("Veuillez proceder a la creation de votre premiere Boutique !");
    });
}

function chargerFacture(form) {
    document.addEventListener("click", (e) =>{
        if(e.target.closest("#"+form+" #bloc-charger-facture")){
            const bloc_charger_facture = e.target.closest("#"+form+" #bloc-charger-facture");
            const charger_facture = bloc_charger_facture.querySelector('#facture');
            charger_facture.click();
        }
    });

    const bloc_charger_facture = document.querySelector("#"+form+" #bloc-charger-facture");
    const charger_facture = bloc_charger_facture.querySelector("#"+form+" #bloc-charger-facture #facture");
    const name_fichier = bloc_charger_facture.querySelector("#"+form+" #bloc-charger-facture #facture-error");
    charger_facture.addEventListener('change', () => {
        if(charger_facture.files.length > 0) {
            const fileName = charger_facture.files[0].name; // 👈 le nom du fichier
            console.log(fileName); // tu peux le mettre dans une variable
            name_fichier.textContent = fileName;
            name_fichier.classList.remove('text-red-700');
            name_fichier.classList.add('text-green-700');
        }
    });
}

function getBillProducts(form) {
    const shopInput = document.querySelector("#"+form+" .shopmatricule");
    const shopname = shopInput?.value ?? 'shopmatricule';

    const meta = $('meta[name="route-store-get-bill-products"]');
    if (!meta.length) {
        console.error("Meta route introuvable !");
        return;
    }

    let route = meta.attr("content");
    if (!route) {
        console.error("Contenu du meta vide !");
        return;
    }

    route = route.replace("__SHOPNAME__", encodeURIComponent(shopname));

    // Appel AJAX
    $.ajax({
        url: route,
        method: "GET",
        success: function(response) {
            console.log("Produits reçus :", response.data);
            // alert(JSON.stringify(response));
            displayProducts(response, form); // 🔥 afficher directement
        },
        error: function(err) {
            console.error("Erreur AJAX :", err);
            alert(JSON.stringify(err))
        }
    });
}

function getDealerList(form) {
    const shopInput = document.querySelector("#"+form+" .shopmatricule");
    const shopmatricule = shopInput?.value ?? 'shopmatricule';

    const meta = $('meta[name="route-store-get-dealer-list"]');
    if (!meta.length) {
        console.error("Meta route introuvable !");
        return;
    }

    let route = meta.attr("content");
    if (!route) {
        console.error("Contenu du meta vide !");
        return;
    }

    route = route.replace("__SHOPNAME__", encodeURIComponent(shopmatricule));

    // Appel AJAX
    $.ajax({
        url: route,
        method: "GET",
        success: function(response) {
            console.log("Produits reçus :", response.data);
            // alert(JSON.stringify(response.data));
            displayDealers(response.data, form); // 🔥 afficher directement
        },
        error: function(err) {
            // alert(JSON.stringify(err))
            console.error("Erreur AJAX :", err);
        }
    });
}

function gettindEverything(element) {
    const shopInput = document.querySelector("#bloc-add-new-produit .shopmatricule");
    const shopmatricule = shopInput?.value ?? 'shopmatricule';

    const meta = $('meta[name="route-store-get-everything"]');
    if (!meta.length) {
        console.error("Meta route introuvable !");
        return;
    }

    let route = meta.attr("content");
    if (!route) {
        console.error("Contenu du meta vide !");
        return;
    }

    route = route.replace("__SHOPMATRICULE__", encodeURIComponent(shopmatricule));
    route = route.replace("__THING__", encodeURIComponent(element));
    // alert(shopmatricule)

    // Appel AJAXresponse
    $.ajax({
        url: route,
        method: "GET",
        success: function(response) {
            console.log("Produits reçus :", response.data);
            // alert(JSON.stringify(response.data));
            if(element === 'factures'){
                displayFacture(response.data, response.type_structure)
            }else if(element === 'fournisseurs'){
                displayFournisseur(response.data, "#bloc-view-"+element+" table tbody");
            }else if(element === "produits"){
                displayProduits(response.dataAll, response.dataOn, response.type_structure, "#bloc-view-"+element);
            }else{
                displaySellerManager(response.data, "#bloc-view-"+element+" table tbody");
            }
        },
        error: function(err) {
            alert(JSON.stringify(err))
            console.error("Erreur AJAX :", err);
        }
    });
}

function gettingElementForUpdating(bloc, matricule, element) {
    return new Promise((resolve, reject) => {

        const meta = $('meta[name="route-store-get-element-for-update"]');
        if (!meta.length) {
            reject("Meta route introuvable !");
            return;
        }

        let route = meta.attr("content");
        if (!route) {
            reject("Contenu du meta vide !");
            return;
        }

        route = route.replace("__MATRICULE__", encodeURIComponent(matricule));
        route = route.replace("__ELEMENT__", element);

        $.ajax({
            url: route,
            method: "GET",
            success: function(response) {
                // alert(JSON.stringify(response));
                if(document.querySelector("#"+bloc+" #loader")){
                    document.querySelector("#"+bloc+" #loader").classList.add('hidden');
                }
                resolve(response); // 👈 tu renvoies bien la réponse ici
            },
            error: function(err) {
                alert(JSON.stringify(err));
                reject(err);
            }
        });
    });
}

function createProductBlock(nom, matricule, codebarre) {
    index_value++;
    document.querySelector("#index-facture").value = index_value;
    compteur++;

    const container = document.createElement('div');
    container.className = "flex justify-evenly product-input-group";

    container.innerHTML = `
        <div class="flex gap-1 w-full m-1">
            <div class="input-group hidden">
            <input type="text" class="bg-gray-100 w-full h-7 px-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-200 outline-none text-[13p]"
                id="product_matricule${compteur}" name="product_matricule${compteur}" value="${matricule}" placeholder="">
            <input type="text" class="bg-gray-100 w-full h-7 px-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-200 outline-none text-[13p]"
                id="code_barre${compteur}" name="code_barre${compteur}" value="${codebarre}" placeholder="">
            </div>

            <div class="input-group text-[13px] w-full">
                <input type="text" class="bg-gray-100 w-full h-7 px-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-200 outline-none text-[13p]"
                    id="nom_produit${compteur}" name="nom_produit${compteur}" value="${nom}" placeholder="Nom du produit" required>
                <span id="nom_produit${compteur}-error" class="text-red-600 text-sm mt-1 block text-[10px] error-message"></span>

            </div>

            <div class="input-group text-[13px]">
                <input type="numeric" class="bg-gray-100 w-full h-7 px-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-200 outline-none text-[13p]"
                    id="quantite${compteur}" name="quantite${compteur}" placeholder="Quantite" required>
                <span id="quantite${compteur}-error" class="text-red-600 text-sm mt-1 block text-[10px] error-message"></span>

            </div>

            <div class="input-group text-[13px]">
                <input type="mumeric" class="bg-gray-100 max-w-[100px] h-7 px-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-200 outline-none text-[13p]"
                    id="prix_unitaire${compteur}" name="prix_unitaire${compteur}" placeholder="Prix unitaire" required>
                <span id="prix_unitaire${compteur}-error" class="text-red-600 text-sm mt-1 block text-[10px] error-message"></span>

            </div>

            <div class="input-group text-[13px] flex items-center text-center" title="Charger des photos du produit">
                <input type="file"
                class="input-add-images-multiple hidden bg-gray-100 w-full h-7 px-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-200 outline-none text-[13px]"
                id="input-add-images-multiple${compteur}"
                name="input-add-images-multiple${compteur}[]"
                multiple
                accept="image/*">
                <span id="nombre-photo${compteur}" class="text-[10px] m-[2px]"></span>
                <i id="icon-add-images-multiple${compteur}" class="icon-add-images-multiple fa fa-image text-[20px] cursor-pointer"></i>
                <span id="input-add-images-multiple${compteur}-error" class="text-red-600 text-sm mt-1 block text-[10px] error-message"></span>

            </div>

            <div class="trash cursor-pointer"><i class="fa fa-trash text-red-300 hover:text-red-600 ml-1"></i></div>
        </div>
    `;
    return container;
}

let compteur = 0;
let index_value = 0;
function createProductBlockTransformation(nom, matricule) {
    index_value++;
    document.querySelector("#index-facture").value = index_value;
    compteur++;

    const container = document.createElement('div');
    container.className = "flex justify-evenly product-input-group";

    container.innerHTML = `
        <div class="flex gap-1 w-full m-1">
            <div class="input-group hidden">
            <input type="text" class="bg-gray-100 w-full h-7 px-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-200 outline-none text-[13p]"
                id="ingredient_matricule${compteur}" name="ingredient_matricule${compteur}" value="${matricule}" placeholder="">

            </div>

            <div class="input-group text-[13px] w-full">
                <input type="text" class="bg-gray-100 w-full h-7 px-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-200 outline-none text-[13p]"
                    id="nom_ingredient${compteur}" name="nom_ingredient${compteur}" value="${nom}" placeholder="Nom du produit" required>
                <span id="nom_ingredient${compteur}-error" class="text-red-600 text-sm mt-1 block text-[10px] error-message"></span>

            </div>

            <div class="input-group text-[13px]">
                <input type="numeric" class="bg-gray-100 w-full h-7 px-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-200 outline-none text-[13p]"
                    id="quantite${compteur}" name="quantite${compteur}" placeholder="Quantite" required>
                <span id="quantite${compteur}-error" class="text-red-600 text-sm mt-1 block text-[10px] error-message"></span>

            </div>

            <div class="input-group text-[13px]">
                <input type="mumeric" class="bg-gray-100 max-w-[100px] h-7 px-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-200 outline-none text-[13p]"
                    id="prix_unitaire${compteur}" name="prix_unitaire${compteur}" placeholder="Prix unitaire" required>
                <span id="prix_unitaire${compteur}-error" class="text-red-600 text-sm mt-1 block text-[10px] error-message"></span>

            </div>

            <div class="input-group text-[13px] flex items-center text-center" title="Charger des photos du produit">
                <input type="file"
                class="input-add-images-multiple hidden bg-gray-100 w-full h-7 px-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-200 outline-none text-[13px]"
                id="input-add-images-multiple${compteur}"
                name="input-add-images-multiple${compteur}[]"
                multiple
                accept="image/*">
                <span id="nombre-photo${compteur}" class="text-[10px] m-[2px]"></span>
                <i id="icon-add-images-multiple${compteur}" class="icon-add-images-multiple fa fa-image text-[20px] cursor-pointer"></i>
                <span id="input-add-images-multiple${compteur}-error" class="text-red-600 text-sm mt-1 block text-[10px] error-message"></span>

            </div>

            <div class="trash cursor-pointer"><i class="fa fa-trash text-red-300 hover:text-red-600 ml-1"></i></div>
        </div>
    `;
    return container;
}

function displayProducts(products, form) {
    let container;

// Sélection du bon conteneur selon le type de boutique
    if (products.shop_type === 'revente') {
        container = droite.querySelector("#bloc-form-revente-type #bloc-add-product-bill #product-list");
    } else if (products.shop_type === 'transformation') {
        container = droite.querySelector("#bloc-form-transformation-type #bloc-add-product-fees #product-list");
    }

// Barre de recherche
    const search = document.createElement('div');
    search.classList.add(
        'w-[75%]', 'outline-none', 'rounded-md', 'items-center',
        'shadow-md', 'h-auto', 'p-2', 'flex', 'relative', 'bg-white'
    );
    search.innerHTML = `
    <input type="text" placeholder="Filtrer ..." class="outline-none text-[14px] w-[100%] h-[30px] p-1">
    <i class="fa fa-search absolute right-[10px]"></i>
`;

// Conteneur des produits
    const container_products = document.createElement('div');
    container_products.classList.add(
        'w-full', 'h-auto', 'p-2', 'flex', 'items-center', 'justify-center', 'gap-2', 'flex-col'
    );

// Réinitialiser le conteneur
    container.innerHTML = "";
    container.appendChild(search);
    container.appendChild(container_products);

// On récupère le champ input correctement
    const inputSearch = search.querySelector("input");

// ✅ Événement de filtrage
    inputSearch.addEventListener('input', function () {
        const searchValue = inputSearch.value.toLowerCase().trim();

        // Récupérer tous les produits affichés
        const productElements = container_products.querySelectorAll("[class^='bill-products']");

        productElements.forEach(prod => {
            const nameElement = prod.querySelector("[class^='nom']");
            const productName = nameElement ? nameElement.textContent.toLowerCase() : '';

            // Masquer ou afficher selon la recherche
            if (productName.includes(searchValue)) {
                prod.classList.remove('hidden');
            } else {
                prod.classList.add('hidden');
            }
        });
    });


    if (products.data.length  === 0){
        const card = document.createElement("div");
        card.innerHTML = `
            <p class="text-gray-500 text-[13px] items-center text-center">Aucun produit disponible pour votre boutique.</p>
        `
        container.appendChild(card);
    }else{
        let cmt = 0;
        if (products.shop_type === "revente"){
            products.data.forEach(prod => {
                const card = document.createElement("div");
                card.classList.add("product-card", 'w-[80%]', 'h-auto', 'bg-white', 'rounded-md');

                card.innerHTML = `
            <div id="bill-products${cmt}" title="Cliquer pour ajouter" class="bill-products${cmt} flex w-full justify-between shadow gap-1 cursor-pointer p-1">
                <img src="/storage/${prod.image}" alt="${prod.nom_produit}" class="w-full text-[13px] basis-[20%] h-[80px] rounded-md">
                <h4 id="nom${cmt}" class="nom${cmt} text-[13px] basis-[35%]">${prod.nom_produit}</h4>
                <p id="description${cmt}" class="text-[13px] basis-[45%]">${prod.description ?? ''}</p>
                <p id="product_matricule${cmt}" class="hidden text-[13px] basis-[45%]">${prod.product_matricule ?? ''}</p>
                <p id="code_barre${cmt}" class="hidden text-[13px] basis-[45%]">${prod.code_barre ?? ''}</p>
                <p id="nom_produit${cmt}" class="hidden text-[13px] basis-[45%]">${prod.nom_produit ?? ''}</p>
            </div>
        `;
                cmt++;
                container_products.appendChild(card)
                container.appendChild(container_products);

                const newBlock = createProductBlock(prod.nom_produit, prod.product_matricule, prod.code_barre);
                card.addEventListener('click', function (e) {
                    document.querySelector('#'+form+" #espace-produit").appendChild(newBlock);
                    droite.querySelector("#bloc-add-product-bill").classList.add('hidden', '-translate-x-[150%]');
                })
                droite.querySelector("#bloc-add-product-bill").addEventListener('click', function (e) {
                    // alert(e.target.id)
                    if (e.target.id === "bloc-add-product-bill"){
                        droite.querySelector("#bloc-add-product-bill").classList.add('hidden', '-translate-x-[150%]');
                    }
                })

                newBlock.querySelector(".trash").addEventListener('click', function () {
                    newBlock.remove();
                })
                // 1. Ajouter le gestionnaire de clic pour l'icône image
                const iconInput = newBlock.querySelector("[id^='icon-add-images-multiple']");
                const fileInput = newBlock.querySelector("[id^='input-add-images-multiple']");

                if (iconInput && fileInput) {
                    iconInput.addEventListener('click', () => {
                        fileInput.click();
                    });

                    // 2. Gérer la mise à jour du nombre de fichiers sélectionnés
                    fileInput.addEventListener('change', () => {
                        updateImageCount(fileInput);
                    });
                }
            });
        }
        else if (products.shop_type === "transformation"){
            products.data.forEach(prod => {
                const card = document.createElement("div");
                card.classList.add("product-card", 'w-[80%]', 'h-auto', 'bg-white', 'rounded-md');
                card.innerHTML = `

            <div id="bill-products${cmt}" title="Cliquer pour ajouter" class="bill-products${cmt} flex w-full justify-between shadow gap-1 cursor-pointer p-1 text-center items-center">
                <img src="/storage/${prod.image}" alt="${prod.nom}" class="text-[13px] w-[100px] h-[80px] rounded-md">
                <h4 id="nom${cmt}" class="nom${cmt} text-[13px] basis-[35%]">${prod.nom}</h4>
                <p id="description${cmt}" class="text-[13px] basis-[45%]">${prod.description ?? ''}</p>
                <p id="product_matricule${cmt}" class="hidden text-[13px] basis-[45%]">${prod.ingredient_matricule ?? ''}</p>

            </div>
        `;
                cmt++;
                container_products.appendChild(card);

                const newBlock = createProductBlockTransformation(prod.nom, prod.ingredient_matricule);
                card.addEventListener('click', function (e) {
                    document.querySelector('#'+form+" #espace-depense").appendChild(newBlock);
                    droite.querySelector("#bloc-add-product-fees").classList.add('hidden', '-translate-x-[150%]');
                })


                newBlock.querySelector(".trash").addEventListener('click', function () {
                    newBlock.remove();
                })
                // 1. Ajouter le gestionnaire de clic pour l'icône image
                const iconInput = newBlock.querySelector("[id^='icon-add-images-multiple']");
                const fileInput = newBlock.querySelector("[id^='input-add-images-multiple']");

                if (iconInput && fileInput) {
                    iconInput.addEventListener('click', () => {
                        fileInput.click();
                    });

                    // 2. Gérer la mise à jour du nombre de fichiers sélectionnés
                    fileInput.addEventListener('change', () => {
                        updateImageCount(fileInput);
                    });
                }
            });
            droite.querySelector("#bloc-add-product-fees").addEventListener('click', function (e) {
                // alert(e.target.id)
                if (e.target.id === "bloc-add-product-fees"){
                    droite.querySelector("#bloc-add-product-fees").classList.add('hidden', '-translate-x-[150%]');
                }
            })
        }

    }

}

function updateImageCount(inputElement) {
    const span = inputElement.parentElement.querySelector("[id^='nombre-photo']");
    if (span) {
        span.textContent = inputElement.files.length;
    }
}

function displayDealers(products, form) {
    const container = document.querySelector('#'+form+' #bloc-select-fournisseur select');
    // alert(JSON.stringify(products))
    container.innerHTML = ""; // vider avant d'ajouter
    if (products.length  === 0){
        const card = document.createElement("option");
        card.innerText = 'Aucun fournisseur trouve';
        card.value = "";
        container.appendChild(card);
    }else{
        let cmt = 0;
        const card = document.createElement("option");
        card.innerText = 'Fournisseurs';
        card.value = "";
        container.appendChild(card);
        products.forEach(prod => {
            const card = document.createElement("option");
            card.classList.add("product-card");

            card.innerText = prod.nom_fournisseur;
            card.value = prod.fournisseur_matricule;
            cmt++;
            container.appendChild(card);
        });
    }

}

function inputBillResend() {
    const container = document.querySelector('#bloc-boutique-container #bloc-add-new-facture');
    let blocAddNewFacture = document.createElement('div');
    blocAddNewFacture.id = "bloc-form-revente-type";
    container.innerHTML = '';
    blocAddNewFacture.innerHTML=`
                <form id="form-add-new-facture" class="bg-white px-8 py-6 rounded-lg shadow-lg w-full z-10">
                @csrf

                <!-- Titre -->
                    <h2 class="text-center text-2xl font-bold text-gray-700 mb-6">Nouvelle facture revente</h2>

                    <!-- Message global -->
                    <div id="global-message-facture" class="hidden text-green-700 font-bold mb-4 text-center bg-green-50 p-1 rounded border border-green-300">
                    </div>

                    <!-- Numéro de facture -->
                    <div class="flex flex-wrap w-full justify-evenly gap-2">
                        <div class="min-w-[75px] mb-4 text-[13px] flex flex-col max-w-[175px]">
                            <label for="numero_facture" class="w-full block mb-2 font-semibold text-gray-600">Numéro de facture</label>
                            <div>
                                <input id="numero_facture" type="text" name="numero_facture" value=""
                                       class="text-[12px] w-full h-7 px-3 border border-gray-300 rounded-md focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none text-sm">
                                <span id="numero_facture-error" class="text-red-600 text-sm mt-1 block text-[10px] error-message"></span>
                            </div>
                        </div>

                        <!-- Date facture -->
                        <div class="min-w-[75px] mb-4 text-[13px] flex flex-col max-w-[130px]">
                            <label for="date_facture" class="w-full ock mb-2 font-semibold text-gray-600">Date de la facture</label>
                            <div>
                                <input id="date_facture" type="date" name="date_facture" value=""
                                       class="text-[12px] w-full h-7 px-3 border border-gray-300 rounded-md focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none text-sm">
                                <span id="date_facture-error" class="text-red-600 text-sm mt-1 block text-[10px] error-message"></span>
                            </div>
                        </div>

                        <!-- Fournisseur -->
                        <div class="min-w-[75px] mb-4 text-[13px] flex flex-col max-w-[175px]">
                            <label for="fournisseur" class="w-full ck mb-2 font-semibold text-gray-600">Fournisseur</label>
                            <div id="bloc-select-fournisseur">
                                <select name="fournisseur" id="fournisseur" class="text-[12px] w-full h-7 px-3 border border-gray-300 rounded-md focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none text-sm">
                                    <option value="">Fournisseurs</option>
                                </select>
                                <span id="fournisseur-error" class="text-red-600 text-sm mt-1 block text-[10px] error-message"></span>
                            </div>
                        </div>

                        <!-- Somme -->
                        <div class="min-w-[75px] mb-4 text-[13px] flex flex-col max-w-[150px]">
                            <label for="total_facture" class="w-full lock mb-2 font-semibold text-gray-600">Somme</label>
                            <div>
                                <input id="total_facture" type="number" name="total_facture" value=""
                                       class="text-[12px] w-full h-7 px-3 border border-gray-300 rounded-md focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none text-sm">
                                <span id="total_facture-error" class="text-red-600 text-sm mt-1 block text-[10px] error-message"></span>
                            </div>
                        </div>
                        <div id="bloc-charger-facture" title="Charger la facture" class="justify-center items-center min-w-[30px] mb-4 text-[13px] flex flex-col max-w-[30px]">
                            <label for="facture" class="relative left-[-10px] w-full mb-2 font-semibold text-gray-600">Facture</label>
                            <div class="hidden">
                                <input id="facture" type="file" name="facture" value=""
                                       class="text-[12px] w-full h-7 px-3 border border-gray-300 rounded-md focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none text-sm">
                            </div>
                            <i id="facture-icon" class="fa fa-file fa-2x cursor-pointer"></i>
                            <span id="facture-error" class="text-red-600 text-sm mt-1 block text-[10px] error-message"></span>
                        </div>

                        <div class="min-w-[75px] mb-4 text-[13px] flex flex-col max-w-[75px]">
                            <label for="marge" class="w-full lock mb-2 font-semibold text-gray-600">Marge</label>
                            <div>
                                <input id="marge" type="number" name="marge" value="1.25"
                                       class="text-[12px] w-full h-7 px-3 border border-gray-300 rounded-md focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none text-sm">
                                <span id="marge-error" class="text-red-700 text-sm mt-1 block text-[10px] error-message"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Espace produits -->
                    <fieldset id="espace-produit" class="mb-4 border border-gray-300 p-3 rounded-md tex-[13px]">
                        <legend class="font-semibold text-black-600 text-[14px] font-bold">Détails des produits</legend>
                        <button id="new-produit-bill" type="button"
                                class="text-[13px] cursor-pointer mt-1 mb-1 bg-green-600 hover:bg-green-700 text-white py-1 px-3 rounded-md text-sm font-semibold transition duration-300">
                            Ajouter un produit
                        </button>
                    </fieldset>

                    <!-- Index facture (peut être caché si technique) -->
                    <input type="hidden" id="index-facture" name="index-facture" value="">

                    <!-- Bouton submit -->
                    <button type="submit"
                            class="cursor-pointer w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-md text-lg font-semibold transition duration-300">
                        Soumettre
                    </button>

                </form>
                <div id="bloc-add-product-bill" title="Clicker pour fermer"
                     class="fixed inset-0 bg-black/70 hidden transform transition duration-300 z-[9999] grid place-items-center">
                    <div id="product-list"
                         class="w-[75%] h-auto px-4 py-7 bg-white rounded-md shadow-lg grid place-items-center gap-2"></div>
                </div>
`
    container.appendChild(blocAddNewFacture);

}

function inputBillTransformation() {
    const container = document.querySelector('#bloc-boutique-container #bloc-add-new-facture');
    let blocAddNewFacture = document.createElement('div');
    blocAddNewFacture.id = "bloc-form-transformation-type";
    container.innerHTML = '';
    blocAddNewFacture.innerHTML=`
                <form id="form-add-new-facture-transformation" class="bg-white px-8 py-6 rounded-lg shadow-lg w-full">
                @csrf

                <!-- Titre -->
                    <h2 class="text-center text-2xl font-bold text-gray-700 mb-6">Nouvelle facture transformation</h2>

                    <!-- Message global -->
                    <div id="global-message-facture" class="hidden text-green-700 font-bold mb-4 text-center bg-green-50 p-1 rounded border border-green-300">
                    </div>

                    <!-- Numéro de facture -->
                    <div class="flex flex-wrap w-full justify-evenly gap-2">
                        <div class="min-w-[75px] mb-4 text-[13px] flex flex-col max-w-[175px]">
                            <label for="numero_facture" class="w-full block mb-2 font-semibold text-gray-600">Numéro de facture</label>
                            <div>
                                <input id="numero_facture" type="text" name="numero_facture" value=""
                                       class="text-[12px] w-full h-7 px-3 border border-gray-300 rounded-md focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none text-sm">
                                <span id="numero_facture-error" class="text-red-600 text-sm mt-1 block text-[10px] error-message"></span>
                            </div>
                        </div>

                        <!-- Date facture -->
                        <div class="min-w-[75px] mb-4 text-[13px] flex flex-col max-w-[130px]">
                            <label for="date_facture" class="w-full ock mb-2 font-semibold text-gray-600">Date de la facture</label>
                            <div>
                                <input id="date_facture" type="date" name="date_facture" value=""
                                       class="text-[12px] w-full h-7 px-3 border border-gray-300 rounded-md focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none text-sm">
                                <span id="date_facture-error" class="text-red-600 text-sm mt-1 block text-[10px] error-message"></span>
                            </div>
                        </div>

                        <!-- Somme -->
                        <div class="min-w-[75px] mb-4 text-[13px] flex flex-col max-w-[150px]">
                            <label for="total_facture" class="w-full lock mb-2 font-semibold text-gray-600">Somme</label>
                            <div>
                                <input id="total_facture" type="number" name="total_facture" value=""
                                       class="text-[12px] w-full h-7 px-3 border border-gray-300 rounded-md focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none text-sm">
                                <span id="total_facture-error" class="text-red-600 text-sm mt-1 block text-[10px] error-message"></span>
                            </div>
                        </div>
                        <div id="bloc-charger-facture" title="Charger la facture" class="justify-center items-center min-w-[30px] mb-4 text-[13px] flex flex-col max-w-[30px]">
                            <label for="facture" class="relative left-[-10px] w-full mb-2 font-semibold text-gray-600">Facture</label>
                            <div class="hidden">
                                <input id="facture" type="file" name="facture" value=""
                                       class="text-[12px] w-full h-7 px-3 border border-gray-300 rounded-md focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none text-sm">
                            </div>
                            <i id="facture-icon" class="fa fa-file fa-2x cursor-pointer"></i>
                            <span id="facture-error" class="text-red-700 text-sm mt-1 block text-[10px] error-message"></span>
                        </div>

                    </div>

                    <!-- Espace produits -->
                    <fieldset id="espace-depense" class="mb-4 border border-gray-300 p-3 rounded-md tex-[13px]">
                        <legend class="font-semibold text-black-600 text-[14px] font-bold">Détails des depenses</legend>
                        <button id="new-depense-bill" type="button"
                                class="text-[13px] cursor-pointer mt-1 mb-1 bg-green-600 hover:bg-green-700 text-white py-1 px-3 rounded-md text-sm font-semibold transition duration-300">
                            Ajouter une depense
                        </button>
                    </fieldset>

                    <!-- Index facture (peut être caché si technique) -->
                    <input type="hidden" id="index-facture" name="index-facture" value="">

                    <!-- Bouton submit -->
                    <button type="submit"
                            class="cursor-pointer w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-md text-lg font-semibold transition duration-300">
                        Soumettre
                    </button>
                </form>

                <div id="bloc-add-product-fees" title="Clicker pour fermer"
                     class="fixed inset-0 bg-black/70 hidden w-full h-auto transform transition duration-300 z-[9999] grid place-items-center">

                    <div id="product-list"
                         class="overflow-auto bg-gray-100 grid w-[75%] h-auto px-4 py-7 rounded-md shadow-lg grid place-items-center gap-2">

                    </div>
                </div>
`
    container.appendChild(blocAddNewFacture);


}


//---------------  STATS ----------------------

function chargerBoutiqueStats() {
    if (!document.querySelector("#bloc-stats")) {
        alert('stats inexistant stats');
        document.querySelector("#parametre").click();
        return; // stoppe ici l'exécution de la fonction
    }
    // Récupérer l’URL de la route depuis le <meta>
    let url = $('meta[name="route-store-get-shop"]').attr("content");

    // Requête GET vers le serveur
    $.get(url, function (data) {
        console.log("Boutiques reçues :", data);

        // Affiche le message renvoyé par Laravel
        // alert(JSON.stringify(data.data));

        let cmp = 0;
        let blocShopCard = document.createElement("div");
        blocShopCard.classList.add("flex");
        blocShopCard.id = "bloc-graphes-stats";
        data.data.forEach(function(element) {
            for (let shop of element){
                // alert(JSON.stringify(shop));
                // alert(JSON.stringify(shop.type_structure));
                // Créer un bloc <div>
                let shopCard = document.createElement("div");
                shopCard.classList.add("shop-card"); // tu peux styliser ça en CSS
                // Remplir le HTML interne
                shopCard.innerHTML = `
               <div id="boutique${cmp}"  class="m-2 cursor-pointer w-[150px] h-[155px] rounded-t-[7px] shadow-md bg-white text-[14px]">
                    <img src="/storage/${shop.logo}" alt="Logo de la boutique" class="w-full h-[75%] p-[3px] rounded-t-[7px]">
                   <h3 class=" text-[13px]">${shop.nom_structure}</h3>
                   <span class=" text-[13px] hidden">${shop.structure_matricule}</span>
                   <p class=" text-[13px] hidden">${shop.type_structure}</p>
               </div>`;
                // Ajouter dans le container
                blocShopCard.appendChild(shopCard);
                shopCard.addEventListener('click', function () {
                    loadDataforGraph(shop.structure_matricule);
                    document.querySelector("#bloc-stats").innerHTML = '';
                });
                if(data.data.length === 1){
                    shopCard.click();
                }
                cmp ++;
            }
        });

        document.querySelector("#bloc-stats").appendChild(blocShopCard);

    }).fail(function (xhr) {
        console.error("Erreur :", xhr.responseText);
        // alert("Veuillez proceder a la creation de votre premiere Boutique !");
    });
}

function loadDataforGraph(matricule_structure) {
    let url = $('meta[name="route-store-get-shop-sold-data"]').attr("content");
    url = url.replace('__SHOPMATRICULE__', matricule_structure);

    // Requête GET vers le serveur
    $.get(url, function (data) {
        console.log("Boutiques reçues :", data);
        // alert(JSON.stringify(data.data));
        renderSalesCharts(data.data);

    }).fail(function (xhr) {
        console.error("Erreur :", xhr.responseText);
        alert(xhr.responseText);
        // alert("Veuillez proceder a la creation de votre premiere Boutique !");
    });
}

function renderSalesCharts(data) {
    const container = document.getElementById('bloc-stats');
    container.innerHTML = ''; // reset

    // 1. Aplatir et filtrer les données valides
    const flatData = data
        .flat()
        .filter(item => item && item.created_at && item.nom_produit) // ignore null
        .map(item => ({
            ...item,
            quantite: parseInt(item.quantite) || 0,
            total: parseFloat(item.total) || 0,
            date: item.created_at.split("T")[0]
        }));

    if (flatData.length === 0) {
        container.innerHTML = "<p>Aucune donnée valide à afficher.</p>";
        return;
    }

    // 2. Déterminer la granularité
    const dates = flatData.map(d => new Date(d.date));
    const minDate = new Date(Math.min(...dates));
    const maxDate = new Date(Math.max(...dates));
    const diffDays = Math.ceil((maxDate - minDate) / (1000 * 60 * 60 * 24));

    let mode = "jour";
    if (diffDays >= 60) mode = "mois";
    else if (diffDays >= 7) mode = "semaine";

    function getKey(date) {
        const d = new Date(date);
        if (mode === "jour") return d.toISOString().split("T")[0];
        else if (mode === "semaine") {
            const firstDay = new Date(d);
            firstDay.setDate(d.getDate() - d.getDay()); // début semaine
            return firstDay.toISOString().split("T")[0] + " (sem)";
        } else if (mode === "mois") {
            return d.getFullYear() + "-" + String(d.getMonth() + 1).padStart(2, "0");
        }
    }

    // 3. Grouper par produit + période
    const groupedByProduct = {};
    flatData.forEach(item => {
        const produit = item.nom_produit;
        const key = getKey(item.date);

        if (!groupedByProduct[produit]) groupedByProduct[produit] = {};
        if (!groupedByProduct[produit][key]) groupedByProduct[produit][key] = 0;

        groupedByProduct[produit][key] += item.quantite;
    });

    // 4. Générer un graphe par produit
    Object.keys(groupedByProduct).forEach(produit => {
        const canvas = document.createElement('canvas');
        canvas.classList.add('w-[400px]', 'h-[250px]', 'bg-white', 'shadow-md', 'gap-2', 'rounded-md')
        canvas.addEventListener('click', function () {
            alert('On y travaille ...')
        })
        container.appendChild(canvas);

        const labels = Object.keys(groupedByProduct[produit]).sort();
        const quantites = labels.map(l => groupedByProduct[produit][l]);

        new Chart(canvas, {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label: `Quantité vendue - ${produit}`,
                    data: quantites,
                    borderColor: 'rgba(54, 162, 235, 0.9)',
                    backgroundColor: 'rgba(54, 162, 235, 0.4)',
                    fill: true
                }]
            },
            options: {
                responsive: false,
                plugins: {
                    title: { display: true, text: `Évolution des ventes de ${produit}` }
                }
            }
        });
    });

    // 5. Graphe global cumulé par période
    const groupedGlobal = {};
    flatData.forEach(item => {
        const key = getKey(item.date);
        if (!groupedGlobal[key]) groupedGlobal[key] = 0;
        groupedGlobal[key] += item.total;
    });

    const globalCanvas = document.createElement('canvas');
    globalCanvas.classList.add('w-[400px]', 'h-[250px]', 'bg-white', 'shadow-md', 'rounded-md')
    globalCanvas.addEventListener('click', function () {
        alert('On y travaille ... aussi !')
    })
    container.appendChild(globalCanvas);

    const globalLabels = Object.keys(groupedGlobal).sort();
    const globalValues = globalLabels.map(l => groupedGlobal[l]);

    new Chart(globalCanvas, {
        type: 'line',
        data: {
            labels: globalLabels,
            datasets: [{
                label: 'Ventes cumulées (FCFA)',
                data: globalValues,
                borderColor: 'rgba(255, 99, 132, 0.9)',
                backgroundColor: 'rgba(255, 99, 132, 0.4)',
                fill: true
            }]
        },
        options: {
            responsive: false,
            plugins: {
                title: { display: true, text: 'Ventes cumulées' }
            }
        }
    });
}

function viderStats(){
    if (document.querySelector("#bloc-graphes-stats")){
        document.querySelector("#bloc-graphes-stats").classList.add('hissen');
    }
    document.querySelector("#bloc-stats").innerHTML ='';

}


//------------------ COMPTA --------------------

function chargerBoutiquesCompta() {
    if (!document.querySelector("#bloc-stats")) {
        alert('stats inexistant compta');
        document.querySelector("#parametre").click();
        return; // stoppe ici l'exécution de la fonction
    }
    // Récupérer l’URL de la route depuis le <meta>
    let url = $('meta[name="route-store-get-shop"]').attr("content");

    // Requête GET vers le serveur
    $.get(url, function (data) {
        console.log("Boutiques reçues :", data);

        // Affiche le message renvoyé par Laravel
        // alert(JSON.stringify(data.data));

        let cmp = 0;
        let blocShopCard = document.createElement("div");
        blocShopCard.classList.add("flex");
        blocShopCard.id = "bloc-graphes-compta";
        data.data.forEach(function(element) {
            for (let shop of element){
                // alert(JSON.stringify(shop));
                // alert(JSON.stringify(shop.type_structure));
                // Créer un bloc <div>
                let shopCard = document.createElement("div");
                shopCard.classList.add("shop-card"); // tu peux styliser ça en CSS
                // Remplir le HTML interne
                shopCard.innerHTML = `
               <div id="boutique${cmp}"  class="m-2 cursor-pointer w-[150px] h-[155px] rounded-t-[7px] shadow-md bg-white text-[14px]">
                    <img src="/storage/${shop.logo}" alt="Logo de la boutique" class="w-full h-[75%] p-[3px] rounded-t-[7px]">
                   <h3 class=" text-[13px]">${shop.nom_structure}</h3>
                   <span class=" text-[13px] hidden">${shop.structure_matricule}</span>
                   <p class=" text-[13px] hidden">${shop.type_structure}</p>
               </div>`;
                // Ajouter dans le container
                blocShopCard.appendChild(shopCard);
                shopCard.addEventListener('click', function () {
                    loadDataforGraphCompta(shop.structure_matricule);
                    document.querySelector("#bloc-comptabilite").innerHTML = '';
                });
                if(data.data.length === 1){
                    shopCard.click();
                }
                cmp ++;
            }
        });

        document.querySelector("#bloc-comptabilite").appendChild(blocShopCard);

    }).fail(function (xhr) {
        console.error("Erreur :", xhr.responseText);
        // alert("Veuillez proceder a la creation de votre premiere Boutique !");
    });
}

function loadDataforGraphCompta(matricule){
    let url = $('meta[name="route-store-get-shop-data-sold-fees"]').attr("content");
    url = url.replace('__SHOPMATRICULE__', matricule);


    // Requête GET vers le serveur
    $.get(url, function (data) {
        console.log("Boutiques reçues :", data);
        // alert(JSON.stringify(data));
        renderFinanceCharts(data);

    }).fail(function (xhr) {
        console.error("Erreur :", xhr.responseText);
        // alert("Veuillez proceder a la creation de votre premiere Boutique !");
    });
}

function renderFinanceCharts(data) {
    const container = document.getElementById('bloc-comptabilite');
    container.innerHTML = ''; // reset

    // Helpers
    function formatDate(iso) {
        return iso.split("T")[0];
    }

    // 1. Préparer les données fees
    const fees = (data.fees || []).map(item => ({
        montant: parseFloat(item.total_facture) || 0,
        date: formatDate(item.created_at)
    }));

    // 2. Préparer les données solds
    const solds = (data.solds || []).map(item => ({
        montant: parseFloat(item.total) || 0,
        date: formatDate(item.created_at)
    }));

    // 3. Grouper par jour
    function groupByDate(list) {
        return list.reduce((acc, item) => {
            if (!acc[item.date]) acc[item.date] = 0;
            acc[item.date] += item.montant;
            return acc;
        }, {});
    }

    const groupedFees = groupByDate(fees);
    const groupedSolds = groupByDate(solds);

    // 4. Fusionner toutes les dates présentes
    const allDates = Array.from(
        new Set([...Object.keys(groupedFees), ...Object.keys(groupedSolds)])
    ).sort();

    // 5. Construire les séries
    const feesSeries = allDates.map(d => groupedFees[d] || 0);
    const soldsSeries = allDates.map(d => groupedSolds[d] || 0);
    const profitSeries = allDates.map((d, i) => soldsSeries[i] - feesSeries[i]);

    // 6. Générer les graphes
    function createChart(labels, values, label, color, title) {
        const canvas = document.createElement('canvas');
        canvas.classList.add('w-[400px]', 'h-[250px]', 'bg-white', 'shadow-md', 'rounded-md', 'p-2', 'm-2');
        container.appendChild(canvas);

        new Chart(canvas, {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label,
                    data: values,
                    borderColor: color,
                    backgroundColor: color.replace("0.9", "0.4"),
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: false,
                plugins: {
                    title: { display: true, text: title }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        grid: { color: 'rgba(200,200,200,0.2)' }
                    }
                }
            }
        });
    }

    // Graphe 1 : Dépenses
    createChart(allDates, feesSeries, "Dépenses (FCFA)", "rgba(255,99,132,0.9)", "Évolution des dépenses");

    // Graphe 2 : Ventes
    createChart(allDates, soldsSeries, "Ventes (FCFA)", "rgba(54,162,235,0.9)", "Évolution des ventes");

    // Graphe 3 : Rentabilité
    createChart(allDates, profitSeries, "Profit (FCFA)", "rgba(75,192,192,0.9)", "Rentabilité (ventes - dépenses)");
}

function viderCompta(){
    if (document.querySelector("#bloc-graphes-compta")){
        document.querySelector("#bloc-graphes-compta").classList.add('hidden');
    }
    document.querySelector("#bloc-comptabilite").innerHTML ='';

}


//------------------ PARAMETRE ------------------------

const bloc_parametre = document.querySelector('#bloc-parametre');

bloc_parametre.addEventListener('click', function (e) {
    const liste = {
        '#boutique-param': "#bloc-option-param-boutique",
        '#package-param': "#bloc-option-param-packages"
    };

    for (const [trigger, target] of Object.entries(liste)) {
        if (e.target.closest(trigger)) {
            document.querySelector(target).classList.remove('hidden', '-translate-x-[150%]');
        }
    }
});

function viderParam() {
    const liste_detail_param = ["bloc-option-param-boutique", "bloc-option-param-boutique", "bloc-update-profil", "bloc-option-param-packages"];
    for (let element of liste_detail_param){
        if(!document.querySelector("#"+element).classList.contains('hidden')){
            document.querySelector("#"+element).classList.add('-translate-x-[150%]', 'hidden');
        }
    }
}

const dictionnaire_adding_param = {
    "bloc-option-param-boutique #button-creer-new-shop": "bloc-option-param-boutique #bloc-add-new-shop-param",
    "bloc-option-param-boutique #button-view-shops": "bloc-option-param-boutique #bloc-shops-param",
};

for (let [cle_add, valeur_add] of Object.entries(dictionnaire_adding_param)) {
    const bouton = document.querySelector("#" + cle_add);
    const bloc = document.querySelector("#" + valeur_add);

    if (!bouton || !bloc) continue; // sécurité

    bouton.addEventListener("click", () => {
        // Fermer tous les blocs
        Object.values(dictionnaire_adding_param).forEach(idBloc => {
            const b = document.querySelector("#" + idBloc);
            if (b) b.classList.add("hidden", "-translate-x-[150%]");
        });

        // Ouvrir celui qui correspond
        bloc.classList.remove("hidden", "-translate-x-[150%]");

        // Reset du form si présent
        const form = bloc.querySelector("form");
        if (form) form.reset();

    });

}

const liste_package = ['Classic (Gratuit)' ,'Super (5 000 FCFA)' ,'Extra (10 000 FCFA)' ,'Diamond (25 000 FCFA)' ,'Elite (50 000 FCFA)'];

function gettingShop() {
    // Récupérer l’URL de la route depuis le <meta>
    let url = $('meta[name="route-store-get-shop"]').attr("content");

    // Requête GET vers le serveur
    $.get(url, function (data) {
        console.log("Boutiques reçues :", data);

        // Affiche le message renvoyé par Laravel
        // alert(JSON.stringify(data.data));

        let blocShopCard = document.createElement("div");
        blocShopCard.classList.add("flex");
        data.data.forEach(function(element) {
            for (let shop of element){
                alert(JSON.stringify(shop));
                // alert(JSON.stringify(shop.type_structure));
                // Créer un bloc <div>
                let shopCard = document.createElement("option");

            }
        });

        document.querySelector("#bloc-boutique").appendChild(blocShopCard);

    }).fail(function (xhr) {
        console.error("Erreur :", xhr.responseText);
        // alert("Veuillez proceder a la creation de votre premiere Boutique !");
    });
}


//--------------------------- BOUTIQUE --------------------------------------


function displaySellerManager(datas, localisation) {
    // alert(JSON.stringify(datas));
    const container = droite.querySelector(localisation);
    container.innerHTML = '';
    let cmt = 1;
    const loader1 = droite.querySelector("#bloc-view-vendeurs #loader");
    const loader2 = droite.querySelector("#bloc-view-gerants #loader");
    if (loader1) loader1.classList.add('hidden');
    if (loader2) loader2.classList.add('hidden');
    datas.forEach(element=>{
        let element_tr = document.createElement('tr');
        element_tr.innerHTML = `
                                <td class="text-center">${element.agent_prenom}</td>
                                <td class="text-center">${element.agent_nom}</td>
                                <td class="text-center">${element.agent_tel1}</td>
                                <td class="text-center">${element.statut}</td>
                                <td class="text-center">${element.privilege}</td>
                                <td id="voir-detail-element-${cmt}" class="text-center cursor-pointer relative"><i class="fas fa-search-plus"></i><span class="absolute z-50 left-0 top-0 text-[10px] h-full text-transparent">${element.user_matricule}</span></td>
                                `
        element_tr.classList.add('h-[30px]', 'text-[13px]');
        if(cmt%2 === 0){
            element_tr.classList.add('bg-blue-100');
        }
        container.appendChild(element_tr);
        dictionnaire_adding["voir-detail-element-"+cmt] = "bloc-view-update-employes";

        cmt++;
    })

}

function displayFacture(datas, type_structure) {
    let cmt = 1;
    // alert(JSON.stringify(datas));
    if(type_structure === 'revente'){
        const containerParent = document.querySelector("#bloc-boutique-container #bloc-view-factures");

        containerParent.innerHTML= `
                                    <div id="loader" class="hidden absolute inset-0 flex items-center justify-center bg-transparent bg-opacity-30 z-50">
                                        <div class="w-12 h-12 border-4 border-blue-500 border-dashed rounded-full animate-spin"></div>
                                    </div>
                                    <button id="button_add_new_facture" class="hover:bg-blue-800 transition duration-300 w-[85%] rounded-md bg-blue-600 px-5 py-2 flex text-center items-center text-[13px] text-white cursor-pointer">Nouvelle facture</button>

                                   <table class="w-full h-auto border border-collapse border-gray-400 bg-white shadow-md rounded-md">
                                        <thead class="bg-gray-100">
                                        <tr>
                                            <th class="border border-gray-400 px-4 py-2">Numero</th>
                                            <th class="border border-gray-400 px-4 py-2">Total</th>
                                            <th class="border border-gray-400 px-4 py-2">Fournisseur</th>
                                            <th class="border border-gray-400 px-4 py-2">Date</th>
                                            <th class="border border-gray-400 px-4 py-2">Responable</th>
                                            <th class="border border-gray-400 px-4 py-2">Details</th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                    `
        const container = containerParent.querySelector("table tbody");
        container.innerHTML = '';

        datas.forEach(element=>{
            let element_tr = document.createElement('tr');
            element_tr.innerHTML = `
                                <td class="text-center">${element.numero}</td>
                                <td class="text-center">${element.total_facture}</td>
                                <td class="text-center">${element.nom_fournisseur}</td>
                                <td class="text-center">${element.date_ajoute}</td>
                                <td class="text-center">${element.prenom_responsable} ${element.nom_responsable} </td>
                                <td id="voir-detail-element-${cmt}" class="text-center cursor-pointer relative"><i class="fas fa-search-plus"></i><span class="absolute z-50 left-0 top-0 text-[10px] h-full text-transparent">${element.facture_matricule}</span></td>
                                `
            element_tr.classList.add('h-[30px]', 'text-[13px]');
            if(cmt%2 === 0){
                element_tr.classList.add('bg-blue-100');
            }
            container.appendChild(element_tr);
            dictionnaire_adding["voir-detail-element-"+cmt] = "bloc-view-update-factures";

            cmt++;
        })
    }
    else if (type_structure === 'transformation'){
        const containerParent = document.querySelector("#bloc-boutique-container #bloc-view-factures");
        containerParent.innerHTML= `
                                     <div id="loader" class="hidden absolute inset-0 flex items-center justify-center bg-transparent bg-opacity-30 z-50">
                                        <div class="w-12 h-12 border-4 border-blue-500 border-dashed rounded-full animate-spin"></div>
                                    </div>

                                    <button id="button_add_new_facture" class="hover:bg-blue-800 transition duration-300 w-[85%] rounded-md bg-blue-600 px-5 py-2 flex text-center items-center text-[13px] text-white cursor-pointer">Nouvelle facture</button>
                                     <button id="button_add_new_ingredient" class="hover:bg-blue-800 transition duration-300 w-[85%] rounded-md bg-blue-600 px-5 py-2 flex text-center items-center text-[13px] text-white cursor-pointer">Nouveau ingredient</button>


                                   <table class="w-full h-auto border border-collapse border-gray-400 bg-white shadow-md rounded-md">
                                        <thead class="bg-gray-100">
                                        <tr>
                                            <th class="border border-gray-400 px-4 py-2">Numero</th>
                                            <th class="border border-gray-400 px-4 py-2">Total</th>
                                            <th class="border border-gray-400 px-4 py-2">Date</th>
                                            <th class="border border-gray-400 px-4 py-2">Responable</th>
                                            <th class="border border-gray-400 px-4 py-2">Details</th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                    `

        const container = containerParent.querySelector("table tbody");
        container.innerHTML = '';
        datas.forEach(element=>{
            let element_tr = document.createElement('tr');
            element_tr.innerHTML = `
                                <td class="text-center">${element.numero}</td>
                                <td class="text-center">${element.total_facture}</td>
                                <td class="text-center">${element.date_ajoute}</td>
                                <td class="text-center">${element.prenom_responsable}</td>
                                <td id="voir-detail-element-${cmt}" class="text-center cursor-pointer"><i class="fas fa-search-plus"></i><span class="absolute z-50 left-0 top-0 text-[10px] h-full text-transparent">${element.charge_matricule}</span></td>
                                `
            element_tr.classList.add('h-[30px]', 'text-[13px]');
            if(cmt%2 === 0){
                element_tr.classList.add('bg-blue-100');
            }
            container.appendChild(element_tr);
            dictionnaire_adding["voir-detail-element-"+cmt] = "bloc-view-update-factures";

            cmt++;
        })
    }

}

function displayFournisseur(datas, localisation) {
    // alert(JSON.stringify(datas));
    const container = droite.querySelector(localisation);
    container.innerHTML = '';
    let cmt = 1;
    datas.forEach(element=>{
        let element_tr = document.createElement('tr');
        element_tr.innerHTML = `
                                <td class="text-center">${element.nom_fournisseur}</td>
                                <td class="text-center">${element.tel1}</td>
                                <td class="text-center">${element.email}</td>
                                <td class="text-center">${element.statut}</td>
                                <td id="voir-detail-element-${cmt}" class="text-center cursor-pointer relative"><i class="fas fa-search-plus"></i><span class="absolute z-50 left-0 top-0 text-[10px] h-full text-transparent">${element.fournisseur_matricule}</span></td>
                                `
        element_tr.classList.add('h-[30px]', 'text-[13px]');
        if(cmt%2 === 0){
            element_tr.classList.add('bg-blue-100');
        }
        container.appendChild(element_tr);
        dictionnaire_adding["voir-detail-element-"+cmt] = "bloc-view-update-fournisseurs";

        cmt++;
    })
}

function displayProduits(dataall, dataon, type_structure, localisation) {
    const containerAll = droite.querySelector(localisation + " #table-All tbody");
    // const containerProduct = droite.querySelector(localisation);
    containerAll.innerHTML = '';
    let cmt = 1;
    let cmt2 = 1;
    // alert(JSON.stringify(dataall))
    dataall.forEach(element=>{
        let element_tr = document.createElement('tr');
        element_tr.innerHTML = `
                                <td class="text-center">${element.nom_produit}</td>
                                <td class="text-center">${element.code_barre}</td>
                                <td class="text-center">${element.date_creation}</td>
                                <td class="text-center">${element.prenom_responsable} ${element.nom_responsable}</td>
                                <td id="voir-detail-element-all-${cmt}" class="relative text-center cursor-pointer relative"><i class="fas fa-search-plus"></i><span class="absolute z-50 left-0 top-0 text-[10px] h-full text-transparent">${element.product_matricule}</span></td>
                                `
        element_tr.classList.add('h-[30px]', 'text-[13px]');
        if(cmt%2 === 0){
            element_tr.classList.add('bg-blue-100');
        }
        containerAll.appendChild(element_tr);

        dictionnaire_adding["voir-detail-element-all-"+cmt] = "bloc-view-update-produits-all";

        cmt++;
    })

    if (type_structure === 'revente'){
        const containerOn = droite.querySelector(localisation + " #table-On tbody");
        containerOn.innerHTML = '';

        dataon.forEach(element=>{
            let element_tr = document.createElement('tr');
            element_tr.innerHTML = `
                                <td class="text-center">${element.nom_produit}</td>
                                <td class="text-center">${element.prix_achat}</td>
                                <td class="text-center">${element.prix_vente}</td>
                                <td class="text-center">${element.date_creation}</td>
                                <td class="text-center">${element.prenom_responsable +' '+ element.nom_responsable}</td>
                                <td id="voir-detail-element-on-${cmt2}" class="relative text-center cursor-pointer"><i class="fas fa-search-plus "></i><span class="absolute z-50 left-0 top-0 text-[10px] h-full text-transparent">${element.product_matricule}</span></td>
                                `
            element_tr.classList.add('h-[30px]', 'text-[13px]');
            if(cmt2%2 === 0){
                element_tr.classList.add('bg-blue-100');
            }
            containerOn.appendChild(element_tr);

            dictionnaire_adding["voir-detail-element-on-"+cmt2] = "bloc-view-update-produits-on";

            cmt2++;
        });
    }

    const loader = droite.querySelector(localisation + " #loader");
    if(loader){
        loader.classList.add('hidden');
    }
}

function loadViewUpdateOptions(bloc, matricule) {
    if(bloc === 'bloc-view-update-produits-all'){
        gettingElementForUpdating(bloc, matricule ,'getProductsAll').then(res=>{
            // alert(JSON.stringify(res.data))
            let bloc_produit = document.createElement('div');
            bloc_produit.classList.add('w-full', 'h-full', 'bg-transparent', 'overflow-auto', 'px-2', 'py-5');
            bloc_produit.innerHTML = `
                                    <form id="form-updating-element-everything-all" class="w-[90%] h-auto rounded-md bg-white shadow-md px-2 py-4">
                                        @csrf <!-- Protection CSRF obligatoire dans Laravel -->

                                        <!-- Titre -->
                                        <h2 class="text-center text-2xl font-bold text-gray-700 mb-6">Detail du produit</h2>

                                        <!-- Message global -->
                                        <div id="global-message-produit" class="hidden text-green-700 font-bold mb-4 text-center bg-green-50 p-1 rounded border border-green-300">
                                        </div>

                                        <div class="mb-4 text-[12px]">
                                            <label for="nom_produit" class="block mb-2 font-semibold text-gray-600">Nom du produit</label>
                                            <input id="nom_produit" type="text" name="nom_produit" value="${res.data.nom_produit}"
                                                   class="text-[12px] w-full h-7 px-3 border border-gray-300 rounded-md focus:border-green-500 focus:ring-2 focus:ring-blue-200 outline-none text-sm">
                                            <span id="nom_produit-error" class="text-red-600 text-[11px] mt-1 block error-message"></span>
                                        </div>

                                        <!-- Champ Nom -->
                                        <div class="mb-4 text-[12px]">
                                            <label for="code_barre" class="block mb-2 font-semibold text-gray-600">Code barre</label>
                                            <input id="code_barre" type="text" name="code_barre" value="${res.data.code_barre}"
                                                   class="text-[12px] w-full h-7 px-3 border border-gray-300 rounded-md focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none">
                                            <span id="code_barre-error" class="text-red-600 text-[11px] mt-1 block error-message"></span>
                                        </div>

                                        <div class="mb-4 text-[12px]">
                                            <label for="description" class="block mb-2 font-semibold text-gray-600">Description</label>
                                            <textarea id="description" name="description"
                                                      class="h-[100px] text-[12px] w-full px-3 border border-gray-300 rounded-md focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none">${res.data.description}</textarea>
                                            <span id="description-error" class="text-red-600 text-[11px] mt-1 block error-message"></span>
                                        </div>

                                        <div class="mb-4 text-[12px]">
                                            <label class="block mb-2 font-semibold text-gray-600">Image du produit (Cliquer sur l'image pour la mettre a jour)</label>
                                            <p id="nom-du-nouveau-image" class="hidden text-green-700"></p>
                                            <div
                                                   class="text-[12px] w-full h-[400px] p-3 border border-gray-300 rounded-md">
                                                   <img id="image-du-produit" src="/storage/${res.data.image}" alt="photo du produit" title="cliquer pour changer l'image" class="w-full h-full cursor-pointer">
                                            </div>
                                        </div>

                                        <div class="mb-4 hidden">
                                            <label for="image" class="block mb-2 font-semibold text-gray-600">Image du produit</label>
                                            <input id="image" type="file" name="image"
                                                   class="text-[12px] w-full h-7 px-3 border border-gray-300 rounded-md focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none text-sm">
                                            <span id="image-error" class="text-red-600 text-[11px] mt-1 block error-message"></span>
                                        </div>
                                        <input class="hidden" type="text" id="matricule" name="matricule" value="${matricule}">
                                        <input class="hidden" type="text" id="element" name="element" value="productAll">

                                        <!-- Bouton -->
                                        <button id="button-button" type="button"
                                                class="opacity-20 w-full bg-gray-600 cursor-pointer relative text-white py-3 rounded-md text-lg font-semibold transition duration-300">
                                                <div id="loader" class="hidden absolute left-[-200px] inset-0 flex items-center justify-center bg-transparent bg-opacity-30 z-50">
                                                    <div class="w-7 h-7 border-4 border-white border-dashed rounded-full animate-spin"></div>
                                                </div>
                                            Mettre a jour
                                        </button>
                                        <button id="button-submit" type="submit"
                                                class="hidden w-full bg-blue-600 hover:bg-blus-700 cursor-pointer relative text-white py-3 rounded-md text-lg font-semibold transition duration-300">
                                                <div id="loader" class="hidden absolute left-[-200px] inset-0 flex items-center justify-center bg-transparent bg-opacity-30 z-50">
                                                    <div class="w-7 h-7 border-4 border-white border-dashed rounded-full animate-spin"></div>
                                                </div>
                                            Mettre a jour
                                        </button>
                                    </form>
                                    `
            bloc_produit.querySelector("#image-du-produit").addEventListener('click', function () {
                bloc_produit.querySelector('#image').click();
            })
            bloc_produit.querySelector('#image').addEventListener('change', function () {
                if(this.files && this.files.length > 0){
                    bloc_produit.querySelector("#nom-du-nouveau-image").classList.remove('hidden');
                    bloc_produit.querySelector("#nom-du-nouveau-image").textContent = "Nouvealle image chargee!";
                }
            });
            const disabled_button = bloc_produit.querySelector("#button-button");
            const submit_button = bloc_produit.querySelector("#button-submit");
            const formu = bloc_produit.querySelector("#form-updating-element-everything-all");
            if(disabled_button){
                disabled_button.addEventListener('click', function () {
                    alert("Veuillez effectuer vos modifications souhaitées avant de mettre à jour le produit");
                })
                formu.addEventListener('input', function () {
                    disabled_button.classList.add('hidden');
                    submit_button.classList.remove('hidden');
                })
            }

            document.querySelector("#"+bloc).innerHTML = '';
            document.querySelector("#"+bloc).appendChild(bloc_produit);
        })
    }
    else if(bloc === 'bloc-view-update-produits-on'){
        gettingElementForUpdating(bloc, matricule ,'getsProductsOn').then(res=>{
            // alert(JSON.stringify(res.data));
            let bloc_produit = document.createElement('div');
            bloc_produit.classList.add('w-full', 'h-full', 'bg-transparent', 'overflow-auto', 'px-2', 'py-5')
            bloc_produit.innerHTML = `
                                    <form id="form-updating-element-everything-on" class="w-[90%] h-auto rounded-md bg-white shadow-md px-2 py-4">
                                        @csrf <!-- Protection CSRF obligatoire dans Laravel -->

                                        <!-- Titre -->
                                        <h2 class="text-center text-2xl font-bold text-gray-700 mb-6">Detail du produit</h2>

                                        <!-- Message global -->
                                        <div id="global-message-produit" class="hidden text-green-700 font-bold mb-4 text-center bg-green-50 p-1 rounded border border-green-300">
                                        </div>

                                        <!-- Champ Pseudo -->
                                        <div class="mb-4 text-[12px]">
                                            <label for="nom_produit" class="block mb-2 font-semibold text-gray-600">Nom du produit</label>
                                            <input id="nom_produit" type="text" name="nom_produit" value="${res.data[0].nom}"
                                                   class="text-[12px] w-full h-7 px-3 border border-gray-300 rounded-md focus:border-green-500 focus:ring-2 focus:ring-blue-200 outline-none text-sm">
                                            <span id="nom_produit-error" class="text-red-600 text-[11px] mt-1 block error-message"></span>
                                        </div>

                                        <!-- Champ Nom -->
                                        <div class="w-full h-auto flex justify-between">
                                            <div class="mb-4 text-[12px]">
                                                <label for="prix_achat" class="block mb-2 font-semibold text-gray-600">Prix d'achat</label>
                                                <input id="prix_achat" type="text" name="prix_achat" value="${res.data[0].prix_achat}"
                                                       class="text-[12px] w-full h-7 px-3 border border-gray-300 rounded-md focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none">
                                                <span id="prix_achat-error" class="text-red-600 text-[11px] mt-1 block error-message"></span>
                                            </div>

                                            <div class="mb-4 text-[12px]">
                                                <label for="prix_vente" class="block mb-2 font-semibold text-gray-600">Prix de vente</label>
                                                <input id="prix_vente" readonly type="text" name="prix_vente" value="${res.data[0].prix_vente}"
                                                       class=" bg-gray-300 font-bold text-[12px] w-full h-7 px-3 border border-gray-300 rounded-md focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none">
                                                <span id="prix_vente-error" class="text-red-600 text-[11px] mt-1 block error-message"></span>
                                            </div>
                                        </div>

                                        <div class="w-full h-auto flex justify-between">
                                            <div class="mb-4 text-[12px]">
                                                <label for="marge" class="block mb-2 font-semibold text-gray-600">Marge</label>
                                                <input id="marge" type="number" step="0.01" name="marge" value="${res.data[0].marge}"
                                                       class="text-[12px] w-full h-7 px-3 border border-gray-300 rounded-md focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none">
                                                <span id="marge-error" class="text-red-600 text-[11px] mt-1 block error-message"></span>
                                            </div>
                                            <div class="mb-4 text-[12px] cursor-pointer flex ">
                                                <div class="grid">
                                                    <label for="" class="block mb-2 font-semibold text-gray-600">Numero facture</label>
                                                    <input type="text" id="numero_facture" name="numero_facture" value="${res.data[0].numero_facture}" readonly
                                                        class=" font-bold bg-gray-300 text-[12px] w-full h-7 px-3 border border-gray-300 rounded-md focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none">
                                                </div>

                                                <i id="icon-view-bill" class="fa fa-file fa-2x top-3" title="Cliquer pour visualiser la facture"></i>
                                            </div>

                                            <div class="mb-4 text-[12px]">
                                                <label for="statut" class="block mb-2 font-semibold text-gray-600">Statut</label>
                                                <select id="statut" name="statut"
                                                       class="text-[12px] w-full h-7 px-3 border border-gray-300 rounded-md focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none">
                                                    <option value="">Statut</option>
                                                    <option value="actif">Actif</option>
                                                    <option value="inactif">Inactif</option>
                                                </select>
                                                <span id="statut-error" class="text-red-600 text-[11px] mt-1 block error-message"></span>
                                            </div>
                                        </div>

                                        <div class="mb-4 text-[12px] w-full h-auto p-[3px]">
                                            <label class="block mb-2 font-semibold text-gray-600">Image du produit (Cliquer sur l'image pour la mettre a jour)</label>
                                            <p id="nom-du-nouveau-image" class="hidden text-green-700"></p>
                                            <div id="big-bloc-image" class="w-full h-auto text-[12px] w-full h-[400px] p-[4px] border border-gray-300 rounded-md">

                                            </div>
                                            <div id="small-blocs-images" class="hidden flex justify-left gap-1 w-full h-[100px]">

                                            </div>
                                        </div>

                                        <div class="mb-4 hidden">
                                            <label for="image" class="block mb-2 font-semibold text-gray-600">Image du produit</label>
                                            <input id="image" type="file" name="image" multiple
                                                   class="text-[12px] w-full h-7 px-3 border border-gray-300 rounded-md focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none text-sm">
                                            <span id="image-error" class="text-red-600 text-[11px] mt-1 block error-message"></span>
                                        </div>

                                        <input class="hidden" type="text" id="matricule" name="matricule" value="${matricule}">
                                        <input class="hidden" type="text" id="element" name="element" value="productOn">

                                        <!-- Bouton -->
                                        <button id="button-button" type="button"
                                                class="opacity-20 w-full bg-gray-600 cursor-pointer relative text-white py-3 rounded-md text-lg font-semibold transition duration-300">
                                                <div id="loader" class="hidden absolute left-[-200px] inset-0 flex items-center justify-center bg-transparent bg-opacity-30 z-50">
                                                    <div class="w-7 h-7 border-4 border-white border-dashed rounded-full animate-spin"></div>
                                                </div>
                                            Mettre a jour
                                        </button>
                                        <button id="button-submit" type="submit"
                                                class="hidden w-full bg-blue-600 hover:bg-green-700 text-white py-3 rounded-md text-lg font-semibold transition duration-300">
                                                 <div id="loader" class="hidden absolute left-[-200px] inset-0 flex items-center justify-center bg-transparent bg-opacity-30 z-50">
                                                    <div class="w-7 h-7 border-4 border-white border-dashed rounded-full animate-spin"></div>
                                                </div>
                                            Mettre a jour
                                        </button>
                                    </form>
                                    <!-- EN DEHORS du formulaire -->
                                    <div id="bloc-view-loaded-bill" class="pt-11 gap-2 flex flex-col items-center hidden fixed inset-0 z-50 bg-black/70 transition duration-300">
                                        <span id="fermer-facture" class="relative left-[300px] text-white font-bold bg-red-600 cursor-pointer h-auto w-auto hover:bg-red-700 px-7 py-1 rounded-md">Fermer</span>
                                        <div id="loader" class="inset-0 flex items-center justify-center bg-transparent bg-opacity-30 z-50">
                                            <div class="w-12 h-12 border-4 border-blue-500 border-dashed rounded-full animate-spin"></div>
                                        </div>
                                    </div>

                                    `
            const disabled_button = bloc_produit.querySelector("#button-button");
            const submit_button = bloc_produit.querySelector("#button-submit");
            const formu = bloc_produit.querySelector("#form-updating-element-everything-on");
            if(disabled_button){
                disabled_button.addEventListener('click', function () {
                    alert("Veuillez effectuer vos modifications souhaitées avant de mettre à jour le produit");
                })
                formu.addEventListener('input', function () {
                    disabled_button.classList.add('hidden');
                    submit_button.classList.remove('hidden');
                })
            }
            if (res.data[0].images.includes(',')) {
                bloc_produit.querySelector("#small-blocs-images").classList.remove('hidden')
                // Si plusieurs images, on les sépare
                let tab_image = res.data[0].images.split(',');

                // Ajouter l'image principale dans #big-bloc-image
                let photo_img_main = document.createElement('img');
                photo_img_main.src = "/storage/" + tab_image[0];
                photo_img_main.alt = "Photo du produit";
                photo_img_main.title = "Cliquer pour mettre à jour les photos";
                photo_img_main.classList.add('w-full', 'max-h-[450px]', 'rounded-md', 'shadow-md');
                bloc_produit.querySelector("#big-bloc-image").append(photo_img_main);
                bloc_produit.querySelector("#big-bloc-image").classList.add('max-h-[450px]', 'w-full', 'flex', 'justify-center');

                // Ajouter les miniatures dans #small-blocs-images
                let cmt = 1; // On commence à partir de l'indice 1 pour les miniatures
                while (cmt < tab_image.length) {
                    let photo_img = document.createElement('img');
                    photo_img.src = "/storage/" + tab_image[cmt];
                    photo_img.alt = "Photo du produit";
                    photo_img.title = "Cliquer pour mettre à jour les photos";
                    photo_img.classList.add('w-[60px]', 'h-[60px]', 'shadow-[0_0_5px_gray]', 'relative', 'top-[7px]', 'rounded-md');

                    bloc_produit.querySelector("#small-blocs-images").append(photo_img); // Ajouter le div avec l'image dans le bloc des miniatures
                    bloc_produit.querySelector("#small-blocs-images").classList.add('h-[60px]', 'max-h-[60px]', 'items-center')
                    cmt++;
                }

            } else {
                if(!bloc_produit.querySelector("#small-blocs-images").classList.contains('hidden')){
                    bloc_produit.querySelector("#small-blocs-images").classList.add('hidden');
                }

                    // Si une seule image
                let photo_img = document.createElement('img');
                photo_img.src = "/storage/" + res.data[0].images;
                photo_img.alt = "Photo du produit";
                photo_img.title = "Cliquer pour mettre à jour les photos";
                photo_img.classList.add("max-h-[450px]", "w-full", 'rounded-md', 'shadow-md')
                bloc_produit.querySelector("#big-bloc-image").append(photo_img);
                bloc_produit.querySelector("#big-bloc-image").classList.add('max-h-[450px]', 'w-full', 'flex', 'justify-center');
            }

            let options = bloc_produit.querySelector("#statut").children;
            if(res.data[0].statut === "actif"){
                options[1].setAttribute('selected', 'true');
            }else{
                options[2].setAttribute('selected', 'true');
            }
            bloc_produit.querySelector("#big-bloc-image").addEventListener('click', function () {
                bloc_produit.querySelector('#image').click();
            })
            bloc_produit.querySelector('#image').addEventListener('change', function () {
                if(this.files && this.files.length > 0){
                    bloc_produit.querySelector("#nom-du-nouveau-image").classList.remove('hidden');
                    bloc_produit.querySelector("#nom-du-nouveau-image").textContent = "Nouvelle(s) image(s) chargeEs) !";
                }
            })
            bloc_produit.querySelector("#icon-view-bill").addEventListener('click', function () {
                const bloc = bloc_produit.querySelector("#bloc-view-loaded-bill");
                bloc.classList.remove('hidden', '-translate-x-[150%]');

                const loader = bloc.querySelector("#loader");
                if (loader) loader.classList.remove('hidden');

                // Vider le bloc (sauf le loader !)
                [...bloc.children].forEach(child => {
                    if (child.id !== 'loader' && child.id !== 'fermer-facture') {
                        child.remove();
                    }
                });
                bloc.querySelector("#fermer-facture").addEventListener('click', function () {
                    bloc.classList.add('hidden', '-translate-x-[150%]')
                })

                // Créer le bon élément (iframe ou image)
                if (res.data[0].facture.includes('pdf')) {
                    const facture = document.createElement('iframe');
                    facture.src = "/storage/"+res.data[0].facture;
                    facture.classList.add('w-[97%]', 'h-[97%]');
                    bloc.appendChild(facture);
                    loader.classList.add('hidden');
                } else {
                    const facture = document.createElement('img');
                    facture.src = "/storage/"+res.data[0].facture;
                    facture.classList.add('w-full', 'h-full', 'object-contain');
                    bloc.appendChild(facture);
                    loader.classList.add('hidden')
                }
            });


            document.querySelector("#"+bloc).innerHTML = '';
            document.querySelector("#"+bloc).appendChild(bloc_produit);
        })
    }
    else if(bloc === 'bloc-view-update-factures'){
        gettingElementForUpdating(bloc, matricule ,'getsBill').then(res=> {
            // alert(JSON.stringify(res.data));
            if (res.data[0].type_structure === "transformation") {
                let bloc_produit = document.createElement('div');
                bloc_produit.classList.add('w-full', 'h-full', 'bg-transparent', 'overflow-auto', 'px-2', 'py-5')
                bloc_produit.innerHTML = `
                                    <form id="form-updating-element-everything-bills" class="w-[90%] h-auto rounded-md bg-white shadow-md px-2 py-4">
                                        @csrf <!-- Protection CSRF obligatoire dans Laravel -->

                                        <!-- Titre -->
                                        <h2 class="text-center text-2xl font-bold text-gray-700 mb-6">Detail de la facture</h2>

                                        <!-- Message global -->
                                        <div id="global-message-facture" class="hidden text-green-700 font-bold mb-4 text-center bg-green-50 p-1 rounded border border-green-300">
                                        </div>

                                        <!-- Champ Pseudo -->
                                        <div class="mb-4 text-[12px]">
                                            <label for="date_ajout" class="block mb-2 font-semibold text-gray-600">Date d'ajout</label>
                                            <input id="date_ajout" type="text" name="date_ajout" value="${res.data[0].date_ajout}" disabled
                                                   class="read-only text-[12px] w-full h-7 px-3 border border-gray-300 rounded-md focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none">
                                            <span id="date_ajout-error" class="text-red-600 text-[11px] mt-1 block error-message"></span>
                                        </div>

                                        <!-- Champ Nom -->
                                        <div class="w-full h-auto flex justify-between gap-3 items-center">
                                            <div class="mb-4 text-[12px] w-full ">
                                                <label for="total_facture" class="block mb-2 font-semibold text-gray-600">Totale Facture</label>
                                                <input id="total_facture" type="text" name="total_facture" value="${res.data[0].total_facture}"
                                                       class="text-[12px] w-full h-7 px-3 border border-gray-300 rounded-md focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none">
                                                <span id="total_facture-error" class="text-red-600 text-[11px] mt-1 block error-message"></span>
                                            </div>

                                            <div  class="mb-4 text-[12px] w-full grid">
                                                <div class="flex justify-center gap-10 w-full">
                                                    <i id="icon-view-bill" class="fa fa-file fa-2x cursor-pointer" title="Cliquer pour visualiser la facture"></i>
                                                    <i id="actualiser-facture" class="fa fa-sync fa-2x cursor-pointer" title="Mettre a jour la facture"></i>
                                                </div>
                                                <span id="facture-error" class="text-red-600 error-message"></span>
                                            </div>

                                            <div class="mb-4 text-[12px] cursor-pointer w-full ">
                                                <label for="" class="block mb-2 font-semibold text-gray-600">Numero facture</label>
                                                <input id="numero_facture" type="text" step="0.01" name="numero_facture" value="${res.data[0].numero}"
                                                       class="text-[12px] w-full h-7 px-3 border border-gray-300 rounded-md focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none">
                                            </div>
                                            <div class="mb-4 text-[12px] cursor-pointer hidden">
                                                <label for="" class="block mb-2 font-semibold text-gray-600">Numero facture</label>
                                                <input id="facture" type="file"  name="facture"
                                                       class="text-[12px] w-full h-7 px-3 border border-gray-300 rounded-md focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none">
                                            </div>
                                        </div>

                                        <input class="hidden" type="text" id="matricule" name="matricule" value="${matricule}">
                                        <input class="hidden" type="text" id="element" name="element" value="bills">

                                        <!-- Bouton -->
                                        <button id="button-button" type="button"
                                                class="opacity-20 w-full bg-gray-600 cursor-pointer relative text-white py-3 rounded-md text-lg font-semibold transition duration-300">
                                                <div id="loader" class="hidden absolute left-[-200px] inset-0 flex items-center justify-center bg-transparent bg-opacity-30 z-50">
                                                    <div class="w-7 h-7 border-4 border-white border-dashed rounded-full animate-spin"></div>
                                                </div>
                                            Mettre a jour
                                        </button>
                                        <button id="button-submit" type="submit"
                                                class="hidden w-full bg-blue-600 hover:bg-green-700 text-white py-3 rounded-md text-lg font-semibold transition duration-300">
                                                 <div id="loader" class="hidden absolute left-[-200px] inset-0 flex items-center justify-center bg-transparent bg-opacity-30 z-50">
                                                    <div class="w-7 h-7 border-4 border-white border-dashed rounded-full animate-spin"></div>
                                                </div>
                                            Mettre a jour
                                        </button>
                                    </form>
                                    <!-- EN DEHORS du formulaire -->
                                    <div id="bloc-view-loaded-bill" class="pt-11 gap-2 flex flex-col items-center hidden fixed inset-0 z-50 bg-black/70 transition duration-300">
                                        <span id="fermer-facture" class="relative left-[300px] text-white font-bold bg-red-600 cursor-pointer h-auto w-auto hover:bg-red-700 px-7 py-1 rounded-md">Fermer</span>
                                        <div id="loader" class="inset-0 flex items-center justify-center bg-transparent bg-opacity-30 z-50">
                                            <div class="w-12 h-12 border-4 border-blue-500 border-dashed rounded-full animate-spin"></div>
                                        </div>
                                    </div>
                                    `
                // alert(matricule);
                const disabled_button = bloc_produit.querySelector("#button-button");
                const submit_button = bloc_produit.querySelector("#button-submit");
                const formu = bloc_produit.querySelector("#form-updating-element-everything-bills");
                if(disabled_button){
                    disabled_button.addEventListener('click', function () {
                        alert("Veuillez effectuer vos modifications souhaitées avant de mettre à jour les facture");
                    })
                    formu.addEventListener('input', function () {
                        disabled_button.classList.add('hidden');
                        submit_button.classList.remove('hidden');
                    })
                }
                bloc_produit.querySelector("#actualiser-facture").addEventListener('click', function () {
                    bloc_produit.querySelector("#facture").click();
                    bloc_produit.querySelector("#facture").addEventListener('change', function () {
                        if(bloc_produit.querySelector("#facture").files.length > 0){
                            bloc_produit.querySelector("#facture-error").textContent = "Nouvelle facture chargee";
                            bloc_produit.querySelector("#facture-error").classList.add('text-green-600', 'text-[13px]');
                        }
                    });

                })

                bloc_produit.querySelector("#icon-view-bill").addEventListener('click', function () {
                    const bloc_view_bill = bloc_produit.querySelector("#bloc-view-loaded-bill");
                    bloc_view_bill.classList.remove('hidden');

                    // Vider seulement le contenu dynamique (iframe ou img)
                    bloc_view_bill.querySelectorAll("iframe, img").forEach(el => el.remove());

                    // Créer le bon élément (iframe ou image)
                    let bfacture;
                    if (res.data[0].facture.includes('pdf')) {
                        bfacture = document.createElement('iframe');
                        bfacture.src = "/storage/" + res.data[0].facture;
                        bfacture.classList.add('w-[97%]', 'h-[97%]');
                    } else {
                        bfacture = document.createElement('img');
                        bfacture.src = "/storage/" + res.data[0].facture;
                        bfacture.classList.add('w-full', 'h-full', 'object-contain');
                    }

                    bloc_view_bill.appendChild(bfacture);
                    const loader = bloc_view_bill.querySelector("#loader");
                    if (loader) loader.classList.add('hidden');

                    bloc_produit.querySelector("#fermer-facture").addEventListener('click', function () {
                        bloc_view_bill.classList.add('hidden');
                    });
                });

                document.querySelector("#"+bloc).innerHTML = '';
                document.querySelector("#"+bloc).appendChild(bloc_produit)

            }
            else if (res.data[0].type_structure === "revente") {
                let bloc_produit = document.createElement('div');
                bloc_produit.classList.add('w-full', 'h-full', 'bg-transparent', 'overflow-auto', 'px-2', 'py-5')
                bloc_produit.innerHTML = `
                                    <form id="form-updating-element-everything-bills" class="w-[90%] h-auto rounded-md bg-white shadow-md px-2 py-4">
                                        @csrf <!-- Protection CSRF obligatoire dans Laravel -->

                                        <!-- Titre -->
                                        <h2 class="text-center text-2xl font-bold text-gray-700 mb-6">Detail de la facture</h2>

                                        <!-- Message global -->
                                        <div id="global-message-facture" class="hidden text-green-700 font-bold mb-4 text-center bg-green-50 p-1 rounded border border-green-300">
                                        </div>

                                        <div class="mb-4 text-[12px]">
                                            <label for="date_ajout" class="block mb-2 font-semibold text-gray-600">Date d'ajout</label>
                                            <input id="date_ajout" type="text" name="date_ajout" value="${res.data[0].date_ajout}" disabled
                                                   class="read-only bg-gray-200 text-[12px] w-full h-7 px-3 border border-gray-300 rounded-md focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none">
                                            <span id="date_ajout-error" class="text-red-600 text-[11px] mt-1 block error-message"></span>
                                        </div>

                                        <div class="flex flex-wrap w-full justify-evenly gap-2">
                                            <div class="min-w-[75px] mb-4 text-[13px] flex flex-col max-w-[175px]">
                                                <label for="numero_facture" class="w-full block mb-2 font-semibold text-gray-600">Numéro facture</label>
                                                <div>
                                                    <input id="numero_facture" type="text" name="numero_facture" value="${res.data[0].numero}"
                                                           class="text-[12px] w-full h-7 px-3 border border-gray-300 rounded-md focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none text-sm">
                                                    <span id="numero_facture-error" class="text-red-600 text-sm mt-1 block text-[10px] error-message"></span>
                                                </div>
                                            </div>

                                            <div class="min-w-[75px] mb-4 text-[13px] flex flex-col max-w-[175px]">
                                                <label for="fournisseur" class="w-full ck mb-2 font-semibold text-gray-600">Fournisseur</label>
                                                <div id="bloc-select-fournisseur">
                                                    <select name="fournisseur" id="fournisseur" class="text-[12px] w-full h-7 px-3 border border-gray-300 rounded-md focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none text-sm">
                                                        <option value="">Fournisseurs</option>
                                                    </select>
                                                    <span id="fournisseur-error" class="text-red-600 text-sm mt-1 block text-[10px] error-message"></span>
                                                </div>
                                            </div>

                                            <!-- Somme -->
                                            <div class="min-w-[75px] mb-4 text-[13px] flex flex-col max-w-[150px]">
                                                <label for="total_facture" class="w-full lock mb-2 font-semibold text-gray-600">Total facture</label>
                                                <div>
                                                    <input id="total_facture" type="number" name="total_facture" value="${res.data[0].total_facture}"
                                                           class="text-[12px] w-full h-7 px-3 border border-gray-300 rounded-md focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none text-sm">
                                                    <span id="total_facture-error" class="text-red-600 text-sm mt-1 block text-[10px] error-message"></span>
                                                </div>
                                            </div>

                                            <div class="min-w-[75px] mb-4 text-[13px] flex flex-col max-w-[75px]">
                                                <label for="marge" class="w-full lock mb-2 font-semibold text-gray-600">Marge</label>
                                                <div>
                                                    <input id="marge" type="number" name="marge" step=".01" value="${res.data[0].marge}"
                                                           class="text-[12px] w-full h-7 px-3 border border-gray-300 rounded-md focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none text-sm">
                                                    <span id="marge-error" class="text-red-700 text-sm mt-1 block text-[10px] error-message"></span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="w-full flex-wrap h-auto flex justify-between gap-3 items-center">
                                            <div  class="mb-4 text-[12px] w-full grid">
                                                <div class="flex justify-center gap-10 w-full">
                                                    <i id="icon-view-bill" class="fa fa-file fa-2x cursor-pointer" title="Cliquer pour visualiser la facture"></i>
                                                    <i id="actualiser-facture" class="fa fa-sync fa-2x cursor-pointer" title="Mettre a jour la facture"></i>
                                                </div>
                                                <span id="facture-error" class="text-red-600 error-message"></span>
                                            </div>

                                            <div class="mb-4 text-[12px] cursor-pointer hidden">
                                                <label for="" class="block mb-2 font-semibold text-gray-600">Numero facture</label>
                                                <input id="facture" type="file"  name="facture"
                                                       class="text-[12px] w-full h-7 px-3 border border-gray-300 rounded-md focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none">
                                            </div>
                                        </div>

                                        <input class="hidden" type="text" id="matricule" name="matricule" value="${matricule}">
                                        <input class="hidden shopmatricule" type="text" id="shopmatricule" name="shopmatricule" value="${res.data[0].structure_matricule}">
                                        <input class="hidden" type="text" id="element" name="element" value="bills">

                                        <!-- Bouton -->
                                        <button id="button-button" type="button"
                                                class="opacity-20 w-full bg-gray-600 cursor-pointer relative text-white py-3 rounded-md text-lg font-semibold transition duration-300">
                                                <div id="loader" class="hidden absolute left-[-200px] inset-0 flex items-center justify-center bg-transparent bg-opacity-30 z-50">
                                                    <div class="w-7 h-7 border-4 border-white border-dashed rounded-full animate-spin"></div>
                                                </div>
                                            Mettre a jour
                                        </button>
                                        <button id="button-submit" type="submit"
                                                class="hidden w-full cursor-pointer bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-md text-lg font-semibold transition duration-300">
                                                 <div id="loader" class="hidden absolute left-[-200px] inset-0 flex items-center justify-center bg-transparent bg-opacity-30 z-50">
                                                    <div class="w-7 h-7 border-4 border-white border-dashed rounded-full animate-spin"></div>
                                                </div>
                                            Mettre a jour
                                        </button>
                                    </form>
                                    <!-- EN DEHORS du formulaire -->
                                    <div id="bloc-view-loaded-bill" class="pt-11 gap-2 flex flex-col items-center hidden fixed inset-0 z-50 bg-black/70 transition duration-300">
                                        <span id="fermer-facture" class="relative left-[300px] text-white font-bold bg-red-600 cursor-pointer h-auto w-auto hover:bg-red-700 px-7 py-1 rounded-md">Fermer</span>
                                        <div id="loader" class="inset-0 flex items-center justify-center bg-transparent bg-opacity-30 z-50">
                                            <div class="w-12 h-12 border-4 border-blue-500 border-dashed rounded-full animate-spin"></div>
                                        </div>
                                    </div>
                                    `
                // alert(matricule);
                const disabled_button = bloc_produit.querySelector("#button-button");
                const submit_button = bloc_produit.querySelector("#button-submit");
                const formu = bloc_produit.querySelector("#form-updating-element-everything-bills");
                if(disabled_button){
                    disabled_button.addEventListener('click', function () {
                        alert("Veuillez effectuer vos modifications souhaitées avant de mettre à jour les facture");
                    })
                    formu.addEventListener('input', function () {
                        disabled_button.classList.add('hidden');
                        submit_button.classList.remove('hidden');
                    })
                }
                bloc_produit.querySelector("#actualiser-facture").addEventListener('click', function () {
                    bloc_produit.querySelector("#facture").click();
                    bloc_produit.querySelector("#facture").addEventListener('change', function () {
                        if(bloc_produit.querySelector("#facture").files.length > 0){
                            bloc_produit.querySelector("#facture-error").textContent = "Nouvelle facture chargee";
                            bloc_produit.querySelector("#facture-error").classList.add('text-green-600', 'text-[13px]');
                        }
                    });

                })

                bloc_produit.querySelector("#icon-view-bill").addEventListener('click', function () {
                    const bloc_view_bill = bloc_produit.querySelector("#bloc-view-loaded-bill");
                    bloc_view_bill.classList.remove('hidden');

                    // Vider seulement le contenu dynamique (iframe ou img)
                    bloc_view_bill.querySelectorAll("iframe, img").forEach(el => el.remove());

                    // Créer le bon élément (iframe ou image)
                    let bfacture;
                    if (res.data[0].facture.includes('pdf')) {
                        bfacture = document.createElement('iframe');
                        bfacture.src = "/storage/" + res.data[0].facture;
                        bfacture.classList.add('w-[97%]', 'h-[97%]');
                    } else {
                        bfacture = document.createElement('img');
                        bfacture.src = "/storage/" + res.data[0].facture;
                        bfacture.classList.add('w-full', 'h-full', 'object-contain');
                    }

                    bloc_view_bill.appendChild(bfacture);
                    const loader = bloc_view_bill.querySelector("#loader");
                    if (loader) loader.classList.add('hidden');

                    bloc_produit.querySelector("#fermer-facture").addEventListener('click', function () {
                        bloc_view_bill.classList.add('hidden');
                    });
                });

                document.querySelector("#"+bloc).innerHTML = '';
                document.querySelector("#"+bloc).appendChild(bloc_produit)
                getDealerList('form-updating-element-everything-bills');
            }
        })
    }
    else if(bloc === 'bloc-view-update-fournisseurs'){
        gettingElementForUpdating(bloc, matricule ,'getsDealer').then(res=>{
            // alert(JSON.stringify(res.data));
            let bloc_produit = document.createElement('div');
            bloc_produit.classList.add('w-full', 'h-full', 'bg-transparent', 'overflow-auto', 'px-2', 'py-5', 'grid', 'place-items-center')
            bloc_produit.innerHTML = `
                                    <form id="form-updating-element-everything-dealers" class="relative bg-white px-3 py-10 rounded-lg shadow-lg w-[75%]">
                                            @csrf <!-- Protection CSRF obligatoire dans Laravel -->

                                            <!-- Titre -->
                                            <h2 class="text-center text-2xl font-bold text-gray-700 mb-10">Detail du fournisseur</h2>

                                            <!-- Message global -->
                                            <div id="global-message-fournisseur"
                                                class="hidden text-green-700 font-bold mb-4 text-center bg-green-50 p-1 rounded border border-green-300"></div>

                                            <div class="w-full h-auto flex flex-wrap gap-3 justify-center mt-4 mb-4">
                                                    <!-- Nom fournisseur -->
                                                <div class="relative w-full max-w-[200px] mb-4">
                                                    <input id="nom_fournisseur" type="text" name="nom_fournisseur" placeholder=" "
                                                           value="${res.data.nom_fournisseur}"
                                                           class="peer w-full h-7 px-3 border border-gray-300 rounded-md
                                                                  focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none text-[12px]" />
                                                    <span id="nom_fournisseur-error" class="error-message text-red-600 text-[12px]"></span>
                                                    <label for="nom_fournisseur"
                                                           class="absolute left-2 top-1 text-gray-400 text-[13px] transition-all
                                                                  peer-placeholder-shown:top-1 peer-placeholder-shown:text-gray-400
                                                                  peer-focus:-top-3 peer-focus:text-[13px] peer-focus:text-blue-600
                                                                  peer-not-placeholder-shown:-top-4 peer-not-placeholder-shown:text-[13px] peer-not-placeholder-shown:text-blue-600
                                                                  bg-white px-1">
                                                        Nom du fournisseur
                                                    </label>
                                                </div>

                                                <!-- Sigle -->
                                                <div class="relative w-full max-w-[200px] mb-4">
                                                    <input id="sigle_fournisseur" type="text" name="sigle_fournisseur" placeholder=" "
                                                           value="${res.data.sigle_fournisseur}"
                                                           class="peer w-full h-7 px-3 border border-gray-300 rounded-md
                                                                  focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none text-[12px]" />
                                                    <span id="sigle_fournisseur-error" class="error-message text-red-600 text-[12px]"></span>
                                                    <label for="sigle_fournisseur"
                                                           class="absolute left-2 top-1 text-gray-400 text-[13px] transition-all
                                                                  peer-placeholder-shown:top-1 peer-focus:-top-3 peer-focus:text-blue-600
                                                                  peer-not-placeholder-shown:-top-4 bg-white px-1">
                                                        Sigle
                                                    </label>
                                                </div>

                                                <!-- Téléphone principal -->
                                                <div class="relative w-full max-w-[200px] mb-4">
                                                    <input id="telephone1" type="text" name="telephone1" placeholder=" "
                                                           value="${res.data.telephone1}"
                                                           class="peer w-full h-7 px-3 border border-gray-300 rounded-md
                                                                  focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none text-[12px]" />
                                                    <span id="telephone1-error" class="error-message text-red-600 text-[12px]"></span>
                                                    <label for="telephone1"
                                                           class="absolute left-2 top-1 text-gray-400 text-[13px] transition-all
                                                                  peer-placeholder-shown:top-1 peer-focus:-top-3 peer-focus:text-blue-600
                                                                  peer-not-placeholder-shown:-top-4 bg-white px-1">
                                                        Téléphone principal
                                                    </label>
                                                </div>

                                                <!-- Téléphone secondaire -->
                                                <div class="relative w-full max-w-[200px] mb-4">
                                                    <input id="telephone2" type="text" name="telephone2" placeholder=" "
                                                           value="${res.data.telephone2}"
                                                           class="peer w-full h-7 px-3 border border-gray-300 rounded-md
                                                                  focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none text-[12px]" />
                                                    <span id="telephone2-error" class="error-message text-red-600 text-[12px]"></span>
                                                    <label for="telephone2"
                                                           class="absolute left-2 top-1 text-gray-400 text-[13px] transition-all
                                                                  peer-placeholder-shown:top-1 peer-focus:-top-3 peer-focus:text-blue-600
                                                                  peer-not-placeholder-shown:-top-4 bg-white px-1">
                                                        Téléphone secondaire
                                                    </label>
                                                </div>

                                                <!-- Téléphone fixe -->
                                                <div class="relative w-full max-w-[200px] mb-4">
                                                    <input id="telephone_fixe" type="text" name="telephone_fixe" placeholder=" "
                                                           value="${res.data.telephone_fixe}"
                                                           class="peer w-full h-7 px-3 border border-gray-300 rounded-md
                                                                  focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none text-[12px]" />
                                                    <span id="telephone_fixe-error" class="error-message text-red-600 text-[12px]"></span>
                                                    <label for="telephone_fixe"
                                                           class="absolute left-2 top-1 text-gray-400 text-[13px] transition-all
                                                                  peer-placeholder-shown:top-1 peer-focus:-top-3 peer-focus:text-blue-600
                                                                  peer-not-placeholder-shown:-top-4 bg-white px-1">
                                                        Téléphone fixe
                                                    </label>
                                                </div>

                                                <!-- Nom agent -->
                                                <div class="relative w-full max-w-[200px] mb-4">
                                                    <input id="nom-agent" type="text" name="nom-agent" placeholder=" "
                                                           value="${res.data.nom_agent}"
                                                           class="peer w-full h-7 px-3 border border-gray-300 rounded-md
                                                                  focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none text-[12px]" />
                                                    <span id="nom-agent-error" class="error-message text-red-600 text-[12px]"></span>
                                                    <label for="nom-agent"
                                                           class="absolute left-2 top-1 text-gray-400 text-[13px] transition-all
                                                                  peer-placeholder-shown:top-1 peer-focus:-top-3 peer-focus:text-blue-600
                                                                  peer-not-placeholder-shown:-top-4 bg-white px-1">
                                                        Nom agent de la structure
                                                    </label>
                                                </div>

                                                <!-- Numéro agent -->
                                                <div class="relative w-full max-w-[200px] mb-4">
                                                    <input id="numero-agent" type="text" name="numero-agent" placeholder=" "
                                                           value="${res.data.telephone_agent}"
                                                           class="peer w-full h-7 px-3 border border-gray-300 rounded-md
                                                                  focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none text-[12px]" />
                                                    <span id="numero-agent-error" class="error-message text-red-600 text-[12px]"></span>
                                                    <label for="numero-agent"
                                                           class="absolute left-2 top-1 text-gray-400 text-[13px] transition-all
                                                                  peer-placeholder-shown:top-1 peer-focus:-top-3 peer-focus:text-blue-600
                                                                  peer-not-placeholder-shown:-top-4 bg-white px-1">
                                                        Téléphone agent
                                                    </label>
                                                </div>

                                                <!-- Email -->
                                                <div class="relative w-full max-w-[200px] mb-4">
                                                    <input id="email" type="email" name="email" placeholder=" "
                                                           value="${res.data.email}"
                                                           class="peer w-full h-7 px-3 border border-gray-300 rounded-md
                                                                  focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none text-[12px]" />
                                                    <span id="email-error" class="error-message text-red-600 text-[12px]"></span>
                                                    <label for="email"
                                                           class="absolute left-2 top-1 text-gray-400 text-[13px] transition-all
                                                                  peer-placeholder-shown:top-1 peer-focus:-top-3 peer-focus:text-blue-600
                                                                  peer-not-placeholder-shown:-top-4 bg-white px-1">
                                                        Email
                                                    </label>
                                                </div>

                                                <!-- Adresse -->
                                                <div class="relative w-full max-w-[200px] mb-4">
                                                    <input id="adresse" type="text" name="adresse" placeholder=" "
                                                           value="${res.data.adresse}"
                                                           class="peer w-full h-7 px-3 border border-gray-300 rounded-md
                                                                  focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none text-[12px]" />
                                                    <span id="adresse-error" class="error-message text-red-600 text-[12px]"></span>
                                                    <label for="adresse"
                                                           class="absolute left-2 top-1 text-gray-400 text-[13px] transition-all
                                                                  peer-placeholder-shown:top-1 peer-focus:-top-3 peer-focus:text-blue-600
                                                                  peer-not-placeholder-shown:-top-4 bg-white px-1">
                                                        Adresse
                                                    </label>
                                                </div>
                                            </div>

                                            <!-- Description -->
                                            <div class="relative w-full mb-4">
                                                <textarea id="description" name="description" placeholder=" "
                                                          class="peer w-full px-3 border border-gray-300 rounded-md
                                                                 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none text-[12px] h-20">${res.data.description}</textarea>
                                                <span id="description-error" class="error-message text-red-600 text-[12px]"></span>
                                                <label for="description"
                                                       class="absolute left-2 top-1 text-gray-400 text-[13px] transition-all
                                                              peer-placeholder-shown:top-1 peer-focus:-top-3 peer-focus:text-blue-600
                                                              peer-not-placeholder-shown:-top-4 bg-white px-1">
                                                    Description
                                                </label>
                                            </div>

                                            <div class="relative w-full mb-4">
                                                <select id="statut" name="statut"
                                                        class="peer w-full h-8 px-3 border border-gray-300 rounded-md
                                                               focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none text-[12px] bg-white">
                                                    <option value="" selected disabled></option>
                                                    <option value="actif">Actif</option>
                                                    <option value="inactif">Inactif</option>
                                                </select>
                                                <span id="statut-error" class="error-message text-red-600 text-[12px]"></span>
                                                <label for="statut"
                                                       class="absolute left-2 top-1 text-gray-400 text-[13px] transition-all
                                                              peer-placeholder-shown:top-1 peer-placeholder-shown:text-gray-400
                                                              peer-focus:-top-3 peer-focus:text-blue-600
                                                              peer-not-placeholder-shown:-top-4 peer-not-placeholder-shown:text-blue-600
                                                              bg-white px-1">
                                                    Statut
                                                </label>
                                            </div>

                                            <div class="mb-4 text-[12px] w-full h-auto p-[3px] flex justify-center items-center">
                                                <div>
                                                    <label class="block mb-2 font-semibold text-gray-600">Logo du fournisseur (Cliquer sur l'image pour la mettre a jour)</label>
                                                    <p id="nom-du-nouveau-image" class="hidden text-green-700"></p>
                                                    <div id="big-bloc-image" class="max-w-[230px] max-h-[230px] text-[12px] p-[5px] border border-gray-300 rounded-[50%]">

                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Logo -->
                                            <div class="relative w-full mb-4 hidden">
                                                <input id="logo" type="file" name="logo"
                                                       class="w-full h-7 px-3 border border-gray-300 rounded-md
                                                              focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none text-[12px]" />
                                                <span id="logo-error" class="error-message text-red-600 text-[12px]"></span>
                                                <label for="logo"
                                                       class="absolute left-2 -top-3 text-[13px] text-blue-600 bg-white px-1">
                                                    Logo
                                                </label>
                                            </div>

                                        <input class="hidden" type="text" id="matricule" name="matricule" value="${matricule}">
                                        <input class="hidden" type="text" id="element" name="element" value="dealers">

                                        <!-- Bouton -->
                                        <button id="button-button" type="button"
                                                class="opacity-20 w-full bg-gray-600 cursor-pointer relative text-white py-3 rounded-md text-lg font-semibold transition duration-300">
                                                <div id="loader" class="hidden absolute left-[-200px] inset-0 flex items-center justify-center bg-transparent bg-opacity-30 z-50">
                                                    <div class="w-7 h-7 border-4 border-white border-dashed rounded-full animate-spin"></div>
                                                </div>
                                            Mettre a jour
                                        </button>
                                        <button id="button-submit" type="submit"
                                                class="hidden w-full bg-blue-600 hover:bg-green-700 text-white py-3 rounded-md text-lg font-semibold transition duration-300">
                                                 <div id="loader" class="hidden absolute left-[-200px] inset-0 flex items-center justify-center bg-transparent bg-opacity-30 z-50">
                                                    <div class="w-7 h-7 border-4 border-white border-dashed rounded-full animate-spin"></div>
                                                </div>
                                            Mettre a jour
                                        </button>
                                    </form>


                                    `
            const disabled_button = bloc_produit.querySelector("#button-button");
            const submit_button = bloc_produit.querySelector("#button-submit");
            const formu = bloc_produit.querySelector("#form-updating-element-everything-dealers");
            if(disabled_button){
                disabled_button.addEventListener('click', function () {
                    alert("Veuillez effectuer vos modifications souhaitées avant de mettre à jour le produit");
                })
                formu.addEventListener('input', function () {
                    disabled_button.classList.add('hidden');
                    submit_button.classList.remove('hidden');
                })
            }
            let photo_img_main = document.createElement('img');
            photo_img_main.src = "/storage/" + res.data.logo;
            photo_img_main.alt = "Photo du produit";
            photo_img_main.id = "current-logo"
            photo_img_main.title = "Cliquer pour mettre à jour les photos";
            photo_img_main.classList.add('w-full', 'max-h-[450px]', 'rounded-[50%]', 'p-3', 'shadow-md', 'cursor-pointer');
            bloc_produit.querySelector("#big-bloc-image").append(photo_img_main);
            bloc_produit.querySelector("#big-bloc-image").classList.add('max-h-[450px]', 'w-full', 'flex', 'justify-center');

            let options = bloc_produit.querySelector("#statut").children;
            if(res.data.statut === "actif"){
                options[1].setAttribute('selected', 'true');
            }else{
                options[2].setAttribute('selected', 'true');
            }
            bloc_produit.querySelector("#current-logo").addEventListener('click', function () {
                bloc_produit.querySelector('#logo').click();
            })
            bloc_produit.querySelector('#logo').addEventListener('change', function () {
                if(this.files && this.files.length > 0){
                    bloc_produit.querySelector("#nom-du-nouveau-image").classList.remove('hidden');
                    bloc_produit.querySelector("#nom-du-nouveau-image").textContent = "Nouvelle(s) image(s) chargeEs) !";
                }
            })


            document.querySelector("#"+bloc).innerHTML = '';
            document.querySelector("#"+bloc).appendChild(bloc_produit);
        })
    }
    else if(bloc === "bloc-view-update-employes"){
        gettingElementForUpdating(bloc, matricule ,'getsEmployes').then(res=>{
            // alert(JSON.stringify(res.shops));
            // alert(JSON.stringify(res.data));
            let bloc_produit = document.createElement('div');
            bloc_produit.classList.add('w-full', 'h-full', 'bg-transparent', 'overflow-auto', 'px-2', 'py-5', 'grid', 'place-items-center')
            bloc_produit.innerHTML = `
                                    <form id="form-updating-element-everything-employes" class="relative bg-white px-3 py-10 rounded-lg shadow-lg w-[75%]">
                                            @csrf <!-- Protection CSRF obligatoire dans Laravel -->

                                            <!-- Titre -->
                                            <h2 class="text-center text-2xl font-bold text-gray-700 mb-10">Detail de l'employer</h2>

                                            <!-- Message global -->
                                            <div id="global-message-employe"
                                                class="hidden text-green-700 font-bold mb-4 text-center bg-green-50 p-1 rounded border border-green-300"></div>

                                            <div class="flex w-full">
                                                <p class="text-[14px] font-bold">Responsable : </p>
                                                <p class="text-[14px] pl-3"> ${res.data[0].prenom_resp+" "+res.data[0].nom_resp} </p>
                                            </div>
                                            <fieldset class="rounded-md shadow-md w-full flex flex-col h-auto gap-3 justify-center mt-4 mb-4 shadow-md gap-2 p-2 rounded-md">
                                                <legend id="legend-profil" class="font-bold underline underline-offset-2 pl-3 text-[17px] text-blue-600">Profil</legend>

                                                <div class="flex flex-wrap text-[14px]">
                                                    <img src="/storage/${res.data[0].pp}" alt="Photo profil" title="Photo de profil de l'employer" class="shadow-md p-1 w-[200px] h-[200px] max-w-[200px] max-h-[200px] rounded-[50%]">
                                                    <div class="min-w=[200px] w-auto h-auto p-2 ml-3 rounded-md text-[14px]">
                                                         <p>${res.data[0].prenom}</p>
                                                         <p>${res.data[0].nom}</p>
                                                         <p>Ajoute le: ${res.data[0].date_ajout}</p>
                                                    </div>
                                                </div>

                                            </fieldset>
                                            <fieldset class="rounded-md shadow-md w-full h-auto flex flex-col gap-3 justify-center mt-4 mb-4 p-3">
                                                <legende class="font-bold underline underline-offset-2 pl-3 text-[17px] text-blue-600">Privileges</legende>
                                                <div id="privilege-employer" class="flex justify-around gap-3 w-full h-auto text-[13px] pl-10">
                                                    <div class="w-full h-auto flex flex-col">
                                                        <label for="eleve">Total</label>
                                                        <input type="radio" class="cursor-pointer w-[20px] h-[20px]" id="eleve" name="privilege" value="eleve">
                                                    </div>
                                                    <div class="w-full h-auto flex flex-col">
                                                        <label for="normal">Normal</label>
                                                        <input type="radio" class="cursor-pointer w-[20px] h-[20px]" id="normal" name="privilege" value="normal">
                                                    </div>
                                                </div>

                                            </fieldset>
                                             <fieldset class="rounded-md shadow-md w-full h-auto flex flex-col gap-3 justify-center mt-4 mb-4 p-3">
                                                <legende class="font-bold underline underline-offset-2 pl-3 text-[17px] text-blue-600">Gestion de stock</legende>
                                                <div id="gestion-stock-employer" class="flex justify-around gap-3 w-full h-auto text-[13px] pl-10">
                                                    <div class="w-full h-auto flex flex-col">
                                                        <label for="ok">Autorise</label>
                                                        <input type="radio" class="cursor-pointer w-[20px] h-[20px]" id="ok" name="gestion_stock" value="ok">
                                                    </div>
                                                    <div class="w-full h-auto flex flex-col">
                                                        <label for="none">Ne pas autorise</label>
                                                        <input type="radio" class="cursor-pointer w-[20px] h-[20px]" id="none" name="gestion_stock" value="none">
                                                    </div>
                                                </div>

                                            </fieldset>
                                            <fieldset class="shadow-md rounded-md w-full h-auto flex flex-col gap-3 justify-center mt-4 mb-4 p-2">
                                                <legend class="font-bold underline underline-offset-2 pl-3 text-[17px] text-blue-600">Fonctions</legend>
                                                <div class="flex justify-around shadow-md rounded-md p-3 text-[14px]">
                                                    <div id="role-employer" class="flex justify-around gap-3 w-full h-auto text-[13px] pl-10">
                                                    <div class="w-full h-auto flex flex-col">
                                                        <label for="caissier">Caissier</label>
                                                        <input type="radio" class="cursor-pointer w-[20px] h-[20px]" id="caissier" name="role" value="caissier">
                                                    </div>
                                                    <div class="w-full h-auto flex flex-col">
                                                        <label for="gerant">Gerant</label>
                                                        <input type="radio" class="cursor-pointer w-[20px] h-[20px]" id="gerant" name="role" value="gerant">
                                                    </div>

                                                </div>

                                                </div>

                                                <div class="shadow-md rounded-md p-3 text-[14px]">
                                                    <p class="font-bold underline underline-offset-2 pl-3 text-[17px] text-blue-600">Accreditations</p>
                                                    <div class="flex gap-3 justify-center">
                                                        <div id="bloc-structure-accreditation" class="w-full h-auto flex justify-around">

                                                        </div>
                                                    </div>
                                                </div>
                                            </fieldset>

                                            <div class="relative w-full mb-4">
                                                <select id="statut" name="statut"
                                                        class="peer w-full h-8 px-3 border border-gray-300 rounded-md
                                                               focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none text-[13px] bg-white">
                                                    <option value="" selected disabled></option>
                                                    <option value="actif">Actif</option>
                                                    <option value="inactif">Inactif</option>
                                                </select>
                                                <span id="statut-error" class="error-message text-red-600 text-[12px]"></span>
                                                <label for="statut"
                                                       class="absolute left-2 top-1 text-gray-400 text-[13px] transition-all
                                                              peer-placeholder-shown:top-1 peer-placeholder-shown:text-gray-400
                                                              peer-focus:-top-3 peer-focus:text-blue-600
                                                              peer-not-placeholder-shown:-top-4 peer-not-placeholder-shown:text-blue-600
                                                              bg-white px-1">
                                                    Statut
                                                </label>
                                            </div>

                                        <input class="hidden" type="text" id="matricule" name="matricule" value="${matricule}">
                                        <input class="hidden" type="text" id="element" name="element" value="employes">

                                        <!-- Bouton -->
                                        <button id="button-button" type="button"
                                                class="opacity-20 w-full bg-gray-600 cursor-pointer relative text-white py-3 rounded-md text-lg font-semibold transition duration-300">
                                                <div id="loader" class="hidden absolute left-[-200px] inset-0 flex items-center justify-center bg-transparent bg-opacity-30 z-50">
                                                    <div class="w-7 h-7 border-4 border-white border-dashed rounded-full animate-spin"></div>
                                                </div>
                                            Mettre a jour
                                        </button>
                                        <button id="button-submit" type="submit"
                                                class="hidden w-full bg-blue-600 hover:bg-green-700 text-white py-3 rounded-md text-lg font-semibold transition duration-300">
                                                 <div id="loader" class="hidden absolute left-[-200px] inset-0 flex items-center justify-center bg-transparent bg-opacity-30 z-50">
                                                    <div class="w-7 h-7 border-4 border-white border-dashed rounded-full animate-spin"></div>
                                                </div>
                                            Mettre a jour
                                        </button>
                                    </form>

                                    `
            const disabled_button = bloc_produit.querySelector("#button-button");
            const submit_button = bloc_produit.querySelector("#button-submit");
            const formu = bloc_produit.querySelector("#form-updating-element-everything-employes");
            if(disabled_button){
                disabled_button.addEventListener('click', function () {
                    alert("Veuillez effectuer vos modifications souhaitées avant de mettre à jour le produit");
                })
                formu.addEventListener('input', function () {
                    disabled_button.classList.add('hidden');
                    submit_button.classList.remove('hidden');
                })
            }

            let options = bloc_produit.querySelector("#statut").children;
            if(res.data[0].statut === "actif"){
                options[1].setAttribute('selected', 'true');
            }else{
                options[2].setAttribute('selected', 'true');
            }
            const bloc_accreditation_shop = bloc_produit.querySelector("#bloc-structure-accreditation");

            res.shops.forEach( shop =>{
                res.data.forEach(data=>{
                    if(shop.structure_matricule === data.structure_matricule){
                        if(data.statut === 'actif'){
                            let element_label = document.createElement('label');
                            let element_input = document.createElement('input');
                            let element_bloc = document.createElement('div');
                            element_label.textContent = shop.nom_structure;
                            element_label.htmlFor = shop.structure_matricule;
                            element_input.id = shop.structure_matricule;
                            element_input.name = shop.structure_matricule;
                            element_input.checked = true;
                            element_input.type = 'checkbox';
                            element_bloc.appendChild(element_label);
                            element_bloc.appendChild(element_input);
                            bloc_accreditation_shop.appendChild(element_bloc);
                            element_input.classList.add('w-[20px]', 'h-[20px]', 'p2', 'cursor-pointer');
                            element_bloc.classList.add("w-auto", "h-auto", "flex", "flex-wrap", "p-2", 'gap-2')
                        }else if(data.statut === 'inactif'){
                            let element_label = document.createElement('label');
                            let element_input = document.createElement('input');
                            let element_bloc = document.createElement('div');
                            element_label.textContent = shop.nom_structure;
                            element_label.htmlFor = shop.structure_matricule;
                            element_input.id = shop.structure_matricule;
                            element_input.name = shop.structure_matricule;
                            element_input.type = 'checkbox';
                            element_bloc.appendChild(element_label);
                            element_bloc.appendChild(element_input);
                            bloc_accreditation_shop.appendChild(element_bloc);
                            element_input.classList.add('w-[20px]', 'h-[20px]', 'p2', 'cursor-pointer');
                            element_bloc.classList.add("w-auto", "h-auto", "flex", "flex-wrap", "p-2", 'gap-2')
                        }
                    }
                })

                const exist = res.data.some(d=>d.structure_matricule === shop.structure_matricule)
                if (!exist) {
                    let element_label = document.createElement('label');
                    let element_input = document.createElement('input');
                    let element_bloc = document.createElement('div');
                    element_label.textContent = shop.nom_structure;
                    element_label.htmlFor = shop.structure_matricule;
                    element_input.id = shop.structure_matricule;
                    element_input.name = shop.structure_matricule;
                    element_input.type = 'checkbox';
                    element_bloc.appendChild(element_label);
                    element_bloc.appendChild(element_input);
                    bloc_accreditation_shop.appendChild(element_bloc);
                    element_input.classList.add('w-[20px]', 'h-[20px]', 'p2', 'cursor-pointer');
                    element_bloc.classList.add("w-auto", "h-auto", "flex", "flex-wrap", "p-2", 'gap-2')
                }

            })
            bloc_produit.querySelector("#role-employer #"+res.data[0].role).setAttribute('checked', 'true');
            bloc_produit.querySelector("#gestion-stock-employer #"+res.data[0].stock).setAttribute('checked', 'true');
            bloc_produit.querySelector("#privilege-employer #"+res.data[0].privilege).setAttribute('checked', 'true');


            document.querySelector("#"+bloc).innerHTML = '';
            document.querySelector("#"+bloc).appendChild(bloc_produit);
        })
    }
}

function loadAccountDetails() {
    let url = $('meta[name="route-store-get-account-details"]').attr("content");

    // Requête GET vers le serveur
    $.get(url, function (data) {
        console.log("Boutiques reçues :", data);
        // alert(JSON.stringify(data.data));
        displayDetailsAccount(data.data);

    }).fail(function (xhr) {
        console.error("Erreur :", xhr.responseText);
        alert(xhr.responseText);
        // alert("Veuillez proceder a la creation de votre premiere Boutique !");
    });
}

function displayDetailsAccount(data) {
    $('#form-account-update-param #pseudo').val(data.pseudo ? data.pseudo : 'inconnu');
    $('#form-account-update-param #email').val(data.email ? data.email : 'inconnu');
    $('#form-account-update-param #telephone1').val(data.telephone1 ? data.telephone1 : 'inconnu');
    const loader = droite.querySelector('#bloc-option-param-compte #form-account-update-param #loader');
    if (loader){
        loader.classList.add('hidden');
    }
}

