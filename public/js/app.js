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
    
    var laDiv = document.getElementById("lignes");	//recupere l'objet DOM qui contient les donn�es
    var titre = document.createElement("label") ;	//cree un label
    laDiv.appendChild(titre) ;						//l'ajoute à la DIV
    titre.setAttribute("class","titre") ;			//definit les proprietes
    titre.innerHTML= "   Produit : ";
    
    var liste = document.createElement("select");	//ajoute une liste pour proposer les produits
    laDiv.appendChild(liste) ;
    liste.setAttribute("name","PRA_ECH"+pNumero) ;
    liste.setAttribute("class","zone");

    //remplit la liste avec les valeurs de la premiere liste construite en PHP à partir de la base
    liste.innerHTML=formRAPPORT_VISITE.elements["PRA_ECH1"].innerHTML;
    var qte = document.createElement("input");
    laDiv.appendChild(qte);
    qte.setAttribute("name","PRA_QTE"+pNumero);
    qte.setAttribute("size","2"); 
    qte.setAttribute("class","zone");
    qte.setAttribute("type","text");
    var bouton = document.createElement("input");
    laDiv.appendChild(bouton);

    //ajoute une gestion evenementielle en faisant evoluer le numero de la ligne
    bouton.setAttribute("onClick","ajoutLigne("+ pNumero +");");
    bouton.setAttribute("type","button");
    bouton.setAttribute("value","+");
    bouton.setAttribute("class","zone");	
    bouton.setAttribute("id","but"+ pNumero);				
}
