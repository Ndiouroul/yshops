let panierDict = [];

const panierProxy = new Proxy(panierDict, {
    set(target, prop, value) {
        // Mettre à jour la valeur
        target[prop] = value;

        if (!isNaN(prop)) { // indices numériques du tableau
            console.log("➡️ Panier modifié :", target);
            if (target.length > 0){
                updateAffichagePanier(target); // action spécifique
            }
        }
        else if(target.length === 0){
            removeNotifPanier()
        }
        return true;
    }
});

function updateAffichagePanier(panier) {
    console.log("🔄 Panier mis à jour :", panier);
    notifPanier()
}
function notifPanier() {
    let iconPanier = document.querySelector("#blocCommandePara #panierCommandeEtat");
    iconPanier.classList.remove('hidden');
}
function removeNotifPanier(){
    let iconPanier = document.querySelector("#blocCommandePara #panierCommandeEtat");
    iconPanier.textContent = '';
    iconPanier.classList.add('hidden');
}

document.addEventListener('DOMContentLoaded', ()=>{
    let matricule= $('meta[name="structure_matricule"]').attr('content');
    getShopDetails(matricule);
    // alert($('meta[name="structure_matricule"]').attr('content'));
})
document.querySelector("#blocCommandePara").addEventListener("click", function () {
    displayDetailsCommande(panierDict);
})
document.querySelector("#fermerPanier").addEventListener('click', function () {
    document.querySelector("#bloc-show-details-commande").classList.add('hidden', '-translate-x-[150%]')
})
document.querySelector("#annulerCommande").addEventListener('click', function () {
    panierProxy.length = 0;
    document.querySelector("#bloc-show-details-commande").classList.add('hidden', '-translate-x-[150%]')
})

function updatePanier(panier, nom, image, prix, matricule, quantite, total) {
    for (let element of panier){
        if(element['nom'] === nom){
            alert("Produit deja ajoute");
            return;
        }
    }
    panier[panier.length] = {
        'nom' : nom,
        'image' : image,
        'prix' : prix,
        'quantite' : quantite,
        'total' : total,
        'matricule' : matricule,
    };
}

function formatNumber(value) {
    return parseFloat(value || 0).toLocaleString('fr-FR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

function getShopDetails(matricule) {
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    let route = $('meta[name="route-get-shop-details"]').attr("content")
    route = route.replace('__MATRICULE__', matricule);
    $.ajax({
        type: 'get',
        url: route,
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
                createBlocDetailsStructure(response.structure);
                createBlocDetailsProducts(response.products);
            }

        },
        error: function(xhr) {
            if (xhr.status === 422) { // Erreurs de validation de Laravel
                const errors = xhr.responseJSON.errors;
                alert(errors);

            } else if (xhr.status === 401) {
                // Rediriger manuellement vers la page de login
            }else  { // Autres erreurs serveur (par exemple 500)
                let errorMessage = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Erreur inattendue du serveur.';
                alert(errorMessage);
            }
        }
    });
}

function createBlocDetailsStructure(details) {
    let container = document.querySelector('#droite #bloc-details-shop');
    let divSubContainer = document.createElement('div');
    divSubContainer.classList.add('p-2', 'items-center', 'flex','gap-7', "p-4", 'ml-2', 'w-[95%]', 'h-full', 'opacity-0', 'translate-y-4', 'transition-all', 'duration-500')
    container.innerHTML = '';
    details.forEach((element, index)=>{
        divSubContainer.innerHTML = `
        <img src="/storage/${element.logo}" alt="Logo" class="p-2 w-47 h-47 rounded-[50%] shadow-md text-[13px]">
        <div class="w-[100%]" >
            <p class="text-[19px] font-bold relative top-[-10px] underline">
            ${element.nom_structure} ${element.sigle_structure ? '('+element.sigle_structure+')' : ''}
            </p>
            <p class="text-[13px] ">${element.telephone1}</p>
            <p class="text-[13px] ">${element.email_structure}</p>
            <p class="text-[13px] ">${element.adresse_structure}</p>
            <p class="text-[11px] text-gray-700 overflow-hidden h-[50px]">${element.description}</p>
        </div>
     `
        container.appendChild(divSubContainer);
        setTimeout(() => {
            divSubContainer.classList.remove('opacity-0', 'translate-y-4');
            divSubContainer.classList.add('opacity-100', 'translate-y-0');
        }, index * 200); // 200ms entre chaque produit
    })

}

function createBlocDetailsProducts(products) {
    let container = document.querySelector('#droite #bloc-products');
    container.innerHTML = '';

    products.forEach((element, index) => {

        let divSubContainer = document.createElement('div');
        divSubContainer.classList.add(
            "p-1", 'w-[175px]', 'h-[250px]', 'rounded-md',
            'bg-white', 'shadow-md', 'grid', 'cursor-pointer',
            'opacity-0', 'translate-y-4', 'transition-all', 'duration-500'
        );

        divSubContainer.id = 'bloc'+index;

        let displayImage = 'default.png';
        if (element.image) {
            let imagesArray = element.image.includes(',') ? element.image.split(',') : [element.image];
            displayImage = imagesArray[0];
        }

        divSubContainer.innerHTML = `
            <img src="/storage/${displayImage}" alt="image du produit" class="w-full h-[170px] mb-1 rounded-md">
            <hr class="w-[90%] relative left-2 h-2">
            <p class="text-[13px] font-bold">${element.nom_produit}</p>
            <p class="text-[13px] ">${formatNumber(element.prix_vente)}</p>
            <p class="text-[13px] ">
                ${element.quantite > 0
            ? '<span class="bg-green-300 py-[1px] px-2">En Stock</span>'
            : '<span class="bg-red-300 py-[1px] px-2">Épuisé</span>'}
            </p>
        `;

        divSubContainer.addEventListener('click', function (){
            displayDetailsProducts(element);
        })
        container.appendChild(divSubContainer);

        // ⚡ Effet cascade : apparition différée
        setTimeout(() => {
            divSubContainer.classList.remove('opacity-0', 'translate-y-4');
            divSubContainer.classList.add('opacity-100', 'translate-y-0');
        }, index * 200); // 200ms entre chaque produit
    });
}

function displayDetailsProducts(element) {
    let container = document.querySelector("#bloc-show-details-products");
    container.innerHTML = '';
    let displayImage = 'default.png';
    let imagesArray = [];
    if (element.image) {
        imagesArray = element.image.includes(',') ? element.image.split(',') : [element.image];
        displayImage = imagesArray[0];
    }

    let divSubcontain = document.createElement('div');
    divSubcontain.classList.add('rounded-md', 'h-auto', 'bg-white', 'w-[40%]', 'min-h-[375px]', 'p-2', 'min-w-[400px]', 'relative')
    divSubcontain.innerHTML=`
        <span id="fermerProduct" class="absolute right-[5px] top-[5px] text-white bg-red-600 cursor-pointer hover:bg-red-700 rounded-md shadow-md px-[10px] py-[2px] text-[13px]">Fermer</span>
        <img src="/storage/${displayImage}" alt="Image du produit" class="w-full h-[300px]">
        <div id="bloc-image-complement" class="flex justify-between w-full h-auto py-2">

        </div>
        <div class="w-full flex items-center">
            <p class="text-gray-600 text-[13px] w-[75%]">${element.description}</p>
            <button id="ajouter-panier" type="w-[100%] button" class="absolute right-[10px] cursor-pointer flex bg-blue-600 rounded-md text-[13px] text-white py-[5px] px-[10px] text-center hover:bg-blue-700">Ajouter panier</button>
        </div>
        <div id="formValiderCommande" class="p-2 mt-7 w-full hidden">
             <div class="flex w-full justify-between text-center items-center w-full">
                 <div class="flex flex-col w-auto items-center">
                    <p class="text-[13px] font-bold">Prix Unitaire</p>
                    <input id="prix_unitaire" name="prix_unitaire" class="w-[80%] rounded-md bg-gray-100 text-center text-[13px]" disabled readonly type="numeric" value="${formatNumber(element.prix_vente)}">
                </div>
                <div class="w-[25%] h-auto flex items-center justify-center text-center">
                    <button type="button" id="enleverProduit" class="hover:bg-orange-700 rounded-md bg-orange-600 text-[20px] cursor-pointer text-white font-bold w-[80%]">-</button>
                </div>
                 <div class="flex flex-col w-auto items-center">
                    <p class="text-[13px] font-bold">Quantite</p>
                    <input id="nombreElementPanier" name="nombreElementPanier" class="w-[80%] rounded-md bg-gray-100 text-center text-[13px]" type="number" value="1">
                </div>
                <div class="w-[25%] h-auto flex items-center justify-center text-center">
                    <button type="button" id="ajouterProduit" class="hover:bg-green-700 rounded-md bg-green-600 text-[20px] cursor-pointer text-white w-[90%] font-bold">+</button>
                </div>
                <div class="flex flex-col w-auto items-center">
                    <p class="text-[13px] font-bold">Total</p>
                    <input id="totalPrix" name="totalPrix" type="numeric" class="w-[80%] rounded-md bg-gray-100 text-center text-[13px]" disabled readonly value="${formatNumber(element.prix_vente)}">
                </div>
                <div class="flex flex-col w-auto items-center hidden">
                    <p class="text-[13px] font-bold">Total</p>
                    <input id="matriculeProduct" name="matriculeProduct" type="numeric" class="w-[80%] rounded-md bg-gray-100 text-center text-[13px]" disabled readonly value="${element.product_matricule}">
                </div>
            </div>

            <div><button type="button" id="ajouterProduitPanier" class="text-[14px] text-white w-full rounded bg-blue-600 font-bold hover:bg-blue-700 cursor-pointer py-[10px] mt-4">Ajouter</button></div>
        </div>
    `
    imagesArray.forEach(item=>{
        let blocImageComplement = document.createElement('div');
        blocImageComplement.classList.add('w-12', 'h-12')
        blocImageComplement.innerHTML = `
            <div><img src="/storage/${item}" alt="Image" class="w-full h-full"></div>
        `
        divSubcontain.querySelector("#bloc-image-complement").appendChild(blocImageComplement);
    })
    container.appendChild(divSubcontain);
    container.classList.remove('hidden', '-translate-x-[150%]');

    divSubcontain.querySelector("#ajouter-panier").addEventListener('click', function (){
        divSubcontain.querySelector("#formValiderCommande").classList.remove('hidden');
    });
    divSubcontain.querySelector("#fermerProduct").addEventListener('click', function (){
        container.classList.add('hidden');
    });
    divSubcontain.querySelector("#nombreElementPanier").addEventListener('change', function (e) {
        let total = element.prix_vente * e.target.value;
        divSubcontain.querySelector("#totalPrix").value = formatNumber(total);
    })
    divSubcontain.querySelector('#ajouterProduit').addEventListener('click', function () {
        divSubcontain.querySelector("#nombreElementPanier").value ++;
        let total = element.prix_vente * divSubcontain.querySelector("#nombreElementPanier").value;
        divSubcontain.querySelector("#totalPrix").value = formatNumber(total);
    })
    divSubcontain.querySelector('#enleverProduit').addEventListener('click', function () {
        if(divSubcontain.querySelector("#nombreElementPanier").value >0){
            divSubcontain.querySelector("#nombreElementPanier").value --;
            let total = element.prix_vente * divSubcontain.querySelector("#nombreElementPanier").value;
            divSubcontain.querySelector("#totalPrix").value = formatNumber(total);
        }
    })

    divSubcontain.querySelector("#ajouterProduitPanier").addEventListener('click', function () {
        updatePanier(panierProxy, element.nom_produit, element.image, element.prix_vente, element.product_matricule,
            parseInt(divSubcontain.querySelector("#nombreElementPanier").value),
            parseFloat(divSubcontain.querySelector("#totalPrix").value)
        );
        container.classList.add('hidden', '-translate-x-[150%]');
    });


}

function updateFinalCommande(){
    let bloc = document.querySelectorAll('[id^="blocElementProduitCommande"]');
    let totalGeneral = 0;
    bloc.forEach(element=>{
        let prix = parseFloat(element.querySelector("[id^='prix']").value.replace(/\s/g, ''));
        let quantite = element.querySelector("[id^='quantite']").value;
        element.querySelector("[id^='total']").value = formatNumberWithSpaces(quantite*prix)
        totalGeneral += quantite*prix;
    })
    document.querySelector("#totalgeneral").value = formatNumberWithSpaces(totalGeneral);
}

function formatNumberWithSpaces(number) {
    return new Intl.NumberFormat('fr-FR').format(number);
}

function displayDetailsCommande(dict) {
    const containerParent = document.querySelector("#bloc-show-details-commande");
    const containerFieldset = document.querySelector("#bloc-show-details-commande #form-send-valide-commande #espace-ajout-details-commande");
    const containerFieldsetTotalGenaral = document.querySelector("#bloc-show-details-commande #form-send-valide-commande #espace-ajout-total-general-commande");

    // Nettoyage des conteneurs
    containerFieldset.innerHTML = '';
    containerFieldsetTotalGenaral.innerHTML = '';

    let cmt = 0;
    let totalGeneral = 0;

    for (let element of dict) {
        let prix = parseFloat(element.prix);
        let quantite = parseFloat(element.quantite);
        let total = prix * quantite;

        // Ajout au total général
        totalGeneral += total;

        let divSubContainer = document.createElement('div');
        divSubContainer.id = 'blocElementProduitCommande'+cmt;
        divSubContainer.innerHTML = `
            <div class="flex w-full h-auto p-1 shadow-md">
                <div class="flex flex-col w-[40%] text-[13px] h-[75px] overflow-hidden text-center">
                    <p class="overflow-hidden flex justify-start">${element.nom}</p>
                    <img src="storage/${element.image}" alt="Photo" class="w-[55px] h-[55px] rounded-[50%]">
                </div>
                <div class="flex flex-col w-[22%] justify-center text-[13px] h-[75px] items-center">
                    <input class="text-center w-full h-auto"
                           id="prix${cmt}"
                           name="prix${cmt}"
                           type="text"
                           value="${formatNumberWithSpaces(prix)}"
                           readonly>
                </div>
                <div class="flex flex-col w-[15%] justify-center text-[13px] h-[75px] items-center">
                    <input class="text-center w-full h-auto"
                           id="quantite${cmt}"
                           name="quantite${cmt}"
                           type="number"
                           value="${quantite}">
                </div>
                <div class="flex flex-col w-[23%] justify-center text-[13px] h-[75px] items-center">
                    <input class="text-center w-full h-auto font-bold"
                           id="total${cmt}"
                           name="total${cmt}"
                           type="text"
                           value="${formatNumberWithSpaces(total)}"
                           readonly>
                </div>
                <div class="flex flex-col w-[23%] justify-center text-[13px] h-[75px] items-center hidden">
                    <input class="text-center w-full h-auto font-bold"
                           id="product_matricule${cmt}"
                           name="product_matricule${cmt}"
                           type="text"
                           value="${element.matricule}"
                           readonly>
                </div>
            </div>
        `;
        containerFieldset.appendChild(divSubContainer);

        divSubContainer.querySelector('#quantite'+cmt).addEventListener('change', function () {
            if (this.value >= 0){
                updateFinalCommande()
            }else{
                this.value = 0;
                updateFinalCommande()
            }
        });

        cmt++;
    }

    // Bloc du total général
    let divSubContainer2 = document.createElement('div');
    divSubContainer2.innerHTML = `
        <div class="text-white px-3 py-3 rounded-md text-[14px] w-auto font-bold flex justify-between m-2 bg-green-600 cursor-pointer">
            <p class="w-full">Total général</p>
            <input id="totalgeneral"
                   type="text"
                    name="totalgeneral"
                   value="${formatNumberWithSpaces(totalGeneral)}"
                   class="w-full font-bold rounded-md pl-3"
                   readonly>
            <span class="relative left-[-75px]">FCFA</span>
             <input id="index"
                   type="number"
                    name="index"
                   value="${cmt}"
                   class="hidden w-full font-bold rounded-md pl-3"
                   readonly>
        </div>
    `;
    containerFieldsetTotalGenaral.appendChild(divSubContainer2);
    containerParent.classList.remove('hidden', '-translate-x-[150%]');

}

$(document).ready(function() {
    // Récupérer le token CSRF et l'URL de la route depuis les balises meta
    const csrfToken = $('meta[name="csrf-token"]').attr('content');

    function envoyerFormulaire(formId, Route, methode) {

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
                if (response.success === true) { // Vérifie la propriété 'success' du JSON du contrôleur Laravel
                    if (response.type === 'ValidCommandeRas'){
                        alert(response.message);
                        panierProxy.length =0;
                    }
                }
                else if (response.success === false){
                    if (response.type === 'noProduct'){
                        alert(response.message);
                    }
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) { // Erreurs de validation de Laravel
                    const errors = xhr.responseJSON.errors;
                    alert(errors);
                } else { // Autres erreurs serveur (par exemple 500)
                    let errorMessage = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Erreur inattendue du serveur.';
                    // alert(errorMessage);
                    if(errorMessage === "Unauthenticated."){
                        alert('Veuillez vous connecte avant de soumettre cette commande');
                        window.location.href = "/connexion";
                    }
                }
            }
        });
    }

    // Gestion de la soumission du formulaire d'inscription
    $('#form-send-valide-commande').submit(function(event) {
        event.preventDefault();
        envoyerFormulaire('form-send-valide-commande', $('meta[name="route-send-commande-details"]').attr('content'), 'POST');
    });

});

document.querySelector('#gauche').addEventListener('click', function (e) {
    if(e.target === document.querySelector("#abonner")){
        linker($('meta[name="route-abonner"]').attr('content'));
    }else if(e.target === document.querySelector("#voter")){
        linker($('meta[name="route-voter"]').attr('content'));
    }else if(e.target === document.querySelector("#liker")){
        linker($('meta[name="route-liker"]').attr('content'));
    }else if(e.target === document.querySelector("#commenter")){
        alert("Nous y travaillons");
    }else if(e.target.closest("#send-research")){
        let chaine = document.querySelector("#chaine-researched");

        if(chaine.classList.contains('ring-1', 'shadow-md', 'shadow-red-600', 'ring-red-600', 'ring-offset-2')){
            chaine.classList.remove('ring-1', 'shadow-md', 'shadow-red-600', 'ring-red-600', 'ring-offset-2');
        }
        if(chaine.value === ''){
            alert("Veuillet specifier un nom de produit a rechercher");
            chaine.classList.add('ring-1', 'shadow-md', 'shadow-red-600', 'ring-red-600', 'ring-offset-2')
        }else{
            researchProducts($('meta[name="route-search-products-shop-details"]').attr('content'), chaine.value);
        }
    }
});
function linker(route){
    let matricule = $("meta[name='structure_matricule']").attr('content');
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    route = route.replace('__MATRICULE__', matricule);
    $.ajax({
        type: 'get',
        url: route,
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
                alert(JSON.stringify(response.message));
            }

        },
        error: function(xhr) {
            if (xhr.status === 422) { // Erreurs de validation de Laravel
                const errors = xhr.responseJSON.errors;
                alert(errors);

            } else if (xhr.status === 401) {
                // Rediriger manuellement vers la page de login
            }else  { // Autres erreurs serveur (par exemple 500)
                let errorMessage = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Erreur inattendue du serveur.';
                alert(errorMessage);
            }
        }
    });
}

function researchProducts(route, chaine) {
    let matricule = $("meta[name='structure_matricule']").attr('content');
    let formData = new FormData();
    formData.append('matricule', matricule);
    formData.append('chaine', chaine);
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    // route = route.replace('__MATRICULE__', tab);
    $.ajax({
        type: 'post',
        url: route,
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
                // alert(JSON.stringify(response.data));
                createBlocDetailsProducts(response.data);
            }

        },
        error: function(xhr) {
            if (xhr.status === 422) { // Erreurs de validation de Laravel
                const errors = xhr.responseJSON.errors;
                alert(errors);

            } else if (xhr.status === 401) {
                // Rediriger manuellement vers la page de login
            }else  { // Autres erreurs serveur (par exemple 500)
                let errorMessage = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Erreur inattendue du serveur.';
                alert(errorMessage);
            }
        }
    });
}
