function logout(){
    window.location = window.location.protocol + "//"+ window.location.host +"/logout.php";
}

function selectionne(pValeur, pSelection,  pObjet) {
    //active l'objet pObjet du formulaire si la valeur s�lectionn�e (pSelection) est �gale � la valeur attendue (pValeur)
    if (pSelection==pValeur) 
        { formRAPPORT_VISITE.elements[pObjet].disabled=false; }
    else { formRAPPORT_VISITE.elements[pObjet].disabled=true; }
}

function ajoutLigne(pNumero){//ajoute une ligne de produits/qt� � la div "lignes"     
    //masque le bouton en cours
    document.getElementById("but"+pNumero).setAttribute("hidden","true");	
    pNumero++;										//incr�mente le num�ro de ligne
    document.getElementsByName("nbechantillon")[0].value = pNumero;

    var laDiv = document.getElementById("lignes");	//recupere l'objet DOM qui contient les donn�es
    var titre = document.createElement("label") ;	//cree un label
    titre.setAttribute("class","titre") ;			//definit les proprietes
    titre.innerHTML= "   Produit : ";
    
    var liste = document.createElement("select");	//ajoute une liste pour proposer les produits
    liste.setAttribute("name","PRA_ECH"+pNumero) ;
    liste.setAttribute("class","zone");
    //remplit la liste avec les valeurs de la premiere liste construite en PHP à partir de la base
    liste.innerHTML = formRAPPORT_VISITE.elements["PRA_ECH1"].innerHTML;

    var qtetitre = document.createElement("label") ;	//cree un label
    qtetitre.setAttribute("class","titre") ;			//definit les proprietes
    qtetitre.setAttribute("id", "qte")
    qtetitre.innerHTML = "   Qté : ";
    
    var qte = document.createElement("input");
    qte.setAttribute("name","PRA_QTE"+pNumero);
    qte.setAttribute("size","2"); 
    qte.setAttribute("class","zone");
    qte.setAttribute("type","text");
    
    // Bouton +
    var bouton = document.createElement("input");
    //ajoute une gestion evenementielle en faisant evoluer le numero de la ligne
    bouton.setAttribute("onClick","ajoutLigne("+ pNumero +");");
    bouton.setAttribute("type","button");
    bouton.setAttribute("value","+");
    bouton.setAttribute("class","zone");	
    bouton.setAttribute("id","but"+ pNumero);
    
    
    // Ajout dans la div
    laDiv.appendChild(document.createElement("br")); // Ajouter
    laDiv.appendChild(titre); // Produit : 						
    laDiv.appendChild(liste); // input select
    laDiv.appendChild(qtetitre); // Qté : 
    laDiv.appendChild(qte); // input text
    laDiv.appendChild(bouton); // Ajouter
}




//////////////////////////////////////////////////////////////////////////
// searchby, orderby_infotype, orderby_input
//
//  FILTER SYSTEM 'TRIER PAR' ON PRATICIENS PAGE
//
//
//////////////////////////////////////////////////////////////////////////

$("#orderby_type").on('change', function(){
    
    var selectedtype = $("#orderby_type").find(':selected').val();
    
    var value = this.value;
    var formdata = {
            'selectioned': value
    };

    if(value == 0){              
        $.ajax({
            url: "http://gsb.test:8080/data/praticiens.php",
            data: formdata,
            method: "POST"
        }).done((data) => {
            document.getElementsByClassName("table")[0].innerHTML = data;
        });
    } else {
        $.ajax({
            url: "http://gsb.test:8080/data/praticiens.php",
            data: formdata,
            method: "POST"
        }).done((data) => {
            console.log(data);
            document.getElementById("orderby_infotype").innerHTML = data;
        });
    }
});


// 2nd option
$("#orderby_infotype").on('change', function(){

    var selectedtype = $("#orderby_type").find(':selected').val();
    var selectedinfotype = $("#orderby_infotype").find(':selected').val();

    var formdata = {
        'selectioned': selectedtype,
        'selectedinfotype': selectedinfotype
    }

    $.ajax({
        url: "http://gsb.test:8080/data/praticiens.php",
        data: formdata,
        method: "POST"
    }).done((data) => {
        document.getElementsByClassName("table")[0].innerHTML = data;
        document.getElementById("pagination").innerHTML = "";
    });

});

//////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////






// When trying to update report
$("#updateform").submit((e) => {
    e.preventDefault();

    var selectedtype = $("[name='PRA_ECH1']").find(':selected').val();

    // check if it's a def saisie
    var saisiedef = 0;
    if($("#saisiedef").prop('checked') == true){
        saisiedef = 1;
    }

    // get number of echantillons
    var nb_echantillonjs = $("[name='nbechantillon']").val();
    var nbarray = {};

    // insert each echantillon into a json list (nbarray)
    for(i=1; i <= nb_echantillonjs; i++){
        var elementmedic = $(`[name='PRA_ECH${i}']`).find("option:selected").val();    
        var elementqte = $(`[name='PRA_QTE${i}']`).val();

        nbarray[`PRA_ECH${i}`] = `${elementmedic}`;
        nbarray[`PRA_QTE${i}`] = `${elementqte}`;
    }

    var formdata = {
        bilan: $("#bilan").val(),
        saisiedef: saisiedef,
        rapid:  $("#rapid").val(),
        produit1: $("#produit1").find("option:selected").val(),
        produit2: $("#produit2").find("option:selected").val(),
        nbechantillon: nb_echantillonjs
        
    };  

    // concatenate formdata and nbarray
    var formdata = $.extend(true, formdata, nbarray);

    // create post request with formdata
    $.ajax({
        type: "POST",
        url: "http://gsb.test:8080/controller/update_rapport_controller.php",
        data: formdata
    }).done((data) => {
        if(data == "Success"){
            document.getElementById("info").innerText = "Votre Rapport à bien été mis à jour.";
        } else {
            document.getElementById("info").innerText = "Erreur: " + data;
        }
    })

});













// When clicking on 'Supprimer', ask if he really want to do it
$(".deleterap").click(function(e) {

    var confirmdelete = confirm("Etes vous sûr de supprimer votre rapport ? Toute suppression est définitif !");
    if(confirmdelete != true){
        e.preventDefault();
        return;
    }
});

// when checking the checkbox (to toggle delete mode), ask if he really wants to delete a report
$('#toggledeleterap').click(function() {
    // prompt and if he keeps saying yes, display 'Supprimer' button
    if($(this).prop('checked') == true){
        var confirmdelete = confirm("Etes vous sûr d'activer le mode suppression ? Toute suppression est définitif !");
        if(confirmdelete == true){
            $(".deleterap").css('display', 'block');
        }
    } else {
        $(".deleterap").css('display', 'none');
    }

});



